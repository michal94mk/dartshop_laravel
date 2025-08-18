<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Category;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Http\Request;

class ProductService
{
    /**
     * Get filtered and paginated products.
     *
     * @param Request $request
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getProducts(Request $request)
    {
        $cacheKey = 'products_list_' . md5(json_encode($request->all()));
        
        return Cache::remember($cacheKey, 1800, function () use ($request, $cacheKey) {
            Log::info('Cache MISS - executing query for key: ' . $cacheKey);
            
            $query = $this->buildProductQuery($request);
            
            // Pagination
            $perPage = (int)($request->per_page ?? 12);
            $perPage = max(1, min($perPage, 50)); // Limit between 1 and 50
            
            return $query->paginate($perPage);
        });
    }

    /**
     * Build product query with filters
     */
    protected function buildProductQuery(Request $request): Builder
    {
        $query = Product::with(['category', 'brand', 'activePromotions']);
        
        // Apply basic filters
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        
        if ($request->has('brand_id')) {
            $query->where('brand_id', $request->brand_id);
        }
        
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }
        
        // Price range filtering
        $this->applyPriceFilters($query, $request);
        
        // Feature filters
        $this->applyFeatureFilters($query, $request);
        
        // Apply sorting
        $this->applySorting($query, $request);
        
        return $query;
    }

    /**
     * Apply price range filters to query
     */
    protected function applyPriceFilters(Builder $query, Request $request): void
    {
        if ($request->has('price_min') && is_numeric($request->price_min)) {
            $priceMin = (float)$request->price_min;
            $query->where(function($q) use ($priceMin) {
                $q->where('price', '>=', $priceMin)
                  ->orWhereHas('activePromotions', function($promotionQuery) use ($priceMin) {
                      $promotionQuery->whereRaw('
                          CASE 
                              WHEN promotions.discount_type = "percentage" THEN 
                                  products.price * (1 - (promotions.discount_value / 100))
                              WHEN promotions.discount_type = "fixed" THEN 
                                  GREATEST(0, products.price - promotions.discount_value)
                              ELSE products.price
                          END >= ?
                      ', [$priceMin]);
                  });
            });
        }
        
        if ($request->has('price_max') && is_numeric($request->price_max)) {
            $priceMax = (float)$request->price_max;
            $query->where(function($q) use ($priceMax) {
                $q->where('price', '<=', $priceMax)
                  ->orWhereHas('activePromotions', function($promotionQuery) use ($priceMax) {
                      $promotionQuery->whereRaw('
                          CASE 
                              WHEN promotions.discount_type = "percentage" THEN 
                                  products.price * (1 - (promotions.discount_value / 100))
                              WHEN promotions.discount_type = "fixed" THEN 
                                  GREATEST(0, products.price - promotions.discount_value)
                              ELSE products.price
                          END <= ?
                      ', [$priceMax]);
                  });
            });
        }
    }

    /**
     * Apply feature filters (featured, in stock, on promotion)
     */
    protected function applyFeatureFilters(Builder $query, Request $request): void
    {
        if ($request->boolean('featured_only')) {
            $columns = DB::getSchemaBuilder()->getColumnListing('products');
            if (in_array('is_featured', $columns)) {
                $query->where('is_featured', true);
            }
        }
        
        if ($request->boolean('in_stock_only')) {
            $columns = DB::getSchemaBuilder()->getColumnListing('products');
            if (in_array('stock', $columns)) {
                $query->where('stock', '>', 0);
            }
        }
        
        if ($request->boolean('on_promotion_only')) {
            $query->whereHas('activePromotions');
        }
    }

    /**
     * Apply sorting to query
     */
    protected function applySorting(Builder $query, Request $request): void
    {
        $sortField = $request->sort_by ?? 'created_at';
        $sortDirection = $request->sort_direction ?? 'desc';
        
        $allowedSortFields = ['created_at', 'name', 'price', 'updated_at', 'category_id'];
        if (!in_array($sortField, $allowedSortFields)) {
            $sortField = 'created_at';
        }
        
        if (!in_array(strtolower($sortDirection), ['asc', 'desc'])) {
            $sortDirection = 'desc';
        }
        
        // Special handling for category sorting
        if ($sortField === 'category_id') {
            // Use join for category sorting to avoid conflicts with eager loading
            $query->leftJoin('categories', 'products.category_id', '=', 'categories.id')
                  ->orderBy('categories.name', $sortDirection)
                  ->select('products.*'); // Ensure we only select products columns
        } else {
            $query->orderBy($sortField, $sortDirection);
        }
    }

    /**
     * Get single product by ID, with promotion and reviews if available.
     *
     * @param int $id
     * @return Product
     */
    public function getProduct(int $id)
    {
        $hasReviews = $this->checkReviewsAvailability();
        
        if ($hasReviews) {
            $product = Product::with(['category', 'brand', 'activePromotions', 'reviews' => function($query) {
                $query->approved()->latest();
            }])->findOrFail($id);
        } else {
            $product = Product::with(['category', 'brand', 'activePromotions'])->findOrFail($id);
        }
        
        return $this->addPromotionInfo($product);
    }

    /**
     * Get latest products with promotion info.
     *
     * @param int $limit
     * @return \Illuminate\Support\Collection|Product[]
     */
    public function getLatestProducts(int $limit = 9)
    {
        $hasReviews = $this->checkReviewsAvailability();
        
        $cacheKey = 'latest_products';
        
        return Cache::remember($cacheKey, 1800, function () use ($hasReviews, $limit) {
            $query = Product::with(['category', 'brand', 'activePromotions']);
            
            if ($hasReviews) {
                $query->with(['reviews' => function($query) {
                    $query->approved()->latest();
                }]);
            }
            
            $products = $query->latest('created_at')->take($limit)->get();
            
            return $products->transform(function ($product) {
                return $this->addPromotionInfo($product);
            });
        });
    }

    /**
     * Check if reviews functionality is available
     */
    protected function checkReviewsAvailability(): bool
    {
        try {
            return class_exists('App\\Models\\Review') && 
                   Schema::hasTable('reviews') && 
                   method_exists('App\\Models\\Review', 'scopeApproved');
        } catch (Exception $e) {
            Log::warning('Error checking for Review model', [
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Add promotion information to product.
     *
     * @param Product $product
     * @return Product
     */
    public function addPromotionInfo($product)
    {
        $bestPromotion = $product->getBestActivePromotion();
        if ($bestPromotion) {
            $product->promotion_price = $product->getPromotionalPrice();
            $product->savings = $product->getSavingsAmount();
            $product->promotion = [
                'id' => $bestPromotion->id,
                'title' => $bestPromotion->title,
                'badge_text' => $bestPromotion->badge_text,
                'badge_color' => $bestPromotion->badge_color,
                'discount_type' => $bestPromotion->discount_type,
                'discount_value' => $bestPromotion->discount_value
            ];
        } else {
            $product->promotion_price = $product->price;
            $product->savings = 0;
        }
        
        return $product;
    }

    /**
     * Get available filters metadata for products.
     *
     * @return array
     */
    public function getFiltersMetadata(): array
    {
        return [
            'categories' => Category::ordered()->get(['id', 'name']),
            'brands' => \App\Models\Brand::orderBy('name')->get(['id', 'name']),
            'price_range' => [
                'min' => Product::min('price'),
                'max' => Product::max('price'),
            ]
        ];
    }
} 