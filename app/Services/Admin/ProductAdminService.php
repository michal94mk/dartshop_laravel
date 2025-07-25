<?php

namespace App\Services\Admin;

use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

/**
 * Service class for admin product management.
 * Handles listing, filtering, creating, updating, deleting products, and form data for the admin panel.
 */
class ProductAdminService
{
    /**
     * Get paginated products with optional filters and sorting.
     *
     * @param Request $request
     * @return LengthAwarePaginator
     */
    public function getProductsWithFilters(Request $request): LengthAwarePaginator
    {
        $query = Product::with(['category', 'brand'])
            ->withCount('orderItems as orders_count');

        // Active/inactive filter
        if ($request->has('is_active')) {
            $isActive = filter_var($request->is_active, FILTER_VALIDATE_BOOLEAN);
            $query->where('is_active', $isActive);
        }

        // Search filter
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Category filter
        if ($request->has('category_id') && !empty($request->category_id)) {
            $query->where('category_id', $request->category_id);
        }

        // Products without category
        if ($request->has('null_category') && $request->boolean('null_category')) {
            $query->whereNull('category_id');
        }

        // Brand filter
        if ($request->has('brand_id') && !empty($request->brand_id)) {
            $query->where('brand_id', $request->brand_id);
        }

        // Products without brand
        if ($request->has('null_brand') && $request->boolean('null_brand')) {
            $query->whereNull('brand_id');
        }

        // Sorting
        $sortField = $request->sort_field ?? 'created_at';
        $sortDirection = $request->sort_direction ?? 'desc';
        $query->orderBy($sortField, $sortDirection);

        // Pagination
        $perPage = $request->get('per_page', 15);
        return $query->paginate($perPage);
    }

    /**
     * Create a new product.
     *
     * @param array $productData
     * @param \Illuminate\Http\UploadedFile|null $imageFile
     * @return Product
     */
    public function createProduct(array $productData, $imageFile = null): Product
    {
        // Handle image upload
        if ($imageFile && $imageFile->isValid()) {
            $imageName = Str::slug($productData['name']) . '-' . time() . '.' . $imageFile->getClientOriginalExtension();
            $imageFile->move(public_path('storage/products'), $imageName);
            $productData['image_url'] = 'products/' . $imageName;
        }
        $product = Product::create($productData);
        return $product->fresh(['category', 'brand']);
    }

    /**
     * Update an existing product.
     *
     * @param Product $product
     * @param array $productData
     * @param \Illuminate\Http\UploadedFile|null $imageFile
     * @return Product
     */
    public function updateProduct(Product $product, array $productData, $imageFile = null): Product
    {
        // Handle image upload
        if ($imageFile && $imageFile->isValid()) {
            $imageName = Str::slug($productData['name'] ?? $product->name) . '-' . time() . '.' . $imageFile->getClientOriginalExtension();
            // Delete old image if exists
            if ($product->image_url) {
                $oldImagePath = public_path('storage/' . $product->image_url);
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }
            $imageFile->move(public_path('storage/products'), $imageName);
            $productData['image_url'] = 'products/' . $imageName;
        }
        $product->update($productData);
        return $product->fresh(['category', 'brand']);
    }

    /**
     * Delete a product (with business rules for orders, carts, reviews, and image cleanup).
     *
     * @param Product $product
     * @return array [success: bool, message: string, code: int]
     */
    public function deleteProduct(Product $product): array
    {
        // Check if product is used in any orders
        $hasOrderItems = $product->orderItems()->exists();
        if ($hasOrderItems) {
            $product->update(['is_active' => false]);
            return [
                'success' => false,
                'message' => 'This product cannot be deleted because it is associated with existing orders. The product has been deactivated instead.',
                'code' => 422
            ];
        }
        // Remove from carts
        $product->cartItems()->delete();
        // Remove reviews
        $product->reviews()->delete();
        // Delete image if exists
        if ($product->image_url) {
            $imagePath = public_path('storage/' . $product->image_url);
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }
        $product->delete();
        return [
            'success' => true,
            'message' => 'Product deleted successfully.',
            'code' => 200
        ];
    }

    /**
     * Get product details with relations.
     *
     * @param int $id
     * @return Product|null
     */
    public function getProductWithDetails($id): ?Product
    {
        return Product::with(['category', 'brand'])->find($id);
    }

    /**
     * Get all categories and brands for product form.
     *
     * @return array
     */
    public function getFormData(): array
    {
        return [
            'categories' => Category::all(),
            'brands' => Brand::all(),
        ];
    }
} 