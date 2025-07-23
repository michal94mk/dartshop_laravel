<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Tutorial;
use App\Http\Requests\Admin\TutorialRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

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
            
            return $this->successResponse('Poradniki pobrane pomyślnie', $tutorials);
        } catch (\Exception $e) {
            return $this->errorResponse('Błąd podczas pobierania poradników: ' . $e->getMessage(), 500);
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
        $validated = $request->validated();

        try {
            // Convert status to is_published
            if (isset($validated['status'])) {
                $validated['is_published'] = $validated['status'] === 'published';
                unset($validated['status']);
            }
            
            // Handle the image upload
            if ($request->hasFile('image') && $request->file('image')->isValid()) {
                $file = $request->file('image');
                $imageName = 'tutorial_' . time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
                
                // Create directory if it doesn't exist
                $uploadPath = public_path('img/tutorials');
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }
                
                // Save to public/img/tutorials
                $file->move($uploadPath, $imageName);
                $validated['image_url'] = 'img/tutorials/' . $imageName;
                
                Log::info('Tutorial image saved:', [
                    'name' => $imageName,
                    'path' => $validated['image_url'],
                    'full_path' => public_path('img/tutorials/' . $imageName),
                    'exists' => file_exists(public_path('img/tutorials/' . $imageName))
                ]);
            }
            
            $tutorial = Tutorial::create($validated);
            return $this->successResponse('Poradnik został utworzony', $tutorial, 201);
        } catch (\Exception $e) {
            Log::error('Error creating tutorial: ' . $e->getMessage());
            return $this->errorResponse('Błąd podczas tworzenia poradnika: ' . $e->getMessage(), 500);
        }
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
        
        return $this->successResponse('Poradnik pobrany', [
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
        try {
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
            
            // Convert status to is_published
            if (isset($updateData['status'])) {
                $updateData['is_published'] = $updateData['status'] === 'published';
                unset($updateData['status']);
            }
            
            // Handle the image upload
            if ($request->hasFile('image') && $request->file('image')->isValid()) {
                $file = $request->file('image');
                $imageName = 'tutorial_' . time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
                
                // Create directory if it doesn't exist
                $uploadPath = public_path('img/tutorials');
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }
                
                // Delete old image if exists
                if ($tutorial->image_url) {
                    $oldImagePath = public_path($tutorial->image_url);
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                }
                
                // Save to public/img/tutorials
                $file->move($uploadPath, $imageName);
                $updateData['image_url'] = 'img/tutorials/' . $imageName;
                
                Log::info('Tutorial image updated:', [
                    'name' => $imageName,
                    'path' => $updateData['image_url'],
                    'full_path' => public_path('img/tutorials/' . $imageName),
                    'exists' => file_exists(public_path('img/tutorials/' . $imageName))
                ]);
            }
            
            $tutorial->update($updateData);
            
            return $this->successResponse('Poradnik został zaktualizowany', $tutorial);
        } catch (\Exception $e) {
            Log::error('Error updating tutorial: ' . $e->getMessage());
            return $this->errorResponse('Błąd podczas aktualizacji poradnika: ' . $e->getMessage(), 500);
        }
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

        return $this->successResponse('Poradnik został usunięty');
    }
} 