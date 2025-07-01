<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Services\ShippingService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class GuestCheckoutController extends Controller
{
    protected $shippingService;

    public function __construct(ShippingService $shippingService)
    {
        $this->shippingService = $shippingService;
    }

    /**
     * Get cart data for guest checkout
     */
    public function index(Request $request)
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
            $cartItems = [];

            foreach ($cartData as $item) {
                $product = Product::with('activePromotions')->find($item['product_id']);
                if ($product) {
                    // Add promotional price information
                    if ($product->hasActivePromotion()) {
                        $product->promotion_price = $product->getPromotionalPrice();
                        $product->savings = $product->getSavingsAmount();
                        $product->promotion = $product->getBestActivePromotion();
                    } else {
                        $product->promotion_price = $product->price;
                        $product->savings = 0;
                        $product->promotion = null;
                    }
                    
                    $cartItems[] = [
                        'product_id' => $product->id,
                        'quantity' => $item['quantity'],
                        'product' => $product
                    ];
                }
            }

            if (empty($cartItems)) {
                return response()->json([
                    'message' => 'Koszyk jest pusty'
                ], 400);
            }

            // Calculate cart total with promotional prices
            $cartTotal = collect($cartItems)->sum(function ($item) {
                return $item['product']->getPromotionalPrice() * $item['quantity'];
            });

            // Get shipping methods with calculated costs
            $shippingMethods = $this->shippingService->getShippingMethodsWithCosts($cartTotal);

            return response()->json([
                'cart_items' => $cartItems,
                'shipping_methods' => $shippingMethods,
                'cart_total' => $cartTotal,
                'free_shipping_threshold' => $this->shippingService->getFreeShippingThreshold()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Wystąpił błąd podczas pobierania danych koszyka'
            ], 500);
        }
    }

    /**
     * Process guest checkout
     */
    public function process(Request $request)
    {
        $validator = Validator::make($request->all(), [
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

            // Pobierz produkty i sprawdź dostępność
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

            if (empty($cartItems)) {
                throw new \Exception('Koszyk jest pusty');
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

            // Utwórz zamówienie (user_id = null dla gości)
            $order = Order::create([
                'user_id' => null, // Zamówienie gościa
                'status' => 'pending',
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $request->shipping['email'],
                'address' => $request->shipping['address'],
                'city' => $request->shipping['city'],
                'postal_code' => $request->shipping['postalCode'],
                'country' => 'PL',
                'subtotal' => $subtotal,
                'shipping_cost' => $shippingCost,
                'discount' => $discount,
                'total' => $total,
                'payment_method' => 'cod',
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
                'message' => 'Zamówienie zostało utworzone',
                'order' => $order->load('items')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Wystąpił błąd podczas przetwarzania zamówienia: ' . $e->getMessage()
            ], 500);
        }
    }
} 