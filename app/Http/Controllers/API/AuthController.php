<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Enums\RoleEnum;
use App\Services\CartService;
use Illuminate\Validation\ValidationException;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    protected $cartService;
    
    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * Login user and create token
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($request->only('email', 'password'), true)) {
            $user = Auth::user();
            
            // Also login via web guard for session-based auth
            Auth::guard('web')->login($user, true);
            
            // Migrate session cart to database
            $this->cartService->migrateSessionCartToDatabase($user);
            
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
        }

        // Auto-login the user after registration
        Auth::login($user, true);
        Auth::guard('web')->login($user, true);
        
        // Migrate session cart to database
        $this->cartService->migrateSessionCartToDatabase($user);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user' => $this->getUserWithRolesAndPermissions($user),
            'token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    /**
     * Handle Google OAuth login/register
     */
    public function handleGoogleCallback(Request $request)
    {
        try {
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
            
            return response()->json([
                'user' => $this->getUserWithRolesAndPermissions($user),
                'token' => $token,
                'token_type' => 'Bearer',
                'is_new_user' => $isNewUser
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error during Google authentication',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get the authenticated user
     */
    public function user(Request $request)
    {
        $user = $request->user();
        return response()->json($this->getUserWithRolesAndPermissions($user));
    }

    /**
     * Logout user (Revoke the token)
     */
    public function logout(Request $request)
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
        
        return response()->json(['message' => 'Successfully logged out'])->withCookie($cookie);
    }

    /**
     * Get user with roles and permissions
     */
    protected function getUserWithRolesAndPermissions(User $user)
    {
        $userData = $user->toArray();
        $userData['roles'] = $user->getRoleNames();
        $userData['permissions'] = $user->getAllPermissions()->pluck('name');
        
        return $userData;
    }
} 