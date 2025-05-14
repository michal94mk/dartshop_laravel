<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\BrandRequest;
use App\Models\Brand;
use Illuminate\Http\Request;

class BrandController extends BaseAdminController
{
    /**
     * Display a listing of brands.
     */
    public function index(Request $request)
    {
        $perPage = $this->getPerPage();
        $query = Brand::query()->with('products');
        
        // Wyszukiwanie przez metodę z BaseAdminController
        $this->applySearch($query, $request, ['name']);
        
        $brands = $query->paginate($perPage);
        
        return view('admin.brands.index', compact('brands'));
    }

    /**
     * Show the form for creating a new brand.
     */
    public function create()
    {
        return view('admin.brands.create');
    }

    /**
     * Store a newly created brand in storage.
     */
    public function store(BrandRequest $request)
    {
        Brand::create($request->validated());

        return redirect()->route('admin.brands.index')
            ->with('success', 'Brand has been added successfully.');
    }

    /**
     * Show the form for editing the specified brand.
     */
    public function edit(Brand $brand)
    {
        return view('admin.brands.edit', compact('brand'));
    }

    /**
     * Update the specified brand in storage.
     */
    public function update(BrandRequest $request, Brand $brand)
    {
        $brand->update($request->validated());

        return redirect()->route('admin.brands.index')
            ->with('success', 'Brand has been updated successfully.');
    }

    /**
     * Remove the specified brand from storage.
     */
    public function destroy(Request $request, Brand $brand)
    {
        if ($brand->products->count() > 0) {
            return back()->with('error', 'Cannot delete brand because it has associated products.');
        }

        $brand->delete();

        return redirect()->route('admin.brands.index')
            ->with('success', 'Brand has been deleted successfully.');
    }
} 