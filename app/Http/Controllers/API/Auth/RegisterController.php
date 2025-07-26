<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Auth\ApiRegisterRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Enums\RoleEnum;
use App\Services\CartService;

class RegisterController extends BaseApiController
{
    public function __construct(
        private CartService $cartService
    ) {}

    public function register(ApiRegisterRequest $request): JsonResponse
    {
        $this->logApiRequest($request, 'User registration attempt');
        
        $validated = $request->validated();

        $user = User::create([
            'name' => $validated['name'],
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'privacy_policy_accepted' => $validated['privacy_policy_accepted'],
            'privacy_policy_accepted_at' => $validated['privacy_policy_accepted'] ? now() : null,
        ]);

        // Assign default user role
        $user->assignRole(RoleEnum::User->value);

        // Send email verification notification
        $user->sendEmailVerificationNotification();

        // Handle newsletter subscription if consent was given
        if ($validated['newsletter_consent'] ?? false) {
            // You might want to handle newsletter subscription here
        }

        // Auto-login the user after registration
        Auth::login($user, true);
        Auth::guard('web')->login($user, true);
        
        // Migrate session cart to database
        $this->cartService->migrateSessionCartToDatabase($user);

        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->createdResponse([
            'token' => $token,
            'user' => $this->getUserWithRolesAndPermissions($user),
            'token_type' => 'Bearer'
        ], 'Registration successful');
    }

    protected function getUserWithRolesAndPermissions(User $user): array
    {
        $userData = $user->toArray();
        $userData['roles'] = $user->getRoleNames();
        $userData['permissions'] = $user->getAllPermissions()->pluck('name');
        
        return $userData;
    }
} 