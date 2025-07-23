<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseApiController;
use App\Models\CartItem;
use App\Models\Product;
use App\Services\CartService;
use App\Services\PromotionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Exception;

class CartController extends BaseApiController
{
    protected $cartService;
    protected $promotionService;
    
    public function __construct(CartService $cartService, PromotionService $promotionService)
    {
        $this->cartService = $cartService;
        $this->promotionService = $promotionService;
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
                $this->promotionService->addPromotionInfo($item->product);
            });
            
            $subtotal = $cartItems->sum(function ($item) {
                return $item->quantity * $item->product->getPromotionalPrice();
            });
            
            return $this->successResponse([
                'items' => $cartItems,
                'subtotal' => $subtotal,
                'count' => $cartItems->sum('quantity'),
            ]);
        } catch (Exception $e) {
            return $this->handleException($e, 'Fetching cart items');
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

            $product = Product::findOrFail($validated['product_id']);
            $cartItem = $this->cartService->addToCart($product, $validated['quantity']);

            return $this->createdResponse($cartItem, 'Product added to cart successfully');
        } catch (ValidationException $e) {
            return $this->validationErrorResponse($e->errors(), 'Validation error');
        } catch (Exception $e) {
            return $this->handleException($e, 'Adding product to cart');
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
                return $this->forbiddenResponse('Unauthorized access to cart item');
            }

            $cartItem->update(['quantity' => $validated['quantity']]);

            return $this->successResponse($cartItem->fresh(), 'Cart item updated successfully');
        } catch (ValidationException $e) {
            return $this->validationErrorResponse($e->errors(), 'Validation error');
        } catch (Exception $e) {
            return $this->handleException($e, 'Updating cart item');
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
                return $this->forbiddenResponse('Unauthorized access to cart item');
            }

            $cartItem->delete();

            return $this->successResponse(null, 'Item removed from cart successfully');
        } catch (Exception $e) {
            return $this->handleException($e, 'Removing cart item');
        }
    }

    /**
     * Clear the cart.
     */
    public function clear(): JsonResponse
    {
        try {
            $this->cartService->clearCart();

            return $this->successResponse(null, 'Cart cleared successfully');
        } catch (Exception $e) {
            return $this->handleException($e, 'Clearing cart');
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

            return $this->successResponse([
                'items' => $this->cartService->getCartItems(),
            ], 'Cart synchronized successfully');
        } catch (ValidationException $e) {
            return $this->validationErrorResponse($e->errors(), 'Validation error');
        } catch (Exception $e) {
            return $this->handleException($e, 'Syncing cart');
        }
    }
} 