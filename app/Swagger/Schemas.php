<?php

namespace App\Swagger;

/**
 * @OA\Schema(
 *     schema="Product",
 *     title="Product",
 *     description="Product model",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Harrows Supergrip Ultra"),
 *     @OA\Property(property="description", type="string", example="Professional dart flights for tournament play"),
 *     @OA\Property(property="price", type="number", format="float", example=29.99),
 *     @OA\Property(property="image_url", type="string", nullable=true, example="https://example.com/image.jpg"),
 *     @OA\Property(property="is_featured", type="boolean", example=false),
 *     @OA\Property(property="is_active", type="boolean", example=true),
 *     @OA\Property(property="brand_id", type="integer", nullable=true, example=1),
 *     @OA\Property(property="category_id", type="integer", nullable=true, example=1),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 *
 * @OA\Schema(
 *     schema="ProductDetailed",
 *     title="Product Detailed",
 *     description="Detailed product model with relationships",
 *     allOf={@OA\Schema(ref="#/components/schemas/Product")},
 *     @OA\Property(property="category", ref="#/components/schemas/Category", nullable=true),
 *     @OA\Property(property="brand", ref="#/components/schemas/Brand", nullable=true),
 *     @OA\Property(property="promotion_price", type="number", format="float", nullable=true, example=24.99),
 *     @OA\Property(property="savings", type="number", format="float", nullable=true, example=5.00),
 *     @OA\Property(property="promotion", type="object", nullable=true,
 *         @OA\Property(property="id", type="integer", example=1),
 *         @OA\Property(property="title", type="string", example="Summer Sale"),
 *         @OA\Property(property="badge_text", type="string", example="20% OFF"),
 *         @OA\Property(property="badge_color", type="string", example="red")
 *     ),
 *     @OA\Property(property="reviews_avg_rating", type="number", format="float", nullable=true, example=4.5),
 *     @OA\Property(property="reviews_count", type="integer", example=12)
 * )
 *
 * @OA\Schema(
 *     schema="User",
 *     title="User",
 *     description="User model",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="John Doe"),
 *     @OA\Property(property="first_name", type="string", example="John"),
 *     @OA\Property(property="last_name", type="string", example="Doe"),
 *     @OA\Property(property="email", type="string", format="email", example="john@example.com"),
 *     @OA\Property(property="email_verified_at", type="string", format="date-time", nullable=true),
 *     @OA\Property(property="roles", type="array", @OA\Items(type="string", example="customer")),
 *     @OA\Property(property="permissions", type="array", @OA\Items(type="string")),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 *
 * @OA\Schema(
 *     schema="Category",
 *     title="Category",
 *     description="Product category model",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Dart Flights"),
 *     @OA\Property(property="slug", type="string", example="dart-flights"),
 *     @OA\Property(property="description", type="string", nullable=true, example="High-quality dart flights"),
 *     @OA\Property(property="is_active", type="boolean", example=true),
 *     @OA\Property(property="products_count", type="integer", example=15),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 *
 * @OA\Schema(
 *     schema="Brand",
 *     title="Brand",
 *     description="Product brand model",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Harrows"),
 *     @OA\Property(property="slug", type="string", example="harrows"),
 *     @OA\Property(property="description", type="string", nullable=true, example="Premium dart equipment manufacturer"),
 *     @OA\Property(property="logo_url", type="string", nullable=true, example="https://example.com/logo.jpg"),
 *     @OA\Property(property="is_active", type="boolean", example=true),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 *
 * @OA\Schema(
 *     schema="CartItem",
 *     title="Cart Item",
 *     description="Shopping cart item model",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="user_id", type="integer", example=1),
 *     @OA\Property(property="product_id", type="integer", example=1),
 *     @OA\Property(property="quantity", type="integer", example=2),
 *     @OA\Property(property="product", ref="#/components/schemas/Product"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 *
 * @OA\Schema(
 *     schema="Order",
 *     title="Order",
 *     description="Order model",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="user_id", type="integer", nullable=true, example=1),
 *     @OA\Property(property="email", type="string", format="email", example="customer@example.com"),
 *     @OA\Property(property="total_amount", type="number", format="float", example=159.98),
 *     @OA\Property(property="shipping_cost", type="number", format="float", example=9.99),
 *     @OA\Property(property="subtotal", type="number", format="float", example=149.99),
 *     @OA\Property(property="status", type="string", enum={"pending", "processing", "shipped", "delivered", "cancelled"}, example="pending"),
 *     @OA\Property(property="payment_status", type="string", enum={"pending", "paid", "failed", "refunded"}, example="pending"),
 *     @OA\Property(property="shipping_method", type="string", example="standard"),
 *     @OA\Property(property="notes", type="string", nullable=true, example="Handle with care"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 *
 * @OA\Schema(
 *     schema="OrderDetailed",
 *     title="Order Detailed",
 *     description="Detailed order model with relationships",
 *     allOf={@OA\Schema(ref="#/components/schemas/Order")},
 *     @OA\Property(property="user", ref="#/components/schemas/User", nullable=true),
 *     @OA\Property(property="items", type="array", @OA\Items(ref="#/components/schemas/OrderItem")),
 *     @OA\Property(property="shipping_address", ref="#/components/schemas/ShippingAddress"),
 *     @OA\Property(property="payment", ref="#/components/schemas/Payment", nullable=true)
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
 *     @OA\Property(property="total", type="number", format="float", example=59.98),
 *     @OA\Property(property="product", ref="#/components/schemas/Product"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 *
 * @OA\Schema(
 *     schema="ShippingAddress",
 *     title="Shipping Address",
 *     description="Shipping address model",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="first_name", type="string", example="John"),
 *     @OA\Property(property="last_name", type="string", example="Doe"),
 *     @OA\Property(property="company", type="string", nullable=true, example="Acme Corp"),
 *     @OA\Property(property="address_line_1", type="string", example="123 Main St"),
 *     @OA\Property(property="address_line_2", type="string", nullable=true, example="Apt 4B"),
 *     @OA\Property(property="city", type="string", example="New York"),
 *     @OA\Property(property="state", type="string", example="NY"),
 *     @OA\Property(property="postal_code", type="string", example="10001"),
 *     @OA\Property(property="country", type="string", example="US"),
 *     @OA\Property(property="phone", type="string", nullable=true, example="+1234567890")
 * )
 *
 * @OA\Schema(
 *     schema="Payment",
 *     title="Payment",
 *     description="Payment model",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="order_id", type="integer", example=1),
 *     @OA\Property(property="amount", type="number", format="float", example=159.98),
 *     @OA\Property(property="currency", type="string", example="USD"),
 *     @OA\Property(property="status", type="string", enum={"pending", "completed", "failed", "refunded"}, example="completed"),
 *     @OA\Property(property="payment_method", type="string", example="stripe"),
 *     @OA\Property(property="transaction_id", type="string", nullable=true, example="pi_1234567890"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 *
 * @OA\Schema(
 *     schema="Review",
 *     title="Review",
 *     description="Product review model",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="user_id", type="integer", example=1),
 *     @OA\Property(property="product_id", type="integer", example=1),
 *     @OA\Property(property="rating", type="integer", minimum=1, maximum=5, example=5),
 *     @OA\Property(property="comment", type="string", example="Great product, excellent quality!"),
 *     @OA\Property(property="status", type="string", enum={"pending", "approved", "rejected"}, example="approved"),
 *     @OA\Property(property="is_featured", type="boolean", example=false),
 *     @OA\Property(property="user", type="object",
 *         @OA\Property(property="id", type="integer", example=1),
 *         @OA\Property(property="name", type="string", example="John Doe")
 *     ),
 *     @OA\Property(property="product", type="object",
 *         @OA\Property(property="id", type="integer", example=1),
 *         @OA\Property(property="name", type="string", example="Harrows Supergrip Ultra")
 *     ),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 *
 * @OA\Schema(
 *     schema="PaginationMeta",
 *     title="Pagination Meta",
 *     description="Pagination metadata",
 *     @OA\Property(property="current_page", type="integer", example=1),
 *     @OA\Property(property="last_page", type="integer", example=5),
 *     @OA\Property(property="per_page", type="integer", example=15),
 *     @OA\Property(property="total", type="integer", example=73),
 *     @OA\Property(property="from", type="integer", example=1),
 *     @OA\Property(property="to", type="integer", example=15)
 * )
 *
 * @OA\Schema(
 *     schema="ErrorResponse",
 *     title="Error Response",
 *     description="Standard error response",
 *     @OA\Property(property="message", type="string", example="Validation failed"),
 *     @OA\Property(property="errors", type="object", nullable=true, example={
 *         "email": {"The email field is required."},
 *         "password": {"The password field is required."}
 *     })
 * )
 *
 * @OA\Schema(
 *     schema="SuccessResponse",
 *     title="Success Response", 
 *     description="Standard success response",
 *     @OA\Property(property="message", type="string", example="Operation completed successfully"),
 *     @OA\Property(property="data", type="object", nullable=true)
 * )
 *
 * @OA\Schema(
 *     schema="Promotion",
 *     title="Promotion",
 *     description="Promotion model",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="title", type="string", example="Summer Sale"),
 *     @OA\Property(property="description", type="string", example="Get 20% off all dart products"),
 *     @OA\Property(property="discount_type", type="string", enum={"percentage", "fixed"}, example="percentage"),
 *     @OA\Property(property="discount_value", type="number", format="float", example=20.00),
 *     @OA\Property(property="badge_text", type="string", example="20% OFF"),
 *     @OA\Property(property="badge_color", type="string", example="red"),
 *     @OA\Property(property="is_active", type="boolean", example=true),
 *     @OA\Property(property="is_featured", type="boolean", example=false),
 *     @OA\Property(property="starts_at", type="string", format="date-time"),
 *     @OA\Property(property="ends_at", type="string", format="date-time", nullable=true),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 *
 * @OA\Schema(
 *     schema="Tutorial",
 *     title="Tutorial",
 *     description="Tutorial model",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="title", type="string", example="Dart Basics for Beginners"),
 *     @OA\Property(property="slug", type="string", example="dart-basics-beginners"),
 *     @OA\Property(property="excerpt", type="string", example="Learn the fundamental techniques of dart throwing"),
 *     @OA\Property(property="content", type="string", example="Full tutorial content here..."),
 *     @OA\Property(property="featured_image_url", type="string", nullable=true, example="https://example.com/tutorial.jpg"),
 *     @OA\Property(property="category", type="string", example="Basics"),
 *     @OA\Property(property="difficulty", type="string", enum={"beginner", "intermediate", "advanced"}, example="beginner"),
 *     @OA\Property(property="is_published", type="boolean", example=true),
 *     @OA\Property(property="views", type="integer", example=1250),
 *     @OA\Property(property="order", type="integer", example=1),
 *     @OA\Property(property="published_at", type="string", format="date-time"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */
class Schemas
{
    // This class serves as a container for OpenAPI schema definitions
} 