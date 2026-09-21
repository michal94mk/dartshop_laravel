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
     * Build a priced quote for an authenticated user's DB cart.
     *
     * @return array{subtotal: float, shipping_cost: float, total: float, total_cents: int, shipping_method: string, lines: array<int, array{product_id: int, product_name: string, quantity: int, unit_amount_cents: int}>}
     */
    public function quoteFromUserCart(User $user, string $shippingMethod): array
    {
        $cartItems = CartItem::where('user_id', $user->id)
            ->with(['product.activePromotions'])
            ->get();

        if ($cartItems->isEmpty()) {
            throw new \Exception('Koszyk jest pusty');
        }

        $lines = [];
        foreach ($cartItems as $cartItem) {
            $unitCents = $this->plnToCents($cartItem->product->getPromotionalPrice());
            $lines[] = [
                'product_id' => (int) $cartItem->product_id,
                'product_name' => $cartItem->product->name,
                'quantity' => (int) $cartItem->quantity,
                'unit_amount_cents' => $unitCents,
            ];
        }

        return $this->quoteFromLines($lines, $shippingMethod);
    }

    /**
     * Build a priced quote for guest cart lines (IDs + quantities only; prices from DB).
     *
     * @param  array<int, array{product_id: int, quantity: int}>  $cartData
     * @return array{subtotal: float, shipping_cost: float, total: float, total_cents: int, shipping_method: string, lines: array<int, array{product_id: int, product_name: string, quantity: int, unit_amount_cents: int}>}
     */
    public function quoteFromGuestCart(array $cartData, string $shippingMethod): array
    {
        $prepared = $this->prepareGuestCartItems($cartData);

        if (empty($prepared)) {
            throw new \Exception('Koszyk jest pusty');
        }

        $lines = [];
        foreach ($prepared as $item) {
            $lines[] = [
                'product_id' => (int) $item['product']->id,
                'product_name' => $item['product']->name,
                'quantity' => (int) $item['quantity'],
                'unit_amount_cents' => $this->plnToCents($item['price']),
            ];
        }

        return $this->quoteFromLines($lines, $shippingMethod);
    }

    /**
     * Recompute totals from a frozen payment snapshot (prices locked at intent creation).
     *
     * @param  array{shipping_method: string, lines: array<int, array{product_id: int, product_name: string, quantity: int, unit_amount_cents: int}>}  $snapshot
     * @return array{subtotal: float, shipping_cost: float, total: float, total_cents: int, shipping_method: string, lines: array}
     */
    public function quoteFromSnapshot(array $snapshot): array
    {
        if (empty($snapshot['lines']) || empty($snapshot['shipping_method'])) {
            throw new \Exception('Brak zaufanego podsumowania płatności');
        }

        return $this->quoteFromLines($snapshot['lines'], $snapshot['shipping_method']);
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
            $quote = $this->quoteFromUserCart($user, $shippingMethod);
            $order = $this->persistOrderFromQuote($quote, $shippingData, $paymentIntentId, $stripeSessionId, $user->id);
            CartItem::where('user_id', $user->id)->delete();

            return $order;
        });
    }

    /**
     * Create order from guest cart data (prices always resolved from DB).
     */
    public function createOrderFromGuestCart(
        array $cartData,
        array $shippingData,
        string $shippingMethod,
        string $paymentIntentId,
        ?string $stripeSessionId = null
    ): Order {
        return DB::transaction(function () use ($cartData, $shippingData, $shippingMethod, $paymentIntentId, $stripeSessionId) {
            $quote = $this->quoteFromGuestCart($cartData, $shippingMethod);

            return $this->persistOrderFromQuote($quote, $shippingData, $paymentIntentId, $stripeSessionId, null);
        });
    }

    /**
     * Create order from the cart snapshot frozen when the PaymentIntent was created.
     *
     * @param  array{shipping_method: string, lines: array}  $snapshot
     */
    public function createOrderFromPaymentSnapshot(
        array $snapshot,
        array $shippingData,
        string $paymentIntentId,
        ?int $userId = null,
        ?string $stripeSessionId = null
    ): Order {
        return DB::transaction(function () use ($snapshot, $shippingData, $paymentIntentId, $userId, $stripeSessionId) {
            $quote = $this->quoteFromSnapshot($snapshot);
            $order = $this->persistOrderFromQuote($quote, $shippingData, $paymentIntentId, $stripeSessionId, $userId);

            if ($userId !== null) {
                CartItem::where('user_id', $userId)->delete();
            }

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
     * @param  array<int, array{product_id: int, product_name: string, quantity: int, unit_amount_cents: int}>  $lines
     * @return array{subtotal: float, shipping_cost: float, total: float, total_cents: int, shipping_method: string, lines: array}
     */
    private function quoteFromLines(array $lines, string $shippingMethod): array
    {
        if (!$this->shippingService->isValidMethod($shippingMethod)) {
            throw new \InvalidArgumentException('Nieprawidłowa metoda wysyłki');
        }

        $subtotalCents = 0;
        foreach ($lines as $line) {
            $subtotalCents += (int) $line['unit_amount_cents'] * (int) $line['quantity'];
        }

        $subtotal = $this->centsToPln($subtotalCents);
        $shippingCost = $this->shippingService->calculateShippingCost($shippingMethod, $subtotal);
        $shippingCents = $this->plnToCents($shippingCost);
        $totalCents = $subtotalCents + $shippingCents;

        return [
            'subtotal' => $subtotal,
            'shipping_cost' => $shippingCost,
            'total' => $this->centsToPln($totalCents),
            'total_cents' => $totalCents,
            'shipping_method' => $shippingMethod,
            'lines' => $lines,
        ];
    }

    /**
     * @param  array{subtotal: float, shipping_cost: float, total: float, shipping_method: string, lines: array}  $quote
     */
    private function persistOrderFromQuote(
        array $quote,
        array $shippingData,
        string $paymentIntentId,
        ?string $stripeSessionId,
        ?int $userId
    ): Order {
        $nameData = $this->parseName($shippingData['name']);

        $order = Order::create([
            'user_id' => $userId,
            'order_number' => Order::generateOrderNumber(),
            'status' => OrderStatus::Processing,
            'first_name' => $nameData['first_name'],
            'last_name' => $nameData['last_name'],
            'email' => $shippingData['email'],
            'address' => $shippingData['address'],
            'city' => $shippingData['city'],
            'postal_code' => $shippingData['postalCode'],
            'country' => 'Polska',
            'subtotal' => (float) $quote['subtotal'],
            'shipping_cost' => (float) $quote['shipping_cost'],
            'discount' => 0.0,
            'total' => (float) $quote['total'],
            'payment_method' => 'stripe',
            'payment_intent_id' => $paymentIntentId,
            'stripe_session_id' => $stripeSessionId,
            'shipping_method' => $quote['shipping_method'],
            'payment_status' => 'paid',
        ]);

        foreach ($quote['lines'] as $line) {
            $unitPrice = $this->centsToPln((int) $line['unit_amount_cents']);
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $line['product_id'],
                'product_name' => $line['product_name'],
                'quantity' => $line['quantity'],
                'product_price' => $unitPrice,
                'total_price' => $unitPrice * $line['quantity'],
            ]);
        }

        return $order;
    }

    public function plnToCents(float $amount): int
    {
        return (int) round($amount * 100);
    }

    public function centsToPln(int $cents): float
    {
        return round($cents / 100, 2);
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