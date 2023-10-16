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
        $products = Product::paginate(10);
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
        return view('admin.products.create', compact('categories', 'brands'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'category_id' => 'required',
            'brand_id' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $input = $request->all();

        if ($request->hasFile('image')) {
            $destinationPath = '/images';
            $image = $request->file('image');
            $imageName = $image->getClientOriginalName();
            $path = $request->file('image')->storeAs($destinationPath, $imageName);
            $input['image'] = '/storage/'.$path;

            Product::create($input);
            return redirect()->route('admin.products.index')->with('success', 'Product has been added.');
        }
    }

    public function show(Product $product)
    {
        return view('admin.products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $categories = Category::all();
        $brands = Brand::all();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'category_id' => 'required',
            'brand_id' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();

            $path = $image->storeAs('public/images', $imageName);

            $imageUrl = Storage::url($path);

            $data['image'] = $imageUrl;
        }

        $product->update($data);

        return redirect()->route('admin.products.index')->with('success', 'Product has been updated.');
    }

    public function destroy(Product $product)
    {
        //dd($product->id);
        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Product has been deleted.');
    }

    public function filterProducts(Request $request)
    {
        $selectedCategories = $request->input('categories', []);

        $query = Product::query();

        if (!empty($selectedCategories)) {
            $query->whereIn('category_id', $selectedCategories);
        }

        $filteredProducts = $query->get();

        if ($request->ajax()) {
            return response()->json(['success' => true, 'filteredProducts' => $filteredProducts]);
        }

        return view('frontend.categories.index', compact('filteredProducts'));
    }
}
