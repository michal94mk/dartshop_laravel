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
     * Aktualizacja profilu użytkownika
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

        // Jeśli email się zmienił, wymuszamy ponowną weryfikację
        if ($user->email !== $validated['email']) {
            $validated['email_verified_at'] = null;
            
            // Aktualizuj dane użytkownika
            $user->update($validated);
            
            // Wyślij ponownie link weryfikacyjny
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
     * Aktualizacja hasła użytkownika
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = $request->user();

        // Sprawdź czy obecne hasło jest prawidłowe
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