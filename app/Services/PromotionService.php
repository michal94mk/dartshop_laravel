<?php

namespace App\Services;

use App\Models\Product;

class PromotionService
{
    /**
     * Add promotion information to product
     */
    public function addPromotionInfo($product)
    {
        $bestPromotion = $product->getBestActivePromotion();
        if ($bestPromotion) {
            $product->promotion_price = $product->getPromotionalPrice();
            $product->savings = $product->getSavingsAmount();
            $product->promotion = [
                'id' => $bestPromotion->id,
                'title' => $bestPromotion->title,
                'badge_text' => $bestPromotion->badge_text,
                'badge_color' => $bestPromotion->badge_color,
                'discount_type' => $bestPromotion->discount_type,
                'discount_value' => $bestPromotion->discount_value
            ];
        } else {
            $product->promotion_price = $product->price;
            $product->savings = 0;
        }
        
        return $product;
    }

    /**
     * Add promotion information to collection of products
     */
    public function addPromotionInfoToCollection($products)
    {
        return $products->transform(function ($product) {
            return $this->addPromotionInfo($product);
        });
    }
} 