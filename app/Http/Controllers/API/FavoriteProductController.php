<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseApiController;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Exception;

class FavoriteProductController extends BaseApiController
{
    /**
     * Toggle favorite status for a product for the authenticated user.
     *
     * @param Product $product
     * @return JsonResponse
     */
    public function toggle(Product $product): JsonResponse
    {
        try {
            $this->logApiRequest(request(), "Toggle favorite for product ID: {$product->id}");
            
            $user = Auth::user();
            
            if (!$user) {
                return $this->unauthorizedResponse('Unauthenticated.');
            }
            
            // Check if the product is already in favorites
            $isFavorite = $user->favoriteProducts()->where('product_id', $product->id)->exists();
            
            if ($isFavorite) {
                // If favorite exists, remove it
                $user->favoriteProducts()->detach($product->id);
                $message = 'Product removed from favorites.';
                $status = false;
            } else {
                // If not a favorite, add it
                $user->favoriteProducts()->attach($product->id);
                $message = 'Product added to favorites.';
                $status = true;
            }
            
            return $this->successResponse(['is_favorite' => $status], $message);
        } catch (Exception $e) {
            return $this->handleException($e, 'Toggling favorite product');
        }
    }
    
    /**
     * Get all favorite products for the authenticated user.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            $this->logApiRequest(request(), 'Fetch favorite products');
            
            $user = Auth::user();
            
            if (!$user) {
                return $this->unauthorizedResponse('Unauthenticated.');
            }
            
            $favoriteProducts = $user->favoriteProducts()->with(['category', 'brand', 'activePromotions'])->get();
            
            // Add promotional price information to each product
            foreach ($favoriteProducts as $product) {
                if ($product->hasActivePromotion()) {
                    $product->promotion_price = $product->getPromotionalPrice();
                    $product->savings = $product->getSavingsAmount();
                    $product->promotion = $product->getBestActivePromotion();
                } else {
                    $product->promotion_price = $product->price;
                    $product->savings = 0;
                    $product->promotion = null;
                }
            }
            
            return $this->successResponse(['favorite_products' => $favoriteProducts], 'Favorite products fetched successfully');
        } catch (Exception $e) {
            return $this->handleException($e, 'Fetching favorite products');
        }
    }
    
    /**
     * Check if a product is in the authenticated user's favorites.
     *
     * @param Product $product
     * @return JsonResponse
     */
    public function check(Product $product): JsonResponse
    {
        try {
            $this->logApiRequest(request(), "Check favorite status for product ID: {$product->id}");
            
            $user = Auth::user();
            
            if (!$user) {
                return $this->successResponse(['is_favorite' => false], 'User not authenticated');
            }
            
            $isFavorite = $user->favoriteProducts()->where('product_id', $product->id)->exists();
            
            return $this->successResponse(['is_favorite' => $isFavorite], 'Favorite status checked successfully');
        } catch (Exception $e) {
            return $this->handleException($e, 'Checking favorite status');
        }
    }
} 