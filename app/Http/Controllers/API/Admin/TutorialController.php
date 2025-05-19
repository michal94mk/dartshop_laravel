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
        $tutorials = Tutorial::with('author')
            ->latest()
            ->get();
            
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
            'author_id' => 'required|exists:users,id',
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

        $tutorial = Tutorial::create($request->all());
        return response()->json($tutorial, 201);
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
            'author_id' => 'sometimes|exists:users,id',
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

        $tutorial->update($request->all());
        return response()->json($tutorial);
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