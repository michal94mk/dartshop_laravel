<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class FavoriteProductController extends Controller
{
    public function toggle(Product $product): JsonResponse
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
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
        
        return response()->json([
            'message' => $message,
            'is_favorite' => $status
        ]);
    }
    
    public function index(): JsonResponse
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
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
        
        return response()->json([
            'favorite_products' => $favoriteProducts
        ]);
    }
    
    public function check(Product $product): JsonResponse
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['is_favorite' => false]);
        }
        
        $isFavorite = $user->favoriteProducts()->where('product_id', $product->id)->exists();
        
        return response()->json([
            'is_favorite' => $isFavorite
        ]);
    }
} 