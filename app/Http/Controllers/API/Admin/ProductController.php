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
use Illuminate\Support\Str;

class ProductController extends BaseAdminController
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        \Illuminate\Support\Facades\Log::info('Admin ProductController initialized');
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
            \Illuminate\Support\Facades\Log::info('Admin ProductController@index called with filters:', $request->all());
            \Illuminate\Support\Facades\Log::info('Request URI: ' . $request->getRequestUri());
            \Illuminate\Support\Facades\Log::info('Request method: ' . $request->method());
            
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
            \Illuminate\Support\Facades\Log::info('Query SQL:', [
                'sql' => $query->toSql(),
                'bindings' => $query->getBindings()
            ]);
            
            // Paginate results
            $perPage = $this->getPerPage($request);
            $products = $query->paginate($perPage);
            
            \Illuminate\Support\Facades\Log::info('Admin ProductController@index success. Product count: ' . $products->count());
            
            return response()->json($products);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Admin ProductController@index error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return $this->errorResponse('Error fetching products: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Store a newly created product in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        \Illuminate\Support\Facades\Log::info('ProductController@store called with data:', $request->except('image'));
        if ($request->hasFile('image')) {
            \Illuminate\Support\Facades\Log::info('Image file received:', [
                'original_name' => $request->file('image')->getClientOriginalName(),
                'mime_type' => $request->file('image')->getMimeType(),
                'size' => $request->file('image')->getSize()
            ]);
        }
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'required|exists:brands,id',
            'image' => 'nullable|image|max:2048', // 2MB max
            'weight' => 'nullable|numeric|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            \Illuminate\Support\Facades\Log::warning('ProductController@store validation failed:', $validator->errors()->toArray());
            return $this->errorResponse(
                'Validation error',
                422,
                $validator->errors()
            );
        }

        try {
            DB::beginTransaction();
            
            $productData = $request->except('image');
            \Illuminate\Support\Facades\Log::info('Creating product with data:', $productData);
            
            // Handle the image upload
            if ($request->hasFile('image') && $request->file('image')->isValid()) {
                $file = $request->file('image');
                $imageName = Str::slug($request->name) . '-' . time() . '.' . $file->getClientOriginalExtension();
                $imagePath = $file->storeAs('products', $imageName, 'public');
                $productData['image'] = '/storage/' . $imagePath;
                \Illuminate\Support\Facades\Log::info('Image saved:', ['path' => $productData['image']]);
            }
            
            $product = Product::create($productData);
            \Illuminate\Support\Facades\Log::info('Product created with ID: ' . $product->id);
            
            DB::commit();
            
            return $this->successResponse(
                'Product created successfully',
                $product->load(['category', 'brand']),
                201
            );
        } catch (\Exception $e) {
            DB::rollBack();
            \Illuminate\Support\Facades\Log::error('Error creating product: ' . $e->getMessage(), [
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
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        try {
            \Illuminate\Support\Facades\Log::info('ProductController@update called for ID: ' . $id . ' with data:', $request->all());
            \Illuminate\Support\Facades\Log::info('Content-Type: ' . $request->header('Content-Type'));
            \Illuminate\Support\Facades\Log::info('Request method: ' . $request->method());
            
            $product = Product::findOrFail($id);
            \Illuminate\Support\Facades\Log::info('Found product to update:', ['id' => $product->id, 'name' => $product->name]);
            
            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|required|string|max:255',
                'description' => 'sometimes|required|string',
                'price' => 'sometimes|required|numeric|min:0',
                'category_id' => 'sometimes|required|exists:categories,id',
                'brand_id' => 'sometimes|required|exists:brands,id',
                'image' => 'nullable|image|max:2048', // 2MB max
                'weight' => 'nullable|numeric|min:0',
                'is_active' => 'nullable|boolean',
            ]);

            if ($validator->fails()) {
                \Illuminate\Support\Facades\Log::warning('ProductController@update validation failed:', $validator->errors()->toArray());
                return $this->errorResponse(
                    'Validation error',
                    422,
                    $validator->errors()
                );
            }
            
            DB::beginTransaction();
            
            // If the request is json, log that specifically
            if ($request->isJson()) {
                \Illuminate\Support\Facades\Log::info('Request is JSON');
            }
            
            // Get all product data except image and method
            $productData = $request->except(['image', '_method']);
            \Illuminate\Support\Facades\Log::info('Updating product with data:', $productData);
            
            // Handle the image upload if it exists
            if ($request->hasFile('image') && $request->file('image')->isValid()) {
                // Delete the old image if it exists
                if ($product->image && Storage::disk('public')->exists(str_replace('/storage/', '', $product->image))) {
                    Storage::disk('public')->delete(str_replace('/storage/', '', $product->image));
                    \Illuminate\Support\Facades\Log::info('Deleted previous image: ' . $product->image);
                }
                
                $file = $request->file('image');
                $imageName = Str::slug($request->name ?? $product->name) . '-' . time() . '.' . $file->getClientOriginalExtension();
                $imagePath = $file->storeAs('products', $imageName, 'public');
                $productData['image'] = '/storage/' . $imagePath;
                \Illuminate\Support\Facades\Log::info('New image saved:', ['path' => $productData['image']]);
            } elseif ($request->has('image') && $request->image === null) {
                // If image is explicitly set to null, remove the existing image
                if ($product->image && Storage::disk('public')->exists(str_replace('/storage/', '', $product->image))) {
                    Storage::disk('public')->delete(str_replace('/storage/', '', $product->image));
                    \Illuminate\Support\Facades\Log::info('Deleted image on nullify: ' . $product->image);
                }
                $productData['image'] = null;
            }
            
            \Illuminate\Support\Facades\Log::info('Final product data for update:', $productData);
            
            // Perform the update
            $product->update($productData);
            \Illuminate\Support\Facades\Log::info('Product updated successfully', ['id' => $product->id]);
            
            DB::commit();
            
            // Return the updated product with relationships
            $updatedProduct = $product->fresh(['category', 'brand']);
            \Illuminate\Support\Facades\Log::info('Returning updated product data:', $updatedProduct->toArray());
            
            return $this->successResponse(
                'Product updated successfully',
                $updatedProduct
            );
        } catch (\Exception $e) {
            DB::rollBack();
            \Illuminate\Support\Facades\Log::error('Error updating product: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            if ($e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
                return $this->errorResponse('Product not found', 404);
            }
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
            \Illuminate\Support\Facades\Log::info('ProductController@destroy called for ID: ' . $id);
            
            $product = Product::findOrFail($id);
            \Illuminate\Support\Facades\Log::info('Found product to delete:', ['id' => $product->id, 'name' => $product->name]);
            
            // Check if product is used in any orders
            $hasOrderItems = DB::table('order_items')->where('product_id', $id)->exists();
            
            if ($hasOrderItems) {
                \Illuminate\Support\Facades\Log::warning('Cannot delete product as it has associated order items:', ['id' => $id]);
                
                // Instead of deleting, deactivate the product
                $product->update(['is_active' => false]);
                \Illuminate\Support\Facades\Log::info('Product deactivated instead of deleted:', ['id' => $id]);
                
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
                \Illuminate\Support\Facades\Log::info('Deleted associated cart items for product:', ['id' => $id]);
            }
            
            // Check if product has reviews
            $hasReviews = DB::table('reviews')->where('product_id', $id)->exists();
            
            if ($hasReviews) {
                // For reviews, we can delete them when deleting the product
                DB::table('reviews')->where('product_id', $id)->delete();
                \Illuminate\Support\Facades\Log::info('Deleted associated reviews for product:', ['id' => $id]);
            }
            
            // Delete the image if it exists
            if ($product->image && Storage::disk('public')->exists(str_replace('/storage/', '', $product->image))) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $product->image));
                \Illuminate\Support\Facades\Log::info('Deleted product image: ' . $product->image);
            }
            
            $product->delete();
            \Illuminate\Support\Facades\Log::info('Product deleted successfully', ['id' => $id]);
            
            return $this->successResponse('Product deleted successfully');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error deleting product: ' . $e->getMessage(), [
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
            \Illuminate\Support\Facades\Log::info('ProductController@getFormData called');
            
            $categories = Category::all();
            $brands = Brand::all();
            
            \Illuminate\Support\Facades\Log::info('ProductController@getFormData success. Categories: ' . $categories->count() . ', Brands: ' . $brands->count());
            
            return response()->json([
                'categories' => $categories,
                'brands' => $brands
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('ProductController@getFormData error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return $this->errorResponse('Error fetching form data: ' . $e->getMessage(), 500);
        }
    }
} 