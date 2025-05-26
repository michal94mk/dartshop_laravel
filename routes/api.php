<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PasswordResetController;
use App\Http\Controllers\Api\EmailVerificationController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\UserOrderController;
use App\Http\Controllers\Api\UserReviewController;
use App\Http\Controllers\Api\Admin\BaseAdminController;
use App\Http\Controllers\Api\Admin\DashboardController;
use App\Http\Controllers\Api\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Api\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Api\Admin\BrandController;
use App\Http\Controllers\Api\Admin\UserController;
use App\Http\Controllers\Api\Admin\OrderController;
use App\Http\Controllers\Api\Admin\ReviewController as AdminReviewController;
use App\Http\Controllers\Api\Admin\PromotionController;
use App\Http\Controllers\Api\Admin\TutorialController as AdminTutorialController;
use App\Http\Controllers\Api\Admin\ContactMessageController as AdminContactMessageController;
use App\Http\Controllers\Api\Admin\AboutPageController;
use App\Http\Controllers\Api\Admin\ImageUploadController;
use App\Http\Controllers\Api\AboutUsController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\FavoriteProductController;
use App\Http\Controllers\Api\TutorialController;
use App\Http\Controllers\Api\CheckoutController;
use App\Http\Controllers\Api\GuestCheckoutController;
use App\Http\Controllers\Api\StripeController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Products API
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/featured', [ProductController::class, 'featured']);
Route::get('/products/{id}', [ProductController::class, 'show']);

// Categories API
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{id}', [CategoryController::class, 'show']);
Route::get('/categories/{id}/products', [CategoryController::class, 'products']);

// Cart API - Only for authenticated users
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/cart', [CartController::class, 'index']);
    Route::post('/cart', [CartController::class, 'store']);
    Route::put('/cart/{cartItem}', [CartController::class, 'update']);
    Route::delete('/cart/{cartItem}', [CartController::class, 'destroy']);
    Route::post('/cart/sync', [CartController::class, 'sync']);
    Route::delete('/cart', [CartController::class, 'clear']);
});

// Auth API
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    
    // User orders
    Route::get('/orders/my-orders', [UserOrderController::class, 'myOrders']);
    Route::get('/orders/my-orders/{id}', [UserOrderController::class, 'show']);
    
    // User reviews
    Route::get('/reviews/my-reviews', [UserReviewController::class, 'myReviews']);
    Route::get('/reviews/my-reviews/{id}', [UserReviewController::class, 'show']);
    
    // Favorite products
    Route::get('/favorites', [FavoriteProductController::class, 'index']);
    Route::post('/favorites/{product}', [FavoriteProductController::class, 'toggle']);
    Route::get('/favorites/check/{product}', [FavoriteProductController::class, 'check']);
    
    // Checkout routes
    Route::prefix('checkout')->group(function () {
        Route::get('/', [CheckoutController::class, 'index']);
        Route::post('/process', [CheckoutController::class, 'process']);
    });
    
    // Stripe payment routes for authenticated users
    Route::prefix('stripe')->group(function () {
        Route::post('/create-checkout-session', [StripeController::class, 'createCheckoutSession']);
        Route::post('/create-payment-intent', [StripeController::class, 'createPaymentIntent']);
        Route::post('/confirm-payment', [StripeController::class, 'confirmPayment']);
    });
});

// Password Reset API
Route::post('/forgot-password', [PasswordResetController::class, 'forgotPassword']);
Route::post('/reset-password', [PasswordResetController::class, 'resetPassword']);

// Email Verification API
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/email/verify/{id}', [EmailVerificationController::class, 'verify'])->name('verification.verify');
    Route::post('/email/verification-notification', [EmailVerificationController::class, 'sendVerificationEmail']);
});

// User Profile API
Route::middleware('auth:sanctum')->group(function () {
    Route::put('/user/profile', [ProfileController::class, 'update']);
    Route::put('/user/password', [ProfileController::class, 'updatePassword']);
});

// Tutorial routes
Route::get('/tutorials', [TutorialController::class, 'index']);
Route::get('/tutorials/{slug}', [TutorialController::class, 'show']);

// Public About page route
Route::get('/about', [AboutUsController::class, 'index']);

// Contact form submission route
Route::post('/contact', [ContactController::class, 'store']);

// Guest Checkout API (for non-authenticated users)
Route::prefix('guest-checkout')->group(function () {
    Route::post('/', [GuestCheckoutController::class, 'index']);
    Route::post('/process', [GuestCheckoutController::class, 'process']);
});

// Guest Stripe payment routes
Route::prefix('guest-stripe')->group(function () {
    Route::post('/create-checkout-session', [StripeController::class, 'createGuestCheckoutSession']);
    Route::post('/create-payment-intent', [StripeController::class, 'createGuestPaymentIntent']);
    Route::post('/confirm-payment', [StripeController::class, 'confirmGuestPayment']);
});

// Stripe webhook and success handling (public routes)
Route::prefix('stripe')->group(function () {
    Route::post('/success', [StripeController::class, 'handleCheckoutSuccess']);
});

// Admin API Routes
Route::middleware(['auth:sanctum', 'role:admin'])->prefix('admin')->group(function () {
    // Dashboard statistics
    Route::get('/dashboard', [DashboardController::class, 'index']);
    
    // Image uploads
    Route::post('/upload/image/about', [ImageUploadController::class, 'uploadAboutImage']);
    
    // Products management - custom routes must be defined BEFORE the resource route
    Route::get('/products/form-data', [AdminProductController::class, 'getFormData']);
    // Standard CRUD routes for products
    Route::apiResource('/products', AdminProductController::class);
    
    // Categories management
    Route::apiResource('/categories', AdminCategoryController::class);
    
    // Brands management
    Route::apiResource('/brands', BrandController::class);
    
    // Users management
    Route::apiResource('/users', UserController::class);
    
    // Promotions management
    Route::apiResource('/promotions', PromotionController::class);
    Route::get('/promotions/generate-code', [PromotionController::class, 'generateCode']);
    
    // Orders management
    Route::apiResource('/orders', OrderController::class);
    Route::put('/orders/{order}/status', [OrderController::class, 'updateStatus']);
    Route::get('/orders/{order}/invoice', [OrderController::class, 'invoice']);
    
    // Reviews management
    Route::get('/reviews/form-data', [AdminReviewController::class, 'getFormData']);
    Route::apiResource('/reviews', AdminReviewController::class);
    Route::post('/reviews/{review}/approve', [AdminReviewController::class, 'approve']);
    Route::post('/reviews/{review}/reject', [AdminReviewController::class, 'reject']);
    Route::post('/reviews/{review}/toggle-featured', [AdminReviewController::class, 'toggleFeatured']);
    
    // Tutorials management
    Route::apiResource('/tutorials', AdminTutorialController::class);
    
    // Contact messages management
    Route::apiResource('/contact-messages', AdminContactMessageController::class)->only(['index', 'show', 'destroy']);
    Route::patch('/contact-messages/{id}/status', [AdminContactMessageController::class, 'updateStatus']);
    Route::patch('/contact-messages/{id}/mark-as-read', [AdminContactMessageController::class, 'markAsRead']);
    Route::patch('/contact-messages/{id}/notes', [AdminContactMessageController::class, 'updateNotes']);
    Route::post('/contact-messages/{id}/respond', [AdminContactMessageController::class, 'respond']);
    
    // About page management
    Route::get('/about', [AboutPageController::class, 'index']);
    Route::put('/about', [AboutPageController::class, 'update']);
    Route::post('/about/upload-image', [AboutPageController::class, 'uploadImage']);
});
