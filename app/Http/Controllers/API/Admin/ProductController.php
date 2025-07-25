<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Product;
use App\Http\Requests\Admin\ProductRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseApiController;
use App\Services\Admin\ProductAdminService;

/**
 * API controller for admin product management.
 * Handles requests for listing, creating, updating, deleting products, and form data.
 */
class ProductController extends BaseApiController
{
    /**
     * @var ProductAdminService
     */
    protected $productAdminService;

    /**
     * Inject the product admin service.
     *
     * @param ProductAdminService $productAdminService
     */
    public function __construct(ProductAdminService $productAdminService)
    {
        $this->productAdminService = $productAdminService;
    }

    /**
     * Display a listing of the products.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $products = $this->productAdminService->getProductsWithFilters($request);
        return $this->paginatedResponse($products);
    }

    /**
     * Store a newly created product in storage.
     *
     * @param ProductRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(ProductRequest $request)
    {
        $productData = $request->validated();
        $imageFile = $request->file('image');
        $product = $this->productAdminService->createProduct($productData, $imageFile);
        return $this->successResponse($product, 'Produkt został utworzony', 201);
    }

    /**
     * Display the specified product.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $product = $this->productAdminService->getProductWithDetails($id);
        if (!$product) {
            return $this->errorResponse('Product not found', 404);
        }
        return $this->successResponse($product, 'Product retrieved');
    }

    /**
     * Update the specified product in storage.
     *
     * @param ProductRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(ProductRequest $request, $id)
    {
        $product = Product::find($id);
        if (!$product) {
            return $this->errorResponse('Product not found', 404);
        }
        $productData = $request->validated();
        $imageFile = $request->file('image');
        $product = $this->productAdminService->updateProduct($product, $productData, $imageFile);
        return $this->successResponse($product, 'Produkt został zaktualizowany');
    }

    /**
     * Remove the specified product from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $product = Product::find($id);
        if (!$product) {
            return $this->errorResponse('Product not found', 404);
        }
        $result = $this->productAdminService->deleteProduct($product);
        if ($result['success']) {
            return $this->successResponse('Produkt został usunięty');
        }
        return $this->errorResponse($result['message'], $result['code']);
    }

    /**
     * Get all categories and brands for product form.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getFormData()
    {
        $formData = $this->productAdminService->getFormData();
        return $this->successResponse($formData, 'Form data retrieved');
    }
} 