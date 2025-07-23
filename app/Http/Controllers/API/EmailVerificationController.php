<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseApiController;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class EmailVerificationController extends BaseApiController
{
    /**
     * Mark user's email as verified
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @param  string  $hash
     * @return \Illuminate\Http\JsonResponse
     */
    public function verify(Request $request, $id, $hash)
    {
        Log::info('Email verification attempt', [
            'id' => $id,
            'hash' => substr($hash, 0, 10) . '...',
            'has_signature' => $request->hasValidSignature(),
            'query_params' => $request->query()
        ]);

        if (!$request->hasValidSignature()) {
            Log::warning('Email verification failed: Invalid signature', ['id' => $id]);
            return redirect(config('app.url') . '/profile?verified=invalid');
        }

        $user = \App\Models\User::findOrFail($id);

        // Verify the hash matches the user's email
        if (! hash_equals(sha1($user->getEmailForVerification()), $hash)) {
            return redirect(config('app.url') . '/profile?verified=invalid');
        }

        if ($user->hasVerifiedEmail()) {
            Log::info('Email already verified', ['user_id' => $user->id, 'email' => $user->email]);
            return redirect(config('app.url') . '/profile?verified=already');
        }

        Log::info('Marking email as verified', ['user_id' => $user->id, 'email' => $user->email]);
        
        if ($user->markEmailAsVerified()) {
            Log::info('Email verified successfully', ['user_id' => $user->id, 'email' => $user->email]);
            event(new Verified($user));
        } else {
            Log::error('Failed to mark email as verified', ['user_id' => $user->id, 'email' => $user->email]);
        }

        // Check if user is authenticated via web session (after adding web middleware)
        $authenticatedUser = Auth::guard('web')->user();
        $isAuthenticated = $authenticatedUser && $authenticatedUser->id == $user->id;
        
        Log::info('Email verification completed', [
            'user_id' => $user->id,
            'email' => $user->email,
            'is_authenticated_via_web' => $isAuthenticated,
            'authenticated_user_id' => $authenticatedUser ? $authenticatedUser->id : null,
            'session_id' => session()->getId(),
            'session_data_keys' => array_keys(session()->all()),
            'has_laravel_session_cookie' => $request->hasCookie('laravel_session'),
            'user_agent' => $request->header('User-Agent')
        ]);

        // Always redirect to profile - Vue router will handle auth checks
        return redirect(config('app.url') . '/profile?verified=success');
    }

    /**
     * Resend verification email
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendVerificationEmail(Request $request)
    {
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
    }
} 