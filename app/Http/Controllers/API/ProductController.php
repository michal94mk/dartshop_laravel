<?php

namespace App\Http\Controllers\API;

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
            'request_path' => $request->path(),
            'is_api' => $request->is('api/*')
        ]);
        
        // Enable query logging for debugging
        DB::enableQueryLog();
        
        try {
            $query = Product::with(['category', 'brand']);
            
            Log::info('Initial query created, checking for products in the database...');
            $productCount = Product::count();
            Log::info('Product count in database: ' . $productCount);
            Log::info('Products Table Exists: ' . (Schema::hasTable('products') ? 'Yes' : 'No'));
            
            if (Schema::hasTable('products')) {
                // Sprawdź strukturę tabeli
                $columns = Schema::getColumnListing('products');
                Log::info('Products table columns: ' . implode(', ', $columns));
            }
            
            // Pobierz przykładowy produkt dla diagnostyki
            $sampleProduct = Product::first();
            if ($sampleProduct) {
                Log::info('Sample product found:', [
                    'id' => $sampleProduct->id,
                    'name' => $sampleProduct->name,
                    'attributes' => $sampleProduct->toArray()
                ]);
            } else {
                Log::warning('No sample product found, even though product count shows: ' . $productCount);
            }
            
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
            
            // Filtrowanie po zakresie cen
            if ($request->has('price_min') && is_numeric($request->price_min)) {
                $query->where('price', '>=', (float)$request->price_min);
            }
            
            if ($request->has('price_max') && is_numeric($request->price_max)) {
                $query->where('price', '<=', (float)$request->price_max);
            }
            
            // Apply sorting
            $sortField = $request->sort_by ?? 'created_at';
            $sortDirection = $request->sort_direction ?? 'desc';
            $query->orderBy($sortField, $sortDirection);
            
            // Pagination
            $perPage = $request->per_page ?? 12;
            $products = $query->paginate($perPage);
            
            // Log the query that was executed
            Log::info('SQL Query', [
                'query' => DB::getQueryLog()
            ]);
            
            // Loguj wynik zapytania
            Log::info('Products query result:', [
                'count' => $products->count(),
                'total' => $products->total(),
                'per_page' => $products->perPage(),
                'first_item_if_exists' => $products->count() > 0 ? $products->items()[0]->toArray() : 'no items'
            ]);
            
            // Mark products as coming from the database
            if ($products->count() > 0) {
                $modifiedProducts = $products->toArray();
                
                // Add a data source marker to each product
                foreach ($modifiedProducts['data'] as &$product) {
                    $product['_data_source'] = 'DATABASE';
                }
                
                Log::info('Returning DATABASE products', [
                    'count' => count($modifiedProducts['data'])
                ]);
                
                return response()->json($modifiedProducts);
            } else {
                // No products found in the database
                Log::info('No products found in database, using empty result');
                $emptyResult = [
                    'current_page' => 1,
                    'data' => [],
                    'first_page_url' => url('/api/products?page=1'),
                    'from' => null,
                    'last_page' => 1,
                    'last_page_url' => url('/api/products?page=1'),
                    'links' => [],
                    'next_page_url' => null,
                    'path' => url('/api/products'),
                    'per_page' => $perPage,
                    'prev_page_url' => null,
                    'to' => null,
                    'total' => 0
                ];
                
                // Add database-based mock data since database is empty
                if ($productCount === 0) {
                    Log::warning('DATABASE IS EMPTY - Creating mock products as fallback');
                    $mockProducts = [
                        [
                            'id' => 1,
                            'name' => '[DATABASE MOCK] Lotki Target Agora A30',
                            'description' => 'Profesjonalne lotki ze stali wolframowej 90%',
                            'short_description' => 'Profesjonalne lotki 90%',
                            'price' => 149.99,
                            'image_url' => 'https://via.placeholder.com/300x300/indigo/fff?text=DB+MOCK+Lotki+Target',
                            'category_id' => 1,
                            'brand_id' => 1,
                            'created_at' => '2023-01-05T12:00:00Z',
                            '_data_source' => 'DATABASE_MOCK'
                        ],
                        [
                            'id' => 2,
                            'name' => '[DATABASE MOCK] Tarcza elektroniczna Winmau Blade 6',
                            'description' => 'Zaawansowana tarcza dla profesjonalistów',
                            'short_description' => 'Tarcza profesjonalna',
                            'price' => 299.99,
                            'image_url' => 'https://via.placeholder.com/300x300/indigo/fff?text=DB+MOCK+Tarcza+Winmau',
                            'category_id' => 2,
                            'brand_id' => 2,
                            'created_at' => '2023-02-10T14:30:00Z',
                            '_data_source' => 'DATABASE_MOCK'
                        ]
                    ];
                    
                    $emptyResult['data'] = $mockProducts;
                    $emptyResult['from'] = 1;
                    $emptyResult['to'] = count($mockProducts);
                    $emptyResult['total'] = count($mockProducts);
                }
                
                return response()->json($emptyResult);
            }
        } catch (Exception $e) {
            Log::error('Error fetching products', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'query_log' => DB::getQueryLog()
            ]);
            
            // Return a fallback response with FALLBACK label
            $fallbackProducts = [
                'current_page' => 1,
                'data' => [
                    [
                        'id' => 9991,
                        'name' => '[FALLBACK] Lotki Target Agora A30',
                        'price' => 99.99,
                        'description' => 'This is a sample product. FALLBACK DATA DUE TO ERROR!',
                        'image_url' => '/images/products/default.jpg',
                        'created_at' => now(),
                        'updated_at' => now(),
                        '_data_source' => 'ERROR_FALLBACK',
                        '_error' => $e->getMessage()
                    ],
                    [
                        'id' => 9992,
                        'name' => '[FALLBACK] Tarcza elektroniczna',
                        'price' => 149.99,
                        'description' => 'This is another sample product. FALLBACK DATA DUE TO ERROR!',
                        'image_url' => '/images/products/default.jpg',
                        'created_at' => now(),
                        'updated_at' => now(),
                        '_data_source' => 'ERROR_FALLBACK',
                        '_error' => $e->getMessage()
                    ]
                ],
                'first_page_url' => url('/api/products?page=1'),
                'from' => 1,
                'last_page' => 1,
                'last_page_url' => url('/api/products?page=1'),
                'links' => [],
                'next_page_url' => null,
                'path' => url('/api/products'),
                'per_page' => 12,
                'prev_page_url' => null,
                'to' => 2,
                'total' => 2
            ];
            
            Log::error('Returning FALLBACK products due to error');
            
            return response()->json($fallbackProducts);
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