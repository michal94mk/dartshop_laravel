<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

/**
 * @OA\Tag(
 *     name="Categories",
 *     description="API Endpoints for category management"
 * )
 */

class CategoryController extends Controller
{
    /**
     * Display a listing of categories.
     *
     * @OA\Get(
     *     path="/api/categories",
     *     summary="Get list of categories",
     *     description="Retrieve all categories with optional filtering",
     *     tags={"Categories"},
     *     @OA\Parameter(
     *         name="with_products_only",
     *         in="query",
     *         description="Show only categories that have products",
     *         required=false,
     *         @OA\Schema(type="boolean")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/CategoryWithProducts")),
     *             @OA\Property(property="meta", type="object",
     *                 @OA\Property(property="total", type="integer", example=5),
     *                 @OA\Property(property="cache_used", type="boolean", example=true)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     )
     * )
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
     * @OA\Get(
     *     path="/api/categories/{id}",
     *     summary="Get category details",
     *     description="Retrieve detailed information about a specific category",
     *     tags={"Categories"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Category ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/CategoryDetail")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Category not found"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     )
     * )
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
     * @OA\Get(
     *     path="/api/categories/{id}/products",
     *     summary="Get products in category",
     *     description="Retrieve all products belonging to a specific category",
     *     tags={"Categories"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Category ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search products by name",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="price_min",
     *         in="query",
     *         description="Minimum price filter",
     *         required=false,
     *         @OA\Schema(type="number", format="float")
     *     ),
     *     @OA\Parameter(
     *         name="price_max",
     *         in="query",
     *         description="Maximum price filter",
     *         required=false,
     *         @OA\Schema(type="number", format="float")
     *     ),
     *     @OA\Parameter(
     *         name="sort_by",
     *         in="query",
     *         description="Sort field (name, price, created_at)",
     *         required=false,
     *         @OA\Schema(type="string", default="created_at")
     *     ),
     *     @OA\Parameter(
     *         name="sort_direction",
     *         in="query",
     *         description="Sort direction (asc, desc)",
     *         required=false,
     *         @OA\Schema(type="string", default="desc")
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Number of products per page",
     *         required=false,
     *         @OA\Schema(type="integer", default=12)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Product")),
     *             @OA\Property(property="meta", ref="#/components/schemas/PaginationMeta"),
     *             @OA\Property(property="links", ref="#/components/schemas/PaginationLinks")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Category not found"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     )
     * )
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
     * @OA\Get(
     *     path="/api/categories/statistics",
     *     summary="Get category statistics",
     *     description="Retrieve statistics about categories for dashboard/admin",
     *     tags={"Categories"},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="total_categories", type="integer", example=10),
     *             @OA\Property(property="categories_with_products", type="integer", example=8),
     *             @OA\Property(property="top_categories", type="array", @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Dart Flights"),
     *                 @OA\Property(property="products_count", type="integer", example=25)
     *             ))
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     )
     * )
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