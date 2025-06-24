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
            $query->where('is_active', true)
                  ->with(['category:id,name', 'brand:id,name']);
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
                          ->where('is_active', true)
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
        $query = Promotion::with(['products:id,name,price,image'])
                          ->ordered();

        // Filtrowanie
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        if ($request->has('discount_type')) {
            $query->where('discount_type', $request->discount_type);
        }

        $promotions = $query->paginate($request->get('per_page', 15));

        return response()->json($promotions);
    }

    /**
     * Pokaż szczegóły promocji (admin)
     */
    public function show(Promotion $promotion): JsonResponse
    {
        $promotion->load(['products:id,name,price,image']);
        
        return response()->json($promotion);
    }

    /**
     * Utwórz nową promocję
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'discount_type' => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0',
            'starts_at' => 'required|date',
            'ends_at' => 'nullable|date|after:starts_at',
            'is_active' => 'boolean',
            'code' => 'nullable|string|max:50|unique:promotions,code',
            'product_ids' => 'array',
            'product_ids.*' => 'exists:products,id'
        ]);

        // Walidacja dodatkowa dla discount_value
        if ($validated['discount_type'] === 'percentage' && $validated['discount_value'] > 100) {
            return response()->json([
                'message' => 'Rabat procentowy nie może być większy niż 100%'
            ], 422);
        }

        $promotion = Promotion::create($validated);

        // Przypisz produkty jeśli zostały wybrane
        if (isset($validated['product_ids'])) {
            $promotion->products()->sync($validated['product_ids']);
        }

        $promotion->load('products');

        return response()->json([
            'message' => 'Promocja została utworzona pomyślnie',
            'promotion' => $promotion
        ], 201);
    }

    /**
     * Zaktualizuj promocję
     */
    public function update(Request $request, Promotion $promotion): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'discount_type' => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0',
            'starts_at' => 'required|date',
            'ends_at' => 'nullable|date|after:starts_at',
            'is_active' => 'boolean',
            'code' => 'nullable|string|max:50|unique:promotions,code,' . $promotion->id,
            'product_ids' => 'array',
            'product_ids.*' => 'exists:products,id'
        ]);

        // Walidacja dodatkowa dla discount_value
        if ($validated['discount_type'] === 'percentage' && $validated['discount_value'] > 100) {
            return response()->json([
                'message' => 'Rabat procentowy nie może być większy niż 100%'
            ], 422);
        }

        $promotion->update($validated);

        // Zaktualizuj przypisane produkty
        if (isset($validated['product_ids'])) {
            $promotion->products()->sync($validated['product_ids']);
        }

        $promotion->load('products');

        return response()->json([
            'message' => 'Promocja została zaktualizowana pomyślnie',
            'promotion' => $promotion
        ]);
    }

    /**
     * Usuń promocję
     */
    public function destroy(Promotion $promotion): JsonResponse
    {
        $promotion->products()->detach(); // Usuń powiązania z produktami
        $promotion->delete();

        return response()->json([
            'message' => 'Promocja została usunięta pomyślnie'
        ]);
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

        return response()->json([
            'message' => 'Produkty zostały przypisane do promocji',
            'promotion' => $promotion->load('products')
        ]);
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

        return response()->json([
            'message' => 'Produkty zostały usunięte z promocji',
            'promotion' => $promotion->load('products')
        ]);
    }

    /**
     * Pobierz dostępne produkty (nie przypisane do żadnej aktywnej promocji)
     */
    public function getAvailableProducts(Request $request): JsonResponse
    {
        $search = $request->get('search', '');
        $excludePromotionId = $request->get('exclude_promotion_id');

        $query = Product::where('is_active', true)
                       ->with(['category:id,name', 'brand:id,name']);

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
            $q->active();
            if ($excludePromotionId) {
                $q->where('promotions.id', '!=', $excludePromotionId);
            }
        });

        $products = $query->paginate($request->get('per_page', 20));

        return response()->json($products);
    }

    /**
     * Przełącz status aktywności promocji
     */
    public function toggleActive(Promotion $promotion): JsonResponse
    {
        $promotion->update(['is_active' => !$promotion->is_active]);

        return response()->json([
            'message' => $promotion->is_active ? 'Promocja została aktywowana' : 'Promocja została dezaktywowana',
            'promotion' => $promotion
        ]);
    }

    /**
     * Przełącz status wyróżnienia promocji
     */
    public function toggleFeatured(Promotion $promotion): JsonResponse
    {
        // Since we don't have is_featured field, we'll just return success message
        return response()->json([
            'message' => 'Funkcja wyróżniania promocji nie jest dostępna w aktualnej wersji',
            'promotion' => $promotion
        ]);
    }

    /**
     * Zaktualizuj kolejność wyświetlania promocji
     */
    public function updateOrder(Request $request): JsonResponse
    {
        // Since we don't have display_order field, we'll just return success message
        return response()->json([
            'message' => 'Funkcja zmiany kolejności promocji nie jest dostępna w aktualnej wersji'
        ]);
    }
} 