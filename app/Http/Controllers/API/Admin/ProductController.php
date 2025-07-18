<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Http\Requests\Admin\ProductRequest;

class ProductController extends BaseAdminController
{
    /**
     * Clear all category-related cache
     */
    private function clearCategoriesCache()
    {
        // Clear cache by pattern (Laravel doesn't have built-in pattern clearing, so we'll clear common ones)
        $commonKeys = [
            'categories_list_' . md5('[]'),
            'categories_list_' . md5('{}'),
            'categories_list_' . md5('{"with_products_only":true}'),
            'categories_list_' . md5('{"with_products_only":false}'),
        ];
        
        foreach ($commonKeys as $key) {
            Cache::forget($key);
        }
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        Log::info('Admin ProductController initialized');
    }

    /**
     * Display a listing of the products.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            Log::info('Admin ProductController@index called with filters:', $request->all());
            Log::info('Request URI: ' . $request->getRequestUri());
            Log::info('Request method: ' . $request->method());
            Log::info('Authenticated user:', [
                'id' => Auth::id() ?? 'none',
                'email' => Auth::user()?->email ?? 'none',
                'is_admin' => Auth::user()?->is_admin ?? false
            ]);
            
            $query = Product::with(['category', 'brand']);
            
            // Apply active/inactive filter
            if ($request->has('is_active')) {
                $isActive = filter_var($request->is_active, FILTER_VALIDATE_BOOLEAN);
                $query->where('is_active', $isActive);
            }
            
            // Apply search filter
            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            }
            
            // Apply category filter
            if ($request->has('category_id') && !empty($request->category_id)) {
                $query->where('category_id', $request->category_id);
            }
            
            // Apply brand filter
            if ($request->has('brand_id') && !empty($request->brand_id)) {
                $query->where('brand_id', $request->brand_id);
            }
            
            // Apply sorting
            $sortField = $request->sort_field ?? 'created_at';
            $sortDirection = $request->sort_direction ?? 'desc';
            $query->orderBy($sortField, $sortDirection);
            
            // Log query for debugging
            Log::info('Query SQL:', [
                'sql' => $query->toSql(),
                'bindings' => $query->getBindings()
            ]);
            
            // Paginate results
            $perPage = $this->getPerPage($request);
            $products = $query->paginate($perPage);
            
            Log::info('Admin ProductController@index success. Product count: ' . $products->count());
            
            return response()->json($products);
        } catch (\Exception $e) {
            Log::error('Admin ProductController@index error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return $this->errorResponse('Error fetching products: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Store a newly created product in storage.
     *
     * @param  \App\Http\Requests\Admin\ProductRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(ProductRequest $request)
    {
        Log::info('ProductController@store called with data:', $request->except('image'));
        if ($request->hasFile('image')) {
            Log::info('Image file received:', [
                'original_name' => $request->file('image')->getClientOriginalName(),
                'mime_type' => $request->file('image')->getMimeType(),
                'size' => $request->file('image')->getSize()
            ]);
        }
        
        try {
            DB::beginTransaction();
            
            $productData = $request->validated();
            Log::info('Creating product with data:', $productData);
            
            // Handle the image upload
            if ($request->hasFile('image') && $request->file('image')->isValid()) {
                $file = $request->file('image');
                $imageName = Str::slug($request->name) . '-' . time() . '.' . $file->getClientOriginalExtension();
                
                // Save to public/storage/products
                $file->move(public_path('storage/products'), $imageName);
                $productData['image_url'] = 'products/' . $imageName;
                
                Log::info('Image saved:', [
                    'name' => $imageName,
                    'path' => $productData['image_url'],
                    'full_path' => public_path('storage/products/' . $imageName),
                    'exists' => file_exists(public_path('storage/products/' . $imageName))
                ]);
            }
            
            $product = Product::create($productData);
            Log::info('Product created with ID: ' . $product->id);
            
            // Clear categories cache since product count changed
            $this->clearCategoriesCache();
            
            DB::commit();
            
            // Load the product with its relationships and return
            $product = $product->fresh(['category', 'brand']);
            
            return $this->successResponse(
                'Product created successfully',
                $product,
                201
            );
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating product: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return $this->errorResponse('Error creating product: ' . $e->getMessage(), 500);
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
        try {
            $product = Product::with(['category', 'brand'])->findOrFail($id);
            
            return response()->json([
                'data' => $product
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse('Product not found', 404);
        }
    }

    /**
     * Update the specified product in storage.
     *
     * @param  \App\Http\Requests\Admin\ProductRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(ProductRequest $request, $id)
    {
        try {
            Log::info('ProductController@update called for ID: ' . $id . ' with data:', $request->except('image'));
            
            $product = Product::findOrFail($id);
            Log::info('Found product to update:', ['id' => $product->id, 'name' => $product->name]);
            
            DB::beginTransaction();
            
            $productData = $request->validated();
            
            // Handle the image upload
            if ($request->hasFile('image') && $request->file('image')->isValid()) {
                $file = $request->file('image');
                $imageName = Str::slug($request->input('name', $product->name)) . '-' . time() . '.' . $file->getClientOriginalExtension();
                
                // Delete old image if exists
                if ($product->image_url) {
                    $oldImagePath = public_path('storage/' . $product->image_url);
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                }
                
                // Save to public/storage/products
                $file->move(public_path('storage/products'), $imageName);
                $productData['image_url'] = 'products/' . $imageName;
                
                Log::info('Image updated:', [
                    'name' => $imageName,
                    'path' => $productData['image_url'],
                    'full_path' => public_path('storage/products/' . $imageName),
                    'exists' => file_exists(public_path('storage/products/' . $imageName))
                ]);
            }
            
            $product->update($productData);
            
            // Clear categories cache
            $this->clearCategoriesCache();
            
            DB::commit();
            
            // Load the product with its relationships and return
            $product = $product->fresh(['category', 'brand']);
            
            return $this->successResponse(
                'Product updated successfully',
                $product
            );
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating product: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return $this->errorResponse('Error updating product: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Remove the specified product from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            Log::info('ProductController@destroy called for ID: ' . $id);
            
            $product = Product::findOrFail($id);
            Log::info('Found product to delete:', ['id' => $product->id, 'name' => $product->name]);
            
            // Check if product is used in any orders
            $hasOrderItems = DB::table('order_items')->where('product_id', $id)->exists();
            
            if ($hasOrderItems) {
                Log::warning('Cannot delete product as it has associated order items:', ['id' => $id]);
                
                // Instead of deleting, deactivate the product
                $product->update(['is_active' => false]);
                Log::info('Product deactivated instead of deleted:', ['id' => $id]);
                
                // Clear categories cache since product might not count anymore
                $this->clearCategoriesCache();
                
                return $this->errorResponse(
                    'This product cannot be deleted because it is associated with existing orders. ' .
                    'Products that have been ordered cannot be deleted to maintain order history integrity. ' .
                    'The product has been deactivated instead - it will no longer appear in the shop.',
                    422
                );
            }
            
            // Check if product is in any shopping carts
            $hasCartItems = DB::table('cart_items')->where('product_id', $id)->exists();
            
            if ($hasCartItems) {
                // For cart items, we can delete them as they are temporary
                DB::table('cart_items')->where('product_id', $id)->delete();
                Log::info('Deleted associated cart items for product:', ['id' => $id]);
            }
            
            // Check if product has reviews
            $hasReviews = DB::table('reviews')->where('product_id', $id)->exists();
            
            if ($hasReviews) {
                // For reviews, we can delete them when deleting the product
                DB::table('reviews')->where('product_id', $id)->delete();
                Log::info('Deleted associated reviews for product:', ['id' => $id]);
            }
            
            // Delete the image if it exists
            if ($product->image_url && Storage::disk('public')->exists(str_replace('/storage/', '', $product->image_url))) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $product->image_url));
                Log::info('Deleted product image: ' . $product->image_url);
            }
            
            $product->delete();
            Log::info('Product deleted successfully', ['id' => $id]);
            
            // Clear categories cache since product count changed
            $this->clearCategoriesCache();
            
            return $this->successResponse('Product deleted successfully');
        } catch (\Exception $e) {
            Log::error('Error deleting product: ' . $e->getMessage(), [
                'id' => $id,
                'trace' => $e->getTraceAsString()
            ]);
            
            if ($e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
                return $this->errorResponse('Product not found', 404);
            }
            return $this->errorResponse('Error deleting product: ' . $e->getMessage(), 500);
        }
    }
    
    /**
     * Get all categories and brands for product form
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getFormData()
    {
        try {
            Log::info('ProductController@getFormData called');
            
            $categories = Category::all();
            $brands = Brand::all();
            
            Log::info('ProductController@getFormData success. Categories: ' . $categories->count() . ', Brands: ' . $brands->count());
            
            return response()->json([
                'categories' => $categories,
                'brands' => $brands
            ]);
        } catch (\Exception $e) {
            Log::error('ProductController@getFormData error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return $this->errorResponse('Error fetching form data: ' . $e->getMessage(), 500);
        }
    }
} 