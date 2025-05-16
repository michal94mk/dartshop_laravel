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
        Log::info('SPA route accessed', [
            'path' => $request->path(),
            'method' => $request->method(),
            'user_agent' => $request->header('User-Agent'),
            'referrer' => $request->header('Referer'),
            'query' => $request->query(),
        ]);
        
        return view('app');
    }
} 