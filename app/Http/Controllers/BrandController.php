<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Brand;


class BrandController extends Controller
{
    public function index()
    {
        $brands = Brand::paginate(10);
        
        // Check if request is for Tailwind view
        if (request()->has('tailwind')) {
            return view('admin.brands.index-tailwind', compact('brands'));
        }
        
        return view('admin.brands.index', compact('brands'));
    }

    public function create()
    {
        // Check if request is for Tailwind view
        if (request()->has('tailwind')) {
            return view('admin.brands.form-tailwind');
        }
        
        return view('admin.brands.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:brands',
        ]);

        Brand::create($data);

        return redirect()->route('admin.brands.index', $request->has('tailwind') ? ['tailwind' => 1] : [])
            ->with('success', 'Brand has been added.');
    }

    public function edit($id)
    {
        $brand = Brand::find($id);
        
        // Check if request is for Tailwind view
        if (request()->has('tailwind')) {
            return view('admin.brands.form-tailwind', compact('brand'));
        }
        
        return view('admin.brands.edit', compact('brand'));
    }

    public function update(Request $request, $id)
    {
        $brand = Brand::find($id);

        $data = $request->validate([
            'name' => 'required|string|max:255|unique:brands,name,'.$id,
        ]);

        $brand->update($data);

        return redirect()->route('admin.brands.index', $request->has('tailwind') ? ['tailwind' => 1] : [])
            ->with('success', 'Brand has been updated.');
    }

    public function destroy(Request $request, Brand $brand)
    {
        if ($brand->products->count() > 0) {
            return back()->with('error', 'Cannot delete the brand because it has associated products.');
        }

        $brand->delete();

        return redirect()->route('admin.brands.index', $request->has('tailwind') ? ['tailwind' => 1] : [])
            ->with('success', 'Brand deleted successfully.');
    }
}
