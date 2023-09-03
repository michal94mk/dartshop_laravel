<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use App\View\Composers\CategoriesComposer;
use App\View\Composers\CartComposer;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */

    public function boot()
    {
        Paginator::useBootstrapThree();
        view()->composer(['layouts.app'], CategoriesComposer::class);
        view()->composer(['layouts.app'], CartComposer::class);
    }
}
