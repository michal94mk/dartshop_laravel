<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseApiController;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\ProductService;
use Illuminate\Support\Facades\Cache;

class ProductController extends BaseApiController
{
    public function __construct(
        private ProductService $productService
    ) {}
    
    /**
     * Get a paginated list of products with filters and promotions.
     */
    public function index(Request $request): JsonResponse
    {
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
    }
    
    /**
     * Get details of a specific product.
     */
    public function show(int $id): JsonResponse
    {
        $this->logApiRequest(request(), "Fetch product details for ID: {$id}");
        
        $product = $this->productService->getProduct($id);
        
        return $this->successResponse($product, 'Product details fetched successfully');
    }
    
    /**
     * Get the latest products.
     */
    public function latest(): JsonResponse
    {
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
    }
} 