<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ContentImageUploadController extends Controller
{
    public function upload(Request $request)
    {
        try {
            $request->validate([
                'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048'
            ]);

            $image = $request->file('image');
            $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            
            // Zapisz w public/img/content
            $path = public_path('img/content');
            if (!file_exists($path)) {
                mkdir($path, 0755, true);
            }
            
            $image->move($path, $filename);
            
            return response()->json([
                'url' => '/img/content/' . $filename
            ]);
            
        } catch (\Exception $e) {
            Log::error('Content image upload error: ' . $e->getMessage());
            return response()->json(['error' => 'Nie udało się przesłać obrazka'], 500);
        }
    }
} 