<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;

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
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0.01',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'required|exists:brands,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $path = $image->storeAs('public/images', $imageName);
            $data['image'] = Storage::url($path);
        }

        Product::create($data);

        return redirect()->route('admin.products.index')
            ->with('success', 'Produkt został dodany pomyślnie.');
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
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0.01',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'required|exists:brands,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($product->image && Storage::exists(str_replace('/storage', 'public', $product->image))) {
                Storage::delete(str_replace('/storage', 'public', $product->image));
            }
            
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $path = $image->storeAs('public/images', $imageName);
            $data['image'] = Storage::url($path);
        }

        $product->update($data);

        return redirect()->route('admin.products.index')
            ->with('success', 'Produkt został zaktualizowany pomyślnie.');
    }

    public function destroy(Product $product)
    {
        // Delete image if exists
        if ($product->image && Storage::exists(str_replace('/storage', 'public', $product->image))) {
            Storage::delete(str_replace('/storage', 'public', $product->image));
        }
        
        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', 'Produkt został usunięty.');
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
