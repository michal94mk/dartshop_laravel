<?php

namespace App\Observers;

use App\Models\Product;
use App\Services\Payment\PaymentService;
use Illuminate\Support\Facades\Cache;

class ProductCacheObserver
{
    /**
     * Handle the Product "updated" event.
     */
    public function updated(Product $product): void
    {
        $this->clearProductCache($product->id);
    }

    /**
     * Handle the Product "deleted" event.
     */
    public function deleted(Product $product): void
    {
        $this->clearProductCache($product->id);
    }

    /**
     * Handle the Product "restored" event.
     */
    public function restored(Product $product): void
    {
        $this->clearProductCache($product->id);
    }

    /**
     * Clear cache for specific product
     */
    private function clearProductCache(int $productId): void
    {
        $cacheKey = "product_with_promotions_{$productId}";
        Cache::forget($cacheKey);
    }
} 