<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;
use Illuminate\Http\JsonResponse;
use Exception;

class ProfileController extends BaseApiController
{
    /**
     * Update user profile
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function update(Request $request): JsonResponse
    {
        try {
            $this->logApiRequest($request, 'Update user profile');
            
            $user = $request->user();

            $validated = $this->validateRequest($request, [
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
                
                return $this->successResponse([
                    'message' => 'Profil został zaktualizowany. Ponieważ zmieniłeś adres e-mail, musisz go ponownie zweryfikować. Link weryfikacyjny został wysłany.',
                    'user' => $user
                ], 'Profile updated successfully');
            }

            $user->update($validated);

            return $this->successResponse([
                'message' => 'Profil został pomyślnie zaktualizowany.',
                'user' => $user
            ], 'Profile updated successfully');
        } catch (Exception $e) {
            return $this->handleException($e, 'Updating user profile');
        }
    }

    /**
     * Update user password
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function updatePassword(Request $request): JsonResponse
    {
        try {
            $this->logApiRequest($request, 'Update user password');
            
            $user = $request->user();
            
            // Prevent password change for Google OAuth users
            if (!empty($user->google_id)) {
                return $this->validationErrorResponse([
                    'password' => ['Użytkownicy zalogowani przez Google OAuth nie mogą zmieniać hasła w tej aplikacji. Aby zmienić hasło, przejdź do ustawień Google.']
                ], 'Google OAuth users cannot change password');
            }

            $validated = $this->validateRequest($request, [
                'current_password' => 'required',
                'password' => 'required|min:8|confirmed',
            ]);

            // Check if current password is correct
            if (!Hash::check($validated['current_password'], $user->password)) {
                return $this->validationErrorResponse([
                    'current_password' => ['Podane aktualne hasło jest nieprawidłowe.']
                ], 'Current password is incorrect');
            }

            $user->update([
                'password' => Hash::make($validated['password']),
            ]);

            return $this->successResponse([
                'message' => 'Hasło zostało pomyślnie zmienione.'
            ], 'Password updated successfully');
        } catch (Exception $e) {
            return $this->handleException($e, 'Updating user password');
        }
    }
} 