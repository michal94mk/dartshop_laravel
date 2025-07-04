<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Update user profile
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
        ]);

        // If email changed, force re-verification
        if ($user->email !== $validated['email']) {
            $validated['email_verified_at'] = null;
            
            // Update user data
            $user->update($validated);
            
            // Send verification link again
            $user->sendEmailVerificationNotification();
            
            return response()->json([
                'message' => 'Profil został zaktualizowany. Ponieważ zmieniłeś adres e-mail, musisz go ponownie zweryfikować. Link weryfikacyjny został wysłany.',
                'user' => $user
            ]);
        }

        $user->update($validated);

        return response()->json([
            'message' => 'Profil został pomyślnie zaktualizowany.',
            'user' => $user
        ]);
    }

    /**
     * Update user password
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updatePassword(Request $request)
    {
        $user = $request->user();
        
        // Prevent password change for Google OAuth users
        if (!empty($user->google_id)) {
            return response()->json([
                'message' => 'Użytkownicy zalogowani przez Google OAuth nie mogą zmieniać hasła w tej aplikacji. Aby zmienić hasło, przejdź do ustawień Google.'
            ], 422);
        }

        $validated = $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        // Check if current password is correct
        if (!Hash::check($validated['current_password'], $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['Podane aktualne hasło jest nieprawidłowe.'],
            ]);
        }

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return response()->json([
            'message' => 'Hasło zostało pomyślnie zmienione.'
        ]);
    }
} 