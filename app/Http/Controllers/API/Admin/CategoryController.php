<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

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
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            \Illuminate\Support\Facades\Log::info('Admin CategoryController@index called');
            \Illuminate\Support\Facades\Log::info('Request URI: ' . request()->getRequestUri());
            \Illuminate\Support\Facades\Log::info('Request method: ' . request()->method());
            \Illuminate\Support\Facades\Log::info('Request headers: ' . json_encode(request()->headers->all()));
            
            // Check authentication
            \Illuminate\Support\Facades\Log::info('Auth check: ' . (\Illuminate\Support\Facades\Auth::check() ? 'true' : 'false'));
            if (\Illuminate\Support\Facades\Auth::check()) {
                $user = \Illuminate\Support\Facades\Auth::user();
                \Illuminate\Support\Facades\Log::info('Auth user: ' . $user->email);
                \Illuminate\Support\Facades\Log::info('User roles: ' . json_encode($user->roles));
            } else {
                \Illuminate\Support\Facades\Log::info('User not authenticated');
            }
            
            \Illuminate\Support\Facades\Log::info('Session data: ' . json_encode(session()->all()));
            
            $categories = Category::withCount('products')->get();
            
            \Illuminate\Support\Facades\Log::info('Admin CategoryController@index success. Categories count: ' . $categories->count());
            
            return response()->json($categories);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('CategoryController@index error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
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
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'parent_id' => 'nullable|exists:categories,id',
                'slug' => 'nullable|string|max:255|unique:categories,slug',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $data = $request->all();
            
            // Generate slug if not provided
            if (empty($data['slug'])) {
                $data['slug'] = Str::slug($data['name']);
            }

            $category = Category::create($data);

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
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'parent_id' => 'nullable|exists:categories,id',
                'slug' => 'nullable|string|max:255|unique:categories,slug,' . $id,
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $data = $request->all();
            
            // Generate slug if not provided
            if (empty($data['slug'])) {
                $data['slug'] = Str::slug($data['name']);
            }

            $category->update($data);

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
        try {
            $category = Category::findOrFail($id);
            $category->delete();

            return $this->successResponse('Category deleted successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Error deleting category: ' . $e->getMessage(), 500);
        }
    }
} 