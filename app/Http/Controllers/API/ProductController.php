<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseApiController;
use Illuminate\Http\Request;
use App\Services\ProductService;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\JsonResponse;

class ProductController extends BaseApiController
{
    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }
    
    /**
     * Get a paginated list of products with filters and promotions.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $this->logApiRequest($request, 'Fetch products');
            
            $products = $this->productService->getProducts($request);
            $products->getCollection()->transform(function ($product) {
                return $this->productService->addPromotionInfo($product);
            });
            
            $cacheKey = 'products_list_' . md5(json_encode($request->all()));
            $fromCache = Cache::has($cacheKey);
            
            $response = $products->toArray();
            $response['meta'] = array_merge($response['meta'] ?? [], [
                'filters_available' => $this->productService->getFiltersMetadata()
            ]);
            
            return $this->responseWithCache($response, $fromCache, 'Products fetched successfully');
        } catch (Exception $e) {
            return $this->handleException($e, 'Fetching products');
        }
    }
    
    /**
     * Get details of a specific product.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        try {
            $this->logApiRequest(request(), "Fetch product details for ID: {$id}");
            
            $product = $this->productService->getProduct($id);
            
            return $this->successResponse($product, 'Product details fetched successfully');
        } catch (Exception $e) {
            return $this->handleException($e, "Fetching product with ID: {$id}");
        }
    }
    
    /**
     * Get the latest products.
     *
     * @return JsonResponse
     */
    public function latest(): JsonResponse
    {
        try {
            $this->logApiRequest(request(), 'Fetch latest products');
            
            $products = $this->productService->getLatestProducts();
            $cacheKey = 'latest_products';
            $fromCache = Cache::has($cacheKey);
            
            return $this->responseWithCache([
                'data' => $products,
                'meta' => [
                    'count' => $products->count(),
                ]
            ], $fromCache, 'Latest products fetched successfully');
        } catch (Exception $e) {
            return $this->handleException($e, 'Fetching latest products');
        }
    }
} 