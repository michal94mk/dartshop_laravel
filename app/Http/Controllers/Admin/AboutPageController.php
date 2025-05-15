<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AboutPage;
use Illuminate\Http\Request;

class AboutPageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $aboutPages = AboutPage::orderBy('display_order')->get();
        
        return view('admin.about-pages.index', compact('aboutPages'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.about-pages.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'display_order' => 'required|integer|min:0',
            'is_active' => 'boolean',
        ]);
        
        AboutPage::create($validated);
        
        return redirect()->route('admin.about-pages.index')
            ->with('success', 'About page created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(AboutPage $aboutPage)
    {
        return view('admin.about-pages.show', compact('aboutPage'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AboutPage $aboutPage)
    {
        return view('admin.about-pages.edit', compact('aboutPage'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AboutPage $aboutPage)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'display_order' => 'required|integer|min:0',
            'is_active' => 'boolean',
        ]);
        
        $aboutPage->update($validated);
        
        return redirect()->route('admin.about-pages.index')
            ->with('success', 'About page updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AboutPage $aboutPage)
    {
        $aboutPage->delete();
        
        return redirect()->route('admin.about-pages.index')
            ->with('success', 'About page deleted successfully.');
    }
}
