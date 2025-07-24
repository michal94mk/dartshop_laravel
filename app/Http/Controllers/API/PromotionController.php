<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Api\BaseApiController;
use App\Models\Promotion;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PromotionController extends BaseApiController
{
    /**
     * Wyświetl listę promocji (publiczne API)
     */
    public function indexPublic(Request $request): JsonResponse
    {
        $query = Promotion::with(['products:id,name,price,image_url'])
                          ->active()
                          ->ordered();

        // Filtrowanie dla publicznego API
        if ($request->has('featured')) {
            $query->featured();
        }

        try {
            $promotions = $query->paginate($request->get('per_page', 10));

            // Dodaj informacje o promocji do każdego produktu w każdej promocji
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

            return $this->successResponse($promotions);
        } catch (\Exception $e) {
            return $this->errorResponse('Wystąpił błąd podczas pobierania promocji: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Wyświetl wyróżnione promocje (publiczne API)
     */
    public function featured(Request $request): JsonResponse
    {
        $promotions = Promotion::with(['products:id,name,price,image_url'])
                              ->active()
                              ->featured()
                              ->ordered()
                              ->limit($request->get('limit', 5))
                              ->get();

        // Dodaj informacje o promocji do każdego produktu w każdej promocji
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

        return response()->json($promotions);
    }

    /**
     * Pokaż szczegóły promocji (publiczne API)
     */
    public function showPublic(Promotion $promotion): JsonResponse
    {
        if (!$promotion->isActive()) {
            return response()->json(['message' => 'Promocja nie jest aktywna'], 404);
        }

        $promotion->load(['products' => function ($query) {
            $query->with(['category:id,name', 'brand:id,name']);
        }]);
        
        return response()->json($promotion);
    }

    /**
     * Pobierz produkty z promocji (publiczne API)
     */
    public function getPromotionProducts(Request $request, Promotion $promotion): JsonResponse
    {
        if (!$promotion->isActive()) {
            return response()->json(['message' => 'Promocja nie jest aktywna'], 404);
        }

        $query = $promotion->products()
                          ->with(['category:id,name', 'brand:id,name']);

        // Filtrowanie
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

        // Sortowanie
        $sortBy = $request->get('sort_by', 'name');
        $sortDirection = $request->get('sort_direction', 'asc');
        
        $allowedSorts = ['name', 'price', 'created_at'];
        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortDirection);
        }

        $products = $query->paginate($request->get('per_page', 12));

        // Dodaj informacje o promocji do każdego produktu
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

        return response()->json($products);
    }

    /**
     * Sprawdź czy produkt ma aktywną promocję
     */
    public function checkProductPromotion($productId): JsonResponse
    {
        try {
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
                ]);
            }

            return $this->successResponse([
                'has_promotion' => true,
                'product' => $product,
                'promotion' => $activePromotion,
                'promotional_price' => $activePromotion->calculatePromotionalPrice((float) $product->price),
                'savings_amount' => (float) $product->price - $activePromotion->calculatePromotionalPrice((float) $product->price),
                'savings_percentage' => $product->price > 0 ? 
                    round((((float) $product->price - $activePromotion->calculatePromotionalPrice((float) $product->price)) / (float) $product->price) * 100, 1) : 0
            ]);
        } catch (\Exception $e) {
            return $this->handleException($e, 'Wystąpił błąd podczas sprawdzania promocji produktu');
        }
    }
} 