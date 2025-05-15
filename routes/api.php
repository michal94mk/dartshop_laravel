<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\CartController;
use App\Http\Controllers\API\AuthController;

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

// Cart API
Route::middleware('api')->group(function () {
    Route::get('/cart', [CartController::class, 'apiGetCart']);
    Route::post('/cart/add', [CartController::class, 'apiAddToCart']);
    Route::post('/cart/remove', [CartController::class, 'apiRemoveFromCart']);
    Route::post('/cart/update', [CartController::class, 'apiUpdateCart']);
    Route::post('/cart/clear', [CartController::class, 'apiClearCart']);
});

// Auth API
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::middleware('auth:api')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
});

// Other API endpoints
Route::get('/promotions', [App\Http\Controllers\Frontend\PromotionController::class, 'apiIndex']);
Route::get('/tutorials', [App\Http\Controllers\Frontend\TutorialController::class, 'apiIndex']);
Route::get('/tutorials/{id}', [App\Http\Controllers\Frontend\TutorialController::class, 'apiShow']);
