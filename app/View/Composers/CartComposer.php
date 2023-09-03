<?php

namespace App\View\Composers;

use Illuminate\View\View;
use Illuminate\Http\Request;

class CartComposer
{
    public function compose(View $view)
    {
        $cart = session('cart', []);
        $view->with('cart', $cart);
    }
}

