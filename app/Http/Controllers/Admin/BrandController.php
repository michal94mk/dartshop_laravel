<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\Brand;
use App\Http\Controllers\Controller;

class BrandController extends BaseAdminController
{
    public function index()
    {
        $perPage = $this->getPerPage();
        $brands = Brand::paginate($perPage);
        
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

        return redirect()->route('admin.brands.index')
            ->with('success', 'Marka została pomyślnie dodana.');
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

        return redirect()->route('admin.brands.index')
            ->with('success', 'Marka została pomyślnie zaktualizowana.');
    }

    public function destroy(Request $request, Brand $brand)
    {
        if ($brand->products->count() > 0) {
            return back()->with('error', 'Nie można usunąć marki, ponieważ ma powiązane produkty.');
        }

        $brand->delete();

        return redirect()->route('admin.brands.index')
            ->with('success', 'Marka została pomyślnie usunięta.');
    }
} 