<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;


class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::paginate(10);
        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:categories',
        ]);

        Category::create($data);

        return redirect()->route('admin.categories.index')->with('success', 'Category has been added.');
    }

    public function edit($id)
    {
        $category = Category::find($id);
        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, $id)
    {
        $category = Category::find($id);

        $data = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,'.$id,
        ]);

        $category->update($data);

        return redirect()->route('admin.categories.index')->with('success', 'Category has been updated.');
    }

    public function destroy(Category $category)
    {
        if ($category->products->count() > 0) {
            return back()->with('error', 'Cannot delete the category because it has associated products.');
        }

        $category->delete();

        return redirect()->route('admin.categories.index')->with('success', 'Category deleted successfully.');
    }
}
