<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseApiController;
use Illuminate\Http\Request;
use App\Services\CategoryService;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class CategoryController extends BaseApiController
{
    protected $categoryService;
    
    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

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
            $categories = $this->categoryService->getCategories($request);
            
            // Transform data for better frontend consumption
            $categoriesData = $categories->map(function ($category) {
                return $this->categoryService->transformCategoryData($category);
            });

            Log::info('Categories fetched successfully', [
                'count' => $categories->count(),
                'cache_used' => Cache::has('categories_list_' . md5(json_encode($request->all()))),
            ]);

            return $this->successResponse([
                'data' => $categoriesData,
                'meta' => [
                    'total' => $categories->count(),
                    'cache_used' => Cache::has('categories_list_' . md5(json_encode($request->all()))),
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
            $categoryData = $this->categoryService->getCategory($id);

            Log::info('Category details fetched', [
                'category_id' => $categoryData['id'],
                'category_name' => $categoryData['name'],
                'products_count' => $categoryData['products_count'],
            ]);

            return $this->successResponse($categoryData);

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
            $products = $this->categoryService->getCategoryProducts($id, $request);

            Log::info('Category products fetched', [
                'category_id' => $id,
                'products_count' => $products->total(),
            ]);

            return $this->successResponse($products);

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
            $stats = $this->categoryService->getStatistics();
            return $this->successResponse($stats);

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