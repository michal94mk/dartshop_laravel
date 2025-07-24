<?php

namespace App\Services;

use App\Models\Tutorial;
use Illuminate\Support\Facades\Log;
use Exception;

class TutorialService
{
    /**
     * Get all published tutorials sorted by order and creation date.
     *
     * @return \Illuminate\Database\Eloquent\Collection|Tutorial[]
     */
    public function getPublishedTutorials()
    {
        return Tutorial::published()
            ->orderBy('order', 'asc')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get a published tutorial by its slug.
     *
     * @param string $slug
     * @return Tutorial|null
     */
    public function getPublishedTutorialBySlug(string $slug)
    {
        return Tutorial::published()
            ->where('slug', $slug)
            ->first();
    }
} 