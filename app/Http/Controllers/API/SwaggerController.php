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
 * 
 * @OA\Schema(
 *     schema="CategoryWithProducts",
 *     title="Category With Products",
 *     description="Category with preview products",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Dart Flights"),
 *     @OA\Property(property="products_count", type="integer", example=25),
 *     @OA\Property(property="is_active", type="boolean", example=true),
 *     @OA\Property(property="preview_products", type="array", @OA\Items(
 *         @OA\Property(property="id", type="integer", example=1),
 *         @OA\Property(property="name", type="string", example="Harrows Supergrip Ultra"),
 *         @OA\Property(property="price", type="number", format="float", example=29.99),
 *         @OA\Property(property="image_url", type="string", nullable=true, example="https://example.com/image.jpg")
 *     )),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 * 
 * @OA\Schema(
 *     schema="CategoryDetail",
 *     title="Category Detail",
 *     description="Detailed category information",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Dart Flights"),
 *     @OA\Property(property="products_count", type="integer", example=25),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 * 
 * @OA\Schema(
 *     schema="CartItem",
 *     title="Cart Item",
 *     description="Shopping cart item",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="user_id", type="integer", example=1),
 *     @OA\Property(property="product_id", type="integer", example=1),
 *     @OA\Property(property="quantity", type="integer", example=2),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time"),
 *     @OA\Property(property="product", ref="#/components/schemas/Product")
 * )
 * 
 * @OA\Schema(
 *     schema="Order",
 *     title="Order",
 *     description="Order model",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="user_id", type="integer", example=1),
 *     @OA\Property(property="order_number", type="string", example="ORD-2024-001"),
 *     @OA\Property(property="status", type="string", example="pending"),
 *     @OA\Property(property="total_amount", type="number", format="float", example=299.99),
 *     @OA\Property(property="shipping_address", type="string", example="123 Main St"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time"),
 *     @OA\Property(property="items", type="array", @OA\Items(ref="#/components/schemas/OrderItem"))
 * )
 * 
 * @OA\Schema(
 *     schema="OrderItem",
 *     title="Order Item",
 *     description="Order item model",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="order_id", type="integer", example=1),
 *     @OA\Property(property="product_id", type="integer", example=1),
 *     @OA\Property(property="quantity", type="integer", example=2),
 *     @OA\Property(property="price", type="number", format="float", example=29.99),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time"),
 *     @OA\Property(property="product", ref="#/components/schemas/Product")
 * )
 * 
 * @OA\Schema(
 *     schema="Review",
 *     title="Review",
 *     description="Product review model",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="user_id", type="integer", example=1),
 *     @OA\Property(property="product_id", type="integer", example=1),
 *     @OA\Property(property="title", type="string", example="Great product!"),
 *     @OA\Property(property="content", type="string", example="This is an excellent product..."),
 *     @OA\Property(property="rating", type="integer", example=5),
 *     @OA\Property(property="is_approved", type="boolean", example=true),
 *     @OA\Property(property="is_featured", type="boolean", example=false),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time"),
 *     @OA\Property(property="product", ref="#/components/schemas/Product"),
 *     @OA\Property(property="user", ref="#/components/schemas/User")
 * )
 * 
 * @OA\Schema(
 *     schema="ContactMessage",
 *     title="Contact Message",
 *     description="Contact form message",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="John Doe"),
 *     @OA\Property(property="email", type="string", format="email", example="john@example.com"),
 *     @OA\Property(property="subject", type="string", example="Question about products"),
 *     @OA\Property(property="message", type="string", example="I have a question about..."),
 *     @OA\Property(property="status", type="string", example="unread"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 * 
 * @OA\Schema(
 *     schema="Tutorial",
 *     title="Tutorial",
 *     description="Tutorial model",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="title", type="string", example="How to choose darts"),
 *     @OA\Property(property="slug", type="string", example="how-to-choose-darts"),
 *     @OA\Property(property="excerpt", type="string", example="Learn how to choose the right darts..."),
 *     @OA\Property(property="featured_image_url", type="string", nullable=true, example="https://example.com/image.jpg"),
 *     @OA\Property(property="thumbnail_image_url", type="string", nullable=true, example="https://example.com/thumb.jpg"),
 *     @OA\Property(property="category", type="string", example="Beginner"),
 *     @OA\Property(property="difficulty", type="string", example="Easy"),
 *     @OA\Property(property="published_at", type="string", format="date-time"),
 *     @OA\Property(property="author", type="string", example="DartShop Admin"),
 *     @OA\Property(property="views", type="integer", example=150),
 *     @OA\Property(property="order", type="integer", example=1),
 *     @OA\Property(property="is_published", type="boolean", example=true),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 * 
 * @OA\Schema(
 *     schema="TutorialDetail",
 *     title="Tutorial Detail",
 *     description="Detailed tutorial information",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="title", type="string", example="How to choose darts"),
 *     @OA\Property(property="slug", type="string", example="how-to-choose-darts"),
 *     @OA\Property(property="content", type="string", example="Full tutorial content..."),
 *     @OA\Property(property="excerpt", type="string", example="Learn how to choose the right darts..."),
 *     @OA\Property(property="featured_image_url", type="string", nullable=true, example="https://example.com/image.jpg"),
 *     @OA\Property(property="thumbnail_image_url", type="string", nullable=true, example="https://example.com/thumb.jpg"),
 *     @OA\Property(property="category", type="string", example="Beginner"),
 *     @OA\Property(property="difficulty", type="string", example="Easy"),
 *     @OA\Property(property="published_at", type="string", format="date-time"),
 *     @OA\Property(property="author", type="string", example="DartShop Admin"),
 *     @OA\Property(property="views", type="integer", example=150),
 *     @OA\Property(property="meta_title", type="string", example="How to Choose Darts - Complete Guide"),
 *     @OA\Property(property="meta_description", type="string", example="Learn how to choose the perfect darts..."),
 *     @OA\Property(property="order", type="integer", example=1)
 * )
 * 
 * @OA\Schema(
 *     schema="AboutUs",
 *     title="About Us",
 *     description="About us page information",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="title", type="string", example="O naszym sklepie"),
 *     @OA\Property(property="content", type="string", example="Jesteśmy specjalistycznym sklepem..."),
 *     @OA\Property(property="image_url", type="string", nullable=true, example="https://example.com/about.jpg"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 * 
 * @OA\Schema(
 *     schema="RecentOrder",
 *     title="Recent Order",
 *     description="Recent order for dashboard",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="total", type="number", format="float", example=299.99),
 *     @OA\Property(property="status", type="string", example="pending"),
 *     @OA\Property(property="user", type="object", nullable=true,
 *         @OA\Property(property="id", type="integer", example=1),
 *         @OA\Property(property="name", type="string", example="John Doe"),
 *         @OA\Property(property="email", type="string", example="john@example.com")
 *     ),
 *     @OA\Property(property="created_at", type="string", example="2024-01-15 10:30:00")
 * )
 * 
 * @OA\Schema(
 *     schema="SalesData",
 *     title="Sales Data",
 *     description="Sales data for charts",
 *     @OA\Property(property="date", type="string", example="2024-01-15"),
 *     @OA\Property(property="total_sales", type="number", format="float", example=1500.00),
 *     @OA\Property(property="order_count", type="integer", example=5)
 * )
 * 
 * @OA\Schema(
 *     schema="TopProduct",
 *     title="Top Product",
 *     description="Top selling product",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Harrows Supergrip Ultra"),
 *     @OA\Property(property="total_sold", type="integer", example=25),
 *     @OA\Property(property="total_revenue", type="number", format="float", example=749.75)
 * )
 * 
 * @OA\Schema(
 *     schema="CategoryData",
 *     title="Category Data",
 *     description="Category statistics for dashboard",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Dart Flights"),
 *     @OA\Property(property="products_count", type="integer", example=15),
 *     @OA\Property(property="total_sales", type="number", format="float", example=2500.00)
 * )
 * 
 * @OA\Schema(
 *     schema="NewsletterSubscription",
 *     title="Newsletter Subscription",
 *     description="Newsletter subscription model",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="email", type="string", format="email", example="user@example.com"),
 *     @OA\Property(property="status", type="string", enum={"active", "pending", "unsubscribed"}, example="active"),
 *     @OA\Property(property="verified_at", type="string", format="date-time", nullable=true),
 *     @OA\Property(property="unsubscribed_at", type="string", format="date-time", nullable=true),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 * 

 * 

 * 
 * @OA\Schema(
 *     schema="ValidationError",
 *     title="Validation Error",
 *     description="Validation error response",
 *     @OA\Property(property="message", type="string", example="The given data was invalid."),
 *     @OA\Property(property="errors", type="object", example={
 *         "field_name": {"The field name field is required."}
 *     })
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