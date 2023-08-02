<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    // Metoda do wyświetlania listy kategorii
    public function index()
    {
        $categories = Category::all();
        return view('categories.index', compact('categories'));
    }

    // Metoda do wyświetlania formularza tworzenia nowej kategorii
    public function create()
    {
        return view('categories.create');
    }

    // Metoda do zapisu nowej kategorii w bazie danych
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:categories',
        ]);

        Category::create($data);

        return redirect()->route('categories.index')->with('success', 'Kategoria została dodana.');
    }

    // Pozostałe metody, takie jak wyświetlanie, edycja, aktualizacja i usuwanie kategorii, możesz zaimplementować według swoich potrzeb.
}
