<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class BrandController extends BaseAdminController
{
    /**
     * Display a listing of the brands.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $query = Brand::withCount('products');
            
            // Apply search filter
            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
            }
            
            // Apply sorting
            $sortField = $request->sort_field ?? 'id';
            $sortDirection = $request->sort_direction ?? 'asc';
            
            // Handle special case for products_count which is not a direct database column
            if ($sortField === 'products_count') {
                $query->orderBy('products_count', $sortDirection);
            } else {
                // Make sure the column exists in the brands table to prevent SQL errors
                if (in_array($sortField, ['id', 'name', 'created_at', 'updated_at'])) {
                    $query->orderBy($sortField, $sortDirection);
                } else {
                    $query->orderBy('id', 'asc'); // Default fallback
                }
            }
            
            // Paginate results
            $perPage = $request->per_page ?? 10;
            $brands = $query->paginate($perPage);
            
            return response()->json($brands);
        } catch (\Exception $e) {
            Log::error('Error fetching brands: ' . $e->getMessage(), [
                'exception' => $e,
                'request' => $request->all()
            ]);
            return $this->errorResponse('Error fetching brands: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Store a newly created brand in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255|unique:brands,name',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $brand = Brand::create([
                'name' => $request->name
            ]);

            return $this->successResponse('Brand created successfully', $brand, 201);
        } catch (\Exception $e) {
            return $this->errorResponse('Error creating brand: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Display the specified brand.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            $brand = Brand::with('products')->findOrFail($id);
            return response()->json($brand);
        } catch (\Exception $e) {
            return $this->errorResponse('Error fetching brand: ' . $e->getMessage(), 404);
        }
    }

    /**
     * Update the specified brand in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        try {
            $brand = Brand::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255|unique:brands,name,' . $id,
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $brand->update([
                'name' => $request->name
            ]);

            return $this->successResponse('Brand updated successfully', $brand);
        } catch (\Exception $e) {
            return $this->errorResponse('Error updating brand: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Remove the specified brand from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            $brand = Brand::findOrFail($id);
            $brand->delete();

            return $this->successResponse('Brand deleted successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Error deleting brand: ' . $e->getMessage(), 500);
        }
    }
} 