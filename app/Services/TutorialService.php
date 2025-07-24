<?php

namespace App\Services;

use App\Models\Tutorial;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Database\Eloquent\Collection;

class TutorialService
{
    /**
     * Get all published tutorials sorted by order and creation date.
     *
     * @return Collection|Tutorial[]
     * @throws Exception
     */
    public function getPublishedTutorials()
    {
        try {
            return Tutorial::published()
                ->orderBy('order', 'asc')
                ->orderBy('created_at', 'desc')
                ->get();
        } catch (Exception $e) {
            Log::error('Error fetching published tutorials', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Get a published tutorial by its slug.
     *
     * @param string $slug
     * @return Tutorial|null
     * @throws Exception
     */
    public function getPublishedTutorialBySlug(string $slug)
    {
        try {
            return Tutorial::published()
                ->where('slug', $slug)
                ->first();
        } catch (Exception $e) {
            Log::error('Error fetching tutorial by slug', [
                'slug' => $slug,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
} 