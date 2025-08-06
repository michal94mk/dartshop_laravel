<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseApiController;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\CartService;
use App\Services\ReservationService;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Http\Requests\Frontend\CheckoutRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use Exception;

class CheckoutController extends BaseApiController
{
    protected $cartService;
    protected $reservationService;

    public function __construct(
        CartService $cartService,
        ReservationService $reservationService
    ) {
        $this->cartService = $cartService;
        $this->reservationService = $reservationService;
    }

    /**
     * Handle checkout and create an order for the authenticated user.
     *
     * @param CheckoutRequest $request
     * @return JsonResponse
     */
    public function store(CheckoutRequest $request): JsonResponse
    {
        try {
            $this->logApiRequest($request, 'Process checkout');

            // Get validated data
            $validated = $request->validated();

            // Pobierz koszyk
            $cartItems = $this->cartService->getCartItems();

            if ($cartItems->isEmpty()) {
                return $this->errorResponse('Koszyk jest pusty', 400);
            }

            // Rozpocznij transakcję
            return DB::transaction(function () use ($validated, $cartItems) {
                try {
                    // Zarezerwuj produkty
                    foreach ($cartItems as $item) {
                        if (!$this->reservationService->reserveProduct($item->product, $item->quantity)) {
                            throw new Exception("Produkt {$item->product->name} jest niedostępny w żądanej ilości.");
                        }
                    }

                    // Przygotuj dane adresowe
                    $shippingData = $validated['shipping_address'];

                    // Oblicz koszty
                    $subtotal = (float) $cartItems->sum(function ($item) {
                        return $item->quantity * $item->product->getPromotionalPrice();
                    });

                    $shippingCost = (float) ($validated['shipping_method'] === 'express' ? 20.00 : 15.00);
                    $total = (float) ($subtotal + $shippingCost);

                    // Utwórz zamówienie
                    $order = Order::create([
                        'user_id' => Auth::id(),
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
                        'total' => $total,
                        'payment_method' => $validated['payment_method'],
                        'shipping_method' => $validated['shipping_method'],
                        'country' => 'PL' // Domyślnie Polska
                    ]);

                    // Dodaj produkty do zamówienia
                    foreach ($cartItems as $item) {
                        OrderItem::create([
                            'order_id' => $order->id,
                            'product_id' => $item->product_id,
                            'product_name' => $item->product->name,
                            'quantity' => $item->quantity,
                            'product_price' => $item->product->getPromotionalPrice(),
                            'total_price' => $item->product->getPromotionalPrice() * $item->quantity
                        ]);
                    }

                    // Wyczyść koszyk
                    $this->cartService->clearCart();

                    // Zwróć utworzone zamówienie
                    return $this->createdResponse([
                        'order' => $order->load('items')
                    ], 'Zamówienie zostało utworzone pomyślnie');

                } catch (Exception $e) {
                    return $this->handleException($e, 'Creating order in transaction');
                }
            });
        } catch (Exception $e) {
            return $this->handleException($e, 'Processing checkout');
        }
    }

    /**
     * Show order details for a given order ID.
     *
     * @param string|int $orderId
     * @return JsonResponse
     */
    public function showOrder($orderId): JsonResponse
    {
        try {
            $this->logApiRequest(request(), "Fetch order details for ID: {$orderId}");
            
            $order = Order::with(['items', 'items.product'])->findOrFail($orderId);

            return $this->successResponse(['order' => $order], 'Order details fetched successfully');
        } catch (Exception $e) {
            return $this->handleException($e, "Fetching order details for ID: {$orderId}");
        }
    }
} 