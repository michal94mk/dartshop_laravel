<?php

namespace App\Swagger;

/**
 * @OA\Get(
 *     path="/api/products",
 *     summary="Get products list",
 *     description="Retrieve a paginated list of products with filtering and search options",
 *     tags={"Products"},
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
 *     @OA\Parameter(
 *         name="search",
 *         in="query",
 *         description="Search term for product name or description",
 *         required=false,
 *         @OA\Schema(type="string", example="dart")
 *     ),
 *     @OA\Parameter(
 *         name="category_id",
 *         in="query",
 *         description="Filter by category ID",
 *         required=false,
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Parameter(
 *         name="brand_id",
 *         in="query",
 *         description="Filter by brand ID",
 *         required=false,
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Parameter(
 *         name="min_price",
 *         in="query",
 *         description="Minimum price filter",
 *         required=false,
 *         @OA\Schema(type="number", format="float", example=10.00)
 *     ),
 *     @OA\Parameter(
 *         name="max_price",
 *         in="query",
 *         description="Maximum price filter",
 *         required=false,
 *         @OA\Schema(type="number", format="float", example=100.00)
 *     ),
 *     @OA\Parameter(
 *         name="sort_by",
 *         in="query",
 *         description="Sort by field",
 *         required=false,
 *         @OA\Schema(type="string", enum={"name", "price", "created_at"}, example="name")
 *     ),
 *     @OA\Parameter(
 *         name="sort_order",
 *         in="query",
 *         description="Sort order",
 *         required=false,
 *         @OA\Schema(type="string", enum={"asc", "desc"}, example="asc")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Products retrieved successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Product")),
 *             @OA\Property(property="meta", type="object",
 *                 @OA\Property(property="current_page", type="integer", example=1),
 *                 @OA\Property(property="last_page", type="integer", example=5),
 *                 @OA\Property(property="per_page", type="integer", example=12),
 *                 @OA\Property(property="total", type="integer", example=58)
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Server error",
 *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
 *     )
 * )
 *
 * @OA\Get(
 *     path="/api/products/{id}",
 *     summary="Get product details",
 *     description="Retrieve detailed information about a specific product",
 *     tags={"Products"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="Product ID",
 *         required=true,
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Product details retrieved successfully",
 *         @OA\JsonContent(ref="#/components/schemas/ProductDetailed")
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Product not found",
 *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
 *     )
 * )
 *
 * @OA\Get(
 *     path="/api/products/latest",
 *     summary="Get latest products",
 *     description="Retrieve the latest added products",
 *     tags={"Products"},
 *     @OA\Parameter(
 *         name="limit",
 *         in="query",
 *         description="Number of products to return",
 *         required=false,
 *         @OA\Schema(type="integer", example=6)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Latest products retrieved successfully",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(ref="#/components/schemas/Product")
 *         )
 *     )
 * )
 *
 * @OA\Get(
 *     path="/api/products/{productId}/reviews",
 *     summary="Get product reviews",
 *     description="Retrieve reviews for a specific product",
 *     tags={"Products"},
 *     @OA\Parameter(
 *         name="productId",
 *         in="path",
 *         description="Product ID",
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
 *     @OA\Response(
 *         response=200,
 *         description="Product reviews retrieved successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Review")),
 *             @OA\Property(property="meta", type="object",
 *                 @OA\Property(property="current_page", type="integer", example=1),
 *                 @OA\Property(property="last_page", type="integer", example=3),
 *                 @OA\Property(property="total", type="integer", example=25)
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Product not found",
 *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
 *     )
 * )
 *
 * @OA\Post(
 *     path="/api/products/{productId}/reviews",
 *     summary="Create product review",
 *     description="Create a new review for a product (authenticated users only)",
 *     tags={"Products"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="productId",
 *         in="path",
 *         description="Product ID",
 *         required=true,
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="rating", type="integer", minimum=1, maximum=5, example=5),
 *             @OA\Property(property="comment", type="string", example="Great product, excellent quality!")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Review created successfully",
 *         @OA\JsonContent(ref="#/components/schemas/Review")
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthorized",
 *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Cannot review this product",
 *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation error",
 *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
 *     )
 * )
 *
 * @OA\Get(
 *     path="/api/products/{productId}/can-review",
 *     summary="Check if user can review product",
 *     description="Check if the authenticated user can review the product",
 *     tags={"Products"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="productId",
 *         in="path",
 *         description="Product ID",
 *         required=true,
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Review eligibility check",
 *         @OA\JsonContent(
 *             @OA\Property(property="can_review", type="boolean", example=true),
 *             @OA\Property(property="reason", type="string", nullable=true, example="User has purchased this product")
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthorized",
 *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
 *     )
 * )
 */
class Products
{
    // This class serves as a container for product endpoint annotations
} 