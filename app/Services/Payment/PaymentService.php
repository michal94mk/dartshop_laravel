<?php

namespace App\Services\Payment;

use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\Checkout\Session;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use App\Services\OrderService;
use App\Services\ShippingService;
use App\Exceptions\PaymentAmountMismatchException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class PaymentService
{
    private const CART_SNAPSHOT_TTL_SECONDS = 86400;

    protected $shippingService;
    protected $orderService;

    public function __construct(ShippingService $shippingService, OrderService $orderService)
    {
        $this->initializeStripe();
        $this->shippingService = $shippingService;
        $this->orderService = $orderService;
    }

    /**
     * Initialize Stripe API key
     */
    private function initializeStripe(): void
    {
        $stripeSecret = Cache::remember('stripe_secret_key', 3600, function () {
            return config('services.stripe.secret');
        });
        
        if (empty($stripeSecret)) {
            throw new \Exception('Stripe secret key is not configured. Please check your .env file.');
        }
        
        Stripe::setApiKey($stripeSecret);
    }

    /**
     * Get available payment methods
     */
    public function getPaymentMethods(): array
    {
        return Cache::remember('stripe_payment_methods', 3600, function () {
            $paymentMethods = ['card', 'blik'];
            
            if (config('services.stripe.p24.enabled', true)) {
                $paymentMethods[] = 'p24';
            }
            
            return $paymentMethods;
        });
    }

    /**
     * Create checkout session for authenticated user
     */
    public function createCheckoutSession(array $shippingData, string $shippingMethod): array
    {
        $user = Auth::user();
        $cartItems = CartItem::where('user_id', $user->id)
            ->with(['product.activePromotions'])
            ->get();

        if ($cartItems->isEmpty()) {
            throw new \Exception('Koszyk jest pusty');
        }

        $lineItems = $this->prepareLineItemsFromCart($cartItems);
        
        $session = Session::create([
            'payment_method_types' => $this->getPaymentMethods(),
            'line_items' => $lineItems,
            'mode' => 'payment',
            'success_url' => config('app.url') . '/payment/success?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => config('app.url') . '/checkout',
            'customer_email' => $shippingData['email'],
            'metadata' => [
                'user_id' => $user->id,
                'shipping_data' => json_encode($shippingData),
                'shipping_method' => $shippingMethod,
            ],
            'shipping_address_collection' => [
                'allowed_countries' => ['PL'],
            ],
            'payment_method_options' => [
                'p24' => [
                    'tos_shown_and_accepted' => true,
                ],
            ],
        ]);

        return [
            'checkout_url' => $session->url,
            'session_id' => $session->id
        ];
    }

    /**
     * Create checkout session for guest user
     */
    public function createGuestCheckoutSession(array $cartData, array $shippingData, string $shippingMethod): array
    {
        $lineItems = $this->prepareLineItemsFromGuestCart($cartData);
        
        if (empty($lineItems)) {
            throw new \Exception('Brak produktów w koszyku');
        }

        $session = Session::create([
            'payment_method_types' => $this->getPaymentMethods(),
            'line_items' => $lineItems,
            'mode' => 'payment',
            'success_url' => config('app.url') . '/payment/success?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => config('app.url') . '/checkout',
            'customer_email' => $shippingData['email'],
            'metadata' => [
                'guest_order' => 'true',
                'shipping_data' => json_encode($shippingData),
                'cart_data' => json_encode($cartData),
                'shipping_method' => $shippingMethod,
            ],
            'shipping_address_collection' => [
                'allowed_countries' => ['PL'],
            ],
            'payment_method_options' => [
                'p24' => [
                    'tos_shown_and_accepted' => true,
                ],
            ],
        ]);

        return [
            'checkout_url' => $session->url,
            'session_id' => $session->id
        ];
    }

    /**
     * Create payment intent for authenticated user.
     * Amount includes shipping; cart lines are frozen in cache for confirm.
     */
    public function createPaymentIntent(string $shippingMethod): array
    {
        /** @var User $user */
        $user = Auth::user();
        $quote = $this->orderService->quoteFromUserCart($user, $shippingMethod);

        $paymentIntent = PaymentIntent::create([
            'amount' => $quote['total_cents'],
            'currency' => 'pln',
            'payment_method_types' => $this->getPaymentMethods(),
            'metadata' => [
                'user_id' => (string) $user->id,
                'shipping_method' => $shippingMethod,
                'cart_items_count' => (string) count($quote['lines']),
                'expected_amount_cents' => (string) $quote['total_cents'],
            ],
        ]);

        $this->storeCartSnapshot($paymentIntent->id, [
            'user_id' => $user->id,
            'guest' => false,
            'shipping_method' => $shippingMethod,
            'lines' => $quote['lines'],
            'expected_amount_cents' => $quote['total_cents'],
        ]);

        return [
            'client_secret' => $paymentIntent->client_secret,
            'payment_intent_id' => $paymentIntent->id,
            'amount' => $quote['total'],
        ];
    }

    /**
     * Create payment intent for guest user.
     */
    public function createGuestPaymentIntent(array $cartData, string $shippingMethod): array
    {
        $quote = $this->orderService->quoteFromGuestCart($cartData, $shippingMethod);

        if ($quote['total_cents'] <= 0) {
            throw new \Exception('Nieprawidłowa suma zamówienia');
        }

        $paymentIntent = PaymentIntent::create([
            'amount' => $quote['total_cents'],
            'currency' => 'pln',
            'payment_method_types' => $this->getPaymentMethods(),
            'metadata' => [
                'guest_order' => 'true',
                'shipping_method' => $shippingMethod,
                'cart_items_count' => (string) count($quote['lines']),
                'expected_amount_cents' => (string) $quote['total_cents'],
            ],
        ]);

        $this->storeCartSnapshot($paymentIntent->id, [
            'user_id' => null,
            'guest' => true,
            'shipping_method' => $shippingMethod,
            'lines' => $quote['lines'],
            'expected_amount_cents' => $quote['total_cents'],
        ]);

        return [
            'client_secret' => $paymentIntent->client_secret,
            'payment_intent_id' => $paymentIntent->id,
            'amount' => $quote['total'],
        ];
    }

    /**
     * @return array{user_id: ?int, guest: bool, shipping_method: string, lines: array, expected_amount_cents: int}|null
     */
    public function pullCartSnapshot(string $paymentIntentId): ?array
    {
        $key = $this->cartSnapshotCacheKey($paymentIntentId);
        $snapshot = Cache::get($key);

        return is_array($snapshot) ? $snapshot : null;
    }

    public function forgetCartSnapshot(string $paymentIntentId): void
    {
        Cache::forget($this->cartSnapshotCacheKey($paymentIntentId));
    }

    /**
     * Ensure the succeeded PaymentIntent covers exactly the frozen quote.
     *
     * @throws PaymentAmountMismatchException
     */
    public function assertPaymentIntentMatchesQuote(object $paymentIntent, int $expectedCents): void
    {
        $paidCents = (int) $paymentIntent->amount;
        $currency = strtolower((string) $paymentIntent->currency);

        if ($currency !== 'pln' || $paidCents !== $expectedCents) {
            throw new PaymentAmountMismatchException($expectedCents, $paidCents);
        }
    }

    private function storeCartSnapshot(string $paymentIntentId, array $snapshot): void
    {
        Cache::put(
            $this->cartSnapshotCacheKey($paymentIntentId),
            $snapshot,
            self::CART_SNAPSHOT_TTL_SECONDS
        );
    }

    private function cartSnapshotCacheKey(string $paymentIntentId): string
    {
        return 'payment_intent_cart:' . $paymentIntentId;
    }

    /**
     * Check payment status
     */
    public function checkPaymentStatus(string $paymentIntentId): array
    {
        $paymentIntent = PaymentIntent::retrieve($paymentIntentId);
        
        return [
            'payment_intent_id' => $paymentIntent->id,
            'status' => $paymentIntent->status,
            'amount' => $paymentIntent->amount,
            'currency' => $paymentIntent->currency,
            'payment_method' => $paymentIntent->payment_method_types[0] ?? 'unknown',
            'last_payment_error' => $paymentIntent->last_payment_error ?? null,
            'created' => $paymentIntent->created,
            'updated' => $paymentIntent->updated
        ];
    }

    /**
     * Retrieve checkout session
     */
    public function getCheckoutSession(string $sessionId): Session
    {
        return Session::retrieve($sessionId);
    }

    /**
     * Retrieve payment intent
     */
    public function getPaymentIntent(string $paymentIntentId): object
    {
        return PaymentIntent::retrieve($paymentIntentId);
    }

    /**
     * Prepare line items from authenticated user cart
     */
    private function prepareLineItemsFromCart($cartItems): array
    {
        $lineItems = [];
        
        foreach ($cartItems as $item) {
            $product = $item->product;
            $finalPrice = $product->getPromotionalPrice();
            
            $lineItems[] = [
                'price_data' => [
                    'currency' => 'pln',
                    'product_data' => [
                        'name' => $product->name,
                        'description' => $product->description ?? '',
                    ],
                    'unit_amount' => (int) ($finalPrice * 100),
                ],
                'quantity' => $item->quantity,
            ];
        }
        
        return $lineItems;
    }

    /**
     * Prepare line items from guest cart data
     */
    private function prepareLineItemsFromGuestCart(array $cartData): array
    {
        $lineItems = [];
        
        foreach ($cartData as $item) {
            $cacheKey = "product_with_promotions_{$item['product_id']}";
            
            $product = Cache::remember($cacheKey, 600, function () use ($item) {
                return Product::with('activePromotions')->find($item['product_id']);
            });
            
            if ($product) {
                $finalPrice = $product->getPromotionalPrice();
                
                $lineItems[] = [
                    'price_data' => [
                        'currency' => 'pln',
                        'product_data' => [
                            'name' => $product->name,
                            'description' => $product->description ?? '',
                        ],
                        'unit_amount' => (int) ($finalPrice * 100),
                    ],
                    'quantity' => $item['quantity'],
                ];
            }
        }
        
        return $lineItems;
    }

    /**
     * Calculate total for authenticated user cart
     */
    private function calculateCartTotal($cartItems): float
    {
        return $cartItems->sum(function ($item) {
            return $item->product->getPromotionalPrice() * $item->quantity;
        });
    }

    /**
     * Calculate total for guest cart
     */
    private function calculateGuestCartTotal(array $cartData): float
    {
        $total = 0;

        foreach ($cartData as $item) {
            $cacheKey = "product_with_promotions_{$item['product_id']}";
            
            $product = Cache::remember($cacheKey, 600, function () use ($item) {
                return Product::with('activePromotions')->find($item['product_id']);
            });
            
            if ($product) {
                $total += $product->getPromotionalPrice() * $item['quantity'];
            }
        }

        return $total;
    }

    /**
     * Clear cache for payment service
     */
    public function clearCache(): void
    {
        Cache::forget('stripe_payment_methods');
        Cache::forget('stripe_secret_key');
    }

    /**
     * Clear product cache for specific product
     */
    public function clearProductCache(int $productId): void
    {
        $cacheKey = "product_with_promotions_{$productId}";
        Cache::forget($cacheKey);
    }

    /**
     * Clear all product cache
     */
    public function clearAllProductCache(): void
    {
        // Clear product cache by pattern - this requires Redis or custom implementation
        $cachePrefix = 'product_with_promotions_';
        
        // For Laravel's default cache driver, we'd need to implement this differently
        // This is a simple approach - in production you might want to use cache tags
        for ($i = 1; $i <= 1000; $i++) {
            Cache::forget($cachePrefix . $i);
        }
    }

    /**
     * Get cache statistics
     */
    public function getCacheStats(): array
    {
        return [
            'stripe_config_cached' => Cache::has('stripe_secret_key'),
            'payment_methods_cached' => Cache::has('stripe_payment_methods'),
            'cache_driver' => config('cache.default'),
        ];
    }
} 