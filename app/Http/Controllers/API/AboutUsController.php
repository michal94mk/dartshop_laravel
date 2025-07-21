<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AboutUs;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\AboutUsRequest;

/**
 * @OA\Tag(
 *     name="About",
 *     description="API Endpoints for about us page"
 * )
 */

class AboutUsController extends Controller
{
    /**
     * Pobierz informacje o stronie "O nas".
     *
     * @OA\Get(
     *     path="/api/about",
     *     summary="Get about us information",
     *     description="Retrieve information about the company",
     *     tags={"About"},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/AboutUs")
     *     )
     * )
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
