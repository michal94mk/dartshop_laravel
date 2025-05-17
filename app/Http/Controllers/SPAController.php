<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SPAController extends Controller
{
    /**
     * Handle the SPA request
     */
    public function index(Request $request)
    {
        // Sprawdź, czy jesteśmy w sekcji administracyjnej
        if (str_starts_with($request->path(), 'admin')) {
            return view('admin');
        }
        
        return view('app');
    }
} 