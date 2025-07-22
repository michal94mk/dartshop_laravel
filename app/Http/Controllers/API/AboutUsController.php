<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AboutUs;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\AboutUsRequest;

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
                'content' => 'Informacje o nas nie są jeszcze dostępne.'
            ]);
        }
        
        return response()->json($aboutUs);
    }
    
    /**
     * Aktualizuj informacje o stronie "O nas".
     *
     * @param  \App\Http\Requests\AboutUsRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function update(AboutUsRequest $request)
    {
        // Znajdź lub utwórz rekord "O nas"
        $aboutUs = AboutUs::firstOrNew();
        
        // Aktualizuj dane
        $aboutUs->fill($request->validated());
        
        $aboutUs->save();
        
        return response()->json($aboutUs);
    }
    

}
