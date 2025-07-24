<?php

namespace App\Services;

use App\Models\TermsOfService;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Exception;

class TermsOfServiceService
{
    /**
     * Get the currently active terms of service.
     *
     * @return TermsOfService|null
     */
    public function getActiveTerms(): ?TermsOfService
    {
        return TermsOfService::getActive();
    }

    /**
     * Accept the terms of service for the given user.
     *
     * @param User $user
     * @return User
     * @throws Exception
     */
    public function acceptTerms(User $user): User
    {
        try {
            $user->update([
                'terms_of_service_accepted' => true,
                'terms_of_service_accepted_at' => now(),
            ]);
            return $user;
        } catch (Exception $e) {
            Log::error('TermsOfServiceService error', [
                'message' => $e->getMessage(),
                'user_id' => $user->id,
            ]);
            throw $e;
        }
    }
} 