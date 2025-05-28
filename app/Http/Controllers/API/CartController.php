<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class CartController extends Controller
{
    /**
     * Display the cart contents for the authenticated user.
     */
    public function index(): JsonResponse
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }
            
            $cartItems = $user->cartItems()->with(['product.activePromotions'])->get();
            
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
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'product_id' => 'required|exists:products,id',
                'quantity' => 'required|integer|min:1',
            ]);

            $user = Auth::user();
            if (!$user) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            $product = Product::findOrFail($validated['product_id']);

            // Check if the item already exists in the cart
            $cartItem = CartItem::where('user_id', $user->id)
                              ->where('product_id', $validated['product_id'])
                              ->first();
            
            if ($cartItem) {
                // Update existing cart item
                $cartItem->update([
                    'quantity' => $cartItem->quantity + $validated['quantity'],
                ]);
            } else {
                // Create new cart item with explicit user_id
                $cartItem = CartItem::create([
                    'user_id' => $user->id,
                    'product_id' => $validated['product_id'],
                    'quantity' => $validated['quantity'],
                ]);
            }

            // Load product with promotions
            $cartItem->load(['product.activePromotions']);
            $product = $cartItem->product;
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
            // Ensure the cart item belongs to the authenticated user
            if ($cartItem->user_id !== Auth::id()) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }

            $validated = $request->validate([
                'quantity' => 'required|integer|min:1',
            ]);

            $cartItem->update([
                'quantity' => $validated['quantity'],
            ]);

            // Load product with promotions
            $cartItem->load(['product.activePromotions']);
            $product = $cartItem->product;
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
    public function destroy(CartItem $cartItem): JsonResponse
    {
        try {
            // Ensure the cart item belongs to the authenticated user
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
     * Sync the frontend cart with the database after login.
     */
    public function sync(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'items' => 'required|array',
                'items.*.product_id' => 'required|exists:products,id',
                'items.*.quantity' => 'required|integer|min:1',
            ]);

            $user = Auth::user();
            if (!$user) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            // Clear current cart items
            CartItem::where('user_id', $user->id)->delete();

            // Add new items from frontend cart
            foreach ($validated['items'] as $item) {
                CartItem::create([
                    'user_id' => $user->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                ]);
            }

            // Load cart items with promotions
            $cartItems = $user->cartItems()->with(['product.activePromotions'])->get();
            
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

            return response()->json([
                'message' => 'Cart synchronized successfully',
                'items' => $cartItems,
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error syncing cart: ' . $e->getMessage());
            return response()->json(['message' => 'Error syncing cart', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Clear all cart items for the authenticated user.
     */
    public function clear(): JsonResponse
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            // Delete all cart items for the user
            $deletedCount = CartItem::where('user_id', $user->id)->delete();

            return response()->json([
                'message' => 'Cart cleared successfully',
                'deleted_items' => $deletedCount,
            ]);
        } catch (\Exception $e) {
            Log::error('Error clearing cart: ' . $e->getMessage());
            return response()->json(['message' => 'Error clearing cart', 'error' => $e->getMessage()], 500);
        }
    }
} 