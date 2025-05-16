<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ContactController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\PaymentController as AdminPaymentController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\PromotionController;
use App\Http\Controllers\Admin\ReviewController as AdminReviewController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\SPAController;
use App\Http\Middleware\RoleMiddleware;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Auth routes
require __DIR__.'/auth.php';

// Admin routes - Admin panel nadal będzie działał w tradycyjnym trybie Blade
Route::prefix('admin')->name('admin.')->middleware(['auth','verified', RoleMiddleware::class.':admin'])->group(function () {
    // Admin dashboard
    Route::get('/', [AdminController::class, 'index'])->name('index');
    
    // Categories management
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::get('/categories/create', [CategoryController::class, 'create'])->name('categories.create');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::get('/categories/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
    Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

    // Brands management
    Route::get('/brands', [BrandController::class, 'index'])->name('brands.index');
    Route::get('/brands/create', [BrandController::class, 'create'])->name('brands.create');
    Route::post('/brands', [BrandController::class, 'store'])->name('brands.store');
    Route::get('/brands/{brand}/edit', [BrandController::class, 'edit'])->name('brands.edit');
    Route::put('/brands/{brand}', [BrandController::class, 'update'])->name('brands.update');
    Route::delete('/brands/{brand}', [BrandController::class, 'destroy'])->name('brands.destroy');

    // Products management
    Route::get('/products', [AdminProductController::class, 'index'])->name('products.index');
    Route::get('/products/create', [AdminProductController::class, 'create'])->name('products.create');
    Route::post('/products', [AdminProductController::class, 'store'])->name('products.store');
    Route::get('/products/{product}', [AdminProductController::class, 'show'])->name('products.show');
    Route::get('/products/{product}/edit', [AdminProductController::class, 'edit'])->name('products.edit');
    Route::put('/products/{product}', [AdminProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{product}', [AdminProductController::class, 'destroy'])->name('products.destroy');

    // Users management
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    
    // Promotions management
    Route::get('/promotions', [PromotionController::class, 'index'])->name('promotions.index');
    Route::get('/promotions/create', [PromotionController::class, 'create'])->name('promotions.create');
    Route::post('/promotions', [PromotionController::class, 'store'])->name('promotions.store');
    Route::get('/promotions/{promotion}/edit', [PromotionController::class, 'edit'])->name('promotions.edit');
    Route::put('/promotions/{promotion}', [PromotionController::class, 'update'])->name('promotions.update');
    Route::delete('/promotions/{promotion}', [PromotionController::class, 'destroy'])->name('promotions.destroy');
    
    // Contact management
    Route::get('/contact', [ContactController::class, 'index'])->name('contact.index');
    Route::get('/contact/{message}', [ContactController::class, 'show'])->name('contact.show');
    Route::post('/contact/{message}/reply', [ContactController::class, 'reply'])->name('contact.reply');
    Route::delete('/contact/{message}', [ContactController::class, 'destroy'])->name('contact.destroy');

    // Orders management
    Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::get('/orders/{order}/edit', [AdminOrderController::class, 'edit'])->name('orders.edit');
    Route::put('/orders/{order}', [AdminOrderController::class, 'update'])->name('orders.update');
    Route::get('/orders/{order}/invoice', [AdminOrderController::class, 'invoice'])->name('orders.invoice');
    Route::get('/orders/export', [AdminOrderController::class, 'export'])->name('orders.export');

    // Payments management
    Route::get('/payments', [AdminPaymentController::class, 'index'])->name('payments.index');
    Route::get('/payments/{payment}', [AdminPaymentController::class, 'show'])->name('payments.show');
    Route::get('/payments/{payment}/edit', [AdminPaymentController::class, 'edit'])->name('payments.edit');
    Route::put('/payments/{payment}', [AdminPaymentController::class, 'update'])->name('payments.update');
    
    // Roles management
    Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
    Route::get('/roles/create', [RoleController::class, 'create'])->name('roles.create');
    Route::post('/roles', [RoleController::class, 'store'])->name('roles.store');
    Route::get('/roles/{role}', [RoleController::class, 'show'])->name('roles.show');
    Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit');
    Route::put('/roles/{role}', [RoleController::class, 'update'])->name('roles.update');
    Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');
    
    // Permissions management
    Route::get('/permissions', [PermissionController::class, 'index'])->name('permissions.index');
    Route::get('/permissions/create', [PermissionController::class, 'create'])->name('permissions.create');
    Route::post('/permissions', [PermissionController::class, 'store'])->name('permissions.store');
    Route::get('/permissions/{permission}', [PermissionController::class, 'show'])->name('permissions.show');
    Route::get('/permissions/{permission}/edit', [PermissionController::class, 'edit'])->name('permissions.edit');
    Route::put('/permissions/{permission}', [PermissionController::class, 'update'])->name('permissions.update');
    Route::delete('/permissions/{permission}', [PermissionController::class, 'destroy'])->name('permissions.destroy');

    // About Pages management
    Route::get('/about-pages', [App\Http\Controllers\Admin\AboutPageController::class, 'index'])->name('about-pages.index');
    Route::get('/about-pages/create', [App\Http\Controllers\Admin\AboutPageController::class, 'create'])->name('about-pages.create');
    Route::post('/about-pages', [App\Http\Controllers\Admin\AboutPageController::class, 'store'])->name('about-pages.store');
    Route::get('/about-pages/{aboutPage}', [App\Http\Controllers\Admin\AboutPageController::class, 'show'])->name('about-pages.show');
    Route::get('/about-pages/{aboutPage}/edit', [App\Http\Controllers\Admin\AboutPageController::class, 'edit'])->name('about-pages.edit');
    Route::put('/about-pages/{aboutPage}', [App\Http\Controllers\Admin\AboutPageController::class, 'update'])->name('about-pages.update');
    Route::delete('/about-pages/{aboutPage}', [App\Http\Controllers\Admin\AboutPageController::class, 'destroy'])->name('about-pages.destroy');
    
    // Tutorials management
    Route::get('/tutorials', [App\Http\Controllers\Admin\TutorialController::class, 'index'])->name('tutorials.index');
    Route::get('/tutorials/create', [App\Http\Controllers\Admin\TutorialController::class, 'create'])->name('tutorials.create');
    Route::post('/tutorials', [App\Http\Controllers\Admin\TutorialController::class, 'store'])->name('tutorials.store');
    Route::get('/tutorials/{tutorial}', [App\Http\Controllers\Admin\TutorialController::class, 'show'])->name('tutorials.show');
    Route::get('/tutorials/{tutorial}/edit', [App\Http\Controllers\Admin\TutorialController::class, 'edit'])->name('tutorials.edit');
    Route::put('/tutorials/{tutorial}', [App\Http\Controllers\Admin\TutorialController::class, 'update'])->name('tutorials.update');
    Route::delete('/tutorials/{tutorial}', [App\Http\Controllers\Admin\TutorialController::class, 'destroy'])->name('tutorials.destroy');

    // Reviews management
    Route::get('/reviews', [AdminReviewController::class, 'index'])->name('reviews.index');
    Route::get('/reviews/{review}', [AdminReviewController::class, 'show'])->name('reviews.show');
    Route::get('/reviews/{review}/edit', [AdminReviewController::class, 'edit'])->name('reviews.edit');
    Route::put('/reviews/{review}', [AdminReviewController::class, 'update'])->name('reviews.update');
    Route::post('/reviews/{review}/approve', [AdminReviewController::class, 'approve'])->name('reviews.approve');
    Route::post('/reviews/{review}/reject', [AdminReviewController::class, 'reject'])->name('reviews.reject');
    Route::post('/reviews/{review}/toggle-featured', [AdminReviewController::class, 'toggleFeatured'])->name('reviews.toggle-featured');
    Route::delete('/reviews/{review}', [AdminReviewController::class, 'destroy'])->name('reviews.destroy');
});

// SPA route - będzie obsługiwać wszystkie ścieżki, które nie są w /admin lub /api
Route::get('/{any?}', [SPAController::class, 'index'])->where('any', '^(?!admin|api).*$')->name('spa');
