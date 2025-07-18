<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CartRequest;
use App\Models\CartItem;
use App\Models\Product;
use App\Services\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class CartController extends Controller
{
    protected $cartService;
    
    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * Display the cart contents.
     */
    public function index(): JsonResponse
    {
        try {
            $cartItems = $this->cartService->getCartItems();
            
            // Add promotion information to each cart item
            $cartItems->each(function ($item) {
                $product = $item->product;
                $bestPromotion = $product->getBestActivePromotion();
                if ($bestPromotion) {
                    $product->promotion_price = $product->getPromotionalPrice();
                    $product->savings = $product->getSavingsAmount();
                    $product->promotion = [
                        'id' => $bestPromotion->id,
                        'title' => $bestPromotion->title,
                        'badge_text' => $bestPromotion->badge_text,
                        'badge_color' => $bestPromotion->badge_color,
                        'discount_type' => $bestPromotion->discount_type,
                        'discount_value' => $bestPromotion->discount_value
                    ];
                } else {
                    $product->promotion_price = $product->price;
                    $product->savings = 0;
                }
            });
            
            $subtotal = $cartItems->sum(function ($item) {
                return $item->quantity * $item->product->getPromotionalPrice();
            });
            
            return response()->json([
                'items' => $cartItems,
                'subtotal' => $subtotal,
                'count' => $cartItems->sum('quantity'),
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching cart items: ' . $e->getMessage());
            return response()->json(['message' => 'Error fetching cart items', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Store a new cart item.
     */
    public function store(CartRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();

            $product = Product::findOrFail($validated['product_id']);
            $cartItem = $this->cartService->addToCart($product, $validated['quantity']);

            return response()->json([
                'message' => 'Product added to cart successfully',
                'cart_item' => $cartItem,
            ], 201);
        } catch (ValidationException $e) {
            return response()->json(['message' => 'Validation error', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Error adding product to cart: ' . $e->getMessage());
            return response()->json(['message' => 'Error adding product to cart', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Update the specified cart item.
     */
    public function update(CartRequest $request, $productId): JsonResponse
    {
        try {
            $validated = $request->validated();

            $product = Product::findOrFail($productId);
            $cartItem = $this->cartService->updateQuantity($productId, $validated['quantity']);

            if (!$cartItem) {
                return response()->json(['message' => 'Cart item not found'], 404);
            }

            return response()->json([
                'message' => 'Cart item updated successfully',
                'cart_item' => $cartItem,
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating cart item: ' . $e->getMessage());
            return response()->json(['message' => 'Error updating cart item', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified cart item.
     */
    public function destroy($productId): JsonResponse
    {
        try {
            $result = $this->cartService->removeFromCart($productId);

            if (!$result) {
                return response()->json(['message' => 'Cart item not found'], 404);
            }

            return response()->json([
                'message' => 'Item removed from cart successfully',
            ]);
        } catch (\Exception $e) {
            Log::error('Error removing cart item: ' . $e->getMessage());
            return response()->json(['message' => 'Error removing cart item', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Clear the cart.
     */
    public function clear(): JsonResponse
    {
        try {
            $this->cartService->clearCart();

            return response()->json([
                'message' => 'Cart cleared successfully',
            ]);
        } catch (\Exception $e) {
            Log::error('Error clearing cart: ' . $e->getMessage());
            return response()->json(['message' => 'Error clearing cart', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Sync the cart items.
     */
    public function sync(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'items' => 'required|array',
                'items.*.product_id' => 'required|exists:products,id',
                'items.*.quantity' => 'required|integer|min:1',
            ]);

            $this->cartService->clearCart();

            foreach ($validated['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);
                $this->cartService->addToCart($product, $item['quantity']);
            }

            return response()->json([
                'message' => 'Cart synchronized successfully',
                'items' => $this->cartService->getCartItems(),
            ]);
        } catch (\Exception $e) {
            Log::error('Error syncing cart: ' . $e->getMessage());
            return response()->json(['message' => 'Error syncing cart', 'error' => $e->getMessage()], 500);
        }
    }
} 