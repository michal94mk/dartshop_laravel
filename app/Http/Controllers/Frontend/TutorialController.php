<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Tutorial;
use Illuminate\Http\Request;

class TutorialController extends Controller
{
    /**
     * Display a listing of tutorials.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = Tutorial::published();
        
        // Filter by category if provided
        if ($request->has('category') && !empty($request->category)) {
            $query->where('category', $request->category);
        }
        
        // Filter by difficulty if provided
        if ($request->has('difficulty') && !empty($request->difficulty)) {
            $query->where('difficulty', $request->difficulty);
        }
        
        // Get distinct categories for filter options
        $categories = Tutorial::published()
            ->select('category')
            ->distinct()
            ->whereNotNull('category')
            ->pluck('category');
        
        $tutorials = $query->latest('published_at')->paginate(9);
        
        return view('frontend.tutorials.index', compact('tutorials', 'categories'));
    }
    
    /**
     * Display the specified tutorial.
     *
     * @param Tutorial $tutorial
     * @return \Illuminate\View\View
     */
    public function show(Tutorial $tutorial)
    {
        // Check if tutorial is published
        if (!$tutorial->is_published || $tutorial->published_at > now()) {
            abort(404);
        }
        
        // Get related tutorials
        $relatedTutorials = Tutorial::published()
            ->where('id', '!=', $tutorial->id)
            ->where(function($query) use ($tutorial) {
                $query->where('category', $tutorial->category)
                      ->orWhere('difficulty', $tutorial->difficulty);
            })
            ->latest('published_at')
            ->take(3)
            ->get();
        
        return view('frontend.tutorials.show', compact('tutorial', 'relatedTutorials'));
    }
}
