<?php

namespace App\Services\Admin;

use App\Models\Tutorial;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Exception;

/**
 * Service class for admin tutorial management.
 * Handles listing, filtering, creating, updating, and deleting tutorials.
 */
class TutorialAdminService
{
    /**
     * Get paginated tutorials with filters and sorting for admin panel.
     *
     * @param Request $request
     * @return LengthAwarePaginator
     * @throws Exception
     */
    public function getPaginatedTutorials(Request $request): LengthAwarePaginator
    {
        try {
            $query = Tutorial::query();
            // Search filter
            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('content', 'like', "%{$search}%");
                });
            }
            // Status filter
            if ($request->has('status') && !empty($request->status)) {
                if ($request->status === 'published') {
                    $query->where('is_published', true);
                } elseif ($request->status === 'draft') {
                    $query->where('is_published', false);
                }
            }
            // Sorting
            $sortField = $request->sort_field ?? 'created_at';
            $sortDirection = $request->sort_direction ?? 'desc';
            $sortFieldMap = [
                'created_at' => 'created_at',
                'title' => 'title'
            ];
            $dbSortField = $sortFieldMap[$sortField] ?? 'created_at';
            $query->orderBy($dbSortField, $sortDirection);
            // Pagination
            $perPage = (int) $request->get('per_page', 15);
            if ($perPage < 5) $perPage = 5;
            if ($perPage > 100) $perPage = 100;
            $tutorials = $query->paginate($perPage);
            // Transform data for frontend
            $tutorials->getCollection()->transform(function ($tutorial) {
                return [
                    'id' => $tutorial->id,
                    'title' => $tutorial->title,
                    'slug' => $tutorial->slug,
                    'excerpt' => $tutorial->excerpt,
                    'content' => $tutorial->content,
                    'image_url' => $tutorial->image_url,
                    'order' => $tutorial->order,
                    'published_at' => $tutorial->published_at,
                    'status' => $tutorial->is_published ? 'published' : 'draft',
                    'author' => $tutorial->user(),
                    'is_published' => $tutorial->is_published,
                    'created_at' => $tutorial->created_at,
                    'updated_at' => $tutorial->updated_at
                ];
            });
            return $tutorials;
        } catch (Exception $e) {
            Log::error('Error fetching paginated tutorials (admin)', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Get a tutorial by ID.
     *
     * @param int $id
     * @return Tutorial|null
     * @throws Exception
     */
    public function getTutorialById(int $id): ?Tutorial
    {
        try {
            return Tutorial::find($id);
        } catch (Exception $e) {
            Log::error('Error fetching tutorial by ID (admin)', [
                'id' => $id,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Create a new tutorial.
     *
     * @param array $data
     * @return Tutorial
     * @throws Exception
     */
    public function createTutorial(array $data): Tutorial
    {
        try {
            // Handle slug
            if (empty($data['slug']) && !empty($data['title'])) {
                $data['slug'] = Str::slug($data['title']);
            }
            // Handle status
            if (isset($data['status'])) {
                $data['is_published'] = $data['status'] === 'published';
                unset($data['status']);
            }
            return Tutorial::create($data);
        } catch (Exception $e) {
            Log::error('Error creating tutorial (admin)', [
                'data' => $data,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Update an existing tutorial.
     *
     * @param Tutorial $tutorial
     * @param array $data
     * @return Tutorial
     * @throws Exception
     */
    public function updateTutorial(Tutorial $tutorial, array $data): Tutorial
    {
        try {
            // Handle slug
            if (empty($data['slug']) && !empty($data['title'])) {
                $data['slug'] = Str::slug($data['title']);
            }
            // Handle status
            if (isset($data['status'])) {
                $data['is_published'] = $data['status'] === 'published';
                unset($data['status']);
            }
            $tutorial->update($data);
            return $tutorial;
        } catch (Exception $e) {
            Log::error('Error updating tutorial (admin)', [
                'id' => $tutorial->id,
                'data' => $data,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Delete a tutorial.
     *
     * @param Tutorial $tutorial
     * @return void
     * @throws Exception
     */
    public function deleteTutorial(Tutorial $tutorial): void
    {
        try {
            $tutorial->delete();
        } catch (Exception $e) {
            Log::error('Error deleting tutorial (admin)', [
                'id' => $tutorial->id,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
} 