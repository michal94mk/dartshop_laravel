<?php

namespace App\Services\Admin;

use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Service class for admin brand management.
 * Handles listing, filtering, creating, updating, deleting brands.
 */
class BrandAdminService
{
    /**
     * Get paginated brands with optional filters and sorting.
     *
     * @param Request $request
     * @return LengthAwarePaginator
     */
    public function getBrandsWithFilters(Request $request): LengthAwarePaginator
    {
        $query = Brand::withCount('products');

        // Search filter
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }

        // Sorting
        $sortField = $request->sort_field ?? 'id';
        $sortDirection = $request->sort_direction ?? 'asc';
        if ($sortField === 'products_count') {
            $query->orderBy('products_count', $sortDirection);
        } elseif (in_array($sortField, ['id', 'name', 'created_at', 'updated_at'])) {
            $query->orderBy($sortField, $sortDirection);
        } else {
            $query->orderBy('id', 'asc');
        }

        // Pagination
        $perPage = $request->get('per_page', 15);
        return $query->paginate($perPage);
    }

    /**
     * Create a new brand.
     *
     * @param array $data
     * @return Brand
     */
    public function createBrand(array $data): Brand
    {
        return Brand::create($data);
    }

    /**
     * Update an existing brand.
     *
     * @param Brand $brand
     * @param array $data
     * @return Brand
     */
    public function updateBrand(Brand $brand, array $data): Brand
    {
        $brand->update($data);
        return $brand;
    }

    /**
     * Delete a brand and detach products.
     *
     * @param Brand $brand
     * @return array [success: bool, message: string]
     */
    public function deleteBrand(Brand $brand): array
    {
        $affectedProductsCount = $brand->products()->count();
        if ($affectedProductsCount > 0) {
            $brand->products()->update(['brand_id' => null]);
        }
        $brand->delete();
        if ($affectedProductsCount > 0) {
            return [
                'success' => true,
                'message' => "Marka '{$brand->name}' została usunięta. {$affectedProductsCount} produktów zostało odłączonych od tej marki."
            ];
        }
        return [
            'success' => true,
            'message' => 'Marka została usunięta.'
        ];
    }

    /**
     * Get brand details with products.
     *
     * @param int $id
     * @return Brand|null
     */
    public function getBrandWithDetails($id): ?Brand
    {
        return Brand::with('products')->find($id);
    }
} 