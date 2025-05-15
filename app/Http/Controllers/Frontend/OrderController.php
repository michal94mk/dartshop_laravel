<?php

namespace App\Http\Controllers\Frontend;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    /**
     * Display the checkout page.
     */
    public function checkout(Request $request)
    {
        $cart = session()->get('cart', []);
        
        if (empty($cart)) {
            return redirect()->route('cart.view')->with('error', 'Twój koszyk jest pusty. Dodaj produkty przed przejściem do kasy.');
        }
        
        $subtotal = 0;
        $quantity = 0;
        
        foreach ($cart as $item) {
            $subtotal += $item['product']->price * $item['quantity'];
            $quantity += $item['quantity'];
        }
        
        $promotion = session()->get('promotion');
        $discountAmount = 0;
        
        if ($promotion) {
            $discountAmount = $promotion['discount_amount'];
        }
        
        $finalTotal = $subtotal - $discountAmount;
        $shippingCost = 15.00;
        
        // Free shipping for orders over 200 zł (can be adjusted based on your business rules)
        if ($finalTotal >= 200) {
            $shippingCost = 0;
        }
        
        $total = $finalTotal + $shippingCost;
        
        return view('frontend.orders.checkout', compact(
            'cart', 
            'subtotal', 
            'quantity', 
            'promotion', 
            'discountAmount', 
            'finalTotal', 
            'shippingCost', 
            'total'
        ));
    }
    
    /**
     * Process the order.
     */
    public function placeOrder(Request $request)
    {
        $cart = session()->get('cart', []);
        
        if (empty($cart)) {
            return redirect()->route('cart.view')->with('error', 'Twój koszyk jest pusty. Dodaj produkty przed złożeniem zamówienia.');
        }
        
        // Validate customer details
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'postal_code' => 'required|string|max:10',
            'notes' => 'nullable|string',
            'payment_method' => 'required|string|in:online,cash_on_delivery,bank_transfer',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        // Calculate order totals
        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += $item['product']->price * $item['quantity'];
        }
        
        $promotion = session()->get('promotion');
        $discountAmount = 0;
        $promotionCode = null;
        
        if ($promotion) {
            $discountAmount = $promotion['discount_amount'];
            $promotionCode = $promotion['code'];
        }
        
        $finalSubtotal = $subtotal - $discountAmount;
        $shippingCost = 15.00;
        
        // Free shipping for orders over 200 zł
        if ($finalSubtotal >= 200) {
            $shippingCost = 0;
        }
        
        $total = $finalSubtotal + $shippingCost;
        
        try {
            DB::beginTransaction();
            
            // Create order
            $order = Order::create([
                'user_id' => Auth::id(),
                'order_number' => Order::generateOrderNumber(),
                'status' => OrderStatus::PENDING,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address,
                'city' => $request->city,
                'postal_code' => $request->postal_code,
                'country' => 'Polska',
                'notes' => $request->notes,
                'subtotal' => $subtotal,
                'shipping_cost' => $shippingCost,
                'discount' => $discountAmount,
                'total' => $total,
                'payment_method' => $request->payment_method,
                'session_id' => session()->getId(),
                'promotion_code' => $promotionCode,
            ]);
            
            // Create order items
            foreach ($cart as $productId => $item) {
                $product = Product::find($productId);
                
                if (!$product) {
                    throw new \Exception("Produkt o ID {$productId} nie istnieje.");
                }
                
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'quantity' => $item['quantity'],
                    'price' => $product->price,
                    'total' => $product->price * $item['quantity'],
                ]);
            }
            
            DB::commit();
            
            // Clear the cart after successful order
            session()->forget('cart');
            session()->forget('promotion');
            
            // Redirect based on payment method
            if ($request->payment_method === 'online') {
                // Store order ID in session for the payment process
                session()->put('order_id', $order->id);
                return redirect()->route('payment.process', ['order' => $order->id]);
            }
            
            return redirect()->route('order.confirmation', ['order' => $order->id])
                ->with('success', 'Twoje zamówienie zostało złożone pomyślnie!');
                
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Wystąpił błąd podczas składania zamówienia: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    /**
     * Show the order confirmation page.
     */
    public function confirmation(Order $order)
    {
        // Verify that the order belongs to the current user or session
        if (Auth::check() && $order->user_id !== Auth::id() && $order->session_id !== session()->getId()) {
            abort(403, 'Unauthorized action.');
        }
        
        return view('frontend.orders.confirmation', compact('order'));
    }
    
    /**
     * Show the order details page.
     */
    public function show(Order $order)
    {
        // Verify that the order belongs to the current user or session
        if (Auth::check() && $order->user_id !== Auth::id() && $order->session_id !== session()->getId()) {
            abort(403, 'Unauthorized action.');
        }
        
        return view('frontend.orders.show', compact('order'));
    }
    
    /**
     * Show the user's orders history.
     */
    public function history()
    {
        if (!Auth::check()) {
            return redirect()->route('login')
                ->with('info', 'Zaloguj się, aby zobaczyć historię zamówień.');
        }
        
        $orders = Order::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('frontend.orders.history', compact('orders'));
    }
}
