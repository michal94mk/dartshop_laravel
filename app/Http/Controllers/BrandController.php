<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Brand;


class BrandController extends Controller
{
    public function index()
    {
        $brands = Brand::paginate(10);
        return view('admin.brands.index', compact('brands'));
    }

    public function create()
    {
        return view('admin.brands.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:brands',
        ]);

        Brand::create($data);

        return redirect()->route('admin.brands.index')->with('success', 'Brand has been added.');
    }

    public function edit($id)
    {
        $brand = Brand::find($id);
        return view('admin.brands.edit', compact('brand'));
    }

    public function update(Request $request, $id)
    {
        $brand = Brand::find($id);

        $data = $request->validate([
            'name' => 'required|string|max:255|unique:brands,name,'.$id,
        ]);

        $brand->update($data);

        return redirect()->route('admin.brands.index')->with('success', 'Brand has been updated.');
    }

    public function destroy(Brand $brand)
    {
        if ($brand->products->count() > 0) {
            return back()->with('error', 'Cannot delete the brand because it has associated products.');
        }

        $brand->delete();

        return redirect()->route('admin.brands.index')->with('success', 'Brand deleted successfully.');
    }
}
