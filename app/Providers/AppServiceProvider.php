<?php

namespace App\Providers;

use App\Services\CartService;
use App\Models\Product;
use App\Observers\ProductCacheObserver;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(CartService::class, function ($app) {
            return new CartService();
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Paginator::useTailwind();
        
        // Register observers
        Product::observe(ProductCacheObserver::class);
    }
}
