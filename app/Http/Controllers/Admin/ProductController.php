<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\ProductRequest;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProductController extends BaseAdminController
{
    /**
     * The image service instance.
     */
    protected ImageService $imageService;

    /**
     * Create a new controller instance.
     */
    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    /**
     * Display a listing of the products.
     */
    public function index(Request $request)
    {
        $perPage = $this->getPerPage();
        $query = Product::with(['category', 'brand']);
        
        // Wyszukiwanie przez metodę z BaseAdminController
        $this->applySearch($query, $request, ['name', 'description']);
        
        $products = $query->paginate($perPage);
        
        return view('admin.products.index', compact('products'));
    }

    /**
     * Display a listing of products for regular users.
     */
    public function indexForRegularUsers()
    {
        $products = Product::paginate(10);
        return view('frontend.categories.index', compact('products'));
    }

    /**
     * Show the form for creating a new product.
     */
    public function create()
    {
        $categories = Category::all();
        $brands = Brand::all();
        
        return view('admin.products.create', compact('categories', 'brands'));
    }

    /**
     * Store a newly created product in storage.
     * 
     * @param ProductRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception When image upload fails or database operation fails
     */
    public function store(ProductRequest $request)
    {
        try {
            $product = new Product();
            $product->fill($request->safe()->except('image'));
            
            // Handle image upload if present
            if ($request->hasFile('image')) {
                $product->image = $this->imageService->uploadImage(
                    $request->file('image'),
                    $product->name
                );
            }
            
            $product->save();
            
            return redirect()->route('admin.products.index')
                ->with('success', 'Product has been added successfully.');
        } catch (\Exception $e) {
            Log::error('Error creating product', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'An error occurred while adding the product: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified product.
     */
    public function show(Product $product)
    {
        return view('admin.products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified product.
     */
    public function edit(Product $product)
    {
        $categories = Category::all();
        $brands = Brand::all();
        
        return view('admin.products.edit', compact('product', 'categories', 'brands'));
    }

    /**
     * Update the specified product in storage.
     * 
     * @param ProductRequest $request
     * @param Product $product
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception When image processing fails or database operation fails
     */
    public function update(ProductRequest $request, Product $product)
    {
        try {
            $product->fill($request->safe()->except('image'));
            
            // Handle image upload if present
            if ($request->hasFile('image')) {
                // Delete the old image if it exists
                $this->imageService->deleteImage($product->image);
                
                // Upload the new image
                $product->image = $this->imageService->uploadImage(
                    $request->file('image'),
                    $product->name
                );
            }
            
            $product->save();
            
            return redirect()->route('admin.products.index')
                ->with('success', 'Product has been updated successfully.');
        } catch (\Exception $e) {
            Log::error('Error updating product', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'An error occurred while updating the product: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified product from storage.
     * 
     * @param Product $product
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception When image deletion fails or database operation fails
     */
    public function destroy(Product $product)
    {
        try {
            // Delete the image if it exists
            $this->imageService->deleteImage($product->image);
            
            $product->delete();
            
            return redirect()->route('admin.products.index')
                ->with('success', 'Product has been deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Error deleting product', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()
                ->with('error', 'An error occurred while deleting the product: ' . $e->getMessage());
        }
    }
    
    /**
     * Filter products based on request parameters.
     */
    public function filterProducts(Request $request)
    {
        $query = Product::query();
        
        // Apply category filter
        if ($request->has('category_id') && $request->category_id) {
            $query->where('category_id', $request->category_id);
        }
        
        // Apply brand filter
        if ($request->has('brand_id') && $request->brand_id) {
            $query->where('brand_id', $request->brand_id);
        }
        
        // Apply price range filter
        if ($request->has('price_min') && $request->price_min) {
            $query->where('price', '>=', $request->price_min);
        }
        
        if ($request->has('price_max') && $request->price_max) {
            $query->where('price', '<=', $request->price_max);
        }
        
        // Get the filtered products
        $products = $query->paginate(12);
        
        return view('frontend.products.filtered', compact('products'));
    }

    /**
     * Search for products.
     */
    public function search(Request $request)
    {
        $search = $request->input('search');
        
        $products = Product::where('name', 'like', "%{$search}%")
            ->orWhere('description', 'like', "%{$search}%")
            ->paginate(10);
            
        return view('frontend.products.search', compact('products', 'search'));
    }
} 