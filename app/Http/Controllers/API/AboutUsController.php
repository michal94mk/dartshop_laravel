<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseApiController;
use Illuminate\Http\Request;
use App\Models\AboutUs;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;

class AboutUsController extends BaseApiController
{
    /**
     * Pobierz informacje o stronie "O nas".
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $aboutUs = AboutUs::first();
        
        if (!$aboutUs) {
            return $this->notFoundResponse('Informacje o nas nie są jeszcze dostępne.');
        }
        return $this->successResponse($aboutUs);
    }
    
    /**
     * Aktualizuj informacje o stronie "O nas".
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function update(Request $request): JsonResponse
    {
        $validated = $this->validateRequest($request, [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        // Znajdź lub utwórz rekord "O nas"
        $aboutUs = AboutUs::firstOrNew();
        
        // Aktualizuj dane
        $aboutUs->fill($validated);
        
        $aboutUs->save();
        
        return $this->successResponse($aboutUs, 'Informacje zostały zaktualizowane pomyślnie.');
    }
}
