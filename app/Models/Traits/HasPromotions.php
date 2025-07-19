<?php

namespace App\Models\Traits;

use App\Models\Promotion;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait HasPromotions
{
    /**
     * Relacja many-to-many z promocjami
     */
    public function promotions(): BelongsToMany
    {
        return $this->belongsToMany(Promotion::class, 'product_promotions')
                    ->withTimestamps();
    }

    /**
     * Zwraca aktywne promocje dla produktu
     */
    public function activePromotions(): BelongsToMany
    {
        return $this->promotions()->active();
    }

    /**
     * Sprawdza czy produkt ma aktywną promocję
     */
    public function hasActivePromotion(): bool
    {
        return $this->activePromotions()->exists();
    }

    /**
     * Zwraca najlepszą aktywną promocję (największy rabat)
     */
    public function getBestActivePromotion(): ?Promotion
    {
        return $this->activePromotions()
                    ->get()
                    ->sortByDesc(function ($promotion) {
                        return $promotion->getDiscountAmount($this->price);
                    })
                    ->first();
    }

    /**
     * Zwraca cenę po rabacie (jeśli jest aktywna promocja)
     */
    public function getPromotionalPrice(): float
    {
        $bestPromotion = $this->getBestActivePromotion();
        
        if (!$bestPromotion) {
            return $this->price;
        }
        
        return $bestPromotion->calculateDiscountedPrice($this->price);
    }

    /**
     * Zwraca kwotę oszczędności
     */
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