<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseApiController;
use Illuminate\Http\Request;
use App\Services\ProductService;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class ProductController extends BaseApiController
{
    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
        Log::info('ProductController initialized');
    }
    
    /**
     * Display a listing of products.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            Log::info('ProductController@index called with parameters', [
                'query_params' => $request->all(),
                'user_agent' => $request->header('User-Agent'),
            ]);
            $products = $this->productService->getProducts($request);
            $products->getCollection()->transform(function ($product) {
                return $this->productService->addPromotionInfo($product);
            });
            $cacheKey = 'products_list_' . md5(json_encode($request->all()));
            $cacheUsed = Cache::has($cacheKey);
            Log::info('Products query successful', [
                'total' => $products->total(),
                'per_page' => $products->perPage(),
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'cache_hit' => $cacheUsed,
                'cache_key' => $cacheKey,
                'filters_applied' => array_intersect_key($request->all(), array_flip([
                    'category_id', 'brand_id', 'search', 'price_min', 'price_max', 
                    'featured_only', 'in_stock_only', 'on_promotion_only', 'sort_by', 'sort_direction'
                ]))
            ]);
            $response = $products->toArray();
            $response['meta'] = array_merge($response['meta'] ?? [], [
                'cache_hit' => $cacheUsed,
                'cache_key' => substr($cacheKey, 0, 20) . '...',
                'filters_available' => $this->productService->getFiltersMetadata()
            ]);
            return $this->successResponse($response);
        } catch (Exception $e) {
            return $this->handleException($e, 'Fetching products');
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
        try {
            Log::info('ProductController@show called with ID', [
                'product_id' => $id
            ]);
            $product = $this->productService->getProduct($id);
            Log::info('Product detail response', [
                'product_id' => $id,
                'product_name' => $product->name,
                'product_columns' => array_keys($product->toArray()),
                'has_category' => $product->category ? true : false,
                'has_brand' => $product->brand ? true : false,
                'has_reviews' => $product->reviews !== null
            ]);
            return $this->successResponse($product);
        } catch (Exception $e) {
            return $this->handleException($e, "Fetching product with ID: {$id}");
        }
    }
    
    /**
     * Display latest products.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function latest()
    {
        try {
            Log::info('ProductController@latest called');
            $products = $this->productService->getLatestProducts();
            $cacheKey = 'latest_products';
            $cacheUsed = Cache::has($cacheKey);
            Log::info('Latest products response', [
                'count' => $products->count(),
                'columns' => $products->count() > 0 ? array_keys($products->first()->toArray()) : [],
                'cache_hit' => $cacheUsed,
                'cache_key' => $cacheKey,
            ]);
            return $this->successResponse([
                'data' => $products,
                'meta' => [
                    'count' => $products->count(),
                    'cache_hit' => $cacheUsed,
                    'cache_key' => $cacheKey,
                ]
            ]);
        } catch (Exception $e) {
            return $this->handleException($e, 'Fetching latest products');
        }
    }
} 