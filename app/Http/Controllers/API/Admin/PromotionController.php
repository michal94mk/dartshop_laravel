<?php

namespace App\Http\Controllers\API\Admin;

use App\Models\Promotion;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\Admin\PromotionRequest;
use App\Http\Controllers\Api\BaseApiController;
use App\Services\Admin\PromotionAdminService;

/**
 * API controller for promotion management.
 * Handles both public promotion endpoints and admin management operations.
 */
class PromotionController extends BaseApiController
{
    /**
     * @var PromotionAdminService
     */
    protected $promotionAdminService;

    /**
     * Inject the promotion admin service.
     *
     * @param PromotionAdminService $promotionAdminService
     */
    public function __construct(PromotionAdminService $promotionAdminService)
    {
        $this->promotionAdminService = $promotionAdminService;
    }

    /**
     * Display a listing of promotions (public API).
     */
    public function indexPublic(Request $request): JsonResponse
    {
        $query = Promotion::with(['products:id,name,price,image'])
                          ->active()
                          ->ordered();

        // Filtering for public API
        if ($request->has('featured')) {
            $query->featured();
        }

        $promotions = $query->paginate($request->get('per_page', 10));

        // Add promotion information to each product in each promotion
        $promotions->getCollection()->transform(function ($promotion) {
            $promotion->products->transform(function ($product) use ($promotion) {
                $product->promotion_price = $promotion->calculateDiscountedPrice($product->price);
                $product->savings = $promotion->getDiscountAmount($product->price);
                $product->promotion = [
                    'id' => $promotion->id,
                    'title' => $promotion->title,
                    'badge_text' => $promotion->badge_text,
                    'badge_color' => $promotion->badge_color,
                    'discount_type' => $promotion->discount_type,
                    'discount_value' => $promotion->discount_value
                ];
                return $product;
            });
            return $promotion;
        });

        return $this->paginatedResponse($promotions);
    }

    /**
     * Display featured promotions (public API).
     */
    public function featured(Request $request): JsonResponse
    {
        $promotions = Promotion::with(['products:id,name,price,image'])
                              ->active()
                              ->featured()
                              ->ordered()
                              ->limit($request->get('limit', 5))
                              ->get();

        // Add promotion information to each product in each promotion
        $promotions->transform(function ($promotion) {
            $promotion->products->transform(function ($product) use ($promotion) {
                $product->promotion_price = $promotion->calculateDiscountedPrice($product->price);
                $product->savings = $promotion->getDiscountAmount($product->price);
                $product->promotion = [
                    'id' => $promotion->id,
                    'title' => $promotion->title,
                    'badge_text' => $promotion->badge_text,
                    'badge_color' => $promotion->badge_color,
                    'discount_type' => $promotion->discount_type,
                    'discount_value' => $promotion->discount_value
                ];
                return $product;
            });
            return $promotion;
        });

        return $this->successResponse($promotions, 'Featured promotions fetched successfully');
    }

    /**
     * Show promotion details (public API).
     */
    public function showPublic(Promotion $promotion): JsonResponse
    {
        if (!$promotion->isActive()) {
            return response()->json(['message' => 'Promocja nie jest aktywna'], 404);
        }

        $promotion->load(['products' => function ($query) {
            $query->with(['category:id,name', 'brand:id,name']);
        }]);
        
        return $this->successResponse($promotion, 'Promotion details fetched successfully');
    }

    /**
     * Get products from promotion (public API).
     */
    public function getPromotionProducts(Request $request, Promotion $promotion): JsonResponse
    {
        if (!$promotion->isActive()) {
            return response()->json(['message' => 'Promocja nie jest aktywna'], 404);
        }

        $query = $promotion->products()
                          ->with(['category:id,name', 'brand:id,name']);

        // Filtering
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'name');
        $sortDirection = $request->get('sort_direction', 'asc');
        
