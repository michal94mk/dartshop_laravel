<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseApiController;
use App\Models\Promotion;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PromotionController extends BaseApiController
{
    /**
     * Get a paginated list of public promotions.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function indexPublic(Request $request): JsonResponse
    {
        $this->logApiRequest($request, 'Fetch public promotions');
        
        $query = Promotion::with(['products:id,name,price,image_url'])
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

        return $this->successResponse($promotions, 'Promotions fetched successfully');
    }

    /**
     * Get a list of featured promotions.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function featured(Request $request): JsonResponse
    {
        $this->logApiRequest($request, 'Fetch featured promotions');
        
        $promotions = Promotion::with(['products:id,name,price,image_url'])
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
     * Get details of a specific promotion.
     *
     * @param Promotion $promotion
     * @return JsonResponse
     */
    public function showPublic(Promotion $promotion): JsonResponse
    {
        $this->logApiRequest(request(), "Fetch promotion details for ID: {$promotion->id}");
        
        if (!$promotion->isActive()) {
            return $this->notFoundResponse('Promocja nie jest aktywna');
        }

        $promotion->load(['products' => function ($query) {
            $query->with(['category:id,name', 'brand:id,name']);
        }]);
        
        return $this->successResponse($promotion, 'Promotion details fetched successfully');
    }

    /**
     * Get products for a specific promotion.
     *
     * @param Request $request
     * @param Promotion $promotion
     * @return JsonResponse
     */
    public function getPromotionProducts(Request $request, Promotion $promotion): JsonResponse
    {
        $this->logApiRequest($request, "Fetch products for promotion ID: {$promotion->id}");
        
        if (!$promotion->isActive()) {
            return $this->notFoundResponse('Promocja nie jest aktywna');
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

        return $this->successResponse($products, 'Promotion products fetched successfully');
    }

    /**
     * Check if a product has an active promotion.
     *
     * @param int $productId
     * @return JsonResponse
     */
    public function checkProductPromotion(int $productId): JsonResponse
    {
        $this->logApiRequest(request(), "Check promotion for product ID: {$productId}");
        
        $product = Product::with(['promotions' => function($query) {
            $query->where('starts_at', '<=', now())
                  ->where(function($q) {
                      $q->whereNull('ends_at')
                        ->orWhere('ends_at', '>=', now());
                  });
        }])->findOrFail($productId);

        $activePromotion = $product->promotions->first();

        if (!$activePromotion) {
            return $this->successResponse([
                'has_promotion' => false,
                'product' => $product
            ], 'Product has no active promotion');
        }

        return $this->successResponse([
            'has_promotion' => true,
            'product' => $product,
            'promotion' => $activePromotion,
            'promotional_price' => $activePromotion->calculatePromotionalPrice((float) $product->price),
            'savings_amount' => (float) $product->price - $activePromotion->calculatePromotionalPrice((float) $product->price),
            'savings_percentage' => $product->price > 0 ? 
                round((((float) $product->price - $activePromotion->calculatePromotionalPrice((float) $product->price)) / (float) $product->price) * 100, 1) : 0
        ], 'Product promotion details fetched successfully');
    }
} 