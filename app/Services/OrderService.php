<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use App\Enums\OrderStatus;
use App\Services\ShippingService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class OrderService
{
    protected $shippingService;

    public function __construct(ShippingService $shippingService)
    {
        $this->shippingService = $shippingService;
    }

    /**
     * Create order from authenticated user's cart
     */
    public function createOrderFromCart(
        User $user,
        array $shippingData,
        string $shippingMethod,
        string $paymentIntentId,
        ?string $stripeSessionId = null
    ): Order {
        return DB::transaction(function () use ($user, $shippingData, $shippingMethod, $paymentIntentId, $stripeSessionId) {
            $cartItems = CartItem::where('user_id', $user->id)
                ->with(['product.activePromotions'])
                ->get();

            if ($cartItems->isEmpty()) {
                throw new \Exception('Koszyk jest pusty');
            }

            // Calculate totals
            $subtotal = $this->calculateCartSubtotal($cartItems);
            $shippingCost = $this->shippingService->calculateShippingCost($shippingMethod, $subtotal);
            $discount = 0;
            $total = $subtotal + $shippingCost - $discount;

            // Parse name
            $nameData = $this->parseName($shippingData['name']);

            // Create order
            $order = Order::create([
                'user_id' => $user->id,
                'order_number' => Order::generateOrderNumber(),
                'status' => OrderStatus::Processing,
                'first_name' => $nameData['first_name'],
                'last_name' => $nameData['last_name'],
                'email' => $shippingData['email'],
                'address' => $shippingData['address'],
                'city' => $shippingData['city'],
                'postal_code' => $shippingData['postalCode'],
                'country' => 'Polska',
                'subtotal' => (float) $subtotal,
                'shipping_cost' => (float) $shippingCost,
                'discount' => (float) $discount,
                'total' => (float) $total,
                'payment_method' => 'stripe',
                'payment_intent_id' => $paymentIntentId,
                'stripe_session_id' => $stripeSessionId,
                'shipping_method' => $shippingMethod,
                'payment_status' => 'paid'
            ]);

            // Create order items
            $this->createOrderItemsFromCart($order, $cartItems);

            // Clear cart
            CartItem::where('user_id', $user->id)->delete();

            return $order;
        });
    }

    /**
     * Create order from guest cart data
     */
    public function createOrderFromGuestCart(
        array $cartData,
        array $shippingData,
        string $shippingMethod,
        string $paymentIntentId,
        ?string $stripeSessionId = null
    ): Order {
        return DB::transaction(function () use ($cartData, $shippingData, $shippingMethod, $paymentIntentId, $stripeSessionId) {
            $cartItems = $this->prepareGuestCartItems($cartData);

            if (empty($cartItems)) {
                throw new \Exception('Koszyk jest pusty');
            }

            // Calculate totals
            $subtotal = $this->calculateGuestCartSubtotal($cartItems);
            $shippingCost = $this->shippingService->calculateShippingCost($shippingMethod, $subtotal);
            $discount = 0;
            $total = $subtotal + $shippingCost - $discount;

            // Parse name
            $nameData = $this->parseName($shippingData['name']);

            // Create order
            $order = Order::create([
                'user_id' => null,
                'order_number' => Order::generateOrderNumber(),
                'status' => OrderStatus::Processing,
                'first_name' => $nameData['first_name'],
                'last_name' => $nameData['last_name'],
                'email' => $shippingData['email'],
                'address' => $shippingData['address'],
                'city' => $shippingData['city'],
                'postal_code' => $shippingData['postalCode'],
                'country' => 'Polska',
                'subtotal' => (float) $subtotal,
                'shipping_cost' => (float) $shippingCost,
                'discount' => (float) $discount,
                'total' => (float) $total,
                'payment_method' => 'stripe',
                'payment_intent_id' => $paymentIntentId,
                'stripe_session_id' => $stripeSessionId,
                'shipping_method' => $shippingMethod,
                'payment_status' => 'paid'
            ]);

            // Create order items
            $this->createOrderItemsFromGuestCart($order, $cartItems);

            return $order;
        });
    }

    /**
     * Validate shipping method
     */
    public function validateShippingMethod(string $shippingMethod): bool
    {
        return $this->shippingService->isValidMethod($shippingMethod);
    }

    /**
     * Check if order already exists by payment intent
     */
    public function orderExistsByPaymentIntent(string $paymentIntentId): ?Order
    {
        return Order::where('payment_intent_id', $paymentIntentId)->first();
    }

    /**
     * Check if order already exists by stripe session
     */
    public function orderExistsByStripeSession(string $sessionId): ?Order
    {
        return Order::where('stripe_session_id', $sessionId)->first();
    }

    /**
     * Calculate subtotal from cart items
     */
    private function calculateCartSubtotal($cartItems): float
    {
        return $cartItems->sum(function ($item) {
            return $item->product->getPromotionalPrice() * $item->quantity;
        });
    }

    /**
     * Calculate subtotal from guest cart items
     */
    private function calculateGuestCartSubtotal(array $cartItems): float
    {
        return array_sum(array_map(function ($item) {
            return $item['price'] * $item['quantity'];
        }, $cartItems));
    }

    /**
     * Parse full name into first and last name
     */
    private function parseName(string $fullName): array
    {
        $nameParts = explode(' ', trim($fullName), 2);
        
        return [
            'first_name' => $nameParts[0],
            'last_name' => isset($nameParts[1]) ? $nameParts[1] : ''
        ];
    }

    /**
     * Create order items from authenticated user's cart
     */
    private function createOrderItemsFromCart(Order $order, $cartItems): void
    {
        foreach ($cartItems as $cartItem) {
            $promotionalPrice = $cartItem->product->getPromotionalPrice();
            
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $cartItem->product_id,
                'product_name' => $cartItem->product->name,
                'quantity' => $cartItem->quantity,
                'product_price' => $promotionalPrice,
                'total_price' => $promotionalPrice * $cartItem->quantity
            ]);
        }
    }

    /**
     * Create order items from guest cart
     */
    private function createOrderItemsFromGuestCart(Order $order, array $cartItems): void
    {
        foreach ($cartItems as $cartItem) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $cartItem['product']->id,
                'product_name' => $cartItem['product']->name,
                'quantity' => $cartItem['quantity'],
                'product_price' => $cartItem['price'],
                'total_price' => $cartItem['total']
            ]);
        }
    }

    /**
     * Prepare cart items from guest cart data
     */
    private function prepareGuestCartItems(array $cartData): array
    {
        $cartItems = [];

        foreach ($cartData as $item) {
            $product = Product::with('activePromotions')->find($item['product_id']);
            
            if (!$product) {
                throw new \Exception("Produkt o ID {$item['product_id']} nie istnieje");
            }

            $promotionalPrice = $product->getPromotionalPrice();
            
            $cartItems[] = [
                'product' => $product,
                'quantity' => $item['quantity'],
                'price' => $promotionalPrice,
                'total' => $promotionalPrice * $item['quantity']
            ];
        }

        return $cartItems;
    }
} 