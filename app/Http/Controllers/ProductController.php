<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with(['category', 'brand'])->paginate(10);
        
        // Check if request is for Tailwind view
        if (request()->has('tailwind')) {
            return view('admin.products.index-tailwind', compact('products'));
        }
        
        return view('admin.products.index', compact('products'));
    }

    public function indexForRegularUsers()
    {
        $products = Product::paginate(10);
        return view('frontend.categories.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::all();
        $brands = Brand::all();
        
        // Check if request is for Tailwind view
        if (request()->has('tailwind')) {
            return view('admin.products.form-tailwind', compact('categories', 'brands'));
        }
        
        return view('admin.products.create', compact('categories', 'brands'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0.01',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'required|exists:brands,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);
        
        try {
            // Create product without image first
            $product = new Product();
            $product->name = $validated['name'];
            $product->description = $validated['description'];
            $product->price = $validated['price'];
            $product->category_id = $validated['category_id'];
            $product->brand_id = $validated['brand_id'];
            
            // Handle image upload if present
            if ($request->hasFile('image') && $request->file('image')->isValid()) {
                $image = $request->file('image');
                $filename = Str::slug($product->name) . '-' . time() . '.' . $image->getClientOriginalExtension();
                
                // Log debug info
                Log::info('Uploading image', [
                    'original_name' => $image->getClientOriginalName(),
                    'mime_type' => $image->getMimeType(),
                    'size' => $image->getSize(),
                    'destination' => 'public/images/' . $filename
                ]);
                
                // Store with direct method instead of helper
                $image->move(public_path('storage/images'), $filename);
                
                // Set the path directly for DB
                $product->image = 'storage/images/' . $filename;
            }
            
            $product->save();
            
            return redirect()->route('admin.products.index')
                ->with('success', 'Produkt został dodany pomyślnie.');
        } catch (\Exception $e) {
            Log::error('Error creating product', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Wystąpił błąd podczas dodawania produktu: ' . $e->getMessage());
        }
    }

    public function show(Product $product)
    {
        // Check if request is for Tailwind view
        if (request()->has('tailwind')) {
            return view('admin.products.show-tailwind', compact('product'));
        }
        
        return view('admin.products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $categories = Category::all();
        $brands = Brand::all();
        
        // Check if request is for Tailwind view
        if (request()->has('tailwind')) {
            return view('admin.products.form-tailwind', compact('product', 'categories', 'brands'));
        }
        
        return view('admin.products.edit', compact('product', 'categories', 'brands'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0.01',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'required|exists:brands,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);
        
        try {
            // Update product properties
            $product->name = $validated['name'];
            $product->description = $validated['description'];
            $product->price = $validated['price'];
            $product->category_id = $validated['category_id'];
            $product->brand_id = $validated['brand_id'];
            
            // Handle image upload if present
            if ($request->hasFile('image') && $request->file('image')->isValid()) {
                // Delete the old image if it exists
                if ($product->image) {
                    $oldImagePath = public_path($product->image);
                    if (file_exists($oldImagePath)) {
                        @unlink($oldImagePath);
                    }
                }
                
                $image = $request->file('image');
                $filename = Str::slug($product->name) . '-' . time() . '.' . $image->getClientOriginalExtension();
                
                // Log debug info
                Log::info('Updating image', [
                    'original_name' => $image->getClientOriginalName(),
                    'mime_type' => $image->getMimeType(),
                    'size' => $image->getSize(),
                    'destination' => 'public/images/' . $filename
                ]);
                
                // Store with direct method
                $image->move(public_path('storage/images'), $filename);
                
                // Make sure path is public accessible
                $product->image = 'storage/images/' . $filename;
            }
            
            $product->save();
            
            return redirect()->route('admin.products.index')
                ->with('success', 'Produkt został zaktualizowany pomyślnie.');
        } catch (\Exception $e) {
            Log::error('Error updating product', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Wystąpił błąd podczas aktualizacji produktu: ' . $e->getMessage());
        }
    }

    public function destroy(Product $product)
    {
        try {
            // Delete the image if it exists
            if ($product->image) {
                $imagePath = public_path($product->image);
                if (file_exists($imagePath)) {
                    @unlink($imagePath);
                }
            }
            
            $product->delete();
            
            return redirect()->route('admin.products.index')
                ->with('success', 'Produkt został usunięty.');
        } catch (\Exception $e) {
            Log::error('Error deleting product', [
                'error' => $e->getMessage(),
                'product_id' => $product->id
            ]);
            
            return redirect()->back()
                ->with('error', 'Wystąpił błąd podczas usuwania produktu: ' . $e->getMessage());
        }
    }

    public function filterProducts(Request $request)
    {
        $selectedCategories = $request->input('categories', []);
        $selectedBrands = $request->input('brands', []);

        $query = Product::query();

        if (!empty($selectedCategories)) {
            $query->whereIn('category_id', $selectedCategories);
        }
        
        if (!empty($selectedBrands)) {
            $query->whereIn('brand_id', $selectedBrands);
        }

        $filteredProducts = $query->get();

        if ($request->ajax()) {
            return response()->json(['success' => true, 'filteredProducts' => $filteredProducts]);
        }

        $categories = Category::all();
        $brands = Brand::all();
        
        // Check if request is for Tailwind view
        if (request()->has('tailwind')) {
            return view('frontend.categories.index-tailwind', compact('filteredProducts', 'categories', 'brands'));
        }

        return view('frontend.categories.index', compact('filteredProducts'));
    }
}
