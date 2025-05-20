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
        Log::info('BrandController@destroy called for ID: ' . $id);
        
        try {
            // First check if the brand exists and load it
            if (!$brand = Brand::find($id)) {
                Log::warning('Brand not found during deletion attempt:', ['id' => $id]);
                
                // Build and log response for debugging
                $response = [
                    'success' => false,
                    'message' => 'Nie znaleziono marki o ID: ' . $id
                ];
                Log::debug('Sending 404 not found response', ['response' => $response]);
                
                return response()->json($response, 404);
            }
            
            // Load product count
            $brand->loadCount('products');
            Log::info('Found brand to delete:', [
                'id' => $brand->id, 
                'name' => $brand->name, 
                'products_count' => $brand->products_count
            ]);
            
            // Check if brand has associated products
            if ($brand->products_count > 0) {
                Log::warning('Cannot delete brand as it has associated products:', ['id' => $id, 'products_count' => $brand->products_count]);
                
                // Instead of deleting, deactivate the brand
                $brand->update(['is_active' => false]);
                Log::info('Brand deactivated instead of deleted:', ['id' => $id]);
                
                // Create error message with explicit newlines instead of \n for better JSON encoding
                $productsText = $brand->products_count == 1 ? 'produkt' : 
                               ($brand->products_count < 5 ? 'produkty' : 'produktów');
                               
                // Build the error message with actual newlines, not string literals
                $errorMessage = "Nie można usunąć marki \"{$brand->name}\" (ID: {$brand->id})." . "\n\n"
                              . "Przyczyna: Marka zawiera {$brand->products_count} {$productsText}." . "\n\n"
                              . "Aby zachować integralność danych, marki zawierające produkty nie mogą zostać usunięte." . "\n"
                              . "Zamiast tego marka została dezaktywowana i nie będzie widoczna dla klientów." . "\n\n"
                              . "Możliwe rozwiązania:" . "\n"
                              . "1. Przypisz produkty do innej marki, a następnie usuń tę markę." . "\n"
                              . "2. Pozostaw markę dezaktywowaną, jeśli chcesz zachować historię zamówień.";
                
                // Debug the actual message being sent, including newline handling
                Log::debug('Error message being sent:', [
                    'raw_message' => $errorMessage,
                    'json_encoded' => json_encode($errorMessage),
                    'has_newlines' => str_contains($errorMessage, "\n") ? 'Yes' : 'No',
                    'length' => strlen($errorMessage)
                ]);
                
                // Create direct JSON response with proper error format
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage
                ], 422, [
                    'Content-Type' => 'application/json;charset=UTF-8'
                ]);
            }
            
            // If we reach here, the brand has no products and can be deleted
            $brand->delete();
            Log::info('Brand deleted successfully', ['id' => $id]);

            return response()->json([
                'success' => true,
                'message' => 'Marka została usunięta pomyślnie'
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting brand: ' . $e->getMessage(), [
                'id' => $id,
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Wystąpił błąd podczas usuwania marki: ' . $e->getMessage()
            ], 500);
        }
    }
} 