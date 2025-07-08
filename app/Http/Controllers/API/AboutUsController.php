<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AboutUs;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AboutUsController extends Controller
{
    /**
     * Pobierz informacje o stronie "O nas".
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $aboutUs = AboutUs::first();
        
        if (!$aboutUs) {
            return response()->json([
                'title' => 'O naszym sklepie',
                'content' => 'Informacje o nas nie są jeszcze dostępne.',
                'image_path' => null,
                'image_position' => 'right'
            ]);
        }
        
        return response()->json($aboutUs);
    }
    
    /**
     * Aktualizuj informacje o stronie "O nas".
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        // Walidacja danych wejściowych
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'image_path' => 'nullable|string',
            'image_position' => 'required|in:left,right,top,bottom',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        
        // Znajdź lub utwórz rekord "O nas"
        $aboutUs = AboutUs::firstOrNew();
        
        // Aktualizuj dane
        $aboutUs->title = $request->title;
        $aboutUs->content = $request->content;
        $aboutUs->image_path = $request->image_path;
        $aboutUs->image_position = $request->image_position;
        
        $aboutUs->save();
        
        return response()->json($aboutUs);
    }
    
    /**
     * Prześlij obrazek dla strony "O nas".
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function uploadImage(Request $request)
    {
        // Walidacja obrazka
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        
        // Przetwarzanie i zapisywanie obrazka
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $path = $image->store('images/about', 'public');
            
            return response()->json(['path' => $path]);
        }
        
        return response()->json(['error' => 'Nie udało się przesłać obrazka.'], 400);
    }
}
