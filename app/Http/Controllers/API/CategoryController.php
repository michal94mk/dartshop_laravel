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
            // Create cache key that includes all query parameters
            $cacheKey = 'categories_list_' . md5(json_encode($request->all()));
            
            $categories = Cache::remember($cacheKey, 300, function () use ($request) { // Reduced cache time to 5 minutes
                $query = Category::withCount('products')->with(['products' => function($query) {
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
                    'products_count' => $category->products_count,
                    'is_active' => true, // Add is_active field for frontend filtering
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
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        Log::info('CategoryController@show called', [
            'id' => $id,
        ]);

        try {
            $category = Category::withCount('products')
                ->with(['products'])
                ->findOrFail($id);

            // Cache category details for 30 minutes
            $cacheKey = 'category_detail_' . $category->id;
            
            $categoryData = Cache::remember($cacheKey, 1800, function () use ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
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
                'id' => $id,
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
     * @param  int  $id
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function products($id, Request $request)
    {
        Log::info('CategoryController@products called', [
            'id' => $id,
            'query_params' => $request->all(),
        ]);

        try {
            $category = Category::findOrFail($id);

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
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            }

            // Apply sorting
            $sortField = $request->sort_by ?? 'created_at';
            $sortDirection = $request->sort_direction ?? 'desc';
            
            // Validate sort field
            $allowedSortFields = ['created_at', 'name', 'price'];
            if (!in_array($sortField, $allowedSortFields)) {
                $sortField = 'created_at';
            }
            
            if (!in_array(strtolower($sortDirection), ['asc', 'desc'])) {
                $sortDirection = 'desc';
            }
            
            $query->orderBy($sortField, $sortDirection);

            // Paginate results
            $perPage = (int)($request->per_page ?? 12);
            $perPage = max(1, min($perPage, 50)); // Limit between 1 and 50
            
            $products = $query->paginate($perPage);

            Log::info('Category products fetched', [
                'category_id' => $category->id,
                'category_name' => $category->name,
                'products_count' => $products->total(),
            ]);

            return response()->json($products);

        } catch (Exception $e) {
            Log::error('Error fetching category products', [
                'id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'error' => 'Wystąpił błąd podczas pobierania produktów kategorii'
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