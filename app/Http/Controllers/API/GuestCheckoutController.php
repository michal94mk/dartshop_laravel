<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Frontend\CheckoutRequest;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\ShippingService;
use App\Services\CartService;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;

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

    /**
     * Handle guest checkout and create an order for a guest user.
     *
     * @param CheckoutRequest $request
     * @return JsonResponse
     */
    public function __invoke(CheckoutRequest $request): JsonResponse
    {
        $this->logApiRequest($request, 'Process guest checkout');

        $validated = $request->validated();

        $cartItems = $this->cartService->getGuestCartItems();

        if (empty($cartItems)) {
            return $this->errorResponse('Cart is empty', 400);
        }

        // Start transaction for guest order creation
        return DB::transaction(function () use ($validated, $cartItems) {
            $shippingData = $validated['shipping_address'];

            // Calculate order costs
            $subtotal = collect($cartItems)->sum(function ($item) {
                return $item['quantity'] * $item['product']->getPromotionalPrice();
            });

            $shippingCost = $validated['shipping_method'] === 'express' ? 20.00 : 15.00;
            $discount = 0;
            $total = $subtotal + $shippingCost - $discount;

            // Create guest order
            $order = Order::create([
                'user_id' => null,
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
                'country' => 'PL'
            ]);

            // Add products to the order
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

            $this->cartService->clearGuestCart();

            $order->load('items');
            return $this->createdResponse([
                'order' => $order
            ], 'Order created successfully');
        });
    }
} 