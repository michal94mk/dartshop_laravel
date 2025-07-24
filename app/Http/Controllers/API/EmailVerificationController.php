<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseApiController;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\JsonResponse;
use Exception;

class EmailVerificationController extends BaseApiController
{
    /**
     * Mark user's email as verified
     *
     * @param  Request  $request
     * @param  int  $id
     * @param  string  $hash
     * @return \Illuminate\Http\JsonResponse
     */
    public function verify(Request $request, int $id, string $hash)
    {
        $this->logApiRequest($request, "Email verification attempt for user ID: {$id}");

        if (!$request->hasValidSignature()) {
            return redirect(config('app.url') . '/profile?verified=invalid');
        }

        $user = \App\Models\User::findOrFail($id);

        // Verify the hash matches the user's email
        if (! hash_equals(sha1($user->getEmailForVerification()), $hash)) {
            return redirect(config('app.url') . '/profile?verified=invalid');
        }

        if ($user->hasVerifiedEmail()) {
            return redirect(config('app.url') . '/profile?verified=already');
        }
        
        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        // Check if user is authenticated via web session (after adding web middleware)
        $authenticatedUser = Auth::guard('web')->user();
        $isAuthenticated = $authenticatedUser && $authenticatedUser->id == $user->id;

        // Always redirect to profile - Vue router will handle auth checks
        return redirect(config('app.url') . '/profile?verified=success');
    }

    /**
     * Resend verification email
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function sendVerificationEmail(Request $request): JsonResponse
    {
        try {
            $this->logApiRequest($request, 'Resend verification email');
            
            $user = $request->user();

            // Prevent email verification for Google OAuth users
            if (!empty($user->google_id)) {
                return $this->errorResponse('Użytkownicy zalogowani przez Google OAuth mają już zweryfikowany email.', 400);
            }

            if ($user->hasVerifiedEmail()) {
                return $this->successResponse(null, 'Adres e-mail jest już zweryfikowany.');
            }

            $user->sendEmailVerificationNotification();

            return $this->successResponse(null, 'Link weryfikacyjny został wysłany ponownie.');
        } catch (Exception $e) {
            return $this->handleException($e, 'Sending verification email');
        }
    }
} 