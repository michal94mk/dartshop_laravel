<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ProductController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        Log::info('ProductController initialized');
    }
    
    /**
     * Display a listing of products.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        Log::info('ProductController@index called with parameters', [
            'query_params' => $request->all(),
            'user_agent' => $request->header('User-Agent'),
        ]);
        
        try {
            // Create base query
            $query = Product::with(['category', 'brand']);
            
            // Apply filters if any
            if ($request->has('category_id')) {
                $query->where('category_id', $request->category_id);
            }
            
            if ($request->has('brand_id')) {
                $query->where('brand_id', $request->brand_id);
            }
            
            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            }
            
            // Price range filtering
            if ($request->has('price_min') && is_numeric($request->price_min)) {
                $query->where('price', '>=', (float)$request->price_min);
            }
            
            if ($request->has('price_max') && is_numeric($request->price_max)) {
                $query->where('price', '<=', (float)$request->price_max);
            }
            
            // Apply sorting
            $sortField = $request->sort_by ?? 'created_at';
            $sortDirection = $request->sort_direction ?? 'desc';
            
            // Validate sort field to prevent SQL injection
            $allowedSortFields = ['created_at', 'name', 'price'];
            if (!in_array($sortField, $allowedSortFields)) {
                $sortField = 'created_at';
            }
            
            $query->orderBy($sortField, $sortDirection);
            
            // Pagination
            $perPage = (int)($request->per_page ?? 12);
            if ($perPage <= 0) {
                $perPage = 12;
            }
            
            $products = $query->paginate($perPage);
            
            Log::info('Products query successful', [
                'total' => $products->total(),
                'per_page' => $products->perPage(),
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage()
            ]);
            
            return response()->json($products);
            
        } catch (Exception $e) {
            Log::error('Error fetching products', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            return response()->json([
                'error' => 'Wystąpił błąd podczas pobierania produktów',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Display the specified product.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        Log::info('ProductController@show called with ID', [
            'product_id' => $id
        ]);
        
        DB::enableQueryLog();
        
        try {
            // Sprawdź, czy istnieje model Review i czy zawiera metodę approved
            $hasReviews = false;
            try {
                if (class_exists('App\\Models\\Review') && 
                    Schema::hasTable('reviews') && 
                    method_exists('App\\Models\\Review', 'scopeApproved')) {
                    $hasReviews = true;
                }
            } catch (Exception $e) {
                Log::warning('Error checking for Review model', [
                    'error' => $e->getMessage()
                ]);
            }
            
            // Jeśli model Review istnieje, pobierz również recenzje
            if ($hasReviews) {
                $product = Product::with(['category', 'brand', 'reviews' => function($query) {
                    $query->approved()->latest();
                }])->findOrFail($id);
            } else {
                $product = Product::with(['category', 'brand'])->findOrFail($id);
            }
            
            Log::info('Product detail response', [
                'product_id' => $id,
                'product_name' => $product->name,
                'product_columns' => array_keys($product->toArray()),
                'has_category' => $product->category ? true : false,
                'has_brand' => $product->brand ? true : false,
                'has_reviews' => $hasReviews,
                'query_log' => DB::getQueryLog()
            ]);
            
            // Return product with a consistent format
            return response()->json($product);
        } catch (Exception $e) {
            Log::error('Error fetching product details', [
                'id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'query_log' => DB::getQueryLog()
            ]);
            
            return response()->json(['error' => 'Product not found'], 404);
        }
    }
    
    /**
     * Display featured products.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function featured()
    {
        Log::info('ProductController@featured called');
        
        DB::enableQueryLog();
        
        try {
            // First try to get featured products
            $query = Product::with(['category', 'brand']);
            
            // Check if 'featured' column exists in the products table
            $columns = DB::getSchemaBuilder()->getColumnListing('products');
            
            if (in_array('featured', $columns)) {
                $query->where('featured', true);
            }
            
            $products = $query->latest()->take(8)->get();
            
            // If no featured products, just get any recent products
            if ($products->isEmpty()) {
                $products = Product::with(['category', 'brand'])
                    ->latest()
                    ->take(8)
                    ->get();
            }
            
            Log::info('Featured products response', [
                'count' => $products->count(),
                'columns' => $products->count() > 0 ? array_keys($products->first()->toArray()) : [],
                'query_log' => DB::getQueryLog()
            ]);
            
            return response()->json($products);
        } catch (Exception $e) {
            Log::error('Error fetching featured products', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'query_log' => DB::getQueryLog()
            ]);
            
            return response()->json([]);
        }
    }
} 