        $allowedSorts = ['name', 'price', 'created_at'];
        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortDirection);
        }

        $products = $query->paginate($request->get('per_page', 12));

        // Add promotion information to each product
        $products->getCollection()->transform(function ($product) use ($promotion) {
            $product->promotion_price = $promotion->calculateDiscountedPrice($product->price);
            $product->savings = $promotion->getDiscountAmount($product->price);
            $product->promotion = [
                'id' => $promotion->id,
                'title' => $promotion->title,
                'badge_text' => $promotion->badge_text,
                'badge_color' => $promotion->badge_color,
                'discount_type' => $promotion->discount_type,
                'discount_value' => $promotion->discount_value
            ];
            return $product;
        });

        return $this->paginatedResponse($products);
    }

    /**
     * Display a listing of promotions (admin).
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $promotions = $this->promotionAdminService->getPromotionsWithFilters($request);
        return $this->paginatedResponse($promotions);
    }

    /**
     * Show promotion details (admin).
     *
     * @param Promotion $promotion
     * @return JsonResponse
     */
    public function show(Promotion $promotion): JsonResponse
    {
        $promotion = $this->promotionAdminService->getPromotionWithDetails($promotion);
        return $this->successResponse($promotion, 'Promocja pobrana');
    }

    /**
     * Create a new promotion.
     *
     * @param PromotionRequest $request
     * @return JsonResponse
     */
    public function store(PromotionRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $promotion = $this->promotionAdminService->createPromotion($validated);
        return $this->successResponse($promotion, 'Promocja została utworzona', 201);
    }

    /**
     * Update an existing promotion.
     *
     * @param PromotionRequest $request
     * @param Promotion $promotion
     * @return JsonResponse
     */
    public function update(PromotionRequest $request, Promotion $promotion): JsonResponse
    {
        $validated = $request->validated();
        $promotion = $this->promotionAdminService->updatePromotion($promotion, $validated);
        return $this->successResponse($promotion, 'Promocja została zaktualizowana');
    }

    /**
     * Delete a promotion.
     *
     * @param Promotion $promotion
     * @return JsonResponse
     */
    public function destroy(Promotion $promotion): JsonResponse
    {
        $this->promotionAdminService->deletePromotion($promotion);
        return $this->successResponse('Promocja została usunięta');
    }

    /**
     * Attach products to a promotion.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function attachProducts(Request $request, $id): JsonResponse
    {
        $validated = $request->validate([
            'product_ids' => 'required|array',
            'product_ids.*' => 'exists:products,id'
        ]);
        $promotion = $this->promotionAdminService->attachProducts($id, $validated['product_ids']);
        return $this->successResponse($promotion, 'Produkty zostały przypisane do promocji');
    }

    /**
     * Detach products from a promotion.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function detachProducts(Request $request, $id): JsonResponse
    {
        $validated = $request->validate([
            'product_ids' => 'required|array',
            'product_ids.*' => 'exists:products,id'
        ]);
        $promotion = $this->promotionAdminService->detachProducts($id, $validated['product_ids']);
        return $this->successResponse($promotion, 'Produkty zostały usunięte z promocji');
    }

    /**
     * Get available products (not assigned to any active promotion).
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getAvailableProducts(Request $request): JsonResponse
    {
        $products = $this->promotionAdminService->getAvailableProducts($request);
        return $this->successResponse($products, 'Available products fetched successfully');
    }

    /**
     * Toggle promotion active status.
     *
     * @param Promotion $promotion
     * @return JsonResponse
     */
    public function toggleActive(Promotion $promotion): JsonResponse
    {
        $promotion = $this->promotionAdminService->toggleActive($promotion);
        $message = $promotion->is_active ? 'Promocja została aktywowana' : 'Promocja została dezaktywowana';
        return $this->successResponse($promotion, $message);
    }

    /**
     * Toggle promotion featured status.
     *
     * @param Promotion $promotion
     * @return JsonResponse
     */
    public function toggleFeatured(Promotion $promotion): JsonResponse
    {
        $promotion = $this->promotionAdminService->toggleFeatured($promotion);
        $message = $promotion->is_featured ? 'Promocja została wyróżniona' : 'Promocja została usunięta z wyróżnionych';
        return $this->successResponse($promotion, $message);
    }

    /**
     * Update promotion display order.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function updateOrder(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'promotions' => 'required|array',
            'promotions.*.id' => 'required|exists:promotions,id',
            'promotions.*.display_order' => 'required|integer|min:0'
        ]);
        $this->promotionAdminService->updateOrder($validated['promotions']);
        return $this->successResponse('Kolejność promocji została zaktualizowana');
    }

    /**
     * Get form data for promotion creation/editing.
     *
     * @return JsonResponse
     */
    public function getFormData(): JsonResponse
    {
        $formData = $this->promotionAdminService->getFormData();
        return $this->successResponse($formData, 'Dane formularza pobrane');
    }
} 