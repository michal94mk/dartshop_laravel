<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\Admin\BrandRequest;

/**
 * @OA\Tag(
 *     name="Admin/Brands",
 *     description="API Endpoints for admin brand management"
 * )
 */

class BrandController extends BaseAdminController
{
    /**
     * Display a listing of the brands.
     *
     * @OA\Get(
     *     path="/api/admin/brands",
     *     summary="Get brands list (admin)",
     *     description="Retrieve all brands with admin filters and pagination",
     *     tags={"Admin/Brands"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search in brand name",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="sort_field",
     *         in="query",
     *         description="Sort field (id, name, products_count, created_at, updated_at)",
     *         required=false,
     *         @OA\Schema(type="string", default="id")
     *     ),
     *     @OA\Parameter(
     *         name="sort_direction",
     *         in="query",
     *         description="Sort direction (asc, desc)",
     *         required=false,
     *         @OA\Schema(type="string", default="asc")
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Items per page",
     *         required=false,
     *         @OA\Schema(type="integer", default=10)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Brand")),
     *             @OA\Property(property="meta", ref="#/components/schemas/PaginationMeta"),
     *             @OA\Property(property="links", ref="#/components/schemas/PaginationLinks")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden - Admin access required"
     *     )
     * )
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
                $query->where('name', 'like', "%{$search}%");
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
     * @param  \App\Http\Requests\Admin\BrandRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(BrandRequest $request)
    {
        try {
            $brand = Brand::create($request->validated());

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
     * @param  \App\Http\Requests\Admin\BrandRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(BrandRequest $request, $id)
    {
        try {
            $brand = Brand::findOrFail($id);

            $brand->update($request->validated());

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
            
            // Check if brand has associated products
            if ($brand->products()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => "Nie można usunąć marki \"{$brand->name}\", ponieważ posiada przypisane produkty."
                ], 422);
            }
            
            $brand->delete();
            
            return $this->successResponse('Brand deleted successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Error deleting brand: ' . $e->getMessage(), 500);
        }
    }
} 