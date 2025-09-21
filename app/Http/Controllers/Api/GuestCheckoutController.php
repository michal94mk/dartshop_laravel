<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Frontend\CheckoutRequest;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
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

        // Try to get cart from session first
        $cartItems = $this->cartService->getGuestCartItems()->toArray();

        // Fallback 1: If session cart is empty, try to get cart from request
        if (empty($cartItems) && $request->has('cart_items')) {
            
            $requestCartItems = $request->input('cart_items', []);
            if (!empty($requestCartItems)) {
                $productIds = collect($requestCartItems)->pluck('product_id');
                $products = \App\Models\Product::with('brand')->whereIn('id', $productIds)->get();
                
                $cartItems = collect($requestCartItems)->map(function ($item) use ($products) {
                    $product = $products->firstWhere('id', $item['product_id']);
                    if (!$product) {
                        return null;
                    }
                    return [
                        'product_id' => $item['product_id'],
                        'quantity' => $item['quantity'],
                        'product' => $product
                    ];
                })->filter()->toArray();
            }
        }

        // If still no cart items, return error
        if (empty($cartItems)) {
            return $this->errorResponse('Cart is empty. Please add items to cart before checkout.', 400);
        }

        // Start transaction for guest order creation
        return DB::transaction(function () use ($validated, $cartItems) {
            $shippingData = $validated['shipping_address'];

            // Calculate order costs
            $subtotal = collect($cartItems)->sum(function ($item) {
                // Handle both array and object formats
                $product = is_array($item) ? $item['product'] : $item->product;
                $quantity = is_array($item) ? $item['quantity'] : $item->quantity;
                
                $price = $product->getPromotionalPrice();
                $itemTotal = $quantity * $price;
                
                return $itemTotal;
            });

            // If subtotal is still 0, there's a product pricing issue
            if ($subtotal == 0) {
                return $this->errorResponse('Order total cannot be zero. Please check product prices.', 400);
            }

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
                // Handle both array and object formats
                $product = is_array($item) ? $item['product'] : $item->product;
                $quantity = is_array($item) ? $item['quantity'] : $item->quantity;
                
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'quantity' => $quantity,
                    'product_price' => $product->getPromotionalPrice(),
                    'total_price' => $product->getPromotionalPrice() * $quantity
                ]);
            }

            // Only clear cart if it came from session
            if (session()->has('cart')) {
                $this->cartService->clearGuestCart();
            }

            $order->load('items');

            return $this->createdResponse([
                'order' => $order
            ], 'Order created successfully');
        });
    }
}