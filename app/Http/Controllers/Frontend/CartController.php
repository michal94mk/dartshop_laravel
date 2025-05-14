<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\Paginator;

class CartController extends Controller
{
    public function addToCart(Request $request, $productId)
    {
        // Find the product
        $product = Product::find($productId);
        if (!$product) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['error' => 'Product not found'], 404);
            }
            return redirect()->back()->with('error', 'Product not found');
        }

        // Get cart from session
        $cart = session()->get('cart', []);

        // Add product to cart
        if (isset($cart[$productId])) {
            $cart[$productId]['quantity']++;
        } else {
            $cart[$productId] = [
                'product' => $product,
                'quantity' => 1,
            ];
        }

        // Calculate total quantity and total price
        $totalQuantity = 0;
        $totalPrice = 0;
        
        foreach ($cart as $item) {
            $totalQuantity += $item['quantity'];
            $totalPrice += $item['product']->price * $item['quantity'];
        }

        // Save cart back to session
        session()->put('cart', $cart);
        session()->save();

        // Respond based on request type
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true, 
                'message' => 'Product added to cart', 
                'cart' => $cart, 
                'total_quantity' => $totalQuantity,
                'total_price' => number_format($totalPrice, 2)
            ]);
        }

        // For regular HTTP requests
        return redirect()->back()->with('success', 'Product has been added to cart');
    }

    public function getCartContents(Request $request)
    {
        $cart = session()->get('cart', []);
        $totalQuantity = 0;
        $totalPrice = 0;
        
        foreach ($cart as $item) {
            $totalQuantity += $item['quantity'];
            $totalPrice += $item['product']->price * $item['quantity'];
        }
        
        return response()->json([
            'success' => true,
            'cart' => $cart,
            'total_quantity' => $totalQuantity,
            'total_price' => number_format($totalPrice, 2)
        ]);
    }

    public function cartView(Request $request)
    {
        $cart = session()->get('cart', []);
        $total = 0;
        $quantity = 0;

        foreach ($cart as $item) {
            $total += $item['product']->price * $item['quantity'];
            $quantity += $item['quantity'];
        }

        // Use cart.blade.php view from the correct path
        return view('frontend.cart.cart', compact('cart', 'total', 'quantity'));
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
        $cart = session()->get('cart', []);

        if (array_key_exists($productId, $cart)) {
            if ($cart[$productId]['quantity'] > 1) {
                $cart[$productId]['quantity']--;
            } else {
                unset($cart[$productId]);
            }

            $totalQuantity = 0;
            $totalPrice = 0;
            
            foreach ($cart as $item) {
                $totalQuantity += $item['quantity'];
                $totalPrice += $item['product']->price * $item['quantity'];
            }

            session()->put('cart', $cart);
            session()->save();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Product removed from cart', 
                    'cart' => $cart, 
                    'total_quantity' => $totalQuantity,
                    'total_price' => number_format($totalPrice, 2)
                ]);
            }

            return redirect()->back()->with('success', 'Product has been removed from cart');
        } 
        
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => false, 'message' => 'Product was not found in the cart', 'cart' => $cart], 404);
        }

        return redirect()->back()->with('error', 'Product was not found in the cart');
    }

    public function emptyCart(Request $request)
    {
        // Remove cart from session
        session()->forget('cart');
        session()->save();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Cart has been emptied',
                'cart' => [],
                'total_quantity' => 0,
                'total_price' => '0.00'
            ]);
        }

        return redirect()->back()->with('success', 'Cart has been emptied');
    }
} 