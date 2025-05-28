<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Promotion;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProductPromotionController extends Controller
{
    /**
     * Pobierz wszystkie aktywne promocje z produktami
     */
    public function index(): JsonResponse
    {
        $promotions = Promotion::active()
            ->withProducts()
            ->orderBy('display_order', 'asc')
            ->orderBy('created_at', 'desc')
            ->get();

        // Filtruj promocje które mają przypisane produkty
        $promotions = $promotions->filter(function ($promotion) {
            return $promotion->products->count() > 0;
        });

        // Dodaj obliczone ceny promocyjne do produktów
        $promotions->each(function ($promotion) {
            $promotion->products->each(function ($product) use ($promotion) {
                $product->promotional_price = $promotion->calculatePromotionalPrice((float) $product->price);
                $product->savings_amount = (float) $product->price - $product->promotional_price;
                $product->savings_percentage = $product->price > 0 ? 
                    round(($product->savings_amount / (float) $product->price) * 100, 1) : 0;
            });
        });

        return response()->json([
            'data' => $promotions,
            'count' => $promotions->count()
        ]);
    }

    /**
     * Pobierz produkty w promocjach (dla strony głównej, kategorii itp.)
     */
    public function getPromotedProducts(Request $request): JsonResponse
    {
        $query = Product::withActivePromotions()
            ->where('is_active', true);

        // Filtruj tylko produkty które mają aktywne promocje
        $query->whereHas('promotions', function ($q) {
            $q->where('is_active', true)
              ->where('starts_at', '<=', now())
              ->where(function($subQ) {
                  $subQ->whereNull('ends_at')
                       ->orWhere('ends_at', '>=', now());
              });
        });

        // Opcjonalne filtrowanie po kategorii
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Opcjonalne filtrowanie po marce
        if ($request->has('brand_id')) {
            $query->where('brand_id', $request->brand_id);
        }

        // Sortowanie
        $sortBy = $request->get('sort_by', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');
        
        $allowedSorts = ['created_at', 'name', 'price', 'discount_value'];
        if (in_array($sortBy, $allowedSorts)) {
            if ($sortBy === 'discount_value') {
                // Sortowanie po wartości zniżki wymaga join z tabelą promocji
                $query->join('product_promotions', 'products.id', '=', 'product_promotions.product_id')
                      ->join('promotions', 'product_promotions.promotion_id', '=', 'promotions.id')
                      ->orderBy('promotions.discount_value', $sortDirection)
                      ->select('products.*');
            } else {
                $query->orderBy($sortBy, $sortDirection);
            }
        }

        // Paginacja
        $perPage = min($request->get('per_page', 12), 50);
        $products = $query->with(['category', 'brand'])->paginate($perPage);

        return response()->json($products);
    }

    /**
     * Pobierz szczegóły konkretnej promocji z produktami
     */
    public function show($id): JsonResponse
    {
        $promotion = Promotion::with(['products' => function($query) {
            $query->where('is_active', true)
                  ->with(['category', 'brand']);
        }])->findOrFail($id);

        // Sprawdź czy promocja jest aktywna
        if (!$promotion->isValid()) {
            return response()->json([
                'message' => 'Promocja nie jest aktywna'
            ], 404);
        }

        // Dodaj obliczone ceny promocyjne
        $promotion->products->each(function ($product) use ($promotion) {
            $product->promotional_price = $promotion->calculatePromotionalPrice((float) $product->price);
            $product->savings_amount = (float) $product->price - $product->promotional_price;
            $product->savings_percentage = $product->price > 0 ? 
                round(($product->savings_amount / (float) $product->price) * 100, 1) : 0;
        });

        return response()->json($promotion);
    }

    /**
     * Sprawdź czy produkt ma aktywną promocję
     */
    public function checkProductPromotion($productId): JsonResponse
    {
        $product = Product::with(['promotions' => function($query) {
            $query->where('is_active', true)
                  ->where('starts_at', '<=', now())
                  ->where(function($q) {
                      $q->whereNull('ends_at')
                        ->orWhere('ends_at', '>=', now());
                  });
        }])->findOrFail($productId);

        $activePromotion = $product->promotions->first();

        if (!$activePromotion) {
            return response()->json([
                'has_promotion' => false,
                'product' => $product
            ]);
        }

        return response()->json([
            'has_promotion' => true,
            'product' => $product,
            'promotion' => $activePromotion,
            'promotional_price' => $activePromotion->calculatePromotionalPrice((float) $product->price),
            'savings_amount' => (float) $product->price - $activePromotion->calculatePromotionalPrice((float) $product->price),
            'savings_percentage' => $product->price > 0 ? 
                round((((float) $product->price - $activePromotion->calculatePromotionalPrice((float) $product->price)) / (float) $product->price) * 100, 1) : 0
        ]);
    }
} 