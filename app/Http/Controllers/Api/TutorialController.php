<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseApiController;
use Illuminate\Http\JsonResponse;
use App\Services\TutorialService;

class TutorialController extends BaseApiController
{
    protected $tutorialService;

    public function __construct(TutorialService $tutorialService)
    {
        $this->tutorialService = $tutorialService;
    }
    /**
     * Display a listing of published tutorials.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $this->logApiRequest(request(), 'Fetch tutorials');
        $tutorials = $this->tutorialService->getPublishedTutorials()
            ->map(function ($tutorial) {
                return [
                    'id' => $tutorial->id,
                    'title' => $tutorial->title,
                    'slug' => $tutorial->slug,
                    'excerpt' => $tutorial->excerpt,
                    'featured_image_url' => $tutorial->featured_image_url,
                    'thumbnail_image_url' => $tutorial->thumbnail_image_url,
                    'category' => $tutorial->category,
                    'difficulty' => $tutorial->difficulty,
                    'published_at' => $tutorial->published_at,
                    'author' => 'DartShop Admin',
                    'views' => $tutorial->views,
                    'order' => $tutorial->order,
                    'is_published' => $tutorial->is_published,
                    'created_at' => $tutorial->created_at,
                    'updated_at' => $tutorial->updated_at,
                ];
            });
        return $this->successResponse($tutorials, 'Tutorials fetched successfully');
    }

    /**
     * Display the specified tutorial.
     *
     * @param  string  $slug
     * @return JsonResponse
     */
    public function show(string $slug): JsonResponse
    {
        $this->logApiRequest(request(), "Fetch tutorial for slug: {$slug}");
        $tutorial = $this->tutorialService->getPublishedTutorialBySlug($slug);
        if (!$tutorial) {
            return $this->notFoundResponse('Tutorial not found');
        }
        return $this->successResponse([
            'id' => $tutorial->id,
            'title' => $tutorial->title,
            'slug' => $tutorial->slug,
            'content' => $tutorial->content,
            'excerpt' => $tutorial->excerpt,
            'featured_image_url' => $tutorial->featured_image_url,
            'thumbnail_image_url' => $tutorial->thumbnail_image_url,
            'category' => $tutorial->category,
            'difficulty' => $tutorial->difficulty,
            'published_at' => $tutorial->published_at,
            'author' => 'DartShop Admin',
            'views' => $tutorial->views,
            'meta_title' => $tutorial->meta_title,
            'meta_description' => $tutorial->meta_description,
            'order' => $tutorial->order,
        ], 'Tutorial fetched successfully');
    }
} 