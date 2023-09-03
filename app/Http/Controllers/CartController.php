<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\Product;

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

        $request->session()->put('cart', $cart);

        return response()->json(['cart' => $cart]);
    }

    public function getCartContents(Request $request)
    {
        $cart = $request->session()->get('cart', []);
        return response()->json(['cart' => $cart]);
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

            $request->session()->put('cart', $cart);
            return response()->json(['message' => 'Product removed from cart', 'cart' => $cart]);
        } else {
            return response()->json(['message' => 'Product not found in cart', 'cart' => $cart], 404);
        }
    }

}
