<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\AboutPage;
use Illuminate\Http\Request;

class AboutPageController extends Controller
{
    /**
     * Display the about us page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $aboutPages = AboutPage::active()->ordered()->get();
        
        return view('frontend.about.index', compact('aboutPages'));
    }
    
    /**
     * Display a specific about page.
     * 
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $aboutPage = AboutPage::active()->findOrFail($id);
        
        return view('frontend.about.show', compact('aboutPage'));
    }
}
