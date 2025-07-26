<?php

use App\Http\Controllers\SPAController;
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

// Auth routes for API
require __DIR__.'/auth.php';

// Main entry point route - named route for SEO
Route::get('/', [SPAController::class, 'index'])->name('home');

// Admin panel route
Route::get('/admin/{any?}', [SPAController::class, 'index'])
    ->where('any', '.*') // Any path after /admin/
    ->name('admin');

// SPA catch-all route - all other routes should be handled by Vue Router
// Exclude API routes and admin routes from the catch-all
Route::get('/{any}', [SPAController::class, 'index'])
    ->where('any', '^(?!api|admin).*$'); // Match any route that doesn't start with 'api' or 'admin'
