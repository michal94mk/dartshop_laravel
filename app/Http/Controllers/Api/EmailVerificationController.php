<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseApiController;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;

class EmailVerificationController extends BaseApiController
{
    /**
     * Verify the user's email address using the verification link.
     *
     * @param Request $request
     * @param int $id
     * @param string $hash
     * @return \Illuminate\Http\RedirectResponse
     */
    public function verify(Request $request, int $id, string $hash)
    {
        $this->logApiRequest($request, "Email verification attempt for user ID: {$id}");
        
        // Debug: Log request details
        \Log::info('Email verification debug', [
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'headers' => $request->headers->all(),
            'query' => $request->query->all(),
            'app_url' => config('app.url'),
            'app_env' => config('app.env'),
        ]);

        if (!$request->hasValidSignature()) {
            \Log::error('Invalid signature for email verification', [
                'user_id' => $id,
                'url' => $request->fullUrl(),
                'signature' => $request->query('signature'),
                'expires' => $request->query('expires'),
            ]);
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
     * Verify the user's email address via API.
     *
     * @param Request $request
     * @param int $id
     * @param string $hash
     * @return JsonResponse
     */
    public function verifyApi(Request $request, int $id, string $hash): JsonResponse
    {
        $this->logApiRequest($request, "API Email verification attempt for user ID: {$id}");
        
        // Debug: Log request details
        \Log::info('API Email verification debug', [
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'headers' => $request->headers->all(),
            'query' => $request->query->all(),
            'app_url' => config('app.url'),
            'app_env' => config('app.env'),
        ]);

        if (!$request->hasValidSignature()) {
            \Log::error('Invalid signature for API email verification', [
                'user_id' => $id,
                'url' => $request->fullUrl(),
                'signature' => $request->query('signature'),
                'expires' => $request->query('expires'),
            ]);
            return $this->errorResponse('Link weryfikacyjny jest nieprawidłowy lub wygasł.', 400);
        }

        $user = \App\Models\User::findOrFail($id);

        // Verify the hash matches the user's email
        if (! hash_equals(sha1($user->getEmailForVerification()), $hash)) {
            return $this->errorResponse('Link weryfikacyjny jest nieprawidłowy.', 400);
        }

        if ($user->hasVerifiedEmail()) {
            return $this->successResponse(null, 'Adres e-mail jest już zweryfikowany.');
        }
        
        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return $this->successResponse(null, 'Adres e-mail został pomyślnie zweryfikowany.');
    }

    /**
     * Resend the email verification link to the authenticated user.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function sendVerificationEmail(Request $request): JsonResponse
    {
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
    }
} 