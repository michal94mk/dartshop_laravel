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
            'weight' => 'nullable|numeric|min:0.01',
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
            $product->weight = $validated['weight'] ?? null;
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
            'weight' => 'nullable|numeric|min:0.01',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'required|exists:brands,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);
        
        try {
            // Update product properties
            $product->name = $validated['name'];
            $product->description = $validated['description'];
            $product->price = $validated['price'];
            $product->weight = $validated['weight'] ?? null;
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
        // Pobierz parametry filtrowania
        $selectedCategories = $request->input('categories', []);
        $selectedBrands = $request->input('brands', []);
        $priceMin = $request->input('price_min');
        $priceMax = $request->input('price_max');
        $sort = $request->input('sort');
        $paginate = $request->input('paginate', 9);

        $query = Product::query()->with(['category', 'brand']);

        // Filtry kategorii
        if (!empty($selectedCategories)) {
            $query->whereIn('category_id', $selectedCategories);
        }
        
        // Filtry marek
        if (!empty($selectedBrands)) {
            $query->whereIn('brand_id', $selectedBrands);
        }

        // Filtry zakresu cen
        if (!empty($priceMin)) {
            $query->where('price', '>=', $priceMin);
        }
        
        if (!empty($priceMax)) {
            $query->where('price', '<=', $priceMax);
        }

        // Sortowanie
        if (!empty($sort)) {
            switch($sort) {
                case 'price-asc':
                    $query->orderBy('price', 'asc');
                    break;
                case 'price-desc':
                    $query->orderBy('price', 'desc');
                    break;
                case 'name-asc':
                    $query->orderBy('name', 'asc');
                    break;
                case 'name-desc':
                    $query->orderBy('name', 'desc');
                    break;
                case 'newest':
                    $query->orderBy('created_at', 'desc');
                    break;
                default:
                    // Domyślnie sortuj według najnowszych
                    $query->orderBy('created_at', 'desc');
            }
        } else {
            // Domyślne sortowanie, jeśli nie podano parametru sort
            $query->orderBy('created_at', 'desc');
        }

        // Paginacja wyników
        $filteredProducts = $query->paginate($paginate);

        // Obsłuż żądania AJAX
        if ($request->ajax()) {
            return response()->json([
                'success' => true, 
                'data' => $filteredProducts->items(),
                'pagination' => [
                    'current_page' => $filteredProducts->currentPage(),
                    'last_page' => $filteredProducts->lastPage(),
                    'per_page' => $filteredProducts->perPage(),
                    'total' => $filteredProducts->total()
                ]
            ]);
        }

        // Pobranie wszystkich kategorii i marek do filtrów
        $categories = Category::all();
        $brands = Brand::all();
        
        return view('frontend.categories.index', compact('filteredProducts', 'categories', 'brands', 'selectedCategories', 'selectedBrands', 'priceMin', 'priceMax', 'sort'));
    }
}
