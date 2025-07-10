<?php

namespace App\Services;

use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Collection;

class CartService
{
    /**
     * Get cart items
     */
    public function getCartItems()
    {
        if (Auth::check()) {
            return CartItem::with('product.brand')
                ->where('user_id', Auth::id())
                ->get();
        }

        $cart = Session::get('cart', []);
        $productIds = collect($cart)->pluck('product_id');
        
        $products = Product::with('brand')->whereIn('id', $productIds)->get();
        
        return collect($cart)->map(function ($item) use ($products) {
            $product = $products->firstWhere('id', $item['product_id']);
            return (object)[
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'product' => $product
            ];
        });
    }

    /**
     * Get cart items for guest user
     */
    private function getGuestCartItems()
    {
        $cart = Session::get('cart', []);
        $productIds = collect($cart)->pluck('product_id');
        
        $products = Product::with('brand')->whereIn('id', $productIds)->get();
        
        return collect($cart)->map(function ($cartItem) use ($products) {
            $product = $products->firstWhere('id', $cartItem['product_id']);
            if ($product) {
                return (object)[
                    'product_id' => $cartItem['product_id'],
                    'quantity' => $cartItem['quantity'],
                    'product' => $product
                ];
            }
            return null;
        })->filter();
    }

    /**
     * Add item to cart
     */
    public function addToCart(Product $product, int $quantity = 1): ?CartItem
    {
        if (Auth::check()) {
            return $this->addToAuthenticatedCart($product, $quantity);
        } else {
            $this->addToGuestCart($product, $quantity);
            return null;
        }
    }

    /**
     * Add item to authenticated user's cart
     */
    private function addToAuthenticatedCart(Product $product, int $quantity): CartItem
    {
        $cartItem = CartItem::firstOrNew([
            'user_id' => Auth::id(),
            'product_id' => $product->id
        ]);

        // Add new quantity to existing quantity (or set if new item)
        $cartItem->quantity = ($cartItem->exists ? $cartItem->quantity : 0) + $quantity;
        $cartItem->save();
        
        return $cartItem->fresh();
    }

    /**
     * Add item to guest cart
     */
    private function addToGuestCart(Product $product, int $quantity): void
    {
        $cart = Session::get('cart', []);
        
        $existingItem = collect($cart)->firstWhere('product_id', $product->id);
        if ($existingItem) {
            $existingItem['quantity'] += $quantity;
            // Update the quantity in the original cart array
            foreach ($cart as &$item) {
                if ($item['product_id'] === $product->id) {
                    $item['quantity'] = $existingItem['quantity'];
                    break;
                }
            }
        } else {
            $cart[] = [
                'product_id' => $product->id,
                'quantity' => $quantity
            ];
        }

        Session::put('cart', $cart);
    }

    /**
     * Update cart item quantity
     */
    public function updateQuantity(int $productId, int $quantity): ?CartItem
    {
        if (Auth::check()) {
            return $this->updateAuthenticatedCartQuantity($productId, $quantity);
        } else {
            $this->updateGuestCartQuantity($productId, $quantity);
            return null;
        }
    }

    /**
     * Update authenticated user's cart item quantity
     */
    private function updateAuthenticatedCartQuantity(int $productId, int $quantity): ?CartItem
    {
        $cartItem = CartItem::where('user_id', Auth::id())
            ->where('product_id', $productId)
            ->first();

        if ($cartItem) {
            $cartItem->update(['quantity' => $quantity]);
            return $cartItem->fresh();
        }

        return null;
    }

    /**
     * Update guest cart item quantity
     */
    private function updateGuestCartQuantity(int $productId, int $quantity): void
    {
        $cart = Session::get('cart', []);
        
        foreach ($cart as &$item) {
            if ($item['product_id'] === $productId) {
                $item['quantity'] = $quantity;
                break;
            }
        }

        Session::put('cart', $cart);
    }

    /**
     * Remove item from cart
     */
    public function removeFromCart(int $productId): bool
    {
        if (Auth::check()) {
            return $this->removeFromAuthenticatedCart($productId);
        } else {
            return $this->removeFromGuestCart($productId);
        }
    }

    /**
     * Remove item from authenticated user's cart
     */
    private function removeFromAuthenticatedCart(int $productId): bool
    {
        $deleted = CartItem::where('user_id', Auth::id())
            ->where('product_id', $productId)
            ->delete();
            
        return $deleted > 0;
    }

    /**
     * Remove item from guest cart
     */
    private function removeFromGuestCart(int $productId): bool
    {
        $cart = Session::get('cart', []);
        $originalCount = count($cart);
        $cart = array_filter($cart, fn($item) => $item['product_id'] !== $productId);
        Session::put('cart', array_values($cart));
        
        return count($cart) < $originalCount;
    }

    /**
     * Clear cart for the current user
     */
    public function clearCart(): void
    {
        if (Auth::check()) {
            CartItem::where('user_id', Auth::id())->delete();
        } else {
            $this->clearGuestCart();
        }
    }

    /**
     * Clear cart for guest user
     */
    public function clearGuestCart(): void
    {
        Session::forget('cart');
    }

    /**
     * Get cart total
     */
    public function getCartTotal(): float
    {
        return $this->getCartItems()->sum(function ($item) {
            return $item['quantity'] * $item['product']->getPromotionalPrice();
        });
    }

    /**
     * Get cart items count
     */
    public function getCartItemsCount(): int
    {
        return $this->getCartItems()->sum('quantity');
    }

    /**
     * Migrate cart items from session to database after user login
     */
    public function migrateSessionCartToDatabase(): void
    {
        if (!Auth::check()) {
            return;
        }

        $sessionCart = Session::get('cart', []);
        
        foreach ($sessionCart as $item) {
            $product = Product::find($item['product_id']);
            if ($product) {
                $cartItem = CartItem::firstOrNew([
                    'user_id' => Auth::id(),
                    'product_id' => $product->id
                ]);
                
                // If the product is already in the cart, add quantities
                if ($cartItem->exists) {
                    $cartItem->quantity += $item['quantity'];
                } else {
                    $cartItem->quantity = $item['quantity'];
                }
                
                $cartItem->save();
            }
        }

        // Clear session cart after migration
        Session::forget('cart');
    }
} 