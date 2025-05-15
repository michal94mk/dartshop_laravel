<?php

namespace App\Http\Controllers\Frontend;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Frontend\OrderRequest;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Frontend OrderController handles all order-related actions for customers.
 * 
 * This controller manages the complete order process flow including
 * displaying the checkout page, processing the order, showing confirmation
 * and order history.
 */
class OrderController extends Controller
{
    /**
     * Display the checkout page with cart items and pricing details.
     *
     * Shows the form for collecting customer shipping and payment information.
     * Calculates all pricing including subtotals, discounts, and shipping costs.
     * 
     * @param Request $request The incoming request
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function checkout(Request $request)
    {
        $cart = session()->get('cart', []);
        
        if (empty($cart)) {
            return redirect()->route('cart.view')->with('error', 'Your cart is empty. Add products before proceeding to checkout.');
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
     * Process the order submission and create all necessary records.
     * 
     * Validates customer input, creates the order record and associated items,
     * calculates final pricing, and directs customer to appropriate payment method.
     * Clears the cart on successful order placement.
     *
     * @param OrderRequest $request The incoming request with order details
     * @return \Illuminate\Http\RedirectResponse
     */
    public function placeOrder(OrderRequest $request)
    {
        $cart = session()->get('cart', []);
        
        if (empty($cart)) {
            return redirect()->route('cart.view')->with('error', 'Your cart is empty. Add products before placing an order.');
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
                'country' => 'Poland',
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
                    throw new \Exception("Product with ID {$productId} does not exist.");
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
                ->with('success', 'Your order has been placed successfully!');
                
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'An error occurred while placing your order: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    /**
     * Show the order confirmation page after successful order placement.
     *
     * Displays a summary of the order details and next steps based on 
     * payment method selected.
     * 
     * @param Order $order The order entity to display confirmation for
     * @return \Illuminate\View\View
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
     * Show the detailed view of a specific order.
     *
     * Displays comprehensive information about the order including
     * all line items, pricing, shipping address, and status.
     * 
     * @param Order $order The order entity to display
     * @return \Illuminate\View\View
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
     * Show the user's order history with all past orders.
     *
     * Provides a paginated list of all orders placed by the authenticated user,
     * with links to view full details of each order.
     * 
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function history()
    {
        if (!Auth::check()) {
            return redirect()->route('login')
                ->with('info', 'Please log in to view your order history.');
        }
        
        $orders = Order::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('frontend.orders.history', compact('orders'));
    }
}
