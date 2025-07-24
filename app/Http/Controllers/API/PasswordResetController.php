<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;

class PasswordResetController extends BaseApiController
{
    /**
     * Wysyłanie linku do resetowania hasła
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function forgotPassword(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'email' => 'required|email',
            ]);

            $status = Password::sendResetLink(
                $request->only('email')
            );

            if ($status === Password::RESET_LINK_SENT) {
                return $this->successResponse(['message' => __($status)]);
            }

            return $this->validationErrorResponse(['email' => [__($status)]]);
        } catch (\Exception $e) {
            return $this->handleException($e, 'Wystąpił błąd podczas wysyłania linku resetującego hasło');
        }
    }

    /**
     * Walidacja tokenu resetowania hasła
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function validateResetToken(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'token' => 'required|string',
                'email' => 'required|email',
            ]);

            // Sprawdź czy token istnieje i nie wygasł w nowej tabeli
            $resetRecord = DB::table('password_reset_tokens')
                ->where('email', $request->email)
                ->where('created_at', '>', now()->subMinutes(60))
                ->first();

            if (!$resetRecord) {
                return $this->validationErrorResponse(['token' => ['Token jest nieprawidłowy lub wygasł.']]);
            }

            // Sprawdź czy token się zgadza (w nowej tabeli token jest hashowany)
            if (!Hash::check($request->token, $resetRecord->token)) {
                return $this->validationErrorResponse(['token' => ['Token jest nieprawidłowy lub wygasł.']]);
            }

            return $this->successResponse([
                'message' => 'Token jest prawidłowy.',
                'email' => $request->email
            ]);
        } catch (\Exception $e) {
            return $this->handleException($e, 'Wystąpił błąd podczas walidacji tokenu resetowania hasła');
        }
    }

    /**
     * Resetowanie hasła
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function resetPassword(Request $request): JsonResponse
    {
        try {
            $request->validate([
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
                return $this->successResponse(['message' => __($status)]);
            }

            return $this->validationErrorResponse(['email' => [__($status)]]);
        } catch (\Exception $e) {
            return $this->handleException($e, 'Wystąpił błąd podczas resetowania hasła');
        }
    }
} 