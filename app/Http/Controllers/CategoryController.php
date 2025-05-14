<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Http\Controllers\Admin\BaseAdminController;

class CategoryController extends BaseAdminController
{
    public function index()
    {
        $perPage = $this->getPerPage();
        $categories = Category::with('products')->paginate($perPage);
        
        // Check if request is for Tailwind view
        if (request()->has('tailwind')) {
            return view('admin.categories.index-tailwind', compact('categories'));
        }
        
        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        // Check if request is for Tailwind view
        if (request()->has('tailwind')) {
            return view('admin.categories.form-tailwind');
        }
        
        return view('admin.categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories',
        ]);

        Category::create($validated);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Kategoria została pomyślnie utworzona.');
    }

    public function show(Category $category)
    {
        return view('admin.categories.show', compact('category'));
    }

    public function edit(Category $category)
    {
        // Check if request is for Tailwind view
        if (request()->has('tailwind')) {
            return view('admin.categories.form-tailwind', compact('category'));
        }
        
        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
        ]);

        $category->update($validated);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Kategoria została pomyślnie zaktualizowana.');
    }

    public function destroy(Category $category)
    {
        // Option 1: Delete related products 
        // $category->products()->delete();
        
        // Option 2: Detach products from category (set category_id to null)
        // $category->products()->update(['category_id' => null]);
        
        $category->delete();

        return redirect()->route('admin.categories.index')
            ->with('success', 'Kategoria została pomyślnie usunięta.');
    }
}
