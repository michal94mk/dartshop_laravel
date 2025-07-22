<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;
use App\Http\Requests\Admin\CategoryRequest;

/**
 * @OA\Tag(
 *     name="Admin/Categories",
 *     description="API Endpoints for admin category management"
 * )
 */

class CategoryController extends BaseAdminController
{
    /**
     * Clear all category-related cache
     */
    private function clearCategoriesCache()
    {
        // Get all cache keys that might contain category data
        $patterns = [
            'categories_list_*',
            'category_detail_*'
        ];
        
        // Clear cache by pattern (Laravel doesn't have built-in pattern clearing, so we'll clear common ones)
        $commonKeys = [
            'categories_list_' . md5('[]'),
            'categories_list_' . md5('{}'),
            'categories_list_' . md5('{"with_products_only":true}'),
            'categories_list_' . md5('{"with_products_only":false}'),
        ];
        
        foreach ($commonKeys as $key) {
            Cache::forget($key);
        }
    }

    /**
     * Display a listing of the categories.
     *
     * @OA\Get(
     *     path="/api/admin/categories",
     *     summary="Get categories list (admin)",
     *     description="Retrieve all categories with admin filters and pagination",
     *     tags={"Admin/Categories"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search in category name",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="sort_field",
     *         in="query",
     *         description="Sort field (id, name, products_count, created_at, updated_at)",
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
     *         description="Items per page",
     *         required=false,
     *         @OA\Schema(type="integer", default=10)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Category")),
     *             @OA\Property(property="meta", ref="#/components/schemas/PaginationMeta"),
     *             @OA\Property(property="links", ref="#/components/schemas/PaginationLinks")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden - Admin access required"
     *     )
     * )
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $query = Category::withCount('products');
            
            // Apply search filter
            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->where('name', 'like', "%{$search}%");
            }
            
            // Apply sorting
            $sortField = $request->sort_field ?? 'created_at';
            $sortDirection = $request->sort_direction ?? 'desc';
            
            // Handle special case for products_count which is not a direct database column
            if ($sortField === 'products_count') {
                $query->orderBy('products_count', $sortDirection);
            } else {
                // Make sure the column exists in the categories table to prevent SQL errors
                if (in_array($sortField, ['id', 'name', 'created_at', 'updated_at'])) {
                    $query->orderBy($sortField, $sortDirection);
                } else {
                    $query->orderBy('created_at', 'desc'); // Default fallback
                }
            }
            
            // Paginate results
            $perPage = $request->per_page ?? 10;
            $categories = $query->paginate($perPage);
            
            return response()->json($categories);
        } catch (\Exception $e) {
            return $this->errorResponse('Error fetching categories: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Store a newly created category in storage.
     *
     * @param  \App\Http\Requests\Admin\CategoryRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CategoryRequest $request)
    {
        try {
            $category = Category::create($request->validated());

            // Clear all categories cache
            $this->clearCategoriesCache();

            return $this->successResponse('Category created successfully', $category, 201);
        } catch (\Exception $e) {
            return $this->errorResponse('Error creating category: ' . $e->getMessage(), 500);
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
        try {
            $category = Category::with('products')->findOrFail($id);
            return response()->json($category);
        } catch (\Exception $e) {
            return $this->errorResponse('Error fetching category: ' . $e->getMessage(), 404);
        }
    }

    /**
     * Update the specified category in storage.
     *
     * @param  \App\Http\Requests\Admin\CategoryRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(CategoryRequest $request, $id)
    {
        try {
            $category = Category::findOrFail($id);

            $category->update($request->validated());

            // Clear all categories cache
            $this->clearCategoriesCache();
            Cache::forget('category_detail_' . $id);

            return $this->successResponse('Category updated successfully', $category);
        } catch (\Exception $e) {
            return $this->errorResponse('Error updating category: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Remove the specified category from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            $category = Category::findOrFail($id);
            
            // Update all products with this category to have null category_id
            if ($category->products()->count() > 0) {
                $affectedProductsCount = $category->products()->count();
                $category->products()->update(['category_id' => null]);
                
                // Clear all categories cache since product relationships changed
                $this->clearCategoriesCache();
                
                $category->delete();
                
                Cache::forget('category_detail_' . $id);
                
                return $this->successResponse(
                    "Kategoria \"{$category->name}\" została usunięta. " .
                    "{$affectedProductsCount} produktów zostało odłączonych od tej kategorii i może być teraz filtrowanych jako 'Bez kategorii'."
                );
            }
            
            $category->delete();
            
            // Clear all categories cache
            $this->clearCategoriesCache();
            Cache::forget('category_detail_' . $id);
            
            return $this->successResponse('Category deleted successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Error deleting category: ' . $e->getMessage(), 500);
        }
    }
} 