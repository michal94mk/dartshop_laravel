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
    /**
     * Add a product to the shopping cart
     *
     * @param Request $request
     * @param int $productId
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function addToCart(Request $request, $productId)
    {
        // Validate product exists
        $product = Product::find($productId);
        if (!$product) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['error' => 'Product not found'], 404);
            }
            return redirect()->back()->with('error', 'Product not found');
        }

        $cart = session()->get('cart', []);

        // Increment quantity if product already in cart, otherwise add it
        if (isset($cart[$productId])) {
            $cart[$productId]['quantity']++;
        } else {
            $cart[$productId] = [
                'product' => $product,
                'quantity' => 1,
            ];
        }

        // Calculate cart totals
        $totalQuantity = 0;
        $totalPrice = 0;
        
        foreach ($cart as $item) {
            $totalQuantity += $item['quantity'];
            $totalPrice += $item['product']->price * $item['quantity'];
        }

        session()->put('cart', $cart);
        session()->save();

        // Handle AJAX/JSON response
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true, 
                'message' => 'Product added to cart', 
                'cart' => $cart, 
                'total_quantity' => $totalQuantity,
                'total_price' => number_format($totalPrice, 2)
            ]);
        }

        return redirect()->back()->with('success', 'Product has been added to cart');
    }

    /**
     * Get current cart contents and totals as JSON response
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
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

    /**
     * Display the cart page with all items and totals
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function cartView(Request $request)
    {
        $cart = session()->get('cart', []);
        $total = 0;
        $quantity = 0;

        foreach ($cart as $item) {
            $total += $item['product']->price * $item['quantity'];
            $quantity += $item['quantity'];
        }

        return view('frontend.cart.cart', compact('cart', 'total', 'quantity'));
    }

    /**
     * Custom pagination for collection items
     * 
     * @param mixed $items
     * @param int $perPage
     * @param int|null $page
     * @param array $options
     * @return LengthAwarePaginator
     */
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

    /**
     * Remove one unit of a product from the cart or delete completely if last unit
     *
     * @param Request $request
     * @param int $productId
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function deleteFromCart(Request $request, $productId)
    {
        $cart = session()->get('cart', []);

        if (array_key_exists($productId, $cart)) {
            // Decrement quantity or remove if last item
            if ($cart[$productId]['quantity'] > 1) {
                $cart[$productId]['quantity']--;
            } else {
                unset($cart[$productId]);
            }

            // Recalculate cart totals
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

    /**
     * Remove all items from the cart
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function emptyCart(Request $request)
    {
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