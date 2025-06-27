<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

class EmailVerificationController extends Controller
{
    /**
     * Mark user's email as verified
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function verify(Request $request, $id)
    {
        if (!$request->hasValidSignature()) {
            return response()->json([
                'message' => 'Link weryfikacyjny jest nieprawidłowy lub wygasł.'
            ], 400);
        }

        $user = \App\Models\User::findOrFail($id);

        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'Adres e-mail jest już zweryfikowany.'
            ]);
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return response()->json([
            'message' => 'Adres e-mail został pomyślnie zweryfikowany.'
        ]);
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