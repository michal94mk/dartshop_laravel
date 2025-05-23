<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();
        $cartItems = CartItem::where('user_id', $user->id)
            ->with('product')
            ->get();

        if ($cartItems->isEmpty()) {
            return response()->json([
                'message' => 'Koszyk jest pusty'
            ], 400);
        }

        return response()->json([
            'cart_items' => $cartItems,
            'user' => $user
        ]);
    }

    public function process(Request $request)
    {
        $user = Auth::user();
        $cartItems = CartItem::where('user_id', $user->id)
            ->with('product')
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
        ]);

        try {
            DB::beginTransaction();

            // Calculate subtotal
            $subtotal = $cartItems->sum(function ($item) {
                return $item->product->price * $item->quantity;
            });

            // For now, we'll set shipping cost to 0 and no discount
            $shippingCost = 0;
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
                'payment_method' => 'stripe', // Will be implemented later
            ]);

            // Create order items
            foreach ($cartItems as $cartItem) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $cartItem->product_id,
                    'product_name' => $cartItem->product->name,
                    'quantity' => $cartItem->quantity,
                    'price' => $cartItem->product->price,
                    'total' => $cartItem->product->price * $cartItem->quantity,
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
} 