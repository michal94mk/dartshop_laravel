<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\CartService;
use App\Services\ReservationService;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CheckoutController extends Controller
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
     * Create a new order
     */
    public function store(Request $request)
    {
        try {
            Log::info('Rozpoczęcie procesu checkoutu', [
                'user_id' => Auth::id(),
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
            $cartItems = $this->cartService->getCartItems();
            Log::info('Pobrano przedmioty z koszyka', [
                'cart_items_count' => $cartItems->count(),
                'cart_items' => $cartItems->toArray()
            ]);

            if ($cartItems->isEmpty()) {
                Log::warning('Próba utworzenia zamówienia z pustym koszykiem');
                return response()->json(['message' => 'Koszyk jest pusty'], 400);
            }

            // Rozpocznij transakcję
            return DB::transaction(function () use ($validated, $cartItems) {
                try {
                    // Zarezerwuj produkty
                    foreach ($cartItems as $item) {
                        Log::info('Próba rezerwacji produktu', [
                            'product_id' => $item->product_id,
                            'quantity' => $item->quantity
                        ]);

                        if (!$this->reservationService->reserveProduct($item->product, $item->quantity)) {
                            throw new \Exception("Produkt {$item->product->name} jest niedostępny w żądanej ilości.");
                        }
                    }

                    // Przygotuj dane adresowe
                    $shippingData = $validated['shipping_address'];

                    // Oblicz koszty
                    $subtotal = (float) $cartItems->sum(function ($item) {
                        return $item->quantity * $item->product->getPromotionalPrice();
                    });

                    Log::info('Obliczono koszty', [
                        'subtotal' => $subtotal,
                        'shipping_method' => $validated['shipping_method']
                    ]);

                    $shippingCost = (float) ($validated['shipping_method'] === 'express' ? 20.00 : 15.00);
                    $total = (float) ($subtotal + $shippingCost);

                    Log::info('Tworzenie zamówienia', [
                        'shipping_data' => $shippingData,
                        'total' => $total,
                        'subtotal' => $subtotal,
                        'shipping_cost' => $shippingCost
                    ]);

                    // Utwórz zamówienie
                    $order = Order::create([
                        'user_id' => Auth::id(),
                        'order_number' => $this->generateOrderNumber(),
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

                    Log::info('Zamówienie utworzone', [
                        'order_id' => $order->id
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

                    Log::info('Dodano produkty do zamówienia', [
                        'order_id' => $order->id,
                        'items_count' => $cartItems->count()
                    ]);

                    // Wyczyść koszyk
                    $this->cartService->clearCart();

                    Log::info('Zamówienie zakończone sukcesem', [
                        'order_id' => $order->id
                    ]);

                    // Zwróć utworzone zamówienie
                    return response()->json([
                        'message' => 'Zamówienie zostało utworzone',
                        'order' => $order->load('items')
                    ], 201);

                } catch (\Exception $e) {
                    Log::error('Błąd w transakcji DB podczas tworzenia zamówienia', [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    throw $e;
                }
            });
        } catch (\Exception $e) {
            Log::error('Błąd podczas tworzenia zamówienia', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'message' => 'Wystąpił błąd podczas tworzenia zamówienia',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show order details
     *
     * @param string|int $orderId
     * @return \Illuminate\Http\JsonResponse
     */
    public function showOrder($orderId)
    {
        $order = Order::with(['items', 'items.product'])->findOrFail($orderId);

        return response()->json([
            'success' => true,
            'order' => $order
        ]);
    }

    /**
     * Generate a unique order number.
     */
    private function generateOrderNumber(): string
    {
        return 'ZAM-' . strtoupper(Str::random(8));
    }
} 