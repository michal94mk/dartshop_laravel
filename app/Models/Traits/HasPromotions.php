<?php

namespace App\Models\Traits;

use App\Models\Promotion;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait HasPromotions
{
    public function promotions(): BelongsToMany
    {
        return $this->belongsToMany(Promotion::class, 'product_promotions')
                    ->withTimestamps();
    }

    public function activePromotions(): BelongsToMany
    {
        return $this->promotions()->active();
    }

    public function hasActivePromotion(): bool
    {
        return $this->activePromotions()->exists();
    }

    public function getBestActivePromotion(): ?Promotion
    {
        return $this->activePromotions()
                    ->get()
                    ->sortByDesc(function ($promotion) {
                        return $promotion->getDiscountAmount($this->price);
                    })
                    ->first();
    }

    public function getPromotionalPrice(): float
    {
        $bestPromotion = $this->getBestActivePromotion();
        
        if (!$bestPromotion) {
            return $this->price;
        }
        
        return $bestPromotion->calculateDiscountedPrice($this->price);
    }

    public function getSavingsAmount(): float
    {
        return $this->price - $this->getPromotionalPrice();
    }

    /**
     * Scope dla produktów z aktywnymi promocjami
     */
    public function scopeWithActivePromotions($query)
    {
        return $query->whereHas('promotions', function ($q) {
            $q->where('is_active', true)
              ->where('starts_at', '<=', now())
              ->where(function ($subQ) {
                  $subQ->whereNull('ends_at')
                        ->orWhere('ends_at', '>=', now());
              });
        });
    }

    /**
     * Scope dla produktów bez aktywnych promocji
     */
    public function scopeWithoutActivePromotions($query)
    {
        return $query->whereDoesntHave('promotions', function ($q) {
            $q->where('is_active', true)
              ->where('starts_at', '<=', now())
              ->where(function ($subQ) {
                  $subQ->whereNull('ends_at')
                        ->orWhere('ends_at', '>=', now());
              });
        });
    }
} 