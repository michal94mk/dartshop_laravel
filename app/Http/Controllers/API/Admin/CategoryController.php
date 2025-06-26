<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class CategoryController extends BaseAdminController
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        \Illuminate\Support\Facades\Log::info('Admin CategoryController initialized');
    }

    /**
     * Display a listing of the categories.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            \Illuminate\Support\Facades\Log::info('Admin CategoryController@index called with filters:', $request->all());
            
            $query = Category::withCount('products');
            
            // Apply search filter
            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
            }
            
            // Apply sorting
            $sortField = $request->sort_field ?? 'created_at';
            $sortDirection = $request->sort_direction ?? 'desc';
            
            // Handle special case for products_count which is not a direct database column
            if ($sortField === 'products_count') {
                $query->orderBy('products_count', $sortDirection);
            } else {
                // Make sure the column exists in the categories table to prevent SQL errors
                if (in_array($sortField, ['id', 'name', 'created_at', 'updated_at'])) {
                    $query->orderBy($sortField, $sortDirection);
                } else {
                    $query->orderBy('created_at', 'desc'); // Default fallback
                }
            }
            
            // Paginate results
            $perPage = $request->per_page ?? 10;
            $categories = $query->paginate($perPage);
            
            \Illuminate\Support\Facades\Log::info('Admin CategoryController@index success. Categories count: ' . $categories->count());
            
            return response()->json($categories);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('CategoryController@index error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);
            return $this->errorResponse('Error fetching categories: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Store a newly created category in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255|unique:categories,name'
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $categoryData = [
                'name' => $request->name
            ];

            $category = Category::create($categoryData);

            return $this->successResponse('Category created successfully', $category, 201);
        } catch (\Exception $e) {
            return $this->errorResponse('Error creating category: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Display the specified category.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            $category = Category::with('products')->findOrFail($id);
            return response()->json($category);
        } catch (\Exception $e) {
            return $this->errorResponse('Error fetching category: ' . $e->getMessage(), 404);
        }
    }

    /**
     * Update the specified category in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        try {
            $category = Category::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255|unique:categories,name,' . $id
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $updateData = [
                'name' => $request->name
            ];

            $category->update($updateData);

            return $this->successResponse('Category updated successfully', $category);
        } catch (\Exception $e) {
            return $this->errorResponse('Error updating category: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Remove the specified category from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        \Illuminate\Support\Facades\Log::info('CategoryController@destroy called for ID: ' . $id);
        
        try {
            // First check if the category exists and load it
            if (!$category = Category::find($id)) {
                \Illuminate\Support\Facades\Log::warning('Category not found during deletion attempt:', ['id' => $id]);
                
                // Build and log response for debugging
                $response = [
                    'success' => false,
                    'message' => 'Nie znaleziono kategorii o ID: ' . $id
                ];
                \Illuminate\Support\Facades\Log::debug('Sending 404 not found response', ['response' => $response]);
                
                return response()->json($response, 404);
            }
            
            // Load product count
            $category->loadCount('products');
            \Illuminate\Support\Facades\Log::info('Found category to delete:', [
                'id' => $category->id, 
                'name' => $category->name, 
                'products_count' => $category->products_count
            ]);
            
            // Check if category has associated products
            if ($category->products_count > 0) {
                \Illuminate\Support\Facades\Log::warning('Cannot delete category as it has associated products:', ['id' => $id, 'products_count' => $category->products_count]);
                
                // Create error message
                $productsText = $category->products_count == 1 ? 'produkt' : 
                               ($category->products_count < 5 ? 'produkty' : 'produktów');
                               
                $errorMessage = "Nie można usunąć kategorii \"{$category->name}\" (ID: {$category->id})." . PHP_EOL . PHP_EOL
                              . "Przyczyna: Kategoria zawiera {$category->products_count} {$productsText}." . PHP_EOL . PHP_EOL
                              . "Aby zachować integralność danych, kategorie zawierające produkty nie mogą zostać usunięte." . PHP_EOL . PHP_EOL
                              . "Możliwe rozwiązania:" . PHP_EOL
                              . "1. Przenieś produkty do innej kategorii, a następnie usuń tę kategorię." . PHP_EOL
                              . "2. Pozostaw kategorię jeśli chcesz zachować historię zamówień.";
                
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage
                ], 422, [
                    'Content-Type' => 'application/json;charset=UTF-8'
                ]);
            }
            
            // If we reach here, the category has no products and can be deleted
            $category->delete();
            \Illuminate\Support\Facades\Log::info('Category deleted successfully', ['id' => $id]);

            return response()->json([
                'success' => true,
                'message' => 'Kategoria została usunięta pomyślnie'
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error deleting category: ' . $e->getMessage(), [
                'id' => $id,
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Wystąpił błąd podczas usuwania kategorii: ' . $e->getMessage()
            ], 500);
        }
    }
} 