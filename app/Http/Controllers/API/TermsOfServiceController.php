<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseApiController;
use App\Models\TermsOfService;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use Exception;

class TermsOfServiceController extends BaseApiController
{
    /**
     * Display the current terms of service.
     */
    public function show(): JsonResponse
    {
        try {
            $this->logApiRequest(request(), 'Fetch terms of service');
            $termsOfService = TermsOfService::getActive();
            return $this->successResponse($termsOfService, 'Terms of service fetched successfully');
        } catch (Exception $e) {
            return $this->handleException($e, 'Fetching terms of service');
        }
    }

    /**
     * Accept terms of service for authenticated user.
     */
    public function accept(Request $request): JsonResponse
    {
        try {
            $this->logApiRequest($request, 'Accept terms of service');
            /** @var User $user */
            $user = Auth::user();
            if (!$user) {
                return $this->unauthorizedResponse();
            }
            $user->update([
                'terms_of_service_accepted' => true,
                'terms_of_service_accepted_at' => now(),
            ]);
            return $this->successResponse([
                'terms_of_service_accepted' => true,
                'terms_of_service_accepted_at' => $user->terms_of_service_accepted_at
            ], 'Regulamin został zaakceptowany');
        } catch (Exception $e) {
            return $this->handleException($e, 'Accepting terms of service');
        }
    }
} 