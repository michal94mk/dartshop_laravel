<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\Checkout\Session;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\CartItem;
use App\Services\ShippingService;
use App\Enums\OrderStatus;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class StripeController extends Controller
{
    protected $shippingService;

    public function __construct(ShippingService $shippingService)
    {
        // Ustawienie klucza API Stripe
        $stripeSecret = config('services.stripe.secret');
        
        if (empty($stripeSecret)) {
            throw new \Exception('Stripe secret key is not configured. Please check your .env file.');
        }
        
        Stripe::setApiKey($stripeSecret);
        $this->shippingService = $shippingService;
    }

    /**
     * Sprawdź czy numer karty jest prawidłowy
     */
    private function validateCardNumber($cardNumber)
    {
        // Usuń spacje i myślniki
        $cardNumber = preg_replace('/[\s\-]/', '', $cardNumber);
        
        // Sprawdź długość (13-19 cyfr)
        if (!preg_match('/^[0-9]{13,19}$/', $cardNumber)) {
            return false;
        }
        
        // Algorytm Luhn (sprawdzenie sumy kontrolnej)
        $sum = 0;
        $length = strlen($cardNumber);
        $parity = $length % 2;
        
        for ($i = 0; $i < $length; $i++) {
            $digit = $cardNumber[$i];
            if ($i % 2 == $parity) {
                $digit *= 2;
                if ($digit > 9) {
                    $digit -= 9;
                }
            }
            $sum += $digit;
        }
        
        return ($sum % 10) == 0;
    }

    /**
     * Utworzenie Stripe Checkout Session dla zalogowanych użytkowników
     */
    public function createCheckoutSession(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'shipping.name' => 'required|string|max:255',
            'shipping.email' => 'required|email|max:255',
            'shipping.address' => 'required|string|max:255',
            'shipping.city' => 'required|string|max:255',
            'shipping.postalCode' => 'required|string|max:10',
            'shipping_method' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Nieprawidłowe dane wysyłki',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = Auth::user();
            $cartItems = CartItem::where('user_id', $user->id)
                ->with(['product.activePromotions'])
                ->get();

            if ($cartItems->isEmpty()) {
                return response()->json([
                    'message' => 'Koszyk jest pusty'
                ], 400);
            }

            // Przygotuj line items dla Stripe
            $lineItems = [];
            foreach ($cartItems as $item) {
                $product = $item->product;
                $finalPrice = $product->getPromotionalPrice(); // Użyj promocyjnej ceny
                
                $lineItems[] = [
                    'price_data' => [
                        'currency' => 'pln',
                        'product_data' => [
                            'name' => $product->name,
                            'description' => $product->description ?? '',
                        ],
                        'unit_amount' => (int) ($finalPrice * 100), // w groszach
                    ],
                    'quantity' => $item->quantity,
                ];
            }

            // Przygotuj metody płatności
            $paymentMethods = ['card', 'blik'];
            if (config('services.stripe.p24.enabled', true)) {
                $paymentMethods[] = 'p24';
            }

            // Utwórz Checkout Session
            $session = Session::create([
                'payment_method_types' => $paymentMethods,
                'line_items' => $lineItems,
                'mode' => 'payment',
                'success_url' => env('APP_FRONTEND_URL') . '/payment/success?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => env('APP_FRONTEND_URL') . '/checkout',
                'customer_email' => $request->shipping['email'],
                'metadata' => [
                    'user_id' => $user->id,
                    'shipping_data' => json_encode($request->shipping),
                    'shipping_method' => $request->shipping_method,
                ],
                'shipping_address_collection' => [
                    'allowed_countries' => ['PL'],
                ],
                // Dodatkowe ustawienia dla Przelewy24
                'payment_method_options' => [
                    'p24' => [
                        'tos_shown_and_accepted' => true,
                    ],
                ],
            ]);

            return response()->json([
                'checkout_url' => $session->url,
                'session_id' => $session->id
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Błąd podczas tworzenia sesji płatności: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Utworzenie Stripe Checkout Session dla gości
     */
    public function createGuestCheckoutSession(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cart_items' => 'required|array',
            'cart_items.*.product_id' => 'required|exists:products,id',
            'cart_items.*.quantity' => 'required|integer|min:1',
            'shipping.name' => 'required|string|max:255',
            'shipping.email' => 'required|email|max:255',
            'shipping.address' => 'required|string|max:255',
            'shipping.city' => 'required|string|max:255',
            'shipping.postalCode' => 'required|string|max:10',
            'shipping_method' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Nieprawidłowe dane',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $cartData = $request->cart_items;
            
            // Przygotuj line items dla Stripe
            $lineItems = [];
            foreach ($cartData as $item) {
                $product = Product::with('activePromotions')->find($item['product_id']);
                if ($product) {
                    $finalPrice = $product->getPromotionalPrice(); // Użyj promocyjnej ceny
                    
                    $lineItems[] = [
                        'price_data' => [
                            'currency' => 'pln',
                            'product_data' => [
                                'name' => $product->name,
                                'description' => $product->description ?? '',
                            ],
                            'unit_amount' => (int) ($finalPrice * 100), // w groszach
                        ],
                        'quantity' => $item['quantity'],
                    ];
                }
            }

            if (empty($lineItems)) {
                return response()->json([
                    'message' => 'Brak produktów w koszyku'
                ], 400);
            }

            // Przygotuj metody płatności
            $paymentMethods = ['card', 'blik'];
            if (config('services.stripe.p24.enabled', true)) {
                $paymentMethods[] = 'p24';
            }

            // Utwórz Checkout Session
            $session = Session::create([
                'payment_method_types' => $paymentMethods,
                'line_items' => $lineItems,
                'mode' => 'payment',
                'success_url' => env('APP_FRONTEND_URL') . '/payment/success?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => env('APP_FRONTEND_URL') . '/checkout',
                'customer_email' => $request->shipping['email'],
                'metadata' => [
                    'guest_order' => 'true',
                    'shipping_data' => json_encode($request->shipping),
                    'cart_data' => json_encode($cartData),
                    'shipping_method' => $request->shipping_method,
                ],
                'shipping_address_collection' => [
                    'allowed_countries' => ['PL'],
                ],
                // Dodatkowe ustawienia dla Przelewy24
                'payment_method_options' => [
                    'p24' => [
                        'tos_shown_and_accepted' => true,
                    ],
                ],
            ]);

            return response()->json([
                'checkout_url' => $session->url,
                'session_id' => $session->id
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Błąd podczas tworzenia sesji płatności: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Utworzenie Payment Intent dla zalogowanych użytkowników
     */
    public function createPaymentIntent(Request $request)
    {
        try {
            $user = Auth::user();
            $cartItems = CartItem::where('user_id', $user->id)
                ->with(['product.activePromotions'])
                ->get();

            if ($cartItems->isEmpty()) {
                return response()->json([
                    'message' => 'Koszyk jest pusty'
                ], 400);
            }

            // Oblicz sumę z promocyjnymi cenami
            $total = $cartItems->sum(function ($item) {
                return $item->product->getPromotionalPrice() * $item->quantity;
            });

            // Konwertuj na grosze (Stripe wymaga kwoty w najmniejszej jednostce waluty)
            $amountInCents = (int) ($total * 100);

            // Przygotuj metody płatności
            $paymentMethods = ['card', 'blik'];
            if (config('services.stripe.p24.enabled', true)) {
                $paymentMethods[] = 'p24';
            }

            // Utwórz Payment Intent
            $paymentIntent = PaymentIntent::create([
                'amount' => $amountInCents,
                'currency' => 'pln',
                'payment_method_types' => $paymentMethods,
                'metadata' => [
                    'user_id' => $user->id,
                    'cart_items_count' => $cartItems->count()
                ]
            ]);

            return response()->json([
                'client_secret' => $paymentIntent->client_secret,
                'payment_intent_id' => $paymentIntent->id,
                'amount' => $total
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Błąd podczas tworzenia płatności: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Utworzenie Payment Intent dla gości
     */
    public function createGuestPaymentIntent(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cart_items' => 'required|array',
            'cart_items.*.product_id' => 'required|exists:products,id',
            'cart_items.*.quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Nieprawidłowe dane koszyka',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $cartData = $request->cart_items;
            $total = 0;

            foreach ($cartData as $item) {
                $product = Product::with('activePromotions')->find($item['product_id']);
                if ($product) {
                    $total += $product->getPromotionalPrice() * $item['quantity'];
                }
            }

            if ($total <= 0) {
                return response()->json([
                    'message' => 'Nieprawidłowa suma zamówienia'
                ], 400);
            }

            // Konwertuj na grosze
            $amountInCents = (int) ($total * 100);

            // Przygotuj metody płatności
            $paymentMethods = ['card', 'blik'];
            if (config('services.stripe.p24.enabled', true)) {
                $paymentMethods[] = 'p24';
            }

            // Utwórz Payment Intent
            $paymentIntent = PaymentIntent::create([
                'amount' => $amountInCents,
                'currency' => 'pln',
                'payment_method_types' => $paymentMethods,
                'metadata' => [
                    'guest_order' => 'true',
                    'cart_items_count' => count($cartData)
                ]
            ]);

            return response()->json([
                'client_secret' => $paymentIntent->client_secret,
                'payment_intent_id' => $paymentIntent->id,
                'amount' => $total
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Błąd podczas tworzenia płatności: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Potwierdzenie płatności i utworzenie zamówienia dla zalogowanych użytkowników
     */
    public function confirmPayment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'payment_intent_id' => 'required|string',
            'shipping.name' => 'required|string|max:255',
            'shipping.email' => 'required|email|max:255',
            'shipping.address' => 'required|string|max:255',
            'shipping.city' => 'required|string|max:255',
            'shipping.postalCode' => 'required|string|max:10',
            'shipping_method' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Nieprawidłowe dane',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $user = Auth::user();
            $cartItems = CartItem::where('user_id', $user->id)
                ->with(['product.activePromotions'])
                ->get();

            if ($cartItems->isEmpty()) {
                return response()->json([
                    'message' => 'Koszyk jest pusty'
                ], 400);
            }

            // Sprawdź status płatności w Stripe
            $paymentIntent = PaymentIntent::retrieve($request->payment_intent_id);
            
            if ($paymentIntent->status !== 'succeeded') {
                return response()->json([
                    'message' => 'Płatność nie została potwierdzona'
                ], 400);
            }

            // Oblicz sumy z promocyjnymi cenami
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

            // Podziel imię i nazwisko
            $nameParts = explode(' ', $request->shipping['name'], 2);
            $firstName = $nameParts[0];
            $lastName = isset($nameParts[1]) ? $nameParts[1] : '';

            // Utwórz zamówienie
            $order = Order::create([
                'user_id' => $user->id,
                'status' => OrderStatus::Processing,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $request->shipping['email'],
                'address' => $request->shipping['address'],
                'city' => $request->shipping['city'],
                'postal_code' => $request->shipping['postalCode'],
                'country' => 'Polska',
                'subtotal' => $subtotal,
                'shipping_cost' => $shippingCost,
                'discount' => $discount,
                'total' => $total,
                'payment_method' => 'stripe',
                'payment_intent_id' => $request->payment_intent_id,
                'shipping_method' => $shippingMethod,
            ]);

            // Utwórz pozycje zamówienia z promocyjnymi cenami
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

            // Wyczyść koszyk
            CartItem::where('user_id', $user->id)->delete();

            DB::commit();

            return response()->json([
                'message' => 'Zamówienie zostało utworzone pomyślnie',
                'order' => $order->load('items')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Błąd podczas przetwarzania zamówienia: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Potwierdzenie płatności i utworzenie zamówienia dla gości
     */
    public function confirmGuestPayment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'payment_intent_id' => 'required|string',
            'shipping.name' => 'required|string|max:255',
            'shipping.email' => 'required|email|max:255',
            'shipping.address' => 'required|string|max:255',
            'shipping.city' => 'required|string|max:255',
            'shipping.postalCode' => 'required|string|max:10',
            'shipping_method' => 'required|string',
            'cart_items' => 'required|array',
            'cart_items.*.product_id' => 'required|exists:products,id',
            'cart_items.*.quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Nieprawidłowe dane',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Sprawdź status płatności w Stripe
            $paymentIntent = PaymentIntent::retrieve($request->payment_intent_id);
            
            if ($paymentIntent->status !== 'succeeded') {
                return response()->json([
                    'message' => 'Płatność nie została potwierdzona'
                ], 400);
            }

            // Pobierz produkty i oblicz sumy
            $cartData = $request->cart_items;
            $cartItems = [];
            $subtotal = 0;

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

                $subtotal += $promotionalPrice * $item['quantity'];
            }

            // Validate and calculate shipping cost
            $shippingMethod = $request->input('shipping_method');
            if (!$this->shippingService->isValidMethod($shippingMethod)) {
                throw new \Exception('Nieprawidłowa metoda wysyłki');
            }

            $shippingCost = $this->shippingService->calculateShippingCost($shippingMethod, $subtotal);
            $discount = 0;
            $total = $subtotal + $shippingCost - $discount;

            // Podziel imię i nazwisko
            $nameParts = explode(' ', $request->shipping['name'], 2);
            $firstName = $nameParts[0];
            $lastName = isset($nameParts[1]) ? $nameParts[1] : '';

            // Utwórz zamówienie
            $order = Order::create([
                'user_id' => null,
                'status' => OrderStatus::Processing,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $request->shipping['email'],
                'address' => $request->shipping['address'],
                'city' => $request->shipping['city'],
                'postal_code' => $request->shipping['postalCode'],
                'country' => 'Polska',
                'subtotal' => $subtotal,
                'shipping_cost' => $shippingCost,
                'discount' => $discount,
                'total' => $total,
                'payment_method' => 'stripe',
                'payment_intent_id' => $request->payment_intent_id,
                'shipping_method' => $shippingMethod,
            ]);

            // Utwórz pozycje zamówienia
            foreach ($cartItems as $cartItem) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $cartItem['product']->id,
                    'product_name' => $cartItem['product']->name,
                    'quantity' => $cartItem['quantity'],
                    'product_price' => $cartItem['price'],
                    'total_price' => $cartItem['total'],
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Zamówienie zostało utworzone pomyślnie',
                'order' => $order->load('items')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Błąd podczas przetwarzania zamówienia: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Sprawdź status płatności
     */
    public function checkPaymentStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'payment_intent_id' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Nieprawidłowe dane',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $paymentIntent = PaymentIntent::retrieve($request->payment_intent_id);
            
            return response()->json([
                'payment_intent_id' => $paymentIntent->id,
                'status' => $paymentIntent->status,
                'amount' => $paymentIntent->amount,
                'currency' => $paymentIntent->currency,
                'payment_method' => $paymentIntent->payment_method_types[0] ?? 'unknown',
                'last_payment_error' => $paymentIntent->last_payment_error ?? null,
                'created' => $paymentIntent->created,
                'updated' => $paymentIntent->updated
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Błąd podczas sprawdzania statusu płatności: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test walidacji numeru karty
     */
    public function testCardValidation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'card_number' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Nieprawidłowe dane',
                'errors' => $validator->errors()
            ], 422);
        }

        $cardNumber = $request->card_number;
        $isValid = $this->validateCardNumber($cardNumber);

        return response()->json([
            'card_number' => $cardNumber,
            'is_valid' => $isValid,
            'message' => $isValid ? 'Numer karty jest prawidłowy' : 'Numer karty jest nieprawidłowy',
            'test_cards' => [
                'visa' => '4242424242424242',
                'mastercard' => '5555555555554444',
                'amex' => '378282246310005'
            ]
        ]);
    }

    /**
     * Obsługa powrotu z Stripe Checkout - sukces płatności
     */
    public function handleCheckoutSuccess(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'session_id' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Brak ID sesji',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Pobierz sesję z Stripe
            $session = Session::retrieve($request->session_id);

            if ($session->payment_status !== 'paid') {
                return response()->json([
                    'message' => 'Płatność nie została potwierdzona'
                ], 400);
            }

            // Sprawdź czy zamówienie już istnieje
            $existingOrder = Order::where('payment_intent_id', $session->payment_intent)
                ->orWhere('stripe_session_id', $session->id)
                ->first();
            if ($existingOrder) {
                return response()->json([
                    'message' => 'Zamówienie już istnieje',
                    'order' => $existingOrder->load('items')
                ]);
            }

            // Pobierz dane z metadata
            $metadata = $session->metadata;
            $shippingData = json_decode($metadata['shipping_data'], true);
            $shippingMethod = $metadata['shipping_method'] ?? 'courier';

            if (isset($metadata['user_id'])) {
                // Zamówienie dla zalogowanego użytkownika
                $user = \App\Models\User::find($metadata['user_id']);
                if (!$user) {
                    throw new \Exception('Użytkownik nie istnieje');
                }

                $cartItems = CartItem::where('user_id', $user->id)
                    ->with(['product.activePromotions'])
                    ->get();

                if ($cartItems->isEmpty()) {
                    throw new \Exception('Koszyk jest pusty');
                }

                // Oblicz sumy z promocyjnymi cenami
                $subtotal = $cartItems->sum(function ($item) {
                    return $item->product->getPromotionalPrice() * $item->quantity;
                });

                // Calculate shipping cost
                $shippingCost = $this->shippingService->calculateShippingCost($shippingMethod, $subtotal);
                $discount = 0;
                $total = $subtotal + $shippingCost - $discount;

                // Podziel imię i nazwisko
                $nameParts = explode(' ', $shippingData['name'], 2);
                $firstName = $nameParts[0];
                $lastName = isset($nameParts[1]) ? $nameParts[1] : '';

                // Utwórz zamówienie
                $order = Order::create([
                    'user_id' => $user->id,
                    'status' => OrderStatus::Processing,
                    'first_name' => $firstName,
                    'last_name' => $lastName,
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
                    'payment_intent_id' => $session->payment_intent,
                    'stripe_session_id' => $session->id,
                    'shipping_method' => $shippingMethod,
                    'payment_status' => 'paid'
                ]);

                // Utwórz pozycje zamówienia z promocyjnymi cenami
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

                // Wyczyść koszyk
                CartItem::where('user_id', $user->id)->delete();

            } else {
                // Zamówienie dla gościa
                $cartData = json_decode($metadata['cart_data'], true);
                $cartItems = [];
                $subtotal = 0;

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

                    $subtotal += $promotionalPrice * $item['quantity'];
                }

                // Calculate shipping cost
                $shippingCost = $this->shippingService->calculateShippingCost($shippingMethod, $subtotal);
                $discount = 0;
                $total = $subtotal + $shippingCost - $discount;

                // Podziel imię i nazwisko
                $nameParts = explode(' ', $shippingData['name'], 2);
                $firstName = $nameParts[0];
                $lastName = isset($nameParts[1]) ? $nameParts[1] : '';

                // Utwórz zamówienie
                $order = Order::create([
                    'user_id' => null,
                    'status' => OrderStatus::Processing,
                    'first_name' => $firstName,
                    'last_name' => $lastName,
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
                    'payment_intent_id' => $session->payment_intent,
                    'stripe_session_id' => $session->id,
                    'shipping_method' => $shippingMethod,
                    'payment_status' => 'paid'
                ]);

                // Utwórz pozycje zamówienia
                foreach ($cartItems as $cartItem) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $cartItem['product']->id,
                        'product_name' => $cartItem['product']->name,
                        'quantity' => $cartItem['quantity'],
                        'product_price' => $cartItem['price'],
                        'total_price' => $cartItem['total'],
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'message' => 'Zamówienie zostało utworzone pomyślnie',
                'order' => $order->load('items')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Błąd podczas przetwarzania zamówienia: ' . $e->getMessage()
            ], 500);
        }
    }
}
