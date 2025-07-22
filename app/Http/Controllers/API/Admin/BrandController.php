<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Brand;
use App\Http\Requests\Admin\BrandRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BrandController extends BaseAdminController
{
    /**
     * Get a paginated list of brands with optional filters.
     *
     * @param Request $request
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
            
            return $this->successResponse('Marki pobrane pomyślnie', $brands);
        } catch (\Exception $e) {
            Log::error('Error fetching brands: ' . $e->getMessage(), [
                'exception' => $e,
                'request' => $request->all()
            ]);
            return $this->errorResponse('Błąd podczas pobierania marek: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Store a newly created brand.
     *
     * @param BrandRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(BrandRequest $request)
    {
        try {
            $brand = Brand::create($request->validated());

            return $this->successResponse('Marka została utworzona', $brand, 201);
        } catch (\Exception $e) {
            return $this->errorResponse('Błąd podczas tworzenia marki: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Display the specified brand.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            $brand = Brand::with('products')->findOrFail($id);
            return $this->successResponse('Marka pobrana pomyślnie', $brand);
        } catch (\Exception $e) {
            return $this->errorResponse('Błąd podczas pobierania marki: ' . $e->getMessage(), 404);
        }
    }

    /**
     * Update the specified brand.
     *
     * @param BrandRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(BrandRequest $request, $id)
    {
        try {
            $brand = Brand::findOrFail($id);

            $brand->update($request->validated());

            return $this->successResponse('Marka została zaktualizowana', $brand);
        } catch (\Exception $e) {
            return $this->errorResponse('Błąd podczas aktualizacji marki: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Remove the specified brand.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            $brand = Brand::findOrFail($id);
            
            // Update all products with this brand to have null brand_id
            if ($brand->products()->count() > 0) {
                $affectedProductsCount = $brand->products()->count();
                $brand->products()->update(['brand_id' => null]);
                
                $brand->delete();
                
                return $this->successResponse(
                    "Marka \"{$brand->name}\" została usunięta. " .
                    "{$affectedProductsCount} produktów zostało odłączonych od tej marki i może być teraz filtrowanych jako 'Bez marki'."
                );
            }
            
            $brand->delete();
            
            return $this->successResponse('Marka została usunięta');
        } catch (\Exception $e) {
            return $this->errorResponse('Błąd podczas usuwania marki: ' . $e->getMessage(), 500);
        }
    }
} 