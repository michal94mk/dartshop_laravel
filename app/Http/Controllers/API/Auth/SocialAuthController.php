<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Api\BaseApiController;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Enums\RoleEnum;
use App\Services\CartService;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

class SocialAuthController extends BaseApiController
{
    public function __construct(
        private CartService $cartService
    ) {}

    public function handleGoogleCallback(Request $request): JsonResponse
    {
        $this->logApiRequest($request, 'Google authentication callback');
        
        $googleUser = Socialite::driver('google')->user();
        
        // Find existing user or create new one
        $user = User::where('google_id', $googleUser->id)->first();
        
        $isNewUser = false;
        
        if (!$user) {
            // Check if user exists with same email
            $user = User::where('email', $googleUser->email)->first();
            
            if ($user) {
                // Link existing account with Google
                $user->update([
                    'google_id' => $googleUser->id,
                    'avatar' => $googleUser->avatar
                ]);
            } else {
                // Create new user
                $user = User::create([
                    'name' => $googleUser->name,
                    'email' => $googleUser->email,
                    'google_id' => $googleUser->id,
                    'avatar' => $googleUser->avatar,
                    'email_verified_at' => now(), // Google accounts are pre-verified
                    'password' => Hash::make(Str::random(24)), // Random password for security
                    'privacy_policy_accepted' => true,
                    'privacy_policy_accepted_at' => now(),
                ]);
                
                // Assign default user role
                $user->assignRole(RoleEnum::User->value);
                
                $isNewUser = true;
            }
        }
        
        // Login user
        Auth::login($user, true);
        Auth::guard('web')->login($user, true);
        
        // Migrate session cart to database
        $this->cartService->migrateSessionCartToDatabase($user);
        
        $token = $user->createToken('auth_token')->plainTextToken;
        
        return $this->successResponse([
            'user' => $this->getUserWithRolesAndPermissions($user),
            'token' => $token,
            'token_type' => 'Bearer',
            'is_new_user' => $isNewUser
        ], 'Google authentication successful');
    }

    protected function getUserWithRolesAndPermissions(User $user): array
    {
        $userData = $user->toArray();
        $userData['roles'] = $user->getRoleNames();
        $userData['permissions'] = $user->getAllPermissions()->pluck('name');
        
        return $userData;
    }
} 