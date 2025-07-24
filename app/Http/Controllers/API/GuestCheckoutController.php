<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseApiController;
use Illuminate\Http\Request;
use App\Http\Requests\Frontend\CheckoutRequest;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Services\ShippingService;
use App\Services\CartService;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;
use Exception;

class GuestCheckoutController extends BaseApiController
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

    public function __invoke(CheckoutRequest $request): JsonResponse
    {
        try {
            $this->logApiRequest($request, 'Process guest checkout');

            // Get validated data
            $validated = $request->validated();

            // Pobierz koszyk
            $cartItems = $this->cartService->getGuestCartItems();

            if (empty($cartItems)) {
                return $this->errorResponse('Koszyk jest pusty', 400);
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

                    $shippingCost = $validated['shipping_method'] === 'express' ? 20.00 : 15.00;
                    $discount = 0;
                    $total = $subtotal + $shippingCost - $discount;

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

                    // Wyczyść koszyk gościa
                    $this->cartService->clearGuestCart();

                    // Zwróć utworzone zamówienie
                    $order->load('items');
                    return $this->createdResponse([
                        'order' => $order
                    ], 'Zamówienie zostało utworzone pomyślnie');

                } catch (Exception $e) {
                    return $this->handleException($e, 'Creating guest order in transaction');
                }
            });

        } catch (Exception $e) {
            return $this->handleException($e, 'Processing guest checkout');
        }
    }
} 