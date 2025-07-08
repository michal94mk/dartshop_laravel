<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TermsOfService;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TermsOfServiceController extends Controller
{
    /**
     * Display the current terms of service.
     */
    public function show()
    {
        $termsOfService = TermsOfService::getActive();
        
        return response()->json([
            'terms_of_service' => $termsOfService
        ]);
    }

    /**
     * Accept terms of service for authenticated user.
     */
    public function accept(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();
        
        if (!$user) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);
        }

        $user->update([
            'terms_of_service_accepted' => true,
            'terms_of_service_accepted_at' => now(),
        ]);

        return response()->json([
            'message' => 'Regulamin został zaakceptowany',
            'terms_of_service_accepted' => true,
            'terms_of_service_accepted_at' => $user->terms_of_service_accepted_at
        ]);
    }
} 