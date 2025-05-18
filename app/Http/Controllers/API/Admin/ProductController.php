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
            
            // Simplified product query for testing
            $query = Product::with(['category', 'brand']);
            
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
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'required|exists:brands,id',
            'image' => 'nullable|image|max:2048', // 2MB max
            'weight' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse(
                'Validation error',
                422,
                $validator->errors()
            );
        }

        try {
            DB::beginTransaction();
            
            $productData = $request->except('image');
            
            // Handle the image upload
            if ($request->hasFile('image') && $request->file('image')->isValid()) {
                $file = $request->file('image');
                $imageName = Str::slug($request->name) . '-' . time() . '.' . $file->getClientOriginalExtension();
                $imagePath = $file->storeAs('products', $imageName, 'public');
                $productData['image'] = 'storage/' . $imagePath;
            }
            
            $product = Product::create($productData);
            
            DB::commit();
            
            return $this->successResponse(
                'Product created successfully',
                $product->load(['category', 'brand']),
                201
            );
        } catch (\Exception $e) {
            DB::rollBack();
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
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'price' => 'sometimes|required|numeric|min:0',
            'category_id' => 'sometimes|required|exists:categories,id',
            'brand_id' => 'sometimes|required|exists:brands,id',
            'image' => 'nullable|image|max:2048', // 2MB max
            'weight' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse(
                'Validation error',
                422,
                $validator->errors()
            );
        }

        try {
            $product = Product::findOrFail($id);
            
            DB::beginTransaction();
            
            $productData = $request->except('image');
            
            // Handle the image upload
            if ($request->hasFile('image') && $request->file('image')->isValid()) {
                // Delete the old image if it exists
                if ($product->image && Storage::disk('public')->exists(str_replace('storage/', '', $product->image))) {
                    Storage::disk('public')->delete(str_replace('storage/', '', $product->image));
                }
                
                $file = $request->file('image');
                $imageName = Str::slug($request->name ?? $product->name) . '-' . time() . '.' . $file->getClientOriginalExtension();
                $imagePath = $file->storeAs('products', $imageName, 'public');
                $productData['image'] = 'storage/' . $imagePath;
            }
            
            $product->update($productData);
            
            DB::commit();
            
            return $this->successResponse(
                'Product updated successfully',
                $product->fresh(['category', 'brand'])
            );
        } catch (\Exception $e) {
            DB::rollBack();
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
            $product = Product::findOrFail($id);
            
            // Delete the image if it exists
            if ($product->image && Storage::disk('public')->exists(str_replace('storage/', '', $product->image))) {
                Storage::disk('public')->delete(str_replace('storage/', '', $product->image));
            }
            
            $product->delete();
            
            return $this->successResponse('Product deleted successfully');
        } catch (\Exception $e) {
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