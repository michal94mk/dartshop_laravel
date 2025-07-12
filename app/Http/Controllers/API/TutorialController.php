<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tutorial;
use Illuminate\Http\Request;

class TutorialController extends Controller
{
    /**
     * Display a listing of published tutorials.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tutorials = Tutorial::published()
            ->orderBy('order', 'asc')
            ->orderBy('created_at', 'desc')
            ->get()
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
            
        return response()->json($tutorials);
    }

    /**
     * Display the specified tutorial.
     *
     * @param  string  $slug
     * @return \Illuminate\Http\Response
     */
    public function show($slug)
    {
        $tutorial = Tutorial::published()
            ->where('slug', $slug)
            ->firstOrFail();
            
        return response()->json([
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
        ]);
    }
} 