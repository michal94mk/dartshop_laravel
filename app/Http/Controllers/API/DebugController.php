<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;
use App\Models\Product;

class DebugController extends Controller
{
    public function checkProducts()
    {
        try {
            // Check if table exists
            $tableExists = Schema::hasTable('products');
            
            // Get product count
            $productCount = 0;
            if ($tableExists) {
                $productCount = DB::table('products')->count();
            }
            
            // Get table schema
            $columns = [];
            if ($tableExists) {
                $columns = Schema::getColumnListing('products');
            }
            
            // Get sample product
            $sampleProduct = null;
            if ($tableExists && $productCount > 0) {
                $sampleProduct = DB::table('products')->first();
            }
            
            return response()->json([
                'success' => true,
                'debug_info' => [
                    'products_table_exists' => $tableExists,
                    'product_count' => $productCount,
                    'columns' => $columns,
                    'sample_product' => $sampleProduct,
                    'php_version' => PHP_VERSION,
                    'laravel_version' => app()->version(),
                    'database_connection' => config('database.default'),
                    'app_debug' => config('app.debug'),
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Debug controller error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ], 500);
        }
    }
    
    public function checkRoutes()
    {
        try {
            $routes = [];
            foreach (Route::getRoutes() as $route) {
                if (strpos($route->uri, 'api/') === 0) {
                    $routes[] = [
                        'method' => implode('|', $route->methods),
                        'uri' => $route->uri,
                        'name' => $route->getName(),
                        'action' => $route->getActionName(),
                    ];
                }
            }
            
            return response()->json([
                'success' => true,
                'api_routes' => $routes
            ]);
        } catch (\Exception $e) {
            Log::error('Route check error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Comprehensive debug endpoint that provides detailed application state
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function debug(Request $request)
    {
        // Enable query logging
        DB::enableQueryLog();
        
        // Log the request
        Log::info('Debug endpoint called', [
            'ip' => $request->ip(),
            'user_agent' => $request->header('User-Agent'),
            'referer' => $request->header('Referer'),
            'request_url' => $request->fullUrl(),
        ]);
        
        try {
            // Get some product data using Eloquent
            $productCount = Product::count();
            $sampleProduct = Product::first();
            
            // Get auth status
            $user = $request->user();
            
            // Check CORS config
            $corsConfig = Config::get('cors');
            
            // Get cookies
            $cookies = $request->cookies->all();
            
            // Get sanctum config
            $sanctumConfig = Config::get('sanctum');
            
            // Check API session state
            $hasApiSession = $request->hasSession() && $request->session()->has('_token');
            
            // Return debug info
            return response()->json([
                'debug_time' => now()->toDateTimeString(),
                'request' => [
                    'ip' => $request->ip(),
                    'headers' => $request->headers->all(),
                    'has_cookies' => count($cookies) > 0,
                    'cookies_count' => count($cookies),
                    'cookie_names' => array_keys($cookies),
                    'session_id' => $request->session()->getId(),
                    'has_session' => $hasApiSession,
                ],
                'auth' => [
                    'authenticated' => !is_null($user),
                    'user_id' => $user ? $user->id : null,
                    'user_email' => $user ? $user->email : null,
                ],
                'cors' => [
                    'enabled' => isset($corsConfig['supports_credentials']),
                    'supports_credentials' => $corsConfig['supports_credentials'] ?? false,
                    'allowed_origins' => $corsConfig['allowed_origins'] ?? [],
                    'allowed_headers' => $corsConfig['allowed_headers'] ?? [],
                ],
                'sanctum' => [
                    'stateful' => $sanctumConfig['stateful'] ?? [],
                    'expiration' => $sanctumConfig['expiration'] ?? null,
                ],
                'products' => [
                    'count' => $productCount,
                    'sample' => $sampleProduct ? $sampleProduct->toArray() : null,
                ],
                'database' => [
                    'queries' => DB::getQueryLog(),
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error in debug endpoint', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 500);
        }
    }
}
