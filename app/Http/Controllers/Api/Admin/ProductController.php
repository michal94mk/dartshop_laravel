<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Product;
use App\Http\Requests\Admin\ProductRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Api\BaseApiController;
use App\Services\Admin\ProductAdminService;

/**
 * API controller for admin product management.
 * Handles requests for listing, creating, updating, deleting products, and form data.
 */
class ProductController extends BaseApiController
{
    public function __construct(
        private ProductAdminService $productAdminService
    ) {}

    /**
     * Display a listing of the products.
     */
    public function index(Request $request): JsonResponse
    {
        $products = $this->productAdminService->getProductsWithFilters($request);
        return $this->paginatedResponse($products);
    }

    /**
     * Store a newly created product in storage.
     */
    public function store(ProductRequest $request): JsonResponse
    {
        $productData = $request->validated();
        $imageFile = $request->file('image');
        $product = $this->productAdminService->createProduct($productData, $imageFile);
        return $this->successResponse($product, 'Produkt został utworzony', 201);
    }

    /**
     * Display the specified product.
     */
    public function show(int $id): JsonResponse
    {
        $product = $this->productAdminService->getProductWithDetails($id);
        if (!$product) {
            return $this->notFoundResponse('Product not found');
        }
        return $this->successResponse($product, 'Product retrieved');
    }

    /**
     * Update the specified product in storage.
     */
    public function update(ProductRequest $request, int $id): JsonResponse
    {
        $product = Product::findOrFail($id);
        $productData = $request->validated();
        $imageFile = $request->file('image');
        $product = $this->productAdminService->updateProduct($product, $productData, $imageFile);
        return $this->successResponse($product, 'Produkt został zaktualizowany');
    }

    /**
     * Remove the specified product from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        $product = Product::findOrFail($id);
        $result = $this->productAdminService->deleteProduct($product);
        if ($result['success']) {
            return $this->successResponse('Produkt został usunięty');
        }
        return $this->errorResponse($result['message'], $result['code']);
    }

    /**
     * Get all categories and brands for product form.
     */
    public function getFormData(): JsonResponse
    {
        $formData = $this->productAdminService->getFormData();
        return $this->successResponse($formData, 'Form data retrieved');
    }
} 