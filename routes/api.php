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
use App\Http\Controllers\API\Admin\PromotionController as AdminPromotionController;
use App\Http\Controllers\API\PromotionController;
use App\Http\Controllers\Api\Admin\TutorialController as AdminTutorialController;
use App\Http\Controllers\Api\Admin\ContactMessageController as AdminContactMessageController;
use App\Http\Controllers\Api\Admin\AboutPageController;
use App\Http\Controllers\Api\Admin\NewsletterController as AdminNewsletterController;
use App\Http\Controllers\API\Admin\ImageUploadController;
use App\Http\Controllers\Api\AboutUsController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\FavoriteProductController;
use App\Http\Controllers\Api\TutorialController;
use App\Http\Controllers\Api\CheckoutController;
use App\Http\Controllers\Api\GuestCheckoutController;
use App\Http\Controllers\API\StripeController;
use App\Http\Controllers\API\StripeWebhookController;
use App\Http\Controllers\Api\NewsletterController;
use App\Http\Controllers\API\PrivacyPolicyController;
use App\Http\Controllers\API\TermsOfServiceController;
use App\Http\Controllers\API\SocialAuthController;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\Api\ShippingController;
use App\Http\Controllers\API\SwaggerController;
use App\Http\Controllers\API\SwaggerSchemasController;

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
Route::get('/products/latest', [ProductController::class, 'latest']);
Route::get('/products/{id}', [ProductController::class, 'show']);
Route::get('/products/{productId}/reviews', [UserReviewController::class, 'getProductReviews']);

// Reviews API - Public endpoint for latest reviews
Route::get('/reviews/latest', [UserReviewController::class, 'getLatestReviews']);

// Categories API
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/statistics', [CategoryController::class, 'statistics']);
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

// Social Auth API
Route::get('/auth/google/redirect', [SocialAuthController::class, 'redirectToGoogle']);
Route::get('/auth/google/callback', [SocialAuthController::class, 'handleGoogleCallback']);
Route::post('/auth/google/login', [SocialAuthController::class, 'loginWithGoogle']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    
    // User orders
    Route::get('/orders/my-orders', [UserOrderController::class, 'myOrders']);
    Route::get('/orders/my-orders/{id}', [UserOrderController::class, 'show']);
    
    // User reviews
    Route::get('/reviews/my-reviews', [UserReviewController::class, 'myReviews']);
    Route::get('/reviews/my-reviews/{id}', [UserReviewController::class, 'show']);
    Route::post('/products/{productId}/reviews', [UserReviewController::class, 'store']);
    Route::put('/reviews/{reviewId}', [UserReviewController::class, 'update']);
    Route::delete('/reviews/{reviewId}', [UserReviewController::class, 'destroy']);
    Route::get('/products/{productId}/can-review', [UserReviewController::class, 'canReview']);
    
    // Favorite products
    Route::get('/favorites', [FavoriteProductController::class, 'index']);
    Route::post('/favorites/{product}', [FavoriteProductController::class, 'toggle']);
    Route::get('/favorites/check/{product}', [FavoriteProductController::class, 'check']);
    
    // Shipping methods for authenticated users
    Route::get('/user/shipping-methods', [ShippingController::class, 'userShippingMethods']);
    
    // Checkout routes
    Route::post('/checkout', [CheckoutController::class, 'store']);
    
    // Stripe payment routes for authenticated users
    Route::prefix('stripe')->group(function () {
        Route::post('/create-checkout-session', [StripeController::class, 'createCheckoutSession']);
        Route::post('/create-payment-intent', [StripeController::class, 'createPaymentIntent']);
        Route::post('/confirm-payment', [StripeController::class, 'confirmPayment']);
    });
});

// Password Reset API
Route::post('/forgot-password', [PasswordResetController::class, 'forgotPassword']);
Route::post('/validate-reset-token', [PasswordResetController::class, 'validateResetToken']);
Route::post('/reset-password', [PasswordResetController::class, 'resetPassword']);

// Email Verification API - use web middleware for session sharing
Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])
    ->middleware(['web', 'signed', 'throttle:6,1'])
    ->name('api.verification.verify');

