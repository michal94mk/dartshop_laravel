<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{
    /**
     * Display a listing of products.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $query = Product::with(['category', 'brand']);
        
        // Apply filters if any
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        
        if ($request->has('brand_id')) {
            $query->where('brand_id', $request->brand_id);
        }
        
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        // Apply sorting
        $sortField = $request->sort_by ?? 'created_at';
        $sortDirection = $request->sort_direction ?? 'desc';
        $query->orderBy($sortField, $sortDirection);
        
        // Pagination
        $perPage = $request->per_page ?? 12;
        $products = $query->paginate($perPage);
        
        return response()->json($products);
    }
    
    /**
     * Display the specified product.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $product = Product::with(['category', 'brand', 'reviews' => function($query) {
            $query->approved()->latest();
        }])->findOrFail($id);
        
        return response()->json($product);
    }
    
    /**
     * Display featured products.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function featured()
    {
        $products = Product::with(['category', 'brand'])
            ->where('featured', true)
            ->latest()
            ->take(8)
            ->get();
        
        return response()->json($products);
    }
} 