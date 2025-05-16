<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SPAController extends Controller
{
    /**
     * Handle the SPA request
     */
    public function index()
    {
        return view('spa');
    }
} 