<?php

namespace App;

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
 * @OA\Tag(name="Authentication", description="API Endpoints for user authentication")
 * @OA\Tag(name="Products", description="API Endpoints for product management")
 * @OA\Tag(name="Orders", description="API Endpoints for order management")
 * @OA\Tag(name="Cart", description="API Endpoints for shopping cart")
 * @OA\Tag(name="Users", description="API Endpoints for user management")
 * @OA\Tag(name="Admin", description="API Endpoints for admin panel")
 *
 * @OA\Schema(
 *     schema="Product",
 *     title="Product",
 *     description="Product model",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Harrows Supergrip Ultra"),
 *     @OA\Property(property="description", type="string", example="Professional dart flights"),
 *     @OA\Property(property="price", type="number", format="float", example=29.99),
 *     @OA\Property(property="image_url", type="string", nullable=true, example="https://example.com/image.jpg"),
 *     @OA\Property(property="is_featured", type="boolean", example=false),
 *     @OA\Property(property="is_active", type="boolean", example=true),
 *     @OA\Property(property="brand_id", type="integer", example=1),
 *     @OA\Property(property="category_id", type="integer", example=1),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time"),
 *     @OA\Property(property="category", ref="#/components/schemas/Category"),
 *     @OA\Property(property="brand", ref="#/components/schemas/Brand"),
 *     @OA\Property(property="average_rating", type="number", format="float", example=4.5),
 *     @OA\Property(property="reviews_count", type="integer", example=10),
 *     @OA\Property(property="promotion_price", type="number", format="float", nullable=true, example=24.99),
 *     @OA\Property(property="savings", type="number", format="float", example=5.00),
 *     @OA\Property(property="promotion", ref="#/components/schemas/Promotion", nullable=true)
 * )
 *
 * @OA\Schema(
 *     schema="Order",
 *     title="Order",
 *     description="Order model representing customer orders in the e-commerce system",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="user_id", type="integer", example=1),
 *     @OA\Property(property="order_number", type="string", example="ZAM-000001"),
 *     @OA\Property(property="status", type="string", enum={"pending","confirmed","shipped","delivered","cancelled"}, example="pending"),
 *     @OA\Property(property="payment_status", type="string", enum={"pending","completed","failed"}, example="pending"),
 *     @OA\Property(property="first_name", type="string", example="John"),
 *     @OA\Property(property="last_name", type="string", example="Doe"),
 *     @OA\Property(property="email", type="string", format="email", example="john@example.com"),
 *     @OA\Property(property="phone", type="string", example="+48123456789"),
 *     @OA\Property(property="address", type="string", example="ul. Przykładowa 1"),
 *     @OA\Property(property="city", type="string", example="Warszawa"),
 *     @OA\Property(property="postal_code", type="string", example="00-001"),
 *     @OA\Property(property="country", type="string", example="Poland"),
 *     @OA\Property(property="subtotal", type="number", format="float", example=49.99),
 *     @OA\Property(property="shipping_cost", type="number", format="float", example=9.99),
 *     @OA\Property(property="discount", type="number", format="float", example=0.00),
 *     @OA\Property(property="total", type="number", format="float", example=59.98),
 *     @OA\Property(property="payment_method", type="string", example="stripe"),
 *     @OA\Property(property="shipping_method", type="string", example="standard"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time"),
 *     @OA\Property(property="user", ref="#/components/schemas/User"),
 *     @OA\Property(property="items", ref="#/components/schemas/OrderItem"),
 *     @OA\Property(property="payment", ref="#/components/schemas/Payment"),
 *     @OA\Property(property="full_name", type="string", example="John Doe"),
 *     @OA\Property(property="full_address", type="string", example="ul. Przykładowa 1, 00-001 Warszawa, Poland")
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
 *     @OA\Property(property="email", type="string", format="email", example="user@example.com"),
 *     @OA\Property(property="email_verified_at", type="string", format="date-time", nullable=true),
 *     @OA\Property(property="is_admin", type="boolean", example=false),
 *     @OA\Property(property="google_id", type="string", nullable=true, example="123456789"),
 *     @OA\Property(property="avatar", type="string", nullable=true, example="https://example.com/avatar.jpg"),
 *     @OA\Property(property="privacy_policy_accepted", type="boolean", example=true),
 *     @OA\Property(property="privacy_policy_accepted_at", type="string", format="date-time", nullable=true),
 *     @OA\Property(property="terms_of_service_accepted", type="boolean", example=true),
 *     @OA\Property(property="terms_of_service_accepted_at", type="string", format="date-time", nullable=true),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time"),
 *     @OA\Property(property="role", type="string", example="user"),
 *     @OA\Property(property="full_name", type="string", example="John Doe"),
 *     @OA\Property(property="display_name", type="string", example="John Doe"),
 *     @OA\Property(property="is_google_user", type="boolean", example=false)
 * )
 *
 * @OA\Schema(
 *     schema="Category",
 *     title="Category",
 *     description="Product category model",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Dart Flights"),
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
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 *
 * @OA\Schema(
 *     schema="OrderItem",
 *     title="OrderItem",
 *     description="Order item model representing individual items within an order",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="order_id", type="integer", example=1),
 *     @OA\Property(property="product_id", type="integer", example=1),
 *     @OA\Property(property="product_name", type="string", example="Harrows Supergrip Ultra"),
 *     @OA\Property(property="quantity", type="integer", example=2),
 *     @OA\Property(property="product_price", type="number", format="float", example=29.99),
 *     @OA\Property(property="total_price", type="number", format="float", example=59.98),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time"),
 *     @OA\Property(property="order", ref="#/components/schemas/Order"),
 *     @OA\Property(property="product", ref="#/components/schemas/Product")
 * )
 *
 * @OA\Schema(
 *     schema="Payment",
 *     title="Payment",
 *     description="Payment model representing payment transactions in the e-commerce system",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="order_id", type="integer", example=1),
 *     @OA\Property(property="user_id", type="integer", example=1),
 *     @OA\Property(property="payment_method", type="string", example="stripe"),
 *     @OA\Property(property="payment_status", type="string", enum={"pending","completed","failed"}, example="completed"),
 *     @OA\Property(property="amount", type="number", format="float", example=59.98),
 *     @OA\Property(property="currency", type="string", example="PLN"),
 *     @OA\Property(property="transaction_id", type="string", example="txn_123456789"),
 *     @OA\Property(property="payment_intent_id", type="string", example="pi_123456789"),
 *     @OA\Property(property="payment_data", type="object", example={"stripe_session_id":"cs_123456789"}),
 *     @OA\Property(property="paid_at", type="string", format="date-time", nullable=true),
 *     @OA\Property(property="failure_reason", type="string", nullable=true),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time"),
 *     @OA\Property(property="order", ref="#/components/schemas/Order"),
 *     @OA\Property(property="user", ref="#/components/schemas/User")
 * )
 *
 * @OA\Schema(
 *     schema="CartItem",
 *     title="CartItem",
 *     description="Shopping cart item model",
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
 *     schema="ShippingAddress",
 *     title="ShippingAddress",
 *     description="Shipping address model for user delivery information",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="user_id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="John Doe"),
 *     @OA\Property(property="address", type="string", example="ul. Przykładowa 1"),
 *     @OA\Property(property="city", type="string", example="Warszawa"),
 *     @OA\Property(property="state", type="string", example="Mazowieckie"),
 *     @OA\Property(property="postal_code", type="string", example="00-001"),
 *     @OA\Property(property="country", type="string", example="Poland"),
 *     @OA\Property(property="phone", type="string", example="+48123456789"),
 *     @OA\Property(property="is_default", type="boolean", example=true),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time"),
 *     @OA\Property(property="user", ref="#/components/schemas/User")
 * )
 *
 * @OA\Schema(
 *     schema="Review",
 *     title="Review",
 *     description="Product review model",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="user_id", type="integer", example=1),
 *     @OA\Property(property="product_id", type="integer", example=1),
 *     @OA\Property(property="rating", type="integer", example=5),
 *     @OA\Property(property="title", type="string", example="Great product!"),
 *     @OA\Property(property="content", type="string", example="This is an excellent product."),
 *     @OA\Property(property="is_approved", type="boolean", example=true),
 *     @OA\Property(property="is_featured", type="boolean", example=false),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time"),
 *     @OA\Property(property="user", ref="#/components/schemas/User"),
 *     @OA\Property(property="product", ref="#/components/schemas/Product")
 * )
 *
 * @OA\Schema(
 *     schema="Promotion",
 *     title="Promotion",
 *     description="Product promotion model",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="title", type="string", example="Summer Sale"),
 *     @OA\Property(property="description", type="string", example="Get 20% off on selected items"),
 *     @OA\Property(property="discount_type", type="string", enum={"percentage", "fixed"}, example="percentage"),
 *     @OA\Property(property="discount_value", type="number", format="float", example=20.0),
 *     @OA\Property(property="start_date", type="string", format="date-time"),
 *     @OA\Property(property="end_date", type="string", format="date-time"),
 *     @OA\Property(property="is_active", type="boolean", example=true),
 *     @OA\Property(property="badge_text", type="string", example="20% OFF"),
 *     @OA\Property(property="badge_color", type="string", example="#ff0000"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 *
 * @OA\Schema(
 *     schema="ProductReservation",
 *     title="ProductReservation",
 *     description="Product reservation model for temporary product holds",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="product_id", type="integer", example=1),
 *     @OA\Property(property="user_id", type="integer", example=1),
 *     @OA\Property(property="session_id", type="string", example="session_123456789"),
 *     @OA\Property(property="quantity", type="integer", example=2),
 *     @OA\Property(property="expires_at", type="string", format="date-time"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time"),
 *     @OA\Property(property="product", ref="#/components/schemas/Product"),
 *     @OA\Property(property="user", ref="#/components/schemas/User")
 * )
 *
 * @OA\Schema(
 *     schema="ContactMessage",
 *     title="ContactMessage",
 *     description="Contact form message model",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="John Doe"),
 *     @OA\Property(property="email", type="string", format="email", example="john@example.com"),
 *     @OA\Property(property="subject", type="string", example="Question about products"),
 *     @OA\Property(property="message", type="string", example="I have a question about your products."),
 *     @OA\Property(property="is_read", type="boolean", example=false),
 *     @OA\Property(property="read_at", type="string", format="date-time", nullable=true),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time"),
 *     @OA\Property(property="status_label", type="string", example="Nieprzeczytana"),
 *     @OA\Property(property="status_color", type="string", example="yellow")
 * )
 *
 * @OA\Schema(
 *     schema="AboutUs",
 *     title="AboutUs",
 *     description="About us page content model",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="title", type="string", example="O nas"),
 *     @OA\Property(property="content", type="string", example="Treść strony O nas..."),
 *     @OA\Property(property="header_style", type="string", example="default"),
 *     @OA\Property(property="header_margin", type="string", example="normal"),
 *     @OA\Property(property="content_layout", type="string", example="single-column"),
 *     @OA\Property(property="meta_title", type="string", example="O nas - DartShop"),
 *     @OA\Property(property="meta_description", type="string", example="Poznaj naszą historię i misję"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 *
 * @OA\Schema(
 *     schema="PrivacyPolicy",
 *     title="PrivacyPolicy",
 *     description="Privacy policy content model",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="title", type="string", example="Polityka Prywatności"),
 *     @OA\Property(property="content", type="string", example="Treść polityki prywatności..."),
 *     @OA\Property(property="version", type="string", example="1.0"),
 *     @OA\Property(property="effective_date", type="string", format="date", example="2024-01-01"),
 *     @OA\Property(property="is_active", type="boolean", example=true),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 *
 * @OA\Schema(
 *     schema="TermsOfService",
 *     title="TermsOfService",
 *     description="Terms of service content model",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="title", type="string", example="Regulamin"),
 *     @OA\Property(property="content", type="string", example="Treść regulaminu..."),
 *     @OA\Property(property="version", type="string", example="1.0"),
 *     @OA\Property(property="effective_date", type="string", format="date", example="2024-01-01"),
 *     @OA\Property(property="is_active", type="boolean", example=true),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 *
 * @OA\Schema(
 *     schema="Tutorial",
 *     title="Tutorial",
 *     description="Tutorial model",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="title", type="string", example="Podstawy rzucania lotkami"),
 *     @OA\Property(property="slug", type="string", example="podstawy-rzucania-lotek"),
 *     @OA\Property(property="content", type="string", example="Treść tutoriala..."),
 *     @OA\Property(property="image_url", type="string", example="img/tutorials/dart-basics-beginners.jpg"),
 *     @OA\Property(property="order", type="integer", example=1),
 *     @OA\Property(property="is_published", type="boolean", example=true),
 *     @OA\Property(property="excerpt", type="string", example="Krótki opis tutoriala..."),
 *     @OA\Property(property="featured_image_url", type="string", example="http://example.com/img/tutorials/dart-basics-beginners.jpg"),
 *     @OA\Property(property="thumbnail_image_url", type="string", example="http://example.com/img/tutorials/dart-basics-beginners.jpg"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 *
 * @OA\Schema(
 *     schema="NewsletterSubscription",
 *     title="NewsletterSubscription",
 *     description="Newsletter subscription model",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="email", type="string", format="email", example="john@example.com"),
 *     @OA\Property(property="status", type="string", enum={"pending","active","unsubscribed"}, example="active"),
 *     @OA\Property(property="verification_token", type="string", nullable=true),
 *     @OA\Property(property="verified_at", type="string", format="date-time", nullable=true),
 *     @OA\Property(property="unsubscribed_at", type="string", format="date-time", nullable=true),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
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
 *     @OA\Property(property="total", type="integer", example=60)
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
 *     description="Category data for charts",
 *     @OA\Property(property="name", type="string", example="Lotki"),
 *     @OA\Property(property="product_count", type="integer", example=15),
 *     @OA\Property(property="total_sales", type="number", format="float", example=2500.00)
 * )
 *
 * @OA\Schema(
 *     schema="CategoryWithProducts",
 *     title="Category With Products",
 *     description="Category with its products",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Lotki"),
 *     @OA\Property(property="slug", type="string", example="lotki"),
 *     @OA\Property(property="description", type="string", example="Opis kategorii lotki"),
 *     @OA\Property(property="image_url", type="string", example="img/categories/lotki.jpg"),
 *     @OA\Property(property="is_active", type="boolean", example=true),
 *     @OA\Property(property="products_count", type="integer", example=15),
 *     @OA\Property(property="products", type="array", @OA\Items(ref="#/components/schemas/Product")),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 *
 * @OA\Schema(
 *     schema="CategoryDetail",
 *     title="Category Detail",
 *     description="Detailed category information",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Lotki"),
 *     @OA\Property(property="slug", type="string", example="lotki"),
 *     @OA\Property(property="description", type="string", example="Opis kategorii lotki"),
 *     @OA\Property(property="image_url", type="string", example="img/categories/lotki.jpg"),
 *     @OA\Property(property="is_active", type="boolean", example=true),
 *     @OA\Property(property="products_count", type="integer", example=15),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 *
 * @OA\Schema(
 *     schema="TutorialDetail",
 *     title="Tutorial Detail",
 *     description="Detailed tutorial information",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="title", type="string", example="Podstawy rzucania lotkami"),
 *     @OA\Property(property="slug", type="string", example="podstawy-rzucania-lotek"),
 *     @OA\Property(property="content", type="string", example="Treść tutoriala..."),
 *     @OA\Property(property="image_url", type="string", example="img/tutorials/dart-basics-beginners.jpg"),
 *     @OA\Property(property="order", type="integer", example=1),
 *     @OA\Property(property="is_published", type="boolean", example=true),
 *     @OA\Property(property="excerpt", type="string", example="Krótki opis tutoriala..."),
 *     @OA\Property(property="featured_image_url", type="string", example="http://example.com/img/tutorials/dart-basics-beginners.jpg"),
 *     @OA\Property(property="thumbnail_image_url", type="string", example="http://example.com/img/tutorials/dart-basics-beginners.jpg"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 *
 * @OA\Schema(
 *     schema="ErrorResponse",
 *     title="Error Response",
 *     description="Standard error response",
 *     @OA\Property(property="message", type="string", example="Error message"),
 *     @OA\Property(property="errors", type="object", nullable=true),
 *     @OA\Property(property="status", type="integer", example=400)
 * )
 *
 * @OA\Schema(
 *     schema="ValidationError",
 *     title="Validation Error",
 *     description="Validation error response",
 *     @OA\Property(property="message", type="string", example="The given data was invalid."),
 *     @OA\Property(property="errors", type="object", example={
 *         "field_name": {"The field name field is required."}
 *     }),
 *     @OA\Property(property="status", type="integer", example=422)
 * )
 *
 * @OA\Schema(
 *     schema="UnauthorizedError",
 *     title="Unauthorized Error",
 *     description="Unauthorized error response",
 *     @OA\Property(property="message", type="string", example="Unauthenticated."),
 *     @OA\Property(property="status", type="integer", example=401)
 * )
 *
 * @OA\Schema(
 *     schema="ForbiddenError",
 *     title="Forbidden Error",
 *     description="Forbidden error response",
 *     @OA\Property(property="message", type="string", example="Access denied."),
 *     @OA\Property(property="status", type="integer", example=403)
 * )
 *
 * @OA\Schema(
 *     schema="NotFoundError",
 *     title="Not Found Error",
 *     description="Not found error response",
 *     @OA\Property(property="message", type="string", example="Resource not found."),
 *     @OA\Property(property="status", type="integer", example=404)
 * )
 *
 * @OA\Schema(
 *     schema="ServerError",
 *     title="Server Error",
 *     description="Server error response",
 *     @OA\Property(property="message", type="string", example="Internal server error."),
 *     @OA\Property(property="status", type="integer", example=500)
 * )
 *
 * @OA\Schema(
 *     schema="SuccessResponse",
 *     title="Success Response",
 *     description="Standard success response",
 *     @OA\Property(property="message", type="string", example="Operation successful"),
 *     @OA\Property(property="data", type="object", nullable=true),
 *     @OA\Property(property="status", type="integer", example=200)
 * )
 *
 * @OA\Schema(
 *     schema="DashboardStats",
 *     title="Dashboard Stats",
 *     description="Dashboard statistics",
 *     @OA\Property(property="total_orders", type="integer", example=150),
 *     @OA\Property(property="total_revenue", type="number", format="float", example=25000.00),
 *     @OA\Property(property="total_products", type="integer", example=45),
 *     @OA\Property(property="total_users", type="integer", example=120),
 *     @OA\Property(property="recent_orders", type="array", @OA\Items(ref="#/components/schemas/RecentOrder")),
 *     @OA\Property(property="top_products", type="array", @OA\Items(ref="#/components/schemas/TopProduct")),
 *     @OA\Property(property="category_data", type="array", @OA\Items(ref="#/components/schemas/CategoryData")),
 *     @OA\Property(property="sales_data", type="array", @OA\Items(ref="#/components/schemas/SalesData"))
 * )
 */

class SwaggerSchemas
{
    // This class is used to hold all Swagger schema definitions
    // The actual schemas are defined in the docblock above
} 