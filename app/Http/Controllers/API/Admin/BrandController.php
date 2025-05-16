<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class BrandController extends BaseAdminController
{
    /**
     * Display a listing of the brands.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            $brands = Brand::withCount('products')->get();
            return response()->json($brands);
        } catch (\Exception $e) {
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
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'logo' => 'nullable|string',
                'slug' => 'nullable|string|max:255|unique:brands,slug',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $data = $request->all();
            
            // Generate slug if not provided
            if (empty($data['slug'])) {
                $data['slug'] = Str::slug($data['name']);
            }

            $brand = Brand::create($data);

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
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'logo' => 'nullable|string',
                'slug' => 'nullable|string|max:255|unique:brands,slug,' . $id,
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $data = $request->all();
            
            // Generate slug if not provided
            if (empty($data['slug'])) {
                $data['slug'] = Str::slug($data['name']);
            }

            $brand->update($data);

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