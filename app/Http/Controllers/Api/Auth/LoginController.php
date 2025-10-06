<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Auth\ApiLoginRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Services\CartService;

class LoginController extends BaseApiController
{
    public function __construct(
        private CartService $cartService
    ) {}

    public function login(ApiLoginRequest $request): JsonResponse
    {
        $this->logApiRequest($request, 'User login attempt');

        $validated = $request->validated();
        $remember = $validated['remember'] ?? false;

        if (Auth::attempt([
            'email' => $validated['email'],
            'password' => $validated['password']
        ], $remember)) {
            $user = Auth::user();
            
            // Also login via web guard for session-based auth
            Auth::guard('web')->login($user, $remember);
            
            // Migrate session cart to database
            $this->cartService->migrateSessionCartToDatabase($user);
            
            $token = $user->createToken('auth_token')->plainTextToken;

            return $this->successResponse([
                'token' => $token,
                'user' => $this->getUserWithRolesAndPermissions($user),
                'token_type' => 'Bearer'
            ], 'Login successful');
        }

        return $this->unauthorizedResponse('The provided credentials are incorrect.');
    }

    public function logout(Request $request): JsonResponse
    {
        // Handle token-based logout (API tokens)
        if ($request->user()) {
            $request->user()->tokens()->delete();
        }
        
        // Handle session-based logout
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        // Set cookie expiry to force browser to remove it
        $cookie = cookie()->forget('laravel_session');
        
        return $this->successResponse(null, 'Successfully logged out')->withCookie($cookie);
    }

    public function user(Request $request): JsonResponse
    {
        $user = $request->user();
        $userData = $this->getUserWithRolesAndPermissions($user);
        
        
        return $this->successResponse($userData);
    }

    protected function getUserWithRolesAndPermissions(User $user): array
    {
        $userData = $user->toArray();
        $userData['roles'] = $user->getRoleNames();
        $userData['permissions'] = $user->getAllPermissions()->pluck('name');
        
        return $userData;
    }
} 