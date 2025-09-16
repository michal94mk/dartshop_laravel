<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseApiController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class SocialAuthController extends BaseApiController
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['redirectToGoogle', 'handleGoogleCallback']);
    }

    /**
     * Redirect to Google OAuth
     */
    public function redirectToGoogle(): JsonResponse
    {
        $this->logApiRequest(request(), 'Redirect to Google OAuth');
        
        Log::info('Google OAuth configuration check', [
            'client_id' => config('services.google.client_id'),
            'client_secret' => config('services.google.client_secret') ? 'SET' : 'NOT SET',
            'redirect' => config('services.google.redirect'),
            'app_env' => config('app.env'),
            'app_url' => config('app.url'),
        ]);
        
        try {
            $url = Socialite::driver('google')->stateless()->redirect()->getTargetUrl();
            Log::info('Google OAuth redirect URL generated successfully', ['url' => $url]);
            return $this->successResponse(['url' => $url], 'Google OAuth redirect URL generated');
        } catch (\Exception $e) {
            Log::error('Google OAuth redirect error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return $this->errorResponse('Błąd podczas generowania URL Google OAuth: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Handle Google OAuth callback
     */
    public function handleGoogleCallback(Request $request): RedirectResponse
    {
        $this->logApiRequest($request, 'Handle Google OAuth callback');
        
        // Debug logging
        Log::info('Google OAuth callback debug', [
            'url' => $request->fullUrl(),
            'query_params' => $request->query(),
            'has_code' => $request->has('code'),
            'code_value' => $request->get('code') ? 'PRESENT' : 'MISSING',
            'all_params' => $request->all()
        ]);
        
        if (!$request->has('code')) {
            Log::error('Google OAuth callback missing code', [
                'url' => $request->fullUrl(),
                'query_params' => $request->query()
            ]);
            return redirect('/login?error=missing_code');
        }

        try {
            $this->forceSSLConfiguration();
            $googleUser = Socialite::driver('google')->stateless()->user();
        } catch (\Exception $e) {
            Log::error('Google OAuth error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect('/login?error=oauth_error');
        }

        try {
            $user = User::where('email', $googleUser->getEmail())->first();
            if ($user) {
                $user->update([
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                    'email_verified_at' => $user->email_verified_at ?? now(),
                ]);
            } else {
                $user = User::create([
                    'name' => $googleUser->getName() ?? 'Użytkownik Google',
                    'first_name' => $googleUser->user['given_name'] ?? '',
                    'last_name' => $googleUser->user['family_name'] ?? '',
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                    'password' => Hash::make(Str::random(50)),
                    'email_verified_at' => now(),
                ]);
                if (method_exists($user, 'assignRole')) {
                    $user->assignRole('user');
                }
            }
            Auth::login($user);
            
            // Create token
            $token = $user->createToken('auth-token')->plainTextToken;
        } catch (\Exception $e) {
            Log::error('User creation/login error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect('/login?error=user_error');
        }
        
        Log::info('Google OAuth successful', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'token_created' => true
        ]);
        
        // Redirect to frontend with token
        $frontendUrl = config('app.url');
        $redirectUrl = $frontendUrl . '/auth/google/success?token=' . $token . '&redirect=/profile';
        
        Log::info('Google OAuth redirect to frontend', [
            'frontend_url' => $frontendUrl,
            'redirect_url' => $redirectUrl,
            'app_url' => config('app.url')
        ]);
        
        return redirect($redirectUrl);
    }

    /**
     * Handle Google login from frontend (with access token)
     */
    public function loginWithGoogle(Request $request): JsonResponse
    {
        $this->logApiRequest($request, 'Login with Google (frontend)');
        $validated = $this->validateRequest($request, [
            'access_token' => 'required|string',
        ]);
        $googleUser = Socialite::driver('google')->userFromToken($validated['access_token']);
        if (!$googleUser || !$googleUser->getEmail()) {
            return $this->errorResponse('Nie udało się pobrać danych z Google', 400);
        }
        $user = User::where('email', $googleUser->getEmail())->first();
        if ($user) {
            $user->update([
                'google_id' => $googleUser->getId(),
                'avatar' => $googleUser->getAvatar(),
                'email_verified_at' => $user->email_verified_at ?? now(),
            ]);
        } else {
            $user = User::create([
                'name' => $googleUser->getName() ?? 'Użytkownik Google',
                'first_name' => $googleUser->user['given_name'] ?? '',
                'last_name' => $googleUser->user['family_name'] ?? '',
                'email' => $googleUser->getEmail(),
                'google_id' => $googleUser->getId(),
                'avatar' => $googleUser->getAvatar(),
                'password' => Hash::make(Str::random(50)),
                'email_verified_at' => now(),
            ]);
            if (method_exists($user, 'assignRole')) {
                $user->assignRole('user');
            }
        }
        Auth::login($user);
        $permissions = method_exists($user, 'getAllPermissions') ? $user->getAllPermissions()->pluck('name')->toArray() : [];
        $userData = $user->toArray();
        $userData['permissions'] = $permissions;
        $userData['roles'] = method_exists($user, 'getRoleNames') ? $user->getRoleNames()->toArray() : [];
        return $this->successResponse([
            'message' => 'Pomyślnie zalogowano przez Google',
            'user' => $userData
        ], 'Google login successful');
    }

    private function forceSSLConfiguration()
    {
        if (app()->environment('local')) {
            putenv("CURL_CA_BUNDLE=");
            putenv("GUZZLE_HTTP_VERIFY=false");
            ini_set('curl.cainfo', '');
            ini_set('openssl.cafile', '');
            ini_set('openssl.verify_peer', 'Off');
            ini_set('openssl.verify_peer_name', 'Off');
            app()->bind(\GuzzleHttp\Client::class, function ($app) {
                return new \GuzzleHttp\Client([
                    'verify' => false,
                    'timeout' => 30,
                    'connect_timeout' => 10,
                    'http_errors' => false,
                    'curl' => [
                        CURLOPT_SSL_VERIFYPEER => false,
                        CURLOPT_SSL_VERIFYHOST => false,
                        CURLOPT_SSLVERSION => CURL_SSLVERSION_TLSv1_2,
                        CURLOPT_CAINFO => '',
                    ],
                ]);
            });
            Log::info('SSL configuration forced for local development', [
                'environment' => app()->environment(),
                'method' => __METHOD__
            ]);
        }
    }
} 