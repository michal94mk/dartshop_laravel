<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Product;
use App\Services\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

/**
 * @OA\Tag(
 *     name="Cart",
 *     description="API Endpoints for shopping cart management"
 * )
 */

class CartController extends Controller
{
    protected $cartService;
    
    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * Display the cart contents.
     *
     * @OA\Get(
     *     path="/api/cart",
     *     summary="Get cart contents",
     *     description="Retrieve all items in the user's shopping cart",
     *     tags={"Cart"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="items", type="array", @OA\Items(ref="#/components/schemas/CartItem")),
     *             @OA\Property(property="subtotal", type="number", format="float", example=299.99),
     *             @OA\Property(property="count", type="integer", example=5)
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     )
     * )
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
     *
     * @OA\Post(
     *     path="/api/cart",
     *     summary="Add item to cart",
     *     description="Add a product to the shopping cart",
     *     tags={"Cart"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"product_id","quantity"},
     *             @OA\Property(property="product_id", type="integer", example=1),
     *             @OA\Property(property="quantity", type="integer", example=2)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Item added successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Product added to cart successfully"),
     *             @OA\Property(property="cart_item", ref="#/components/schemas/CartItem")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     )
     * )
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'product_id' => 'required|exists:products,id',
                'quantity' => 'required|integer|min:1',
            ]);

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
    public function update(Request $request, CartItem $cartItem): JsonResponse
    {
        try {
            $validated = $request->validate([
                'quantity' => 'required|integer|min:1',
            ]);

            // Check if cart item belongs to current user
            if ($cartItem->user_id !== Auth::id()) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }

            $cartItem->update(['quantity' => $validated['quantity']]);

            return response()->json([
                'message' => 'Cart item updated successfully',
                'cart_item' => $cartItem->fresh(),
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating cart item: ' . $e->getMessage());
            return response()->json(['message' => 'Error updating cart item', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified cart item.
     */
    public function destroy(CartItem $cartItem): JsonResponse
    {
        try {
            // Check if cart item belongs to current user
            if ($cartItem->user_id !== Auth::id()) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }

            $cartItem->delete();

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