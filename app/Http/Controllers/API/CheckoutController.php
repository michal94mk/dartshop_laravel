<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\ShippingService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CheckoutController extends Controller
{
    protected $shippingService;

    public function __construct(ShippingService $shippingService)
    {
        $this->middleware('auth')->except(['showOrder']);
        $this->shippingService = $shippingService;
    }

    public function index()
    {
        $user = Auth::user();
        $cartItems = CartItem::where('user_id', $user->id)
            ->with(['product.activePromotions'])
            ->get();

        if ($cartItems->isEmpty()) {
            return response()->json([
                'message' => 'Koszyk jest pusty'
            ], 400);
        }

        // Add promotional price information to each product
        foreach ($cartItems as $cartItem) {
            $product = $cartItem->product;
            if ($product->hasActivePromotion()) {
                $product->promotion_price = $product->getPromotionalPrice();
                $product->savings = $product->getSavingsAmount();
                $product->promotion = $product->getBestActivePromotion();
            } else {
                $product->promotion_price = $product->price;
                $product->savings = 0;
                $product->promotion = null;
            }
        }

        // Calculate cart total with promotional prices
        $cartTotal = $cartItems->sum(function ($item) {
            return $item->product->getPromotionalPrice() * $item->quantity;
        });

        // Get shipping methods with calculated costs
        $shippingMethods = $this->shippingService->getShippingMethodsWithCosts($cartTotal);

        return response()->json([
            'cart_items' => $cartItems,
            'user' => $user,
            'shipping_methods' => $shippingMethods,
            'cart_total' => $cartTotal,
            'free_shipping_threshold' => $this->shippingService->getFreeShippingThreshold()
        ]);
    }

    public function process(Request $request)
    {
        $user = Auth::user();
        $cartItems = CartItem::where('user_id', $user->id)
            ->with(['product.activePromotions'])
            ->get();

        if ($cartItems->isEmpty()) {
            return response()->json([
                'message' => 'Koszyk jest pusty'
            ], 400);
        }

        $request->validate([
            'shipping.name' => 'required|string|max:255',
            'shipping.email' => 'required|email|max:255',
            'shipping.address' => 'required|string|max:255',
            'shipping.city' => 'required|string|max:255',
            'shipping.postalCode' => 'required|string|max:10',
            'shipping_method' => 'required|string',
        ]);

        try {
            DB::beginTransaction();

            // Calculate subtotal with promotional prices
            $subtotal = $cartItems->sum(function ($item) {
                return $item->product->getPromotionalPrice() * $item->quantity;
            });

            // Validate and calculate shipping cost
            $shippingMethod = $request->input('shipping_method');
            if (!$this->shippingService->isValidMethod($shippingMethod)) {
                return response()->json([
                    'message' => 'Nieprawidłowa metoda wysyłki'
                ], 400);
            }

            $shippingCost = $this->shippingService->calculateShippingCost($shippingMethod, $subtotal);
            $discount = 0;
            $total = $subtotal + $shippingCost - $discount;

            // Split name into first name and last name
            $nameParts = explode(' ', $request->shipping['name'], 2);
            $firstName = $nameParts[0];
            $lastName = isset($nameParts[1]) ? $nameParts[1] : '';

            // Create order
            $order = Order::create([
                'user_id' => $user->id,
                'status' => 'pending',
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $request->shipping['email'],
                'address' => $request->shipping['address'],
                'city' => $request->shipping['city'],
                'postal_code' => $request->shipping['postalCode'],
                'country' => 'PL', // Default to Poland for now
                'subtotal' => $subtotal,
                'shipping_cost' => $shippingCost,
                'discount' => $discount,
                'total' => $total,
                'payment_method' => 'cod',
                'shipping_method' => $shippingMethod,
            ]);

            // Create order items with promotional prices
            foreach ($cartItems as $cartItem) {
                $promotionalPrice = $cartItem->product->getPromotionalPrice();
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $cartItem->product_id,
                    'product_name' => $cartItem->product->name,
                    'quantity' => $cartItem->quantity,
                    'price' => $promotionalPrice,
                    'total' => $promotionalPrice * $cartItem->quantity,
                ]);
            }

            // Clear the cart
            CartItem::where('user_id', $user->id)->delete();

            DB::commit();

            return response()->json([
                'message' => 'Zamówienie zostało utworzone',
                'order' => $order->load('items')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Wystąpił błąd podczas przetwarzania zamówienia'
            ], 500);
        }
    }

    /**
     * Show order details for success page
     */
    public function showOrder($orderId)
    {
        $order = Order::with(['items'])->findOrFail($orderId);
        
        return response()->json([
            'order' => $order
        ]);
    }
} 