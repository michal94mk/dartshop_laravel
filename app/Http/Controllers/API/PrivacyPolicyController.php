<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseApiController;
use App\Models\User;
use App\Services\PrivacyPolicyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;

class PrivacyPolicyController extends BaseApiController
{
    protected $privacyPolicyService;

    public function __construct(PrivacyPolicyService $privacyPolicyService)
    {
        $this->privacyPolicyService = $privacyPolicyService;
    }

    /**
     * Get the current privacy policy.
     *
     * @return JsonResponse
     */
    public function show(): JsonResponse
    {
        $this->logApiRequest(request(), 'Fetch privacy policy');
        
        $privacyPolicy = $this->privacyPolicyService->getPrivacyPolicy();
        
        return $this->successResponse(['privacy_policy' => $privacyPolicy], 'Privacy policy fetched successfully');
    }

    /**
     * Accept the privacy policy for the authenticated user.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function accept(Request $request): JsonResponse
    {
        $this->logApiRequest($request, 'Accept privacy policy');
        
        /** @var User $user */
        $user = Auth::user();
        
        if (!$user) {
            return $this->unauthorizedResponse('Unauthorized');
        }

        $user->update([
            'privacy_policy_accepted' => true,
            'privacy_policy_accepted_at' => now(),
        ]);

        return $this->successResponse([
            'message' => 'Privacy policy has been accepted',
            'privacy_policy_accepted' => true,
            'privacy_policy_accepted_at' => $user->privacy_policy_accepted_at
        ], 'Privacy policy accepted successfully');
    }
}
