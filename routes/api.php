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
use App\Http\Controllers\Api\Admin\ReviewController;
use App\Http\Controllers\Api\Admin\PromotionController;
use App\Http\Controllers\Api\Admin\TutorialController;
use App\Http\Controllers\Api\Admin\ContactMessageController;
use App\Http\Controllers\Api\Admin\AboutPageController;

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
Route::get('/promotions', function() {
    return response()->json([
        'data' => []
    ]);
});
Route::get('/tutorials', function() {
    return response()->json([
        'data' => []
    ]);
});
Route::get('/tutorials/{id}', function($id) {
    return response()->json([
        'data' => null
    ]);
});

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

// Test products endpoint
Route::get('/test-products', function() {
    return response()->json([
        'success' => true,
        'products' => \App\Models\Product::with(['category', 'brand'])->take(5)->get()
    ]);
});

// Test auth endpoints
Route::get('/test-auth', function() {
    return response()->json([
        'success' => true,
        'message' => 'You are authenticated',
        'user' => \Illuminate\Support\Facades\Auth::user()
    ]);
})->middleware('auth:sanctum');

Route::get('/test-admin', function() {
    return response()->json([
        'success' => true,
        'message' => 'You are an admin',
        'user' => \Illuminate\Support\Facades\Auth::user()
    ]);
})->middleware(['auth:sanctum', 'role:admin']);

// Debug route for users
Route::get('/debug-users', function() {
    $users = \App\Models\User::all();
    $usersArray = $users->map(function($user) {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'is_admin' => $user->is_admin,
            'email_verified_at' => $user->email_verified_at,
            'created_at' => $user->created_at
        ];
    });
    
    return response()->json([
        'success' => true,
        'total_users' => $users->count(),
        'users' => $usersArray
    ]);
})->middleware(['auth:sanctum', 'role:admin']);

// Admin API Routes
Route::middleware(['auth:sanctum', 'role:admin'])->prefix('admin')->group(function () {
    // Dashboard statistics
    Route::get('/dashboard', [DashboardController::class, 'index']);
    
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
    Route::get('/reviews/form-data', [ReviewController::class, 'getFormData']);
    Route::apiResource('/reviews', ReviewController::class);
    Route::post('/reviews/{review}/approve', [ReviewController::class, 'approve']);
    Route::post('/reviews/{review}/reject', [ReviewController::class, 'reject']);
    Route::post('/reviews/{review}/toggle-featured', [ReviewController::class, 'toggleFeatured']);
    
    // Tutorials management
    Route::apiResource('/tutorials', TutorialController::class);
    
    // Contact messages management
    Route::apiResource('/contact-messages', ContactMessageController::class)->only(['index', 'show', 'destroy']);
    Route::patch('/contact-messages/{id}/status', [ContactMessageController::class, 'updateStatus']);
    Route::patch('/contact-messages/{id}/mark-as-read', [ContactMessageController::class, 'markAsRead']);
    Route::patch('/contact-messages/{id}/notes', [ContactMessageController::class, 'updateNotes']);
    Route::post('/contact-messages/{id}/respond', [ContactMessageController::class, 'respond']);
    
    // About page management
    Route::get('/about', [AboutPageController::class, 'index']);
    Route::put('/about', [AboutPageController::class, 'update']);
});
