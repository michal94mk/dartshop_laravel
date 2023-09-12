<?php

namespace App\View\Composers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Session;

class CartComposer
{
    public function compose(View $view)
    {
        $cart = Session::get('cart', []);

        $totalQty = 0;

        foreach ($cart as $item) {
            $totalQty += $item['quantity'];
        }

        $view->with('totalQty', $totalQty);
    }
}
