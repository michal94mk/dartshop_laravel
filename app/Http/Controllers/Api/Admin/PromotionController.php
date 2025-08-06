<?php

namespace App\Http\Controllers\API\Admin;

use App\Models\Promotion;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\Admin\PromotionRequest;
use App\Http\Requests\Admin\AttachProductsRequest;
use App\Http\Requests\Admin\PromotionOrderRequest;
use App\Http\Controllers\Api\BaseApiController;
use App\Services\Admin\PromotionAdminService;

/**
 * API controller for promotion management.
 * Handles both public promotion endpoints and admin management operations.
 */
class PromotionController extends BaseApiController
{
    public function __construct(
        private PromotionAdminService $promotionAdminService
    ) {}

    /**
     * Display a listing of promotions (public API).
     */
    public function indexPublic(Request $request): JsonResponse
    {
        $promotions = $this->promotionAdminService->getPublicPromotions($request);
        return $this->paginatedResponse($promotions);
    }

    /**
     * Display featured promotions (public API).
     */
    public function featured(Request $request): JsonResponse
    {
        $promotions = $this->promotionAdminService->getFeaturedPromotions($request);
        return $this->successResponse($promotions, 'Featured promotions fetched successfully');
    }

    /**
     * Show promotion details (public API).
     */
    public function showPublic(Promotion $promotion): JsonResponse
    {
        if (!$promotion->isActive()) {
            return $this->notFoundResponse('Promocja nie jest aktywna');
        }

        $promotion = $this->promotionAdminService->getPublicPromotionDetails($promotion);
        return $this->successResponse($promotion, 'Promotion details fetched successfully');
    }

    /**
     * Get products from promotion (public API).
     */
    public function getPromotionProducts(Request $request, Promotion $promotion): JsonResponse
    {
        if (!$promotion->isActive()) {
            return $this->notFoundResponse('Promocja nie jest aktywna');
        }

        $products = $this->promotionAdminService->getPromotionProducts($request, $promotion);
        return $this->paginatedResponse($products);
    }

    /**
     * Display a listing of promotions (admin).
     */
    public function index(Request $request): JsonResponse
    {
        $promotions = $this->promotionAdminService->getPromotionsWithFilters($request);
        return $this->paginatedResponse($promotions);
    }

    /**
     * Show promotion details (admin).
     */
    public function show(Promotion $promotion): JsonResponse
    {
        $promotion = $this->promotionAdminService->getPromotionWithDetails($promotion);
        return $this->successResponse($promotion, 'Promocja pobrana');
    }

    /**
     * Create a new promotion.
     */
    public function store(PromotionRequest $request): JsonResponse
    {
        $promotion = $this->promotionAdminService->createPromotion($request->validated());
        return $this->successResponse($promotion, 'Promocja została utworzona', 201);
    }

    /**
     * Update an existing promotion.
     */
    public function update(PromotionRequest $request, Promotion $promotion): JsonResponse
    {
        $promotion = $this->promotionAdminService->updatePromotion($promotion, $request->validated());
        return $this->successResponse($promotion, 'Promocja została zaktualizowana');
    }

    /**
     * Delete a promotion.
     */
    public function destroy(Promotion $promotion): JsonResponse
    {
        $this->promotionAdminService->deletePromotion($promotion);
        return $this->successResponse('Promocja została usunięta');
    }

    /**
     * Attach products to a promotion.
     */
    public function attachProducts(AttachProductsRequest $request, int $id): JsonResponse
    {
        $promotion = $this->promotionAdminService->attachProducts($id, $request->validated()['product_ids']);
        return $this->successResponse($promotion, 'Produkty zostały przypisane do promocji');
    }

    /**
     * Detach products from a promotion.
     */
    public function detachProducts(AttachProductsRequest $request, int $id): JsonResponse
    {
        $promotion = $this->promotionAdminService->detachProducts($id, $request->validated()['product_ids']);
        return $this->successResponse($promotion, 'Produkty zostały usunięte z promocji');
    }

    /**
     * Get available products (not assigned to any active promotion).
     */
    public function getAvailableProducts(Request $request): JsonResponse
    {
        $products = $this->promotionAdminService->getAvailableProducts($request);
        return $this->successResponse($products, 'Available products fetched successfully');
    }

    /**
     * Toggle promotion active status.
     */
    public function toggleActive(Promotion $promotion): JsonResponse
    {
        $promotion = $this->promotionAdminService->toggleActive($promotion);
        $message = $promotion->is_active ? 'Promocja została aktywowana' : 'Promocja została dezaktywowana';
        return $this->successResponse($promotion, $message);
    }

    /**
     * Toggle promotion featured status.
     */
    public function toggleFeatured(Promotion $promotion): JsonResponse
    {
        $promotion = $this->promotionAdminService->toggleFeatured($promotion);
        $message = $promotion->is_featured ? 'Promocja została wyróżniona' : 'Promocja została usunięta z wyróżnionych';
        return $this->successResponse($promotion, $message);
    }

    /**
     * Update promotion display order.
     */
    public function updateOrder(PromotionOrderRequest $request): JsonResponse
    {
        $this->promotionAdminService->updateOrder($request->validated()['promotions']);
        return $this->successResponse('Kolejność promocji została zaktualizowana');
    }

    /**
     * Get form data for promotion creation/editing.
     */
    public function getFormData(): JsonResponse
    {
        $formData = $this->promotionAdminService->getFormData();
        return $this->successResponse($formData, 'Dane formularza pobrane');
    }
} 