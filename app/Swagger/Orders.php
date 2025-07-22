<?php

namespace App\Swagger;

/**
 * @OA\Get(
 *     path="/api/orders/my-orders",
 *     summary="Get user orders",
 *     description="Retrieve all orders for the authenticated user",
 *     tags={"Orders"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="User orders retrieved successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Order"))
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthorized",
 *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
 *     )
 * )
 *
 * @OA\Get(
 *     path="/api/orders/my-orders/{id}",
 *     summary="Get specific user order",
 *     description="Retrieve details of a specific order for the authenticated user",
 *     tags={"Orders"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="Order ID",
 *         required=true,
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Order details retrieved successfully",
 *         @OA\JsonContent(ref="#/components/schemas/OrderDetailed")
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthorized",
 *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Order not found",
 *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
 *     )
 * )
 *
 * @OA\Post(
 *     path="/api/checkout",
 *     summary="Create order (checkout)",
 *     description="Create a new order from cart items for authenticated user",
 *     tags={"Orders"},
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="shipping_address", type="object",
 *                 @OA\Property(property="first_name", type="string", example="John"),
 *                 @OA\Property(property="last_name", type="string", example="Doe"),
 *                 @OA\Property(property="company", type="string", nullable=true, example="Acme Corp"),
 *                 @OA\Property(property="address_line_1", type="string", example="123 Main St"),
 *                 @OA\Property(property="address_line_2", type="string", nullable=true, example="Apt 4B"),
 *                 @OA\Property(property="city", type="string", example="New York"),
 *                 @OA\Property(property="state", type="string", example="NY"),
 *                 @OA\Property(property="postal_code", type="string", example="10001"),
 *                 @OA\Property(property="country", type="string", example="US"),
 *                 @OA\Property(property="phone", type="string", nullable=true, example="+1234567890")
 *             ),
 *             @OA\Property(property="shipping_method", type="string", example="standard"),
 *             @OA\Property(property="notes", type="string", nullable=true, example="Please handle with care")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Order created successfully",
 *         @OA\JsonContent(ref="#/components/schemas/OrderDetailed")
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Bad request - Empty cart or other validation error",
 *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthorized",
 *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation error",
 *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
 *     )
 * )
 *
 * @OA\Post(
 *     path="/api/guest-checkout",
 *     summary="Guest checkout",
 *     description="Create an order for guest users (without authentication)",
 *     tags={"Orders"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="email", type="string", format="email", example="guest@example.com"),
 *             @OA\Property(property="items", type="array", @OA\Items(
 *                 @OA\Property(property="product_id", type="integer", example=1),
 *                 @OA\Property(property="quantity", type="integer", example=2)
 *             )),
 *             @OA\Property(property="shipping_address", type="object",
 *                 @OA\Property(property="first_name", type="string", example="Jane"),
 *                 @OA\Property(property="last_name", type="string", example="Smith"),
 *                 @OA\Property(property="address_line_1", type="string", example="456 Oak Ave"),
 *                 @OA\Property(property="city", type="string", example="Los Angeles"),
 *                 @OA\Property(property="state", type="string", example="CA"),
 *                 @OA\Property(property="postal_code", type="string", example="90210"),
 *                 @OA\Property(property="country", type="string", example="US")
 *             ),
 *             @OA\Property(property="shipping_method", type="string", example="express")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Guest order created successfully",
 *         @OA\JsonContent(ref="#/components/schemas/OrderDetailed")
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation error",
 *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
 *     )
 * )
 *
 * @OA\Get(
 *     path="/api/orders/{order}",
 *     summary="Get order details (public)",
 *     description="Retrieve order details for success/confirmation page",
 *     tags={"Orders"},
 *     @OA\Parameter(
 *         name="order",
 *         in="path",
 *         description="Order ID",
 *         required=true,
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Order details retrieved successfully",
 *         @OA\JsonContent(ref="#/components/schemas/OrderDetailed")
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Order not found",
 *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
 *     )
 * )
 *
 * @OA\Get(
 *     path="/api/shipping-methods",
 *     summary="Get shipping methods",
 *     description="Retrieve available shipping methods with costs",
 *     tags={"Orders"},
 *     @OA\Parameter(
 *         name="cart_total",
 *         in="query",
 *         description="Cart total amount for shipping calculation",
 *         required=true,
 *         @OA\Schema(type="number", format="float", example=89.99)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Shipping methods retrieved successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="methods", type="array", @OA\Items(
 *                 @OA\Property(property="id", type="string", example="standard"),
 *                 @OA\Property(property="name", type="string", example="Standard Shipping"),
 *                 @OA\Property(property="description", type="string", example="5-7 business days"),
 *                 @OA\Property(property="cost", type="number", format="float", example=9.99),
 *                 @OA\Property(property="estimated_days", type="integer", example=7)
 *             )),
 *             @OA\Property(property="free_shipping_threshold", type="number", format="float", example=100.00)
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation error",
 *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
 *     )
 * )
 *
 * @OA\Post(
 *     path="/api/stripe/create-checkout-session",
 *     summary="Create Stripe checkout session",
 *     description="Create a Stripe checkout session for payment processing",
 *     tags={"Payment"},
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="order_id", type="integer", example=1),
 *             @OA\Property(property="success_url", type="string", example="https://example.com/success"),
 *             @OA\Property(property="cancel_url", type="string", example="https://example.com/cancel")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Checkout session created successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="checkout_url", type="string", example="https://checkout.stripe.com/pay/..."),
 *             @OA\Property(property="session_id", type="string", example="cs_123456789")
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthorized",
 *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Order not found",
 *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
 *     )
 * )
 *
 * @OA\Post(
 *     path="/api/stripe/webhook",
 *     summary="Stripe webhook handler",
 *     description="Handle Stripe webhook events for payment processing",
 *     tags={"Payment"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="type", type="string", example="checkout.session.completed"),
 *             @OA\Property(property="data", type="object")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Webhook processed successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="received", type="boolean", example=true)
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Invalid webhook data",
 *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
 *     )
 * )
 */
class Orders
{
    // This class serves as a container for order and payment endpoint annotations
} 