<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DebugController;
use App\Http\Controllers\Api\PasswordResetController;
use App\Http\Controllers\Api\EmailVerificationController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\Admin\BaseAdminController;
use App\Http\Controllers\Api\Admin\DashboardController;
use App\Http\Controllers\Api\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Api\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Api\Admin\BrandController;
use App\Http\Controllers\Api\Admin\UserController;
use App\Http\Controllers\Api\Admin\OrderController;

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

// Products API - w wersji REST
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
});

// Auth API
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
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

// Other API endpoints
Route::get('/promotions', [App\Http\Controllers\Frontend\PromotionController::class, 'apiIndex']);
Route::get('/tutorials', [App\Http\Controllers\Frontend\TutorialController::class, 'apiIndex']);
Route::get('/tutorials/{id}', [App\Http\Controllers\Frontend\TutorialController::class, 'apiShow']);

// Debug routes
Route::get('/debug/products', [DebugController::class, 'checkProducts']);
Route::get('/debug/routes', [DebugController::class, 'checkRoutes']);
Route::get('/debug', [DebugController::class, 'debug']);

// Simple test endpoint
Route::get('/test', function() {
    return response()->json([
        'success' => true,
        'message' => 'API test endpoint working correctly',
        'time' => now()->toDateTimeString()
    ]);
});

// Admin API Routes
Route::middleware(['auth:sanctum', 'role:admin'])->prefix('admin')->group(function () {
    // Dashboard statistics
    Route::get('/dashboard', [DashboardController::class, 'index']);
    
    // Products management
    Route::apiResource('/products', AdminProductController::class);
    
    // Categories management
    Route::apiResource('/categories', AdminCategoryController::class);
    
    // Brands management
    Route::apiResource('/brands', BrandController::class);
    
    // Users management
    Route::apiResource('/users', UserController::class);
    
    // Promotions management - using full path since controller doesn't exist yet
    Route::apiResource('/promotions', App\Http\Controllers\Api\Admin\PromotionController::class);
    
    // Orders management
    Route::apiResource('/orders', OrderController::class);
    Route::put('/orders/{order}/status', [OrderController::class, 'updateStatus']);
    Route::get('/orders/{order}/invoice', [OrderController::class, 'invoice']);
    
    // Reviews management - using full path since controller doesn't exist yet
    Route::apiResource('/reviews', App\Http\Controllers\Api\Admin\ReviewController::class);
    Route::post('/reviews/{review}/approve', [App\Http\Controllers\Api\Admin\ReviewController::class, 'approve']);
    Route::post('/reviews/{review}/reject', [App\Http\Controllers\Api\Admin\ReviewController::class, 'reject']);
    
    // Payments management - using full path since controller doesn't exist yet
    Route::apiResource('/payments', App\Http\Controllers\Api\Admin\PaymentController::class);
    
    // Roles and permissions - using full path since controllers don't exist yet
    Route::apiResource('/roles', App\Http\Controllers\Api\Admin\RoleController::class);
    Route::apiResource('/permissions', App\Http\Controllers\Api\Admin\PermissionController::class);
});
