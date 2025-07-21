<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TermsOfService;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * @OA\Tag(
 *     name="Terms of Service",
 *     description="API Endpoints for terms of service management"
 * )
 */

class TermsOfServiceController extends Controller
{
    /**
     * Display the current terms of service.
     *
     * @OA\Get(
     *     path="/api/terms-of-service",
     *     summary="Get terms of service",
     *     description="Get current active terms of service",
     *     tags={"Terms of Service"},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="terms_of_service", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="title", type="string", example="Regulamin DartShop"),
     *                 @OA\Property(property="version", type="string", example="1.0"),
     *                 @OA\Property(property="effective_date", type="string", format="date-time"),
     *                 @OA\Property(property="content", type="string", example="<h2>1. Postanowienia ogólne</h2>...")
     *             )
     *         )
     *     )
     * )
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
     *
     * @OA\Post(
     *     path="/api/terms-of-service/accept",
     *     summary="Accept terms of service",
     *     description="Accept terms of service for authenticated user",
     *     tags={"Terms of Service"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Terms of service accepted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Regulamin został zaakceptowany"),
     *             @OA\Property(property="terms_of_service_accepted", type="boolean", example=true),
     *             @OA\Property(property="terms_of_service_accepted_at", type="string", format="date-time")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
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