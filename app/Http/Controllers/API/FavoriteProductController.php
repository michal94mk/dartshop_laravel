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
    /**
     * Toggle a product's favorite status for the authenticated user.
     */
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
    
    /**
     * Get all favorite products for the authenticated user.
     */
    public function index(): JsonResponse
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }
        
        $favoriteProducts = $user->favoriteProducts()->with(['category', 'brand'])->get();
        
        return response()->json([
            'favorite_products' => $favoriteProducts
        ]);
    }
    
    /**
     * Check if a product is in the user's favorites.
     */
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