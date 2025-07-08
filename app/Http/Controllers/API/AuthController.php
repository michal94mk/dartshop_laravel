<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Enums\RoleEnum;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Login user and create token
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($request->only('email', 'password'), true)) { // Remember user
            $user = Auth::user();
            
            // Also login via web guard for session-based auth
            Auth::guard('web')->login($user, true);
            
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'user' => $this->getUserWithRolesAndPermissions($user),
                'token' => $token,
                'token_type' => 'Bearer',
            ]);
        }

        throw ValidationException::withMessages([
            'email' => ['The provided credentials are incorrect.'],
        ]);
    }

    /**
     * Register a new user
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'privacy_policy_accepted' => 'required|boolean|accepted',
            'newsletter_consent' => 'boolean',
        ]);

        $user = User::create([
            'name' => $request->name,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'privacy_policy_accepted' => $request->privacy_policy_accepted,
            'privacy_policy_accepted_at' => $request->privacy_policy_accepted ? now() : null,
        ]);

        // Assign default user role
        $user->assignRole(RoleEnum::User->value);

        // Send email verification notification
        $user->sendEmailVerificationNotification();

        // Handle newsletter subscription if consent was given
        if ($request->newsletter_consent) {
            // You might want to handle newsletter subscription here
            // For example, create a newsletter subscription record
        }

        // Auto-login the user after registration (both Sanctum and web session)
        Auth::login($user, true); // Remember user
        Auth::guard('web')->login($user, true);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user' => $this->getUserWithRolesAndPermissions($user),
            'token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    /**
     * Get the authenticated user
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function user(Request $request)
    {
        $user = $request->user();
        return response()->json($this->getUserWithRolesAndPermissions($user));
    }

    /**
     * Logout user (Revoke the token)
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        // Handle token-based logout (API tokens)
        if ($request->user()) {
            // Delete all tokens instead of just current one
            $request->user()->tokens()->delete();
        }
        
        // Handle session-based logout (cookies)
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        // Set cookie expiry to force browser to remove it
        $cookie = cookie()->forget('laravel_session');
        
        return response()->json(['message' => 'Successfully logged out'])->withCookie($cookie);
    }

    /**
     * Get user with roles and permissions
     *
     * @param  \App\Models\User  $user
     * @return array
     */
    protected function getUserWithRolesAndPermissions(User $user)
    {
        $userData = $user->toArray();
        $userData['roles'] = $user->getRoleNames();
        $userData['permissions'] = $user->getAllPermissions()->pluck('name');
        
        return $userData;
    }
} 