<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseApiController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use App\Services\TermsOfServiceService;

class TermsOfServiceController extends BaseApiController
{
    protected $termsService;

    public function __construct(TermsOfServiceService $termsService)
    {
        $this->termsService = $termsService;
    }
    /**
     * Display the current terms of service.
     */
    public function show(): JsonResponse
    {
        $this->logApiRequest(request(), 'Fetch terms of service');
        $termsOfService = $this->termsService->getActiveTerms();
        return $this->successResponse($termsOfService, 'Terms of service fetched successfully');
    }

    /**
     * Accept terms of service for authenticated user.
     */
    public function accept(Request $request): JsonResponse
    {
        $this->logApiRequest($request, 'Accept terms of service');
        /** @var User $user */
        $user = Auth::user();
        if (!$user) {
            return $this->unauthorizedResponse();
        }
        $this->termsService->acceptTerms($user);
        return $this->successResponse([
            'terms_of_service_accepted' => true,
            'terms_of_service_accepted_at' => $user->terms_of_service_accepted_at
        ], 'Regulamin został zaakceptowany');
    }
} 