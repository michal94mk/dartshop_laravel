<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TutorialRequest;
use App\Models\Tutorial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class TutorialController extends BaseAdminController
{
    /**
     * Display a listing of the tutorials.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            // Don't try to eager load user since we don't have user_id in table
            $query = Tutorial::query();
            
            // Apply search filter
            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('content', 'like', "%{$search}%");
                });
            }
            
            // Apply status filter
            if ($request->has('status') && !empty($request->status)) {
                switch ($request->status) {
                    case 'published':
                        $query->where('is_published', true);
                        break;
                    case 'draft':
                        $query->where('is_published', false);
                        break;
                }
            }
            
            // Apply sorting
            $sortField = $request->sort_field ?? 'created_at';
            $sortDirection = $request->sort_direction ?? 'desc';
            
            // Map frontend sort fields to database fields
            $sortFieldMap = [
                'created_at' => 'created_at',
                'title' => 'title'
            ];
            
            $dbSortField = $sortFieldMap[$sortField] ?? 'created_at';
            $query->orderBy($dbSortField, $sortDirection);
            
            // Paginate results
            $perPage = $this->getPerPage($request);
            $tutorials = $query->paginate($perPage);
            
            // Transform the data
            $tutorials->getCollection()->transform(function ($tutorial) {
                return [
                    'id' => $tutorial->id,
                    'title' => $tutorial->title,
                    'slug' => $tutorial->slug,
                    'excerpt' => $tutorial->excerpt, // Uses getter
                    'content' => $tutorial->content,
                    'image_url' => $tutorial->image_url,
                    'order' => $tutorial->order,
                    'published_at' => $tutorial->published_at, // Uses getter (returns created_at)
                    'status' => $tutorial->is_published ? 'published' : 'draft',
                    'author' => $tutorial->user(), // Uses getter method
                    'is_published' => $tutorial->is_published,
                    'created_at' => $tutorial->created_at,
                    'updated_at' => $tutorial->updated_at
                ];
            });
            
            return response()->json($tutorials);
        } catch (\Exception $e) {
            return $this->errorResponse('Error fetching tutorials: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Store a newly created tutorial in storage.
     *
     * @param  \App\Http\Requests\Admin\TutorialRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(TutorialRequest $request)
    {
        // Generate slug if not provided
        $slug = $request->slug ?: Str::slug($request->title);

        // Transform the input data to match the database schema
        $tutorialData = $request->validated();
        $tutorialData['slug'] = $slug;
        $tutorialData['order'] = $request->order ?? 0;
        $tutorialData['is_published'] = $request->status === 'published';

        $tutorial = Tutorial::create($tutorialData);
        
        // Return the transformed tutorial data
        return response()->json([
            'id' => $tutorial->id,
            'title' => $tutorial->title,
            'slug' => $tutorial->slug,
            'excerpt' => $tutorial->excerpt, // Uses getter
            'content' => $tutorial->content,
            'image_url' => $tutorial->image_url,
            'order' => $tutorial->order,
            'published_at' => $tutorial->published_at, // Uses getter
            'status' => $tutorial->is_published ? 'published' : 'draft',
            'author' => $tutorial->user(), // Uses getter method
            'is_published' => $tutorial->is_published,
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
        $tutorial = Tutorial::findOrFail($id);
        
        return response()->json([
            'id' => $tutorial->id,
            'title' => $tutorial->title,
            'slug' => $tutorial->slug,
            'excerpt' => $tutorial->excerpt, // Uses getter
            'content' => $tutorial->content,
            'image_url' => $tutorial->image_url,
            'order' => $tutorial->order,
            'published_at' => $tutorial->published_at, // Uses getter
            'status' => $tutorial->is_published ? 'published' : 'draft',
            'author' => $tutorial->user(), // Uses getter method
            'is_published' => $tutorial->is_published,
            'created_at' => $tutorial->created_at,
            'updated_at' => $tutorial->updated_at
        ]);
    }

    /**
     * Update the specified tutorial in storage.
     *
     * @param  \App\Http\Requests\Admin\TutorialRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(TutorialRequest $request, $id)
    {
        $tutorial = Tutorial::findOrFail($id);

        // Generate slug if title is changed but slug is not provided
        $slug = $request->slug;
        if ($request->has('title') && (!$request->has('slug') || empty($request->slug))) {
            $slug = Str::slug($request->title);
        }

        // Transform the input data to match the database schema
        $updateData = $request->validated();
        
        if ($slug) {
            $updateData['slug'] = $slug;
        }
        
        if ($request->has('status')) {
            $updateData['is_published'] = $request->status === 'published';
        }

        $tutorial->update($updateData);
        
        // Refresh to get updated data
        $tutorial->refresh();
        
        // Return the transformed tutorial data
        return response()->json([
            'id' => $tutorial->id,
            'title' => $tutorial->title,
            'slug' => $tutorial->slug,
            'excerpt' => $tutorial->excerpt, // Uses getter
            'content' => $tutorial->content,
            'image_url' => $tutorial->image_url,
            'order' => $tutorial->order,
            'published_at' => $tutorial->published_at, // Uses getter
            'status' => $tutorial->is_published ? 'published' : 'draft',
            'author' => $tutorial->user(), // Uses getter method
            'is_published' => $tutorial->is_published,
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