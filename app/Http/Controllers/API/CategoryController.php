<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseApiController;
use Illuminate\Http\Request;
use App\Services\CategoryService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\JsonResponse;
use Exception;

class CategoryController extends BaseApiController
{
    protected $categoryService;
    
    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    /**
     * Get a list of categories with optional filters and preview products.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $this->logApiRequest($request, 'Fetch categories');

        try {
            $categories = $this->categoryService->getCategories($request);
            
            // Transform data for better frontend consumption
            $categoriesData = $categories->map(function ($category) {
                return $this->categoryService->transformCategoryData($category);
            });

            $cacheKey = 'categories_list_' . md5(json_encode($request->all()));
            $fromCache = Cache::has($cacheKey);

            return $this->responseWithCache([
                'data' => $categoriesData,
                'meta' => [
                    'total' => $categories->count(),
                ],
            ], $fromCache, 'Categories fetched successfully');

        } catch (Exception $e) {
            return $this->handleException($e, 'Fetching categories');
        }
    }

    /**
     * Get details of a specific category.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $this->logApiRequest(request(), "Fetch category details for ID: {$id}");

        try {
            $categoryData = $this->categoryService->getCategory($id);

            return $this->successResponse($categoryData, 'Category details fetched successfully');

        } catch (Exception $e) {
            return $this->handleException($e, "Fetching category details for ID: {$id}");
        }
    }

    /**
     * Get products for a specific category with filters.
     *
     * @param int $id
     * @param Request $request
     * @return JsonResponse
     */
    public function products(int $id, Request $request): JsonResponse
    {
        $this->logApiRequest($request, "Fetch products for category ID: {$id}");

        try {
            $products = $this->categoryService->getCategoryProducts($id, $request);

            return $this->successResponse($products, 'Category products fetched successfully');

        } catch (Exception $e) {
            return $this->handleException($e, "Fetching category products for ID: {$id}");
        }
    }

    /**
     * Get category statistics for dashboard/admin.
     *
     * @return JsonResponse
     */
    public function statistics(): JsonResponse
    {
        try {
            $this->logApiRequest(request(), 'Fetch category statistics');
            
            $stats = $this->categoryService->getStatistics();
            return $this->successResponse($stats, 'Category statistics fetched successfully');

        } catch (Exception $e) {
            return $this->handleException($e, 'Fetching category statistics');
        }
    }
} 