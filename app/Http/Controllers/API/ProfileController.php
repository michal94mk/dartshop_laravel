<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;

/**
 * @OA\Tag(
 *     name="Profile",
 *     description="API Endpoints for user profile management"
 * )
 */

class ProfileController extends Controller
{
    /**
     * Update user profile
     *
     * @OA\Put(
     *     path="/api/user/profile",
     *     summary="Update user profile",
     *     description="Update user profile information",
     *     tags={"Profile"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","email"},
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="john@example.com")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Profile updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Profil został pomyślnie zaktualizowany."),
     *             @OA\Property(property="user", ref="#/components/schemas/User")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     )
     * )
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
     * @OA\Put(
     *     path="/api/user/password",
     *     summary="Update user password",
     *     description="Update user password (not available for Google OAuth users)",
     *     tags={"Profile"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"current_password","password","password_confirmation"},
     *             @OA\Property(property="current_password", type="string", example="oldpassword123"),
     *             @OA\Property(property="password", type="string", example="newpassword123"),
     *             @OA\Property(property="password_confirmation", type="string", example="newpassword123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Password updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Hasło zostało pomyślnie zmienione.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error or Google OAuth user",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     )
     * )
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