<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Api\BaseApiController;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PasswordController extends BaseApiController
{
    public function confirmPassword(Request $request): JsonResponse
    {
        $this->logApiRequest($request, 'Confirm password');
        
        $validated = $this->validateRequest($request, [
            'password' => ['required', 'current_password'],
        ]);

        // If we get here, the password is confirmed
        $request->session()->put('auth.password_confirmed_at', time());

        return $this->successResponse(null, 'Hasło zostało potwierdzone.');
    }

    public function updatePassword(Request $request): JsonResponse
    {
        $this->logApiRequest($request, 'Update password');
        
        $validated = $this->validateRequest($request, [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return $this->successResponse(null, 'Hasło zostało zaktualizowane.');
    }
} 