<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

/**
 * @OA\Tag(
 *     name="Email Verification",
 *     description="API Endpoints for email verification"
 * )
 */

class EmailVerificationController extends Controller
{
    /**
     * Mark user's email as verified
     *
     * @OA\Get(
     *     path="/api/email/verify/{id}/{hash}",
     *     summary="Verify email address",
     *     description="Verify user's email address using verification link",
     *     tags={"Email Verification"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="User ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="hash",
     *         in="path",
     *         description="Email verification hash",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=302,
     *         description="Redirect to profile page with verification status",
     *         @OA\Header(
     *             header="Location",
     *             description="Redirect URL",
     *             @OA\Schema(type="string", example="/profile?verified=success")
     *         )
     *     )
     * )
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
     * @OA\Post(
     *     path="/api/email/verification-notification",
     *     summary="Send verification email",
     *     description="Send email verification notification to user",
     *     tags={"Email Verification"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Verification email sent successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Verification link sent!")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request - Google OAuth user or already verified",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendVerificationEmail(Request $request)
    {
        $user = $request->user();

        // Prevent email verification for Google OAuth users
        if (!empty($user->google_id)) {
            return response()->json([
                'message' => 'Użytkownicy zalogowani przez Google OAuth mają już zweryfikowany email.'
            ], 400);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'Adres e-mail jest już zweryfikowany.'
            ]);
        }

        $user->sendEmailVerificationNotification();

        return response()->json([
            'message' => 'Link weryfikacyjny został wysłany ponownie.'
        ]);
    }
} 