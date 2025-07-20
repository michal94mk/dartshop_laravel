<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="DartShop API Documentation",
 *     description="API documentation for DartShop Laravel application",
 *     @OA\Contact(
 *         email="admin@dartshop.com",
 *         name="DartShop Support"
 *     ),
 *     @OA\License(
 *         name="MIT",
 *         url="https://opensource.org/licenses/MIT"
 *     )
 * )
 * 
 * @OA\Server(
 *     url=L5_SWAGGER_CONST_HOST,
 *     description="API Server"
 * )
 * 
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 * 
 * @OA\Tag(
 *     name="Authentication",
 *     description="API Endpoints for user authentication"
 * )
 * @OA\Tag(
 *     name="Products",
 *     description="API Endpoints for product management"
 * )
 * @OA\Tag(
 *     name="Orders",
 *     description="API Endpoints for order management"
 * )
 * @OA\Tag(
 *     name="Cart",
 *     description="API Endpoints for shopping cart"
 * )
 * @OA\Tag(
 *     name="Users",
 *     description="API Endpoints for user management"
 * )
 * @OA\Tag(
 *     name="Admin",
 *     description="API Endpoints for admin panel"
 * )
 * 
 * @OA\Schema(
 *     schema="ErrorResponse",
 *     title="Error Response",
 *     description="Standard error response",
 *     @OA\Property(property="error", type="string", example="Error message"),
 *     @OA\Property(property="message", type="string", example="Detailed error description")
 * )
 * 
 * @OA\Schema(
 *     schema="SuccessResponse",
 *     title="Success Response",
 *     description="Standard success response",
 *     @OA\Property(property="status", type="string", example="ok"),
 *     @OA\Property(property="message", type="string", example="Operation completed successfully"),
 *     @OA\Property(property="timestamp", type="string", format="date-time")
 * )
 * 
 * @OA\Schema(
 *     schema="PaginationMeta",
 *     title="Pagination Meta",
 *     description="Pagination metadata",
 *     @OA\Property(property="current_page", type="integer", example=1),
 *     @OA\Property(property="from", type="integer", example=1),
 *     @OA\Property(property="last_page", type="integer", example=5),
 *     @OA\Property(property="per_page", type="integer", example=12),
 *     @OA\Property(property="to", type="integer", example=12),
 *     @OA\Property(property="total", type="integer", example=60),
 *     @OA\Property(property="cache_hit", type="boolean", example=false),
 *     @OA\Property(property="cache_key", type="string", example="products_list_abc123..."),
 *     @OA\Property(property="filters_available", type="object",
 *         @OA\Property(property="categories", type="array", @OA\Items(ref="#/components/schemas/Category")),
 *         @OA\Property(property="brands", type="array", @OA\Items(ref="#/components/schemas/Brand")),
 *         @OA\Property(property="price_range", type="object",
 *             @OA\Property(property="min", type="number", example=10.00),
 *             @OA\Property(property="max", type="number", example=500.00)
 *         )
 *     )
 * )
 * 
 * @OA\Schema(
 *     schema="PaginationLinks",
 *     title="Pagination Links",
 *     description="Pagination links",
 *     @OA\Property(property="first", type="string", example="http://example.com/api/products?page=1"),
 *     @OA\Property(property="last", type="string", example="http://example.com/api/products?page=5"),
 *     @OA\Property(property="prev", type="string", nullable=true, example=null),
 *     @OA\Property(property="next", type="string", example="http://example.com/api/products?page=2")
 * )
 */
class SwaggerController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/health",
     *     summary="Health check endpoint",
     *     tags={"System"},
     *     @OA\Response(
     *         response=200,
     *         description="API is healthy",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="ok"),
     *             @OA\Property(property="message", type="string", example="API is running"),
     *             @OA\Property(property="timestamp", type="string", example="2024-01-01T00:00:00Z")
     *         )
     *     )
     * )
     */
    public function health()
    {
        return response()->json([
            'status' => 'ok',
            'message' => 'API is running',
            'timestamp' => now()->toISOString()
        ]);
    }
} 