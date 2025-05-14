<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Promotion;
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
        $promotion = session()->get('promotion');
        $total = 0;
        $quantity = 0;

        foreach ($cart as $item) {
            $total += $item['product']->price * $item['quantity'];
            $quantity += $item['quantity'];
        }

        $discountAmount = 0;
        if ($promotion) {
            $discountAmount = $promotion['discount_amount'];
        }

        $finalTotal = $total - $discountAmount;
        $shippingCost = 15.00;

        // Free shipping for orders over 200 zł (can be adjusted based on your business rules)
        if ($finalTotal >= 200) {
            $shippingCost = 0;
        }

        return view('frontend.cart.cart', compact('cart', 'total', 'quantity', 'promotion', 'discountAmount', 'finalTotal', 'shippingCost'));
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
        session()->forget('promotion');
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

    /**
     * Apply a promotion code to the cart
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function applyPromotion(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:50'
        ]);

        $code = $request->input('code');
        $cart = session()->get('cart', []);
        
        // Calculate current cart total
        $cartTotal = 0;
        foreach ($cart as $item) {
            $cartTotal += $item['product']->price * $item['quantity'];
        }

        // Find the promotion by code
        $promotion = Promotion::where('code', $code)->first();

        if (!$promotion) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Kod promocyjny jest nieprawidłowy.'
                ]);
            }
            return redirect()->back()->with('error', 'Kod promocyjny jest nieprawidłowy.');
        }

        if (!$promotion->isValid()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Kod promocyjny wygasł lub jest nieaktywny.'
                ]);
            }
            return redirect()->back()->with('error', 'Kod promocyjny wygasł lub jest nieaktywny.');
        }

        if ($promotion->minimum_order_value && $cartTotal < $promotion->minimum_order_value) {
            $message = "Minimalna wartość zamówienia to {$promotion->minimum_order_value} zł.";
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false, 
                    'message' => $message
                ]);
            }
            return redirect()->back()->with('error', $message);
        }

        // Calculate discount
        $discountAmount = $promotion->calculateDiscountAmount($cartTotal);

        // Store promotion in session
        session()->put('promotion', [
            'id' => $promotion->id,
            'code' => $promotion->code,
            'discount_type' => $promotion->discount_type,
            'discount_value' => $promotion->discount_value,
            'discount_amount' => $discountAmount
        ]);
        session()->save();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true, 
                'message' => 'Kod promocyjny został pomyślnie zastosowany.',
                'promotion' => session()->get('promotion'),
                'cart_total' => $cartTotal,
                'discount_amount' => $discountAmount,
                'final_total' => $cartTotal - $discountAmount
            ]);
        }

        return redirect()->back()->with('success', 'Kod promocyjny został pomyślnie zastosowany.');
    }

    /**
     * Remove the promotion code from the cart
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function removePromotion(Request $request)
    {
        session()->forget('promotion');
        session()->save();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Kod promocyjny został usunięty.'
            ]);
        }

        return redirect()->back()->with('success', 'Kod promocyjny został usunięty.');
    }
} 