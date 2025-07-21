<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
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

/**
 * @OA\Tag(
 *     name="Guest Checkout",
 *     description="API Endpoints for guest checkout process"
 * )
 */

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
     *
     * @OA\Post(
     *     path="/api/guest-checkout",
     *     summary="Process guest checkout",
     *     description="Process checkout for non-authenticated users",
     *     tags={"Guest Checkout"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"shipping_address","shipping_method","payment_method"},
     *             @OA\Property(property="shipping_address", type="object",
     *                 @OA\Property(property="first_name", type="string", example="John"),
     *                 @OA\Property(property="last_name", type="string", example="Doe"),
     *                 @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *                 @OA\Property(property="phone", type="string", example="+48123456789"),
     *                 @OA\Property(property="street", type="string", example="ul. Przykładowa 1"),
     *                 @OA\Property(property="city", type="string", example="Warszawa"),
     *                 @OA\Property(property="postal_code", type="string", example="00-001"),
     *                 @OA\Property(property="country", type="string", example="Polska")
     *             ),
     *             @OA\Property(property="shipping_method", type="string", enum={"standard", "express"}, example="standard"),
     *             @OA\Property(property="payment_method", type="string", enum={"stripe", "bank_transfer"}, example="stripe"),
     *             @OA\Property(property="notes", type="string", nullable=true, example="Dostawa do biura")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Order created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Order created successfully"),
     *             @OA\Property(property="order", ref="#/components/schemas/Order"),
     *             @OA\Property(property="payment_url", type="string", nullable=true, example="https://checkout.stripe.com/...")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request - Empty cart or product unavailable",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     )
     * )
     */
    public function __invoke(CheckoutRequest $request)
    {
        try {
            Log::info('Rozpoczęcie procesu checkoutu dla gościa', [
                'request_data' => $request->all()
            ]);

            // Get validated data
            $validated = $request->validated();

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