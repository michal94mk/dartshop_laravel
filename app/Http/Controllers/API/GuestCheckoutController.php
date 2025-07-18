<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Services\ShippingService;
use App\Services\CartService;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GuestCheckoutController extends Controller
{
    protected $shippingService;
    protected $cartService;

    public function __construct(
        ShippingService $shippingService,
        CartService $cartService
    ) {
        $this->shippingService = $shippingService;
        $this->cartService = $cartService;
    }

    /**
     * Process guest checkout
     */
    public function __invoke(Request $request)
    {
        try {
            Log::info('Rozpoczęcie procesu checkoutu dla gościa', [
                'request_data' => $request->all()
            ]);

            // Walidacja danych
            $validated = $request->validate([
                'shipping_address.first_name' => 'required|string|max:255',
                'shipping_address.last_name' => 'required|string|max:255',
                'shipping_address.email' => 'required|email|max:255',
                'shipping_address.street' => 'required|string|max:255',
                'shipping_address.city' => 'required|string|max:255',
                'shipping_address.postal_code' => 'required|string|max:10|regex:/^[0-9]{2}-[0-9]{3}$/',
                'shipping_address.phone' => 'nullable|string|max:20',
                'payment_method' => 'required|string|in:stripe,cod',
                'shipping_method' => 'required|string|in:courier,inpost,pickup,express',
                'notes' => 'nullable|string'
            ]);

            Log::info('Walidacja danych przeszła pomyślnie', [
                'validated_data' => $validated
            ]);

            // Pobierz koszyk
            $cartItems = $this->cartService->getGuestCartItems();
            Log::info('Pobrano przedmioty z koszyka gościa', [
                'cart_items_count' => count($cartItems),
                'cart_items' => $cartItems
            ]);

            if (empty($cartItems)) {
                Log::warning('Próba utworzenia zamówienia z pustym koszykiem');
                return response()->json(['message' => 'Koszyk jest pusty'], 400);
            }

            // Rozpocznij transakcję
            return DB::transaction(function () use ($validated, $cartItems) {
                try {
                    // Przygotuj dane adresowe
                    $shippingData = $validated['shipping_address'];

                    // Oblicz koszty
                    $subtotal = collect($cartItems)->sum(function ($item) {
                        return $item['quantity'] * $item['product']->getPromotionalPrice();
                    });

                    Log::info('Obliczono koszty', [
                        'subtotal' => $subtotal,
                        'shipping_method' => $validated['shipping_method']
                    ]);

                    $shippingCost = $validated['shipping_method'] === 'express' ? 20.00 : 15.00;
                    $discount = 0;
                    $total = $subtotal + $shippingCost - $discount;

                    Log::info('Tworzenie zamówienia dla gościa', [
                        'shipping_data' => $shippingData,
                        'total' => $total,
                        'subtotal' => $subtotal,
                        'shipping_cost' => $shippingCost,
                        'discount' => $discount
                    ]);

                    // Utwórz zamówienie
                    $order = Order::create([
                        'user_id' => null, // Zamówienie gościa
                        'order_number' => Order::generateOrderNumber(),
                        'status' => OrderStatus::Pending,
                        'payment_status' => PaymentStatus::Pending,
                        'first_name' => $shippingData['first_name'],
                        'last_name' => $shippingData['last_name'],
                        'email' => $shippingData['email'],
                        'phone' => $shippingData['phone'] ?? null,
                        'address' => $shippingData['street'],
                        'city' => $shippingData['city'],
                        'postal_code' => $shippingData['postal_code'],
                        'notes' => $validated['notes'] ?? null,
                        'subtotal' => $subtotal,
                        'shipping_cost' => $shippingCost,
                        'discount' => $discount,
                        'total' => $total,
                        'payment_method' => $validated['payment_method'],
                        'shipping_method' => $validated['shipping_method'],
                        'country' => 'PL' // Domyślnie Polska
                    ]);

                    Log::info('Zamówienie utworzone', [
                        'order_id' => $order->id
                    ]);

                    // Dodaj produkty do zamówienia
                    foreach ($cartItems as $item) {
                        OrderItem::create([
                            'order_id' => $order->id,
                            'product_id' => $item['product']->id,
                            'product_name' => $item['product']->name,
                            'quantity' => $item['quantity'],
                            'product_price' => $item['product']->getPromotionalPrice(),
                            'total_price' => $item['product']->getPromotionalPrice() * $item['quantity']
                        ]);
                    }

                    Log::info('Dodano produkty do zamówienia', [
                        'order_id' => $order->id,
                        'items_count' => count($cartItems)
                    ]);

                    // Wyczyść koszyk gościa
                    $this->cartService->clearGuestCart();

                    Log::info('Zamówienie gościa zakończone sukcesem', [
                        'order_id' => $order->id
                    ]);

                    // Zwróć utworzone zamówienie
                    return response()->json([
                        'message' => 'Zamówienie zostało utworzone',
                        'order' => $order->load('items')
                    ], 201);

                } catch (\Exception $e) {
                    Log::error('Błąd w transakcji DB podczas tworzenia zamówienia gościa', [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    throw $e;
                }
            });

        } catch (\Exception $e) {
            Log::error('Błąd podczas przetwarzania zamówienia gościa', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'Wystąpił błąd podczas tworzenia zamówienia',
                'error' => $e->getMessage()
            ], 500);
        }
    }


} 