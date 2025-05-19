<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tutorial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class TutorialController extends BaseAdminController
{
    /**
     * Display a listing of the tutorials.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tutorials = Tutorial::with('user:id,name')
            ->latest()
            ->get()
            ->map(function ($tutorial) {
                return [
                    'id' => $tutorial->id,
                    'title' => $tutorial->title,
                    'slug' => $tutorial->slug,
                    'excerpt' => $tutorial->excerpt,
                    'content' => $tutorial->content,
                    'image_url' => $tutorial->featured_image ? asset('storage/images/' . $tutorial->featured_image) : null,
                    'published_at' => $tutorial->published_at,
                    'status' => $tutorial->is_published ? 'published' : 'draft',
                    'author' => $tutorial->user,
                    'meta_title' => $tutorial->meta_title,
                    'meta_description' => $tutorial->meta_description,
                    'featured' => false, // Default value as it's not in the schema
                    'created_at' => $tutorial->created_at,
                    'updated_at' => $tutorial->updated_at
                ];
            });
            
        return response()->json($tutorials);
    }

    /**
     * Store a newly created tutorial in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:tutorials',
            'excerpt' => 'nullable|string',
            'content' => 'required|string',
            'image_url' => 'nullable|url',
            'published_at' => 'nullable|date',
            'status' => 'required|string|in:draft,published,scheduled',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'featured' => 'boolean'
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        // Generate slug if not provided
        if (empty($request->slug)) {
            $request->merge(['slug' => Str::slug($request->title)]);
        }

        // Transform the input data to match the database schema
        $tutorialData = [
            'title' => $request->title,
            'slug' => $request->slug,
            'excerpt' => $request->excerpt,
            'content' => $request->content,
            'meta_title' => $request->meta_title,
            'meta_description' => $request->meta_description,
            'user_id' => $request->user()->id, // Correct way to get authenticated user ID
        ];
        
        // Handle status
        $tutorialData['is_published'] = $request->status === 'published';
        
        // Handle published date
        if ($request->status === 'published' && !$request->published_at) {
            $tutorialData['published_at'] = now();
        } elseif ($request->status === 'scheduled') {
            $tutorialData['published_at'] = $request->published_at;
        }
        
        // Handle image URL
        if ($request->image_url) {
            // This would typically involve image processing/storage
            // For now, just store the URL or filename
            $tutorialData['featured_image'] = basename($request->image_url);
        }

        $tutorial = Tutorial::create($tutorialData);
        
        // Return the transformed tutorial data
        return response()->json([
            'id' => $tutorial->id,
            'title' => $tutorial->title,
            'slug' => $tutorial->slug,
            'excerpt' => $tutorial->excerpt,
            'content' => $tutorial->content,
            'image_url' => $tutorial->featured_image ? asset('storage/images/' . $tutorial->featured_image) : null,
            'published_at' => $tutorial->published_at,
            'status' => $tutorial->is_published ? 'published' : 'draft',
            'author' => $tutorial->user,
            'meta_title' => $tutorial->meta_title,
            'meta_description' => $tutorial->meta_description,
            'featured' => false, // Default value
            'created_at' => $tutorial->created_at,
            'updated_at' => $tutorial->updated_at
        ], 201);
    }

    /**
     * Display the specified tutorial.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $tutorial = Tutorial::with('author')->findOrFail($id);
        return response()->json($tutorial);
    }

    /**
     * Update the specified tutorial in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $tutorial = Tutorial::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|string|max:255',
            'slug' => 'sometimes|string|max:255|unique:tutorials,slug,' . $tutorial->id,
            'excerpt' => 'nullable|string',
            'content' => 'sometimes|string',
            'image_url' => 'nullable|url',
            'published_at' => 'nullable|date',
            'status' => 'sometimes|string|in:draft,published,scheduled',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'featured' => 'boolean'
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        // Generate slug if title is changed but slug is not provided
        if ($request->has('title') && (!$request->has('slug') || empty($request->slug))) {
            $request->merge(['slug' => Str::slug($request->title)]);
        }

        // Transform the input data to match the database schema
        $updateData = [
            'title' => $request->title ?? $tutorial->title,
            'slug' => $request->slug ?? $tutorial->slug,
            'excerpt' => $request->excerpt,
            'content' => $request->content ?? $tutorial->content,
            'meta_title' => $request->meta_title,
            'meta_description' => $request->meta_description,
        ];
        
        // Handle status conversion
        if ($request->has('status')) {
            $updateData['is_published'] = $request->status === 'published';
            if ($request->status === 'published' && !$tutorial->published_at) {
                $updateData['published_at'] = now();
            } elseif ($request->status === 'scheduled') {
                $updateData['published_at'] = $request->published_at;
                $updateData['is_published'] = false;
            }
        }
        
        // Handle image URL
        if ($request->has('image_url') && $request->image_url) {
            // This would typically involve image processing/storage
            // For now, just store the URL or filename
            $updateData['featured_image'] = basename($request->image_url);
        }

        $tutorial->update($updateData);
        
        // Return the transformed tutorial data
        return response()->json([
            'id' => $tutorial->id,
            'title' => $tutorial->title,
            'slug' => $tutorial->slug,
            'excerpt' => $tutorial->excerpt,
            'content' => $tutorial->content,
            'image_url' => $tutorial->featured_image ? asset('storage/images/' . $tutorial->featured_image) : null,
            'published_at' => $tutorial->published_at,
            'status' => $tutorial->is_published ? 'published' : 'draft',
            'author' => $tutorial->user,
            'meta_title' => $tutorial->meta_title,
            'meta_description' => $tutorial->meta_description,
            'featured' => false, // Default value
            'created_at' => $tutorial->created_at,
            'updated_at' => $tutorial->updated_at
        ]);
    }

    /**
     * Remove the specified tutorial from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $tutorial = Tutorial::findOrFail($id);
        $tutorial->delete();

        return response()->json(null, 204);
    }
} 