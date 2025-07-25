<?php

namespace App\Services\Admin;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;

/**
 * Service class for admin category management.
 * Handles listing, filtering, creating, updating, deleting categories, and cache clearing.
 */
class CategoryAdminService
{
    /**
     * Get paginated categories with optional filters and sorting.
     *
     * @param Request $request
     * @return LengthAwarePaginator
     */
    public function getCategoriesWithFilters(Request $request): LengthAwarePaginator
    {
        $query = Category::withCount('products');

        // Search filter
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }

        // Sorting
        $sortField = $request->sort_field ?? 'created_at';
        $sortDirection = $request->sort_direction ?? 'desc';
        if ($sortField === 'products_count') {
            $query->orderBy('products_count', $sortDirection);
        } elseif (in_array($sortField, ['id', 'name', 'created_at', 'updated_at'])) {
            $query->orderBy($sortField, $sortDirection);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        // Pagination
        $perPage = $request->get('per_page', 15);
        return $query->paginate($perPage);
    }

    /**
     * Create a new category.
     *
     * @param array $data
     * @return Category
     */
    public function createCategory(array $data): Category
    {
        $category = Category::create($data);
        $this->clearCategoriesCache();
        return $category;
    }

    /**
     * Update an existing category.
     *
     * @param Category $category
     * @param array $data
     * @return Category
     */
    public function updateCategory(Category $category, array $data): Category
    {
        $category->update($data);
        $this->clearCategoriesCache();
        Cache::forget('category_detail_' . $category->id);
        return $category;
    }

    /**
     * Delete a category and detach products.
     *
     * @param Category $category
     * @return array [success: bool, message: string]
     */
    public function deleteCategory(Category $category): array
    {
        $affectedProductsCount = $category->products()->count();
        if ($affectedProductsCount > 0) {
            $category->products()->update(['category_id' => null]);
        }
        $this->clearCategoriesCache();
        $category->delete();
        Cache::forget('category_detail_' . $category->id);
        if ($affectedProductsCount > 0) {
            return [
                'success' => true,
                'message' => "Category '{$category->name}' deleted. {$affectedProductsCount} products detached."
            ];
        }
        return [
            'success' => true,
            'message' => 'Category deleted.'
        ];
    }

    /**
     * Get category details with products.
     *
     * @param int $id
     * @return Category|null
     */
    public function getCategoryWithDetails($id): ?Category
    {
        return Category::with('products')->find($id);
    }

    /**
     * Clear all category-related cache.
     */
    public function clearCategoriesCache(): void
    {
        $commonKeys = [
            'categories_list_' . md5('[]'),
            'categories_list_' . md5('{}'),
            'categories_list_' . md5('{"with_products_only":true}'),
            'categories_list_' . md5('{"with_products_only":false}')
        ];
        foreach ($commonKeys as $key) {
            Cache::forget($key);
        }
    }
} 