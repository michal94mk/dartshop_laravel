<?php

namespace App\Http\Controllers\Api;

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
        $query = Product::with(['activePromotions']);
        
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

        $products = $query->with(['category', 'brand', 'activePromotions'])
                          ->paginate($request->get('per_page', 12));

        // Dodaj obliczone ceny promocyjne do produktów
        $products->getCollection()->each(function ($product) {
            $bestPromotion = $product->getBestActivePromotion();
            if ($bestPromotion) {
                $product->promotional_price = $bestPromotion->calculatePromotionalPrice((float) $product->price);
                $product->savings_amount = (float) $product->price - $product->promotional_price;
                $product->savings_percentage = $product->price > 0 ? 
                    round(($product->savings_amount / (float) $product->price) * 100, 1) : 0;
            }
        });

        return response()->json([
            'data' => $products,
            'meta' => [
                'total' => $products->total(),
                'per_page' => $products->perPage(),
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
            ]
        ]);
    }

    /**
     * Pobierz szczegóły konkretnej promocji z produktami
     */
    public function show($id): JsonResponse
    {
        $promotion = Promotion::with(['products' => function($query) {
            $query->with(['category', 'brand']);
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
            $query->where('starts_at', '<=', now())
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