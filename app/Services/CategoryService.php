<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

class CategoryService
{
    /**
     * Get categories list with optional filters
     */
    public function getCategories(Request $request)
    {
        $cacheKey = 'categories_list_' . md5(json_encode($request->all()));
        
        return Cache::remember($cacheKey, 300, function () use ($request) {
            $query = Category::withCount('products')->with(['products' => function($query) {
                $query->limit(3); // Load only first 3 products for preview
            }]);

            if ($request->boolean('with_products_only')) {
                $query->withProducts();
            }

            $query->ordered();

            return $query->get();
        });
    }

    /**
     * Transform category data for API response
     */
    public function transformCategoryData($category)
    {
        return [
            'id' => $category->id,
            'name' => $category->name,
            'products_count' => $category->products_count,
            'is_active' => true,
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
    }

    /**
     * Get single category by ID
     */
    public function getCategory($id)
    {
        $category = Category::withCount('products')
            ->with(['products'])
            ->findOrFail($id);

        $cacheKey = 'category_detail_' . $category->id;
        
        return Cache::remember($cacheKey, 1800, function () use ($category) {
            return [
                'id' => $category->id,
                'name' => $category->name,
                'products_count' => $category->products_count,
                'created_at' => $category->created_at,
                'updated_at' => $category->updated_at,
            ];
        });
    }

    /**
     * Get products for category with filters
     */
    public function getCategoryProducts($categoryId, Request $request)
    {
        $category = Category::findOrFail($categoryId);

        $query = $this->buildProductsQuery($category, $request);
        
        $perPage = (int)($request->per_page ?? 12);
        $perPage = max(1, min($perPage, 50));
        
        return $query->paginate($perPage);
    }

    /**
     * Build products query with filters
     */
    protected function buildProductsQuery(Category $category, Request $request): Builder
    {
        $query = Product::with(['category', 'brand', 'activePromotions'])
                       ->where('category_id', $category->id);

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

        $this->applySorting($query, $request);

        return $query;
    }

    /**
     * Apply sorting to query
     */
    protected function applySorting(Builder $query, Request $request): void
    {
        $sortField = $request->sort_by ?? 'created_at';
        $sortDirection = $request->sort_direction ?? 'desc';
        
        $allowedSortFields = ['created_at', 'name', 'price'];
        if (!in_array($sortField, $allowedSortFields)) {
            $sortField = 'created_at';
        }
        
        if (!in_array(strtolower($sortDirection), ['asc', 'desc'])) {
            $sortDirection = 'desc';
        }
        
        $query->orderBy($sortField, $sortDirection);
    }

    /**
     * Get category statistics
     */
    public function getStatistics()
    {
        return Cache::remember('categories_statistics', 1800, function () {
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
    }
} 