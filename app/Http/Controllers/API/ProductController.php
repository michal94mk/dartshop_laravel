<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Cache;
use App\Models\Category;

class ProductController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        Log::info('ProductController initialized');
    }
    
    /**
     * Display a listing of products.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        Log::info('ProductController@index called with parameters', [
            'query_params' => $request->all(),
            'user_agent' => $request->header('User-Agent'),
        ]);
        
        try {
            // Create cache key based on query parameters
            $cacheKey = 'products_list_' . md5(json_encode($request->all()));
            
            // Cache products for 30 minutes
            $products = Cache::remember($cacheKey, 1800, function () use ($request) {
                // Create base query with promotions
                $query = Product::with(['category', 'brand', 'activePromotions']);
                
                // Only show active products by default
                $query->where('is_active', true);
                
                // Apply filters if any
                if ($request->has('category_id')) {
                    $query->where('category_id', $request->category_id);
                }
                
                if ($request->has('brand_id')) {
                    $query->where('brand_id', $request->brand_id);
                }
                
                if ($request->has('search')) {
                    $search = $request->search;
                    $query->where(function($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                          ->orWhere('description', 'like', "%{$search}%");
                    });
                }
                
                // Price range filtering - uwzględnia również ceny promocyjne
                if ($request->has('price_min') && is_numeric($request->price_min)) {
                    $priceMin = (float)$request->price_min;
                    $query->where(function($q) use ($priceMin) {
                        // Produkty gdzie oryginalna cena jest >= min
                        $q->where('price', '>=', $priceMin)
                          // LUB produkty z promocją gdzie cena promocyjna jest >= min
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
                        // Produkty gdzie oryginalna cena jest <= max
                        $q->where('price', '<=', $priceMax)
                          // LUB produkty z promocją gdzie cena promocyjna jest <= max
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
                
                // Featured products filter
                if ($request->boolean('featured_only')) {
                    $columns = DB::getSchemaBuilder()->getColumnListing('products');
                    if (in_array('featured', $columns)) {
                        $query->where('featured', true);
                    }
                }
                
                // In stock filter
                if ($request->boolean('in_stock_only')) {
                    $columns = DB::getSchemaBuilder()->getColumnListing('products');
                    if (in_array('stock', $columns)) {
                        $query->where('stock', '>', 0);
                    }
                }
                
                // On promotion filter
                if ($request->boolean('on_promotion_only')) {
                    $query->whereHas('activePromotions');
                }
                
                // Apply sorting
                $sortField = $request->sort_by ?? 'created_at';
                $sortDirection = $request->sort_direction ?? 'desc';
                
                // Validate sort field to prevent SQL injection
                $allowedSortFields = ['created_at', 'name', 'price', 'updated_at'];
                if (!in_array($sortField, $allowedSortFields)) {
                    $sortField = 'created_at';
                }
                
                if (!in_array(strtolower($sortDirection), ['asc', 'desc'])) {
                    $sortDirection = 'desc';
                }
                
                $query->orderBy($sortField, $sortDirection);
                
                // Pagination
                $perPage = (int)($request->per_page ?? 12);
                $perPage = max(1, min($perPage, 50)); // Limit between 1 and 50
                
                return $query->paginate($perPage);
            });
            
            // Add promotion information to each product
            $products->getCollection()->transform(function ($product) {
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
            });
            
            Log::info('Products query successful', [
                'total' => $products->total(),
                'per_page' => $products->perPage(),
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'cache_used' => Cache::has($cacheKey),
                'filters_applied' => array_intersect_key($request->all(), array_flip([
                    'category_id', 'brand_id', 'search', 'price_min', 'price_max', 
                    'featured_only', 'in_stock_only', 'on_promotion_only', 'sort_by', 'sort_direction'
                ]))
            ]);
            
            // Add metadata to response
            $response = $products->toArray();
            $response['meta'] = array_merge($response['meta'] ?? [], [
                'cache_used' => Cache::has($cacheKey),
                'filters_available' => [
                    'categories' => Category::active()->ordered()->get(['id', 'name', 'slug']),
                    'brands' => \App\Models\Brand::where('is_active', true)->orderBy('name')->get(['id', 'name']),
                    'price_range' => [
                        'min' => Product::where('is_active', true)->min('price'),
                        'max' => Product::where('is_active', true)->max('price'),
                    ]
                ]
            ]);
            
            return response()->json($response);
            
        } catch (Exception $e) {
            Log::error('Error fetching products', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            return response()->json([
                'error' => 'Wystąpił błąd podczas pobierania produktów',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Display the specified product.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        Log::info('ProductController@show called with ID', [
            'product_id' => $id
        ]);
        
        DB::enableQueryLog();
        
        try {
            // Sprawdź, czy istnieje model Review i czy zawiera metodę approved
            $hasReviews = false;
            try {
                if (class_exists('App\\Models\\Review') && 
                    Schema::hasTable('reviews') && 
                    method_exists('App\\Models\\Review', 'scopeApproved')) {
                    $hasReviews = true;
                }
            } catch (Exception $e) {
                Log::warning('Error checking for Review model', [
                    'error' => $e->getMessage()
                ]);
            }
            
            // Jeśli model Review istnieje, pobierz również recenzje
            if ($hasReviews) {
                $product = Product::with(['category', 'brand', 'activePromotions', 'reviews' => function($query) {
                    $query->approved()->latest();
                }])->findOrFail($id);
            } else {
                $product = Product::with(['category', 'brand', 'activePromotions'])->findOrFail($id);
            }
            
            // Add promotion information
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
            
            Log::info('Product detail response', [
                'product_id' => $id,
                'product_name' => $product->name,
                'product_columns' => array_keys($product->toArray()),
                'has_category' => $product->category ? true : false,
                'has_brand' => $product->brand ? true : false,
                'has_reviews' => $hasReviews,
                'query_log' => DB::getQueryLog()
            ]);
            
            // Return product with a consistent format
            return response()->json($product);
        } catch (Exception $e) {
            Log::error('Error fetching product details', [
                'id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'query_log' => DB::getQueryLog()
            ]);
            
            return response()->json(['error' => 'Product not found'], 404);
        }
    }
    
    /**
     * Display featured products.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function featured()
    {
        Log::info('ProductController@featured called');
        
        DB::enableQueryLog();
        
        try {
            // Check if Review model exists
            $hasReviews = false;
            try {
                if (class_exists('App\\Models\\Review') && 
                    Schema::hasTable('reviews') && 
                    method_exists('App\\Models\\Review', 'scopeApproved')) {
                    $hasReviews = true;
                }
            } catch (Exception $e) {
                Log::warning('Error checking for Review model in featured', [
                    'error' => $e->getMessage()
                ]);
            }
            
            // Cache featured products for 1 hour
            $products = Cache::remember('featured_products', 3600, function () use ($hasReviews) {
                // First try to get featured products
                $query = Product::with(['category', 'brand', 'activePromotions'])
                              ->where('is_active', true);
                
                // Add reviews if available
                if ($hasReviews) {
                    $query->with(['reviews' => function($query) {
                        $query->approved()->latest();
                    }]);
                }
                
                // Check if 'featured' column exists in the products table
                $columns = DB::getSchemaBuilder()->getColumnListing('products');
                
                if (in_array('featured', $columns)) {
                    $query->where('featured', true);
                }
                
                $products = $query->latest()->take(8)->get();
                
                // If no featured products, just get any recent products
                if ($products->isEmpty()) {
                    $query = Product::with(['category', 'brand', 'activePromotions'])
                        ->where('is_active', true);
                        
                    if ($hasReviews) {
                        $query->with(['reviews' => function($query) {
                            $query->approved()->latest();
                        }]);
                    }
                    
                    $products = $query->latest()->take(8)->get();
                }
                
                return $products;
            });
            
            // Add promotion information to each product
            $products->transform(function ($product) {
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
            });
            
            Log::info('Featured products response', [
                'count' => $products->count(),
                'columns' => $products->count() > 0 ? array_keys($products->first()->toArray()) : [],
                'query_log' => DB::getQueryLog(),
                'cache_used' => Cache::has('featured_products'),
            ]);
            
            return response()->json([
                'data' => $products,
                'meta' => [
                    'count' => $products->count(),
                    'cache_used' => Cache::has('featured_products'),
                ]
            ]);
            
        } catch (Exception $e) {
            Log::error('Error fetching featured products', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'query_log' => DB::getQueryLog()
            ]);
            
            return response()->json([
                'data' => [],
                'error' => 'Wystąpił błąd podczas pobierania polecanych produktów'
            ], 500);
        }
    }
} 