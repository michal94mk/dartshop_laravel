<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;

class PasswordResetController extends BaseApiController
{
    /**
     * Send a password reset link to the user's email address.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function forgotPassword(Request $request): JsonResponse
    {
        $this->logApiRequest($request, 'Send password reset link');
        
        $validated = $this->validateRequest($request, [
            'email' => 'required|email',
        ]);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return $this->successResponse(['message' => __($status)], 'Password reset link sent successfully');
        }

        return $this->validationErrorResponse(['email' => [__($status)]]);
    }

    /**
     * Validate the password reset token for the given email.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function validateResetToken(Request $request): JsonResponse
    {
        $this->logApiRequest($request, 'Validate password reset token');
        
        $validated = $this->validateRequest($request, [
            'token' => 'required|string',
            'email' => 'required|email',
        ]);

        // Check if token exists and hasn't expired in the new table
        $resetRecord = DB::table('password_reset_tokens')
            ->where('email', $validated['email'])
            ->where('created_at', '>', now()->subMinutes(60))
            ->first();

        if (!$resetRecord) {
            return $this->validationErrorResponse(['token' => ['Token is invalid or expired.']]);
        }

        // Check if token matches (in new table token is hashed)
        if (!Hash::check($validated['token'], $resetRecord->token)) {
            return $this->validationErrorResponse(['token' => ['Token is invalid or expired.']]);
        }

        return $this->successResponse([
            'message' => 'Token is valid.',
            'email' => $validated['email']
        ], 'Token validation successful');
    }

    /**
     * Reset the user's password using the provided token.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function resetPassword(Request $request): JsonResponse
    {
        $this->logApiRequest($request, 'Reset password');
        
        $validated = $this->validateRequest($request, [
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return $this->successResponse(['message' => __($status)], 'Password reset successful');
        }

        return $this->validationErrorResponse(['email' => [__($status)]]);
    }
} 