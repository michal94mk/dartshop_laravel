<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Models\Promotion;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;
use App\Http\Requests\Admin\PromotionRequest;

class PromotionController extends BaseAdminController
{
    /**
     * Wyświetl listę promocji (publiczne API)
     */
    public function indexPublic(Request $request): JsonResponse
    {
        $query = Promotion::with(['products:id,name,price,image'])
                          ->active()
                          ->ordered();

        // Filtrowanie dla publicznego API
        if ($request->has('featured')) {
            $query->featured();
        }

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

        return response()->json($promotions);
    }

    /**
     * Wyświetl wyróżnione promocje (publiczne API)
     */
    public function featured(Request $request): JsonResponse
    {
        $promotions = Promotion::with(['products:id,name,price,image'])
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
     * Wyświetl listę promocji (admin)
     */
    public function index(Request $request): JsonResponse
    {
        $query = Promotion::with(['products:id,name,price,image_url'])
                          ->ordered();

        // Filtrowanie
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        if ($request->has('discount_type')) {
            $query->where('discount_type', $request->discount_type);
        }

        if ($request->has('is_featured')) {
            $query->where('is_featured', $request->boolean('is_featured'));
        }

        $promotions = $query->paginate($request->get('per_page', 15));

        // Dodaj informacje o liczbie produktów dla każdej promocji
        $promotions->getCollection()->transform(function ($promotion) {
            $promotion->products_count = $promotion->products->count();
            return $promotion;
        });

        return $this->successResponse('Promocje pobrane pomyślnie', $promotions);
    }

    /**
     * Pokaż szczegóły promocji (admin)
     */
    public function show(Promotion $promotion): JsonResponse
    {
        $promotion->load(['products:id,name,price,image_url']);
        $promotion->products_count = $promotion->products->count();
        return $this->successResponse('Promocja pobrana', $promotion);
    }

    /**
     * Utwórz nową promocję
     */
    public function store(PromotionRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $promotion = Promotion::create($validated);
        if (isset($validated['product_ids'])) {
            $promotion->products()->sync($validated['product_ids']);
        }
        $promotion->load('products:id,name,price,image_url');
        $promotion->products_count = $promotion->products->count();
        return $this->successResponse('Promocja została utworzona', $promotion, 201);
    }

    /**
     * Zaktualizuj promocję
     */
    public function update(PromotionRequest $request, Promotion $promotion): JsonResponse
    {
        $validated = $request->validated();
        $promotion->update($validated);
        if (isset($validated['product_ids'])) {
            $promotion->products()->sync($validated['product_ids']);
        }
        $promotion->load('products:id,name,price,image_url');
        $promotion->products_count = $promotion->products->count();
        return $this->successResponse('Promocja została zaktualizowana', $promotion);
    }

    /**
     * Usuń promocję
     */
    public function destroy(Promotion $promotion): JsonResponse
    {
        $promotion->products()->detach();
        $promotion->delete();
        return $this->successResponse('Promocja została usunięta');
    }

    /**
     * Przypisz produkty do promocji
     */
    public function attachProducts(Request $request, $id): JsonResponse
    {
        $promotion = Promotion::findOrFail($id);
        $validated = $request->validate([
            'product_ids' => 'required|array',
            'product_ids.*' => 'exists:products,id'
        ]);
        $promotion->products()->syncWithoutDetaching($validated['product_ids']);
        $promotion->load('products:id,name,price,image_url');
        $promotion->products_count = $promotion->products->count();
        return $this->successResponse('Produkty zostały przypisane do promocji', $promotion);
    }

    /**
     * Usuń produkty z promocji
     */
    public function detachProducts(Request $request, $id): JsonResponse
    {
        $promotion = Promotion::findOrFail($id);
        $validated = $request->validate([
            'product_ids' => 'required|array',
            'product_ids.*' => 'exists:products,id'
        ]);
        $promotion->products()->detach($validated['product_ids']);
        $promotion->load('products:id,name,price,image_url');
        $promotion->products_count = $promotion->products->count();
        return $this->successResponse('Produkty zostały usunięte z promocji', $promotion);
    }

    /**
     * Pobierz dostępne produkty (nie przypisane do żadnej aktywnej promocji)
     */
    public function getAvailableProducts(Request $request): JsonResponse
    {
        $search = $request->get('search', '');
        $excludePromotionId = $request->get('exclude_promotion_id');

        $query = Product::with(['category:id,name', 'brand:id,name', 'promotions:id,title,name']);

        // Wyszukiwanie
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Wykluczenie produktów już przypisanych do innych aktywnych promocji
        // (ale pozwalamy na produkty z edytowanej promocji)
        $query->whereDoesntHave('promotions', function ($q) use ($excludePromotionId) {
            $q->where('is_active', true)
              ->where('starts_at', '<=', now())
              ->where(function ($subQ) {
                  $subQ->whereNull('ends_at')
                        ->orWhere('ends_at', '>=', now());
              });
            if ($excludePromotionId) {
                $q->where('promotions.id', '!=', $excludePromotionId);
            }
        });

        $products = $query->paginate($request->get('per_page', 20));

        // Dodaj informacje o liczbie promocji dla każdego produktu
        $products->getCollection()->transform(function ($product) {
            $product->promotions_count = $product->promotions->count();
            return $product;
        });

        return response()->json($products);
    }

    /**
     * Przełącz status aktywności promocji
     */
    public function toggleActive(Promotion $promotion): JsonResponse
    {
        $promotion->update(['is_active' => !$promotion->is_active]);
        $promotion->load('products:id,name,price,image_url');
        $promotion->products_count = $promotion->products->count();
        return $this->successResponse($promotion->is_active ? 'Promocja została aktywowana' : 'Promocja została dezaktywowana', $promotion);
    }

    /**
     * Przełącz status wyróżnienia promocji
     */
    public function toggleFeatured(Promotion $promotion): JsonResponse
    {
        $promotion->update(['is_featured' => !$promotion->is_featured]);
        $promotion->load('products:id,name,price,image_url');
        $promotion->products_count = $promotion->products->count();
        return $this->successResponse($promotion->is_featured ? 'Promocja została wyróżniona' : 'Promocja została usunięta z wyróżnionych', $promotion);
    }

    /**
     * Zaktualizuj kolejność wyświetlania promocji
     */
    public function updateOrder(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'promotions' => 'required|array',
            'promotions.*.id' => 'required|exists:promotions,id',
            'promotions.*.display_order' => 'required|integer|min:0'
        ]);
        foreach ($validated['promotions'] as $promotionData) {
            Promotion::where('id', $promotionData['id'])
                    ->update(['display_order' => $promotionData['display_order']]);
        }
        return $this->successResponse('Kolejność promocji została zaktualizowana');
    }
} 