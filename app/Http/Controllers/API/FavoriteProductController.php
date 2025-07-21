<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

/**
 * @OA\Tag(
 *     name="Favorites",
 *     description="API Endpoints for favorite products management"
 * )
 */

class FavoriteProductController extends Controller
{
    /**
     * Toggle a product's favorite status for the authenticated user.
     *
     * @OA\Post(
     *     path="/api/favorites/{product}",
     *     summary="Toggle favorite product",
     *     description="Add or remove a product from user's favorites",
     *     tags={"Favorites"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="product",
     *         in="path",
     *         description="Product ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Product added to favorites."),
     *             @OA\Property(property="is_favorite", type="boolean", example=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
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
     *
     * @OA\Get(
     *     path="/api/favorites",
     *     summary="Get favorite products",
     *     description="Retrieve all favorite products for the authenticated user",
     *     tags={"Favorites"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="favorite_products", type="array", @OA\Items(ref="#/components/schemas/Product"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
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
    
    /**
     * Check if a product is in the user's favorites.
     *
     * @OA\Get(
     *     path="/api/favorites/check/{product}",
     *     summary="Check if product is favorite",
     *     description="Check if a product is in the user's favorites",
     *     tags={"Favorites"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="product",
     *         in="path",
     *         description="Product ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="is_favorite", type="boolean", example=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
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