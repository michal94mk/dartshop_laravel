<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;

class PasswordResetController extends Controller
{
    /**
     * Wysyłanie linku do resetowania hasła
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return response()->json(['message' => __($status)]);
        }

        throw ValidationException::withMessages([
            'email' => [__($status)],
        ]);
    }

    /**
     * Walidacja tokenu resetowania hasła
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function validateResetToken(Request $request)
    {
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
            return response()->json([
                'message' => 'Token jest nieprawidłowy lub wygasł.'
            ], 422);
        }

        // Sprawdź czy token się zgadza (w nowej tabeli token jest hashowany)
        if (!Hash::check($request->token, $resetRecord->token)) {
            return response()->json([
                'message' => 'Token jest nieprawidłowy lub wygasł.'
            ], 422);
        }

        return response()->json([
            'message' => 'Token jest prawidłowy.',
            'email' => $request->email
        ]);
    }

    /**
     * Resetowanie hasła
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function resetPassword(Request $request)
    {
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
            return response()->json(['message' => __($status)]);
        }

        throw ValidationException::withMessages([
            'email' => [__($status)],
        ]);
    }
} 