<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\CategoryRequest;
use App\Http\Traits\HandlesTailwindViews;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends BaseAdminController
{
    use HandlesTailwindViews;

    /**
     * Display a listing of categories.
     */
    public function index()
    {
        $perPage = $this->getPerPage();
        $categories = Category::with('products')->paginate($perPage);
        
        return view($this->getViewType(
            'admin.categories.index', 
            'admin.categories.index-tailwind'
        ), compact('categories'));
    }

    /**
     * Show the form for creating a new category.
     */
    public function create()
    {
        return view($this->getViewType(
            'admin.categories.create', 
            'admin.categories.form-tailwind'
        ));
    }

    /**
     * Store a newly created category in storage.
     */
    public function store(CategoryRequest $request)
    {
        Category::create($request->validated());

        return redirect()->route('admin.categories.index', $this->appendTailwindParam())
            ->with('success', 'Kategoria została pomyślnie utworzona.');
    }

    /**
     * Display the specified category.
     */
    public function show(Category $category)
    {
        return view('admin.categories.show', compact('category'));
    }

    /**
     * Show the form for editing the specified category.
     */
    public function edit(Category $category)
    {
        return view($this->getViewType(
            'admin.categories.edit', 
            'admin.categories.form-tailwind'
        ), compact('category'));
    }

    /**
     * Update the specified category in storage.
     */
    public function update(CategoryRequest $request, Category $category)
    {
        $category->update($request->validated());

        return redirect()->route('admin.categories.index', $this->appendTailwindParam())
            ->with('success', 'Kategoria została pomyślnie zaktualizowana.');
    }

    /**
     * Remove the specified category from storage.
     */
    public function destroy(Category $category)
    {
        // Option 1: Delete related products 
        // $category->products()->delete();
        
        // Option 2: Detach products from category (set category_id to null)
        // $category->products()->update(['category_id' => null]);
        
        $category->delete();

        return redirect()->route('admin.categories.index', $this->appendTailwindParam())
            ->with('success', 'Kategoria została pomyślnie usunięta.');
    }
} 