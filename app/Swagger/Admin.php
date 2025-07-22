<?php

namespace App\Swagger;

/**
 * @OA\Get(
 *     path="/api/admin/dashboard",
 *     summary="Get admin dashboard data",
 *     description="Retrieve dashboard statistics and metrics for admin panel",
 *     tags={"Admin"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="Dashboard data retrieved successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="stats", type="object",
 *                 @OA\Property(property="product_count", type="integer", example=127),
 *                 @OA\Property(property="user_count", type="integer", example=1250),
 *                 @OA\Property(property="order_count", type="integer", example=856),
 *                 @OA\Property(property="review_count", type="integer", example=324)
 *             ),
 *             @OA\Property(property="recent_orders", type="array", @OA\Items(ref="#/components/schemas/Order")),
 *             @OA\Property(property="sales_data", type="array", @OA\Items(
 *                 @OA\Property(property="date", type="string", format="date", example="2024-01-15"),
 *                 @OA\Property(property="total", type="number", format="float", example=1250.00)
 *             )),
 *             @OA\Property(property="top_products", type="array", @OA\Items(ref="#/components/schemas/Product"))
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthorized",
 *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Forbidden - Admin access required",
 *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
 *     )
 * )
 *
 * @OA\Get(
 *     path="/api/admin/products",
 *     summary="Get products (admin)",
 *     description="Retrieve paginated list of all products for admin management",
 *     tags={"Admin"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="page",
 *         in="query",
 *         description="Page number",
 *         required=false,
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Parameter(
 *         name="per_page",
 *         in="query",
 *         description="Items per page",
 *         required=false,
 *         @OA\Schema(type="integer", example=15)
 *     ),
 *     @OA\Parameter(
 *         name="search",
 *         in="query",
 *         description="Search term",
 *         required=false,
 *         @OA\Schema(type="string", example="dart")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Products retrieved successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Product")),
 *             @OA\Property(property="meta", ref="#/components/schemas/PaginationMeta")
 *         )
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Forbidden - Admin access required",
 *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
 *     )
 * )
 *
 * @OA\Post(
 *     path="/api/admin/products",
 *     summary="Create product (admin)",
 *     description="Create a new product",
 *     tags={"Admin"},
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="name", type="string", example="Professional Dart Set"),
 *             @OA\Property(property="description", type="string", example="High-quality professional dart set"),
 *             @OA\Property(property="price", type="number", format="float", example=89.99),
 *             @OA\Property(property="category_id", type="integer", nullable=true, example=1),
 *             @OA\Property(property="brand_id", type="integer", nullable=true, example=1),
 *             @OA\Property(property="image_url", type="string", nullable=true, example="https://example.com/image.jpg"),
 *             @OA\Property(property="is_featured", type="boolean", example=false),
 *             @OA\Property(property="is_active", type="boolean", example=true)
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Product created successfully",
 *         @OA\JsonContent(ref="#/components/schemas/Product")
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation error",
 *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Forbidden - Admin access required",
 *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
 *     )
 * )
 *
 * @OA\Get(
 *     path="/api/admin/products/{id}",
 *     summary="Get product (admin)",
 *     description="Get detailed product information for admin",
 *     tags={"Admin"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="Product ID",
 *         required=true,
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Product details retrieved",
 *         @OA\JsonContent(ref="#/components/schemas/Product")
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Product not found",
 *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
 *     )
 * )
 *
 * @OA\Put(
 *     path="/api/admin/products/{id}",
 *     summary="Update product (admin)",
 *     description="Update an existing product",
 *     tags={"Admin"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="Product ID",
 *         required=true,
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="name", type="string", example="Updated Product Name"),
 *             @OA\Property(property="description", type="string", example="Updated description"),
 *             @OA\Property(property="price", type="number", format="float", example=99.99),
 *             @OA\Property(property="is_active", type="boolean", example=true)
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Product updated successfully",
 *         @OA\JsonContent(ref="#/components/schemas/Product")
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Product not found",
 *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
 *     )
 * )
 *
 * @OA\Delete(
 *     path="/api/admin/products/{id}",
 *     summary="Delete product (admin)",
 *     description="Delete a product",
 *     tags={"Admin"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="Product ID",
 *         required=true,
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Product deleted successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Product deleted successfully")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Product not found",
 *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
 *     )
 * )
 *
 * @OA\Get(
 *     path="/api/admin/users",
 *     summary="Get users (admin)",
 *     description="Retrieve paginated list of all users for admin management",
 *     tags={"Admin"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="page",
 *         in="query",
 *         description="Page number",
 *         required=false,
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Parameter(
 *         name="search",
 *         in="query",
 *         description="Search by name or email",
 *         required=false,
 *         @OA\Schema(type="string", example="john")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Users retrieved successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/User")),
 *             @OA\Property(property="meta", ref="#/components/schemas/PaginationMeta")
 *         )
 *     )
 * )
 *
 * @OA\Get(
 *     path="/api/admin/orders",
 *     summary="Get orders (admin)",
 *     description="Retrieve paginated list of all orders for admin management",
 *     tags={"Admin"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="page",
 *         in="query",
 *         description="Page number",
 *         required=false,
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Parameter(
 *         name="status",
 *         in="query",
 *         description="Filter by order status",
 *         required=false,
 *         @OA\Schema(type="string", enum={"pending", "processing", "shipped", "delivered", "cancelled"})
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Orders retrieved successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Order")),
 *             @OA\Property(property="meta", ref="#/components/schemas/PaginationMeta")
 *         )
 *     )
 * )
 *
 * @OA\Put(
 *     path="/api/admin/orders/{order}/status",
 *     summary="Update order status (admin)",
 *     description="Update the status of an order",
 *     tags={"Admin"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="order",
 *         in="path",
 *         description="Order ID",
 *         required=true,
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", enum={"pending", "processing", "shipped", "delivered", "cancelled"}, example="processing")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Order status updated successfully",
 *         @OA\JsonContent(ref="#/components/schemas/Order")
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Order not found",
 *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
 *     )
 * )
 *
 * @OA\Get(
 *     path="/api/admin/categories",
 *     summary="Get categories (admin)",
 *     description="Retrieve all categories for admin management",
 *     tags={"Admin"},
 *     security={{"bearerAuth":{}}},
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
 * @OA\Post(
 *     path="/api/admin/categories",
 *     summary="Create category (admin)",
 *     description="Create a new product category",
 *     tags={"Admin"},
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="name", type="string", example="Dart Accessories"),
 *             @OA\Property(property="slug", type="string", example="dart-accessories"),
 *             @OA\Property(property="description", type="string", example="Various dart accessories and equipment"),
 *             @OA\Property(property="is_active", type="boolean", example=true)
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Category created successfully",
 *         @OA\JsonContent(ref="#/components/schemas/Category")
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation error",
 *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
 *     )
 * )
 */
class Admin
{
    // This class serves as a container for admin endpoint annotations
} 