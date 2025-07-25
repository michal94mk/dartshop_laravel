<?php

namespace App\Services\Admin;

use App\Models\Promotion;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Service class for admin promotion management.
 * Handles listing, filtering, creating, updating, deleting, and managing promotion products.
 */
class PromotionAdminService
{
    /**
     * Get paginated promotions with optional filters and sorting.
     *
     * @param Request $request
     * @return LengthAwarePaginator
     */
    public function getPromotionsWithFilters(Request $request): LengthAwarePaginator
    {
        $query = Promotion::with(['products:id,name,price,image_url'])
                          ->ordered();

        // Search filtering
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Status filtering
        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        // Discount type filtering
        if ($request->has('discount_type') && !empty($request->discount_type)) {
            $query->where('discount_type', $request->discount_type);
        }

        // Featured filtering
        if ($request->has('is_featured')) {
            $query->where('is_featured', $request->boolean('is_featured'));
        }

        // Pagination
        $perPage = $request->get('per_page', 15);
        $promotions = $query->paginate($perPage);

        // Add products count for each promotion
        $promotions->getCollection()->transform(function ($promotion) {
            $promotion->products_count = $promotion->products->count();
            return $promotion;
        });

        return $promotions;
    }

    /**
     * Get promotion with products for admin view.
     *
     * @param Promotion $promotion
     * @return Promotion
     */
    public function getPromotionWithDetails(Promotion $promotion): Promotion
    {
        $promotion->load(['products:id,name,price,image_url']);
        $promotion->products_count = $promotion->products->count();
        return $promotion;
    }

    /**
     * Create a new promotion.
     *
     * @param array $validatedData
     * @return Promotion
     */
    public function createPromotion(array $validatedData): Promotion
    {
        $promotion = Promotion::create($validatedData);
        
        if (isset($validatedData['product_ids'])) {
            $promotion->products()->sync($validatedData['product_ids']);
        }
        
        return $this->getPromotionWithDetails($promotion);
    }

    /**
     * Update an existing promotion.
     *
     * @param Promotion $promotion
     * @param array $validatedData
     * @return Promotion
     */
    public function updatePromotion(Promotion $promotion, array $validatedData): Promotion
    {
        $promotion->update($validatedData);
        
        if (isset($validatedData['product_ids'])) {
            $promotion->products()->sync($validatedData['product_ids']);
        }
        
        return $this->getPromotionWithDetails($promotion);
    }

    /**
     * Delete a promotion.
     *
     * @param Promotion $promotion
     * @return bool
     */
    public function deletePromotion(Promotion $promotion): bool
    {
        $promotion->products()->detach();
        return $promotion->delete();
    }

    /**
     * Attach products to a promotion.
     *
     * @param int $promotionId
     * @param array $productIds
     * @return Promotion
     */
    public function attachProducts(int $promotionId, array $productIds): Promotion
    {
        $promotion = Promotion::findOrFail($promotionId);
        $promotion->products()->syncWithoutDetaching($productIds);
        return $this->getPromotionWithDetails($promotion);
    }

    /**
     * Detach products from a promotion.
     *
     * @param int $promotionId
     * @param array $productIds
     * @return Promotion
     */
    public function detachProducts(int $promotionId, array $productIds): Promotion
    {
        $promotion = Promotion::findOrFail($promotionId);
        $promotion->products()->detach($productIds);
        return $this->getPromotionWithDetails($promotion);
    }

    /**
     * Get available products (not assigned to any active promotion).
     *
     * @param Request $request
     * @return LengthAwarePaginator
     */
    public function getAvailableProducts(Request $request): LengthAwarePaginator
    {
        $search = $request->get('search', '');
        $excludePromotionId = $request->get('exclude_promotion_id');

        $query = Product::with(['category:id,name', 'brand:id,name', 'promotions:id,title,name']);

        // Search filtering
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Exclude products already assigned to other active promotions
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

        $perPage = $request->get('per_page', 20);
        $products = $query->paginate($perPage);

        // Add promotions count for each product
        $products->getCollection()->transform(function ($product) {
            $product->promotions_count = $product->promotions->count();
            return $product;
        });

        return $products;
    }

    /**
     * Toggle promotion active status.
     *
     * @param Promotion $promotion
     * @return Promotion
     */
    public function toggleActive(Promotion $promotion): Promotion
    {
        $promotion->update(['is_active' => !$promotion->is_active]);
        return $this->getPromotionWithDetails($promotion);
    }

    /**
     * Toggle promotion featured status.
     *
     * @param Promotion $promotion
     * @return Promotion
     */
    public function toggleFeatured(Promotion $promotion): Promotion
    {
        $promotion->update(['is_featured' => !$promotion->is_featured]);
        return $this->getPromotionWithDetails($promotion);
    }

    /**
     * Update promotion display order.
     *
     * @param array $promotionsData
     * @return bool
     */
    public function updateOrder(array $promotionsData): bool
    {
        foreach ($promotionsData as $promotionData) {
            Promotion::where('id', $promotionData['id'])
                    ->update(['display_order' => $promotionData['display_order']]);
        }
        return true;
    }

    /**
     * Get form data for promotion creation/editing.
     *
     * @return array
     */
    public function getFormData(): array
    {
        return [
            'discount_types' => [
                'percentage' => 'Procent',
                'fixed' => 'Kwota stała'
            ],
            'badge_colors' => [
                'red' => 'Czerwony',
                'green' => 'Zielony',
                'blue' => 'Niebieski',
                'yellow' => 'Żółty',
                'purple' => 'Fioletowy',
                'orange' => 'Pomarańczowy'
            ]
        ];
    }
} 