Route::middleware('auth:sanctum')->group(function () {
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

// Newsletter API
Route::prefix('newsletter')->group(function () {
    Route::post('/subscribe', [NewsletterController::class, 'subscribe']);
    Route::get('/verify', [NewsletterController::class, 'verify']);
    Route::post('/unsubscribe', [NewsletterController::class, 'unsubscribe']);
    Route::post('/status', [NewsletterController::class, 'status']);
    

});

// Guest Checkout API (for non-authenticated users)
Route::middleware(['web'])->group(function () {
    Route::post('/guest-checkout', GuestCheckoutController::class);
});

// Shipping methods API - public access
Route::get('/shipping-methods', [ShippingController::class, 'index']);

// Guest Stripe payment routes
Route::prefix('guest-stripe')->group(function () {
    Route::post('/create-checkout-session', [StripeController::class, 'createGuestCheckoutSession']);
    Route::post('/create-payment-intent', [StripeController::class, 'createGuestPaymentIntent']);
    Route::post('/confirm-payment', [StripeController::class, 'confirmGuestPayment']);
});

// Stripe webhook and success handling (public routes)
Route::prefix('stripe')->group(function () {
    Route::post('/success', [StripeController::class, 'handleCheckoutSuccess']);
    Route::post('/test-card', [StripeController::class, 'testCardValidation']);
    Route::post('/check-status', [StripeController::class, 'checkPaymentStatus']);
    Route::post('/webhook', [StripeWebhookController::class, 'handleWebhook']);
});

// Public order endpoint for success page
Route::get('/orders/{order}', [CheckoutController::class, 'showOrder']);

// Public Promotions API
Route::prefix('promotions')->group(function () {
    Route::get('/', [PromotionController::class, 'indexPublic']);
    Route::get('/featured', [PromotionController::class, 'featured']);
    Route::get('/{promotion}', [PromotionController::class, 'showPublic']);
    Route::get('/{promotion}/products', [PromotionController::class, 'getPromotionProducts']);
});

// Admin API Routes
Route::middleware(['auth:sanctum', 'role:admin'])->prefix('admin')->group(function () {

    
    // Dashboard statistics
    Route::get('/dashboard', [DashboardController::class, 'index']);
    
    // Image uploads
    Route::post('/upload/image/tutorial', [ImageUploadController::class, 'uploadTutorialImage']);
    
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
    Route::post('/users/{id}/verify', [UserController::class, 'verify']);
    Route::delete('/users/{id}/force', [UserController::class, 'forceDestroy']);
    
    // Promotions management
    Route::apiResource('/promotions', AdminPromotionController::class);
    Route::post('/promotions/{promotion}/toggle-active', [AdminPromotionController::class, 'toggleActive']);
    Route::post('/promotions/{promotion}/toggle-featured', [AdminPromotionController::class, 'toggleFeatured']);
    Route::post('/promotions/{id}/attach-products', [AdminPromotionController::class, 'attachProducts']);
    Route::post('/promotions/{id}/detach-products', [AdminPromotionController::class, 'detachProducts']);
    Route::post('/promotions/update-order', [AdminPromotionController::class, 'updateOrder']);
    Route::get('/available-products', [AdminPromotionController::class, 'getAvailableProducts']);
    
    // Orders management
    Route::apiResource('/orders', OrderController::class);
    Route::put('/orders/{order}/status', [OrderController::class, 'updateStatus']);
    
    // Reviews management
    Route::get('/reviews/form-data', [AdminReviewController::class, 'getFormData']);
    Route::get('/reviews/featured-count', [AdminReviewController::class, 'getFeaturedCount']);
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
    
    // Newsletter management
    Route::get('/newsletter', [AdminNewsletterController::class, 'index']);
    Route::delete('/newsletter/{newsletter}', [AdminNewsletterController::class, 'destroy']);
    Route::get('/newsletter/export', [AdminNewsletterController::class, 'export']);
    Route::get('/newsletter/stats', [AdminNewsletterController::class, 'stats']);
    
    // Privacy Policy management
    Route::apiResource('/privacy-policies', \App\Http\Controllers\API\Admin\PrivacyPolicyController::class);
    Route::post('/privacy-policies/{privacyPolicy}/set-active', [\App\Http\Controllers\API\Admin\PrivacyPolicyController::class, 'setActive']);
    Route::get('/privacy-policies/users/without-acceptance', [\App\Http\Controllers\API\Admin\PrivacyPolicyController::class, 'getUsersWithoutAcceptance']);
    Route::get('/privacy-policies/stats/acceptance', [\App\Http\Controllers\API\Admin\PrivacyPolicyController::class, 'getAcceptanceStats']);
    
    // Terms of Service management
    Route::apiResource('/terms-of-service', \App\Http\Controllers\API\Admin\TermsOfServiceController::class);
    Route::post('/terms-of-service/{termsOfService}/set-active', [\App\Http\Controllers\API\Admin\TermsOfServiceController::class, 'setActive']);
    Route::get('/terms-of-service/users/without-acceptance', [\App\Http\Controllers\API\Admin\TermsOfServiceController::class, 'getUsersWithoutAcceptance']);
    Route::get('/terms-of-service/stats/acceptance', [\App\Http\Controllers\API\Admin\TermsOfServiceController::class, 'getAcceptanceStats']);
});

// Privacy Policy API
Route::get('/privacy-policy', [PrivacyPolicyController::class, 'show']);

// Privacy Policy acceptance for authenticated users
Route::middleware('auth:sanctum')->post('/privacy-policy/accept', [PrivacyPolicyController::class, 'accept']);

// Terms of Service API
Route::get('/terms-of-service', [TermsOfServiceController::class, 'show']);

// Terms of Service acceptance for authenticated users
Route::middleware('auth:sanctum')->post('/terms-of-service/accept', [TermsOfServiceController::class, 'accept']);