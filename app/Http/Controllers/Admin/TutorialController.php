<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tutorial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TutorialController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tutorials = Tutorial::latest()->get();
        
        return view('admin.tutorials.index', compact('tutorials'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.tutorials.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:tutorials',
            'content' => 'required',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'thumbnail_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:1024',
            'category' => 'nullable|string|max:100',
            'difficulty' => 'required|in:beginner,intermediate,advanced',
            'is_published' => 'boolean',
            'published_at' => 'nullable|date',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'excerpt' => 'nullable|string',
        ]);
        
        // Set slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }
        
        // Handle featured image upload
        if ($request->hasFile('featured_image')) {
            $validated['featured_image'] = $this->uploadImage($request->file('featured_image'), 'tutorials/featured');
        }
        
        // Handle thumbnail image upload
        if ($request->hasFile('thumbnail_image')) {
            $validated['thumbnail_image'] = $this->uploadImage($request->file('thumbnail_image'), 'tutorials/thumbnails');
        }
        
        // Set published_at if published and not set
        if (isset($validated['is_published']) && $validated['is_published'] && empty($validated['published_at'])) {
            $validated['published_at'] = now();
        }
        
        Tutorial::create($validated);
        
        return redirect()->route('admin.tutorials.index')
            ->with('success', 'Tutorial created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Tutorial $tutorial)
    {
        return view('admin.tutorials.show', compact('tutorial'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Tutorial $tutorial)
    {
        return view('admin.tutorials.edit', compact('tutorial'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Tutorial $tutorial)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:tutorials,slug,' . $tutorial->id,
            'content' => 'required',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'thumbnail_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:1024',
            'category' => 'nullable|string|max:100',
            'difficulty' => 'required|in:beginner,intermediate,advanced',
            'is_published' => 'boolean',
            'published_at' => 'nullable|date',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'excerpt' => 'nullable|string',
        ]);
        
        // Set slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }
        
        // Handle featured image upload
        if ($request->hasFile('featured_image')) {
            // Delete old image if exists
            if ($tutorial->featured_image) {
                Storage::disk('public')->delete('images/' . $tutorial->featured_image);
            }
            
            $validated['featured_image'] = $this->uploadImage($request->file('featured_image'), 'tutorials/featured');
        }
        
        // Handle thumbnail image upload
        if ($request->hasFile('thumbnail_image')) {
            // Delete old image if exists
            if ($tutorial->thumbnail_image) {
                Storage::disk('public')->delete('images/' . $tutorial->thumbnail_image);
            }
            
            $validated['thumbnail_image'] = $this->uploadImage($request->file('thumbnail_image'), 'tutorials/thumbnails');
        }
        
        // Set published_at if published and not set
        if (isset($validated['is_published']) && $validated['is_published'] && empty($validated['published_at'])) {
            $validated['published_at'] = now();
        }
        
        $tutorial->update($validated);
        
        return redirect()->route('admin.tutorials.index')
            ->with('success', 'Tutorial updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tutorial $tutorial)
    {
        // Delete images if they exist
        if ($tutorial->featured_image) {
            Storage::disk('public')->delete('images/' . $tutorial->featured_image);
        }
        
        if ($tutorial->thumbnail_image) {
            Storage::disk('public')->delete('images/' . $tutorial->thumbnail_image);
        }
        
        $tutorial->delete();
        
        return redirect()->route('admin.tutorials.index')
            ->with('success', 'Tutorial deleted successfully.');
    }
    
    /**
     * Upload an image and return the path.
     *
     * @param \Illuminate\Http\UploadedFile $image
     * @param string $path
     * @return string
     */
    private function uploadImage($image, $path)
    {
        $filename = time() . '_' . Str::slug($image->getClientOriginalName()) . '.' . $image->getClientOriginalExtension();
        $image->storeAs('images/' . $path, $filename, 'public');
        
        return $path . '/' . $filename;
    }
}
