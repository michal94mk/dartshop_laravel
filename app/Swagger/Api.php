<?php

namespace App\Swagger;

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="DartShop API Documentation",
 *     description="Comprehensive API documentation for DartShop Laravel e-commerce platform. This API provides endpoints for managing products, orders, users, shopping cart, payments, and administrative functions for a dart equipment store.",
 *     @OA\Contact(
 *         email="admin@dartshop.com",
 *         name="DartShop API Support",
 *         url="https://dartshop.com/support"
 *     ),
 *     @OA\License(
 *         name="MIT",
 *         url="https://opensource.org/licenses/MIT"
 *     )
 * )
 *
 * @OA\Server(
 *     url=L5_SWAGGER_CONST_HOST,
 *     description="DartShop API Server - Development Environment"
 * )
 *
 * @OA\Server(
 *     url="https://api.dartshop.com",
 *     description="DartShop API Server - Production Environment"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     description="Laravel Sanctum Bearer Token Authentication. Include the token in the Authorization header as 'Bearer {token}'"
 * )
 *
 * @OA\Tag(
 *     name="Authentication",
 *     description="User authentication and authorization endpoints including login, registration, password reset, and social authentication"
 * )
 * 
 * @OA\Tag(
 *     name="Products",
 *     description="Product catalog management endpoints for browsing, searching, and retrieving product information"
 * )
 * 
 * @OA\Tag(
 *     name="Categories",
 *     description="Product category management endpoints for organizing and filtering products"
 * )
 * 
 * @OA\Tag(
 *     name="Cart",
 *     description="Shopping cart management endpoints for adding, updating, and removing items from user's cart"
 * )
 * 
 * @OA\Tag(
 *     name="Orders",
 *     description="Order processing and management endpoints including checkout, order history, and order tracking"
 * )
 * 
 * @OA\Tag(
 *     name="Payment",
 *     description="Payment processing endpoints using Stripe integration for secure transactions"
 * )
 * 
 * @OA\Tag(
 *     name="Admin",
 *     description="Administrative endpoints for managing products, users, orders, and system settings (requires admin privileges)"
 * )
 * 
 * @OA\Tag(
 *     name="Reviews",
 *     description="Product review system endpoints for customers to rate and review purchased products"
 * )
 * 
 * @OA\Tag(
 *     name="User Management",
 *     description="User profile management, favorites, and account settings endpoints"
 * )
 * 
 * @OA\Tag(
 *     name="Content",
 *     description="Content management endpoints for tutorials, contact forms, newsletter, and static pages"
 * )
 *
 * @OA\ExternalDocumentation(
 *     description="DartShop API Guide",
 *     url="https://docs.dartshop.com/api"
 * )
 */
class Api
{
    // This class serves as a container for main OpenAPI documentation annotations
} 