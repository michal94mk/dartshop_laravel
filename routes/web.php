<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\PromotionController;
use App\Http\Middleware\RoleMiddleware;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\Frontend\CartController as FrontendCartController;
use App\Http\Controllers\Frontend\CategoryController as FrontendCategoryController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;

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


Route::get('/', [HomeController::class, 'indexNewestProductsForHomepage'])->name('home');
Route::get('/tailwind', function () {
    return redirect()->route('home', ['tailwind' => 1]);
})->name('tailwind.home');
Route::get('/categories', [HomeController::class, 'indexForRegularUsers'])->name('frontend.categories.index');
Route::get('/products/{id}', [HomeController::class, 'showProduct'])->name('frontend.products.show');
Route::get('/filter/products', [ProductController::class, 'filterProducts'])->name('filter.products');

// Promotions routes
Route::get('/promotions', [PromotionController::class, 'showPromotions'])->name('frontend.promotions');

// Contact routes
Route::get('/contact', [ContactController::class, 'showContactForm'])->name('frontend.contact');
Route::post('/contact', [ContactController::class, 'submitContactForm'])->name('frontend.contact.submit');

// Route::prefix('cart')->group(function () {
//     Route::post('add/{product}', [CartController::class, 'addToCart'])->name('cart.add');
//     Route::post('remove/{product}', [CartController::class, 'removeFromCart'])->name('cart.remove');
// });

Route::post('/cart/add/{product}', [CartController::class, 'addToCart'])->name('cart.add');
Route::post('/cart/delete/{product}', [CartController::class, 'deleteFromCart'])->name('cart.delete');
Route::post('/cart/update', [CartController::class, 'updateCart'])->name('cart.update');
Route::post('/cart/empty', [CartController::class, 'emptyCart'])->name('cart.empty');
Route::get('/cart/contents', [CartController::class, 'getCartContents'])->name('cart.contents');
Route::get('/cart/view', [CartController::class, 'cartView'])->name('cart.view');


Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');


Route::middleware(['auth','verified'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth','verified', RoleMiddleware::class.':admin'])->group(function () {
    Route::get('/admin/categories', [CategoryController::class, 'index'])->name('admin.categories.index');
    Route::get('/admin/categories/create', [CategoryController::class, 'create'])->name('admin.categories.create');
    Route::post('/admin/categories', [CategoryController::class, 'store'])->name('admin.categories.store');
    Route::get('/admin/categories/{category}/edit', [CategoryController::class, 'edit'])->name('admin.categories.edit');
    Route::put('/admin/categories/{category}', [CategoryController::class, 'update'])->name('admin.categories.update');
    Route::delete('/admin/categories/{category}', [CategoryController::class, 'destroy'])->name('admin.categories.destroy');

    Route::get('/admin/brands', [BrandController::class, 'index'])->name('admin.brands.index');
    Route::get('/admin/brands/create', [BrandController::class, 'create'])->name('admin.brands.create');
    Route::post('/admin/brands', [BrandController::class, 'store'])->name('admin.brands.store');
    Route::get('/admin/brands/{brand}/edit', [BrandController::class, 'edit'])->name('admin.brands.edit');
    Route::put('/admin/brands/{brand}', [BrandController::class, 'update'])->name('admin.brands.update');
    Route::delete('/admin/brands/{brand}', [BrandController::class, 'destroy'])->name('admin.brands.destroy');

    Route::get('/admin/products', [ProductController::class, 'index'])->name('admin.products.index');
    Route::get('/admin/products/create', [ProductController::class, 'create'])->name('admin.products.create');
    Route::post('/admin/products', [ProductController::class, 'store'])->name('admin.products.store');
    Route::get('/admin/products/{product}', [ProductController::class, 'show'])->name('admin.products.show');
    Route::get('/admin/products/{product}/edit', [ProductController::class, 'edit'])->name('admin.products.edit');
    Route::put('/admin/products/{product}', [ProductController::class, 'update'])->name('admin.products.update');
    Route::delete('/admin/products/{product}', [ProductController::class, 'destroy'])->name('admin.products.destroy');

    Route::get('/admin/users', [UserController::class, 'index'])->name('admin.users.index');
    Route::get('/admin/users/create', [UserController::class, 'create'])->name('admin.users.create');
    Route::post('/admin/users', [UserController::class, 'store'])->name('admin.users.store');
    Route::get('/admin/users/{user}', [UserController::class, 'show'])->name('admin.users.show');
    Route::get('/admin/users/{user}/edit', [UserController::class, 'edit'])->name('admin.users.edit');
    Route::put('/admin/users/{user}', [UserController::class, 'update'])->name('admin.users.update');
    Route::delete('/admin/users/{user}', [UserController::class, 'destroy'])->name('admin.users.destroy');
    
    // Admin promotions routes
    Route::get('/admin/promotions', [PromotionController::class, 'index'])->name('admin.promotions.index');
    Route::get('/admin/promotions/create', [PromotionController::class, 'create'])->name('admin.promotions.create');
    Route::post('/admin/promotions', [PromotionController::class, 'store'])->name('admin.promotions.store');
    Route::get('/admin/promotions/{promotion}/edit', [PromotionController::class, 'edit'])->name('admin.promotions.edit');
    Route::put('/admin/promotions/{promotion}', [PromotionController::class, 'update'])->name('admin.promotions.update');
    Route::delete('/admin/promotions/{promotion}', [PromotionController::class, 'destroy'])->name('admin.promotions.destroy');
    
    // Admin contact routes
    Route::get('/admin/contact', [ContactController::class, 'index'])->name('admin.contact.index');
    Route::get('/admin/contact/{message}', [ContactController::class, 'show'])->name('admin.contact.show');
    Route::post('/admin/contact/{message}/reply', [ContactController::class, 'reply'])->name('admin.contact.reply');
    Route::delete('/admin/contact/{message}', [ContactController::class, 'destroy'])->name('admin.contact.destroy');

    // Roles and Permissions routes
    Route::get('/admin/roles', [RoleController::class, 'index'])->name('admin.roles.index');
    Route::get('/admin/roles/create', [RoleController::class, 'create'])->name('admin.roles.create');
    Route::post('/admin/roles', [RoleController::class, 'store'])->name('admin.roles.store');
    Route::get('/admin/roles/{role}', [RoleController::class, 'show'])->name('admin.roles.show');
    Route::get('/admin/roles/{role}/edit', [RoleController::class, 'edit'])->name('admin.roles.edit');
    Route::put('/admin/roles/{role}', [RoleController::class, 'update'])->name('admin.roles.update');
    Route::delete('/admin/roles/{role}', [RoleController::class, 'destroy'])->name('admin.roles.destroy');
    
    Route::get('/admin/permissions', [PermissionController::class, 'index'])->name('admin.permissions.index');
    Route::get('/admin/permissions/create', [PermissionController::class, 'create'])->name('admin.permissions.create');
    Route::post('/admin/permissions', [PermissionController::class, 'store'])->name('admin.permissions.store');
    Route::get('/admin/permissions/{permission}', [PermissionController::class, 'show'])->name('admin.permissions.show');
    Route::get('/admin/permissions/{permission}/edit', [PermissionController::class, 'edit'])->name('admin.permissions.edit');
    Route::put('/admin/permissions/{permission}', [PermissionController::class, 'update'])->name('admin.permissions.update');
    Route::delete('/admin/permissions/{permission}', [PermissionController::class, 'destroy'])->name('admin.permissions.destroy');
});

require __DIR__.'/auth.php';
