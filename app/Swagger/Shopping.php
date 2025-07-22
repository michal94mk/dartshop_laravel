<?php

namespace App\Swagger;

/**
 * @OA\Get(
 *     path="/api/cart",
 *     summary="Get cart contents",
 *     description="Retrieve all items in the user's shopping cart",
 *     tags={"Cart"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="Cart contents retrieved successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="items", type="array", @OA\Items(ref="#/components/schemas/CartItem")),
 *             @OA\Property(property="subtotal", type="number", format="float", example=89.99),
 *             @OA\Property(property="total_quantity", type="integer", example=3)
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthorized",
 *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
 *     )
 * )
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
 *             @OA\Property(property="product_id", type="integer", example=1),
 *             @OA\Property(property="quantity", type="integer", example=2)
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Item added to cart successfully",
 *         @OA\JsonContent(ref="#/components/schemas/CartItem")
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthorized",
 *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation error",
 *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
 *     )
 * )
 *
 * @OA\Put(
 *     path="/api/cart/{cartItem}",
 *     summary="Update cart item",
 *     description="Update quantity of an item in the cart",
 *     tags={"Cart"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="cartItem",
 *         in="path",
 *         description="Cart item ID",
 *         required=true,
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="quantity", type="integer", example=3)
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Cart item updated successfully",
 *         @OA\JsonContent(ref="#/components/schemas/CartItem")
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthorized",
 *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Cart item not found",
 *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
 *     )
 * )
 *
 * @OA\Delete(
 *     path="/api/cart/{cartItem}",
 *     summary="Remove item from cart",
 *     description="Remove a specific item from the shopping cart",
 *     tags={"Cart"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="cartItem",
 *         in="path",
 *         description="Cart item ID",
 *         required=true,
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Item removed from cart successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Item removed from cart")
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthorized",
 *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Cart item not found",
 *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
 *     )
 * )
 *
 * @OA\Delete(
 *     path="/api/cart",
 *     summary="Clear cart",
 *     description="Remove all items from the shopping cart",
 *     tags={"Cart"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="Cart cleared successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Cart cleared successfully")
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthorized",
 *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
 *     )
 * )
 *
 * @OA\Get(
 *     path="/api/categories",
 *     summary="Get categories list",
 *     description="Retrieve all product categories",
 *     tags={"Categories"},
 *     @OA\Parameter(
 *         name="with_products_only",
 *         in="query",
 *         description="Include only categories that have products",
 *         required=false,
 *         @OA\Schema(type="boolean", example=true)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Categories retrieved successfully",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(ref="#/components/schemas/Category")
 *         )
 *     )
 * )
 *
 * @OA\Get(
 *     path="/api/categories/{id}",
 *     summary="Get category details",
 *     description="Retrieve detailed information about a specific category",
 *     tags={"Categories"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="Category ID",
 *         required=true,
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Category details retrieved successfully",
 *         @OA\JsonContent(ref="#/components/schemas/Category")
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Category not found",
 *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
 *     )
 * )
 *
 * @OA\Get(
 *     path="/api/categories/{id}/products",
 *     summary="Get category products",
 *     description="Retrieve all products in a specific category",
 *     tags={"Categories"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="Category ID",
 *         required=true,
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Parameter(
 *         name="page",
 *         in="query",
 *         description="Page number for pagination",
 *         required=false,
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Parameter(
 *         name="per_page",
 *         in="query",
 *         description="Number of items per page",
 *         required=false,
 *         @OA\Schema(type="integer", example=12)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Category products retrieved successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Product")),
 *             @OA\Property(property="meta", type="object",
 *                 @OA\Property(property="current_page", type="integer", example=1),
 *                 @OA\Property(property="last_page", type="integer", example=3),
 *                 @OA\Property(property="total", type="integer", example=35)
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Category not found",
 *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
 *     )
 * )
 *
 * @OA\Get(
 *     path="/api/categories/statistics",
 *     summary="Get category statistics",
 *     description="Retrieve statistics about categories and their products",
 *     tags={"Categories"},
 *     @OA\Response(
 *         response=200,
 *         description="Category statistics retrieved successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="total_categories", type="integer", example=8),
 *             @OA\Property(property="categories_with_products", type="integer", example=6),
 *             @OA\Property(property="total_products", type="integer", example=127)
 *         )
 *     )
 * )
 */
class Shopping
{
    // This class serves as a container for shopping and category endpoint annotations
} 