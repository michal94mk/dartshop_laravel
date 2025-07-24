<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseApiController;
use App\Models\AboutUs;
use Illuminate\Http\JsonResponse;

class AboutUsController extends BaseApiController
{
    /**
     * Get about us page data.
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
}
