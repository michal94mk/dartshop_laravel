<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductController extends BaseAdminController
{
    /**
     * Display a listing of the products.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $query = Product::with(['category', 'brand']);
            
            // Apply filters
            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            }
            
            if ($request->has('category_id')) {
                $query->where('category_id', $request->category_id);
            }
            
            if ($request->has('brand_id')) {
                $query->where('brand_id', $request->brand_id);
            }
            
            if ($request->has('min_price')) {
                $query->where('price', '>=', $request->min_price);
            }
            
            if ($request->has('max_price')) {
                $query->where('price', '<=', $request->max_price);
            }
            
            // Apply sorting
            $sortField = $request->sort_field ?? 'created_at';
            $sortDirection = $request->sort_direction ?? 'desc';
            $query->orderBy($sortField, $sortDirection);
            
            // Paginate results
            $perPage = $this->getPerPage($request);
            $products = $query->paginate($perPage);
            
            return response()->json($products);
        } catch (\Exception $e) {
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
            'stock' => 'nullable|integer|min:0',
            'featured' => 'nullable|boolean',
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
            'stock' => 'nullable|integer|min:0',
            'featured' => 'nullable|boolean',
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
            $categories = Category::select('id', 'name')->orderBy('name')->get();
            $brands = Brand::select('id', 'name')->orderBy('name')->get();
            
            return response()->json([
                'categories' => $categories,
                'brands' => $brands
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse('Error fetching form data: ' . $e->getMessage(), 500);
        }
    }
} 