<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Cart;
use Illuminate\Support\Facades\Session;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Arr;

class CartController extends Controller
{
    public function addToCart(Request $request, $productId)
    {
        $product = Product::find($productId);
        if (!$product) {
            return response()->json(['error' => 'Product not found'], 404);
        }

        $cart = $request->session()->get('cart', []);

        if (array_key_exists($productId, $cart)) {
            $cart[$productId]['quantity']++;
        } else {
            $cart[$productId] = [
                'product' => $product,
                'quantity' => 1,
            ];
        }

        $totalQuantity = array_sum(array_column($cart, 'quantity'));

        $request->session()->put('cart', $cart);

        return response()->json(['cart' => $cart, 'quantity','total_quantity' => $totalQuantity]);
    }

    public function getCartContents(Request $request)
    {
        $cart = $request->session()->get('cart', []);
        return response()->json(['cart' => $cart]);
    }

    public function cartView(Request $request)
    {
        $cart = $request->session()->get('cart', []);
        $total = 0;
        $quantity = 0;

        foreach ($cart as $item) {
            $total += $item['product']->price * $item['quantity'];
            $quantity += $item['quantity'];
        }

        // Check if request is for Tailwind view
        if (request()->has('tailwind')) {
            return view('frontend.cart.cart-tailwind', compact('cart', 'total', 'quantity'));
        }

        // Default pagination for original template
        $products = collect($cart);
        $perPage = 10;
        $page = $request->input('page', 1);
        $pagedData = $this->paginate($products, $perPage, $page);

        return view('frontend.cart.cart', compact('pagedData', 'total', 'quantity'));
    }

    protected function paginate($items, $perPage, $page, $options = [])
    {
        $items = $items instanceof Collection ? $items : Collection::make($items);

        $page = $page ?: Paginator::resolveCurrentPage() ?: 1;

        return new LengthAwarePaginator(
            $items->forPage($page, $perPage),
            $items->count(),
            $perPage,
            $page,
            $options
        );
    }

    public function deleteFromCart(Request $request, $productId)
    {
        $cart = $request->session()->get('cart', []);

        if (array_key_exists($productId, $cart)) {
            if ($cart[$productId]['quantity'] > 1) {
                $cart[$productId]['quantity']--;
            } else {
                unset($cart[$productId]);
            }

            $totalQuantity = array_sum(array_column($cart, 'quantity'));

            $request->session()->put('cart', $cart);

            return response()->json(['message' => 'Product removed from cart', 'cart' => $cart, 'total_quantity' => $totalQuantity]);
        } else {
            return response()->json(['message' => 'Product not found in cart', 'cart' => $cart], 404);
        }
    }
}
