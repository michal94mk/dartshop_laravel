<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseApiController;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\CartService;
use App\Services\ReservationService;
use App\Services\ShippingService;
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
    protected $shippingService;

    public function __construct(
        CartService $cartService,
        ReservationService $reservationService,
        ShippingService $shippingService
    ) {
        $this->cartService = $cartService;
        $this->reservationService = $reservationService;
        $this->shippingService = $shippingService;
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

            // Get cart items
            $cartItems = $this->cartService->getCartItems();

            if ($cartItems->isEmpty()) {
                return $this->errorResponse('Cart is empty', 400);
            }

            // Start database transaction
            return DB::transaction(function () use ($validated, $cartItems) {
                try {
                    // Reserve products
                    foreach ($cartItems as $item) {
                        if (!$this->reservationService->reserveProduct($item->product, $item->quantity)) {
                            throw new Exception("Product {$item->product->name} is not available in the requested quantity.");
                        }
                    }

                    // Prepare shipping data
                    $shippingData = $validated['shipping_address'];

                    // Calculate costs
                    $subtotal = (float) $cartItems->sum(function ($item) {
                        $price = $item->product->getPromotionalPrice();
                        \Illuminate\Support\Facades\Log::info('Cart item calculation', [
                            'product' => $item->product->name,
                            'quantity' => $item->quantity,
                            'price' => $price,
                            'total' => $item->quantity * $price
                        ]);
                        return $item->quantity * $price;
                    });

                    \Illuminate\Support\Facades\Log::info('Order calculation', [
                        'subtotal' => $subtotal,
                        'cart_items_count' => $cartItems->count()
                    ]);

                    // Calculate shipping cost using ShippingService
                    $shippingMethod = $validated['shipping_method'];
                    $originalShippingCost = $this->shippingService->getShippingMethod($shippingMethod)['cost'] ?? 0.00;
                    $shippingCost = $this->shippingService->calculateShippingCost($shippingMethod, $subtotal);
                    $discount = $originalShippingCost - $shippingCost;
                    $total = (float) ($subtotal + $shippingCost);

                    // Create order
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
                        'discount' => $discount,
                        'total' => $total,
                        'payment_method' => $validated['payment_method'],
                        'shipping_method' => $validated['shipping_method'],
                        'country' => 'PL' // Default to Poland
                    ]);

                    // Add products to order
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

                    // Clear cart
                    $this->cartService->clearCart();

                    // Return created order
                    return $this->createdResponse([
                        'order' => $order->load('items')
                    ], 'Order created successfully');

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