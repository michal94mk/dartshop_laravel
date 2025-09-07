<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Cart\AddToCartRequest;
use App\Http\Requests\Cart\UpdateCartItemRequest;
use App\Http\Requests\Cart\SyncCartRequest;
use App\Models\CartItem;
use App\Models\Product;
use App\Services\CartService;
use App\Services\PromotionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class CartController extends BaseApiController
{
    public function __construct(
        private CartService $cartService,
        private PromotionService $promotionService
    ) {}

    /**
     * Get the contents of the cart for the current user.
     */
    public function index(): JsonResponse
    {
        $this->logApiRequest(request(), 'Fetch cart items');
        
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
    }

    /**
     * Add a new item to the cart.
     */
    public function store(AddToCartRequest $request): JsonResponse
    {
        $this->logApiRequest($request, 'Add item to cart');

        $product = Product::findOrFail($request->validated()['product_id']);
        $cartItem = $this->cartService->addToCart($product, $request->validated()['quantity']);

        return $this->createdResponse($cartItem, 'Product added to cart successfully');
    }

    /**
     * Update the quantity of a specific cart item.
     */
    public function update(UpdateCartItemRequest $request, CartItem $cartItem): JsonResponse
    {
        $this->logApiRequest($request, 'Update cart item');

        // Check if cart item belongs to current user
        if ($cartItem->user_id !== Auth::id()) {
            return $this->forbiddenResponse('Unauthorized access to cart item');
        }

        $cartItem->update(['quantity' => $request->validated()['quantity']]);

        return $this->successResponse($cartItem->fresh(), 'Cart item updated successfully');
    }

    /**
     * Remove a specific item from the cart.
     */
    public function destroy(CartItem $cartItem): JsonResponse
    {
        $this->logApiRequest(request(), 'Remove cart item');
        
        // Check if cart item belongs to current user
        if ($cartItem->user_id !== Auth::id()) {
            return $this->forbiddenResponse('Unauthorized access to cart item');
        }

        $cartItem->delete();

        return $this->successResponse(null, 'Cart item removed successfully');
    }

    /**
     * Clear all items from the cart.
     */
    public function clear(): JsonResponse
    {
        $this->logApiRequest(request(), 'Clear cart');
        
        $this->cartService->clearCart();

        return $this->successResponse(null, 'Cart cleared successfully');
    }

    /**
     * Sync the cart with the provided items from the frontend.
     */
    public function sync(SyncCartRequest $request): JsonResponse
    {
        $this->logApiRequest($request, 'Sync cart items');

        $this->cartService->clearCart();
        foreach ($request->validated()['items'] as $item) {
            $product = Product::findOrFail($item['product_id']);
            $this->cartService->addToCart($product, $item['quantity']);
        }
        
        return $this->successResponse([
            'items' => $this->cartService->getCartItems(),
        ], 'Cart synchronized successfully');
    }
} 