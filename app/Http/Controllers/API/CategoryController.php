<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class CategoryController extends Controller
{
    /**
     * Display a listing of categories.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        Log::info('CategoryController@index called', [
            'query_params' => $request->all(),
        ]);

        try {
            // Cache categories for 1 hour
            $cacheKey = 'categories_list_' . md5(json_encode($request->all()));
            
            $categories = Cache::remember($cacheKey, 3600, function () use ($request) {
                $query = Category::with(['products' => function($query) {
                    $query->limit(3); // Load only first 3 products for preview
                }]);

                // Option to include only categories with products
                if ($request->boolean('with_products_only')) {
                    $query->withProducts();
                }

                // Apply sorting by name
                $query->ordered();

                return $query->get();
            });

            // Transform data for better frontend consumption
            $categoriesData = $categories->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'slug' => $category->slug,
                    'is_active' => $category->is_active,
                    'products_count' => $category->products_count,
                    'preview_products' => $category->products->map(function ($product) {
                        return [
                            'id' => $product->id,
                            'name' => $product->name,
                            'price' => $product->price,
                            'image_url' => $product->image_url,
                        ];
                    }),
                    'created_at' => $category->created_at,
                    'updated_at' => $category->updated_at,
                ];
            });

            Log::info('Categories fetched successfully', [
                'count' => $categories->count(),
                'cache_used' => Cache::has($cacheKey),
            ]);

            return response()->json([
                'data' => $categoriesData,
                'meta' => [
                    'total' => $categories->count(),
                    'cache_used' => Cache::has($cacheKey),
                ],
            ]);

        } catch (Exception $e) {
            Log::error('Error fetching categories', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'error' => 'Wystąpił błąd podczas pobierania kategorii',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified category.
     *
     * @param  mixed  $identifier (ID or slug)
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($identifier)
    {
        Log::info('CategoryController@show called', [
            'identifier' => $identifier,
        ]);

        try {
            // Try to find by ID first, then by slug
            $category = null;
            
            if (is_numeric($identifier)) {
                $category = Category::with(['products'])->find($identifier);
            }
            
            if (!$category) {
                $category = Category::with(['products'])->where('slug', $identifier)->first();
            }

            if (!$category) {
                return response()->json([
                    'error' => 'Kategoria nie została znaleziona'
                ], 404);
            }

            // Cache category details for 30 minutes
            $cacheKey = 'category_detail_' . $category->id;
            
            $categoryData = Cache::remember($cacheKey, 1800, function () use ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'slug' => $category->slug,
                    'products_count' => $category->products_count,
                    'created_at' => $category->created_at,
                    'updated_at' => $category->updated_at,
                ];
            });

            Log::info('Category details fetched', [
                'category_id' => $category->id,
                'category_name' => $category->name,
                'products_count' => $category->products_count,
            ]);

            return response()->json($categoryData);

        } catch (Exception $e) {
            Log::error('Error fetching category details', [
                'identifier' => $identifier,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'error' => 'Wystąpił błąd podczas pobierania kategorii'
            ], 500);
        }
    }

    /**
     * Display products for the specified category.
     *
     * @param  mixed  $identifier (ID or slug)
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function products($identifier, Request $request)
    {
        Log::info('CategoryController@products called', [
            'identifier' => $identifier,
            'query_params' => $request->all(),
        ]);

        try {
            // Find category by ID or slug
            $category = null;
            
            if (is_numeric($identifier)) {
                $category = Category::find($identifier);
            }
            
            if (!$category) {
                $category = Category::where('slug', $identifier)->first();
            }

            if (!$category) {
                return response()->json([
                    'error' => 'Kategoria nie została znaleziona'
                ], 404);
            }

            // Build products query for this category
            $query = Product::with(['category', 'brand', 'activePromotions'])
                           ->where('category_id', $category->id);

            // Apply additional filters
            if ($request->has('brand_id')) {
                $query->where('brand_id', $request->brand_id);
            }

            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
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

            $products = $query->paginate($perPage);

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

            Log::info('Category products fetched successfully', [
                'category_id' => $category->id,
                'category_name' => $category->name,
                'total_products' => $products->total(),
                'per_page' => $products->perPage(),
                'current_page' => $products->currentPage(),
                'filters_applied' => array_intersect_key($request->all(), array_flip(['brand_id', 'search', 'price_min', 'price_max', 'sort_by', 'sort_direction']))
            ]);

            // Add category information to response
            $response = $products->toArray();
            $response['category'] = [
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug,
            ];

            return response()->json($response);

        } catch (Exception $e) {
            Log::error('Error fetching category products', [
                'identifier' => $identifier,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'error' => 'Wystąpił błąd podczas pobierania produktów kategorii',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get category statistics for dashboard/admin
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function statistics()
    {
        try {
            $stats = Cache::remember('categories_statistics', 1800, function () {
                return [
                    'total_categories' => Category::count(),
                    'categories_with_products' => Category::withProducts()->count(),
                    'top_categories' => Category::withCount('products')
                        ->orderByDesc('products_count')
                        ->take(5)
                        ->get()
                        ->map(function ($category) {
                            return [
                                'id' => $category->id,
                                'name' => $category->name,
                                'products_count' => $category->products_count,
                            ];
                        }),
                ];
            });

            return response()->json($stats);

        } catch (Exception $e) {
            Log::error('Error fetching category statistics', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'error' => 'Wystąpił błąd podczas pobierania statystyk kategorii'
            ], 500);
        }
    }
} 