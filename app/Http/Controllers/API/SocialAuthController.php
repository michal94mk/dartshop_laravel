<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseApiController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends BaseApiController
{
    public function __construct()
    {
        // SSL configuration is now handled by SocialiteServiceProvider
    }

    /**
     * Redirect to Google OAuth
     */
    public function redirectToGoogle()
    {
        try {
            $url = Socialite::driver('google')->stateless()->redirect()->getTargetUrl();
            
            return $this->successResponse(['url' => $url]);
        } catch (\Exception $e) {
            Log::error('Google redirect error: ' . $e->getMessage());
            return $this->serverErrorResponse('Błąd podczas przekierowania do Google: ' . $e->getMessage(), $e);
        }
    }

    /**
     * Handle Google OAuth callback
     */
    public function handleGoogleCallback(Request $request)
    {
        try {
            Log::info('Google OAuth callback started', [
                'url' => $request->fullUrl(),
                'has_code' => $request->has('code'),
                'code_length' => $request->has('code') ? strlen($request->get('code')) : 0,
                'all_params' => $request->all()
            ]);
            
            // Check if we received authorization code
            if (!$request->has('code')) {
                Log::error('Google OAuth callback: No authorization code');
                return $this->errorResponse('Brak kodu autoryzacyjnego od Google', 400);
            }

            // Force SSL configuration before calling Socialite
            $this->forceSSLConfiguration();

            Log::info('About to get Google user with Socialite');

            // Get user data from Google
            $googleUser = Socialite::driver('google')->stateless()->user();
            
            Log::info('Google user data retrieved', [
                'google_id' => $googleUser->getId(),
                'email' => $googleUser->getEmail(),
                'name' => $googleUser->getName(),
                'avatar' => $googleUser->getAvatar()
            ]);
            
            // Check if user already exists
            $user = User::where('email', $googleUser->getEmail())->first();
            
            Log::info('User lookup result', [
                'email' => $googleUser->getEmail(),
                'user_exists' => $user ? true : false,
                'user_id' => $user ? $user->id : null
            ]);
            
            if ($user) {
                // Jeśli użytkownik istnieje, zaktualizuj dane Google
                $user->update([
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                    'email_verified_at' => $user->email_verified_at ?? now(), // Mark email as verified
                ]);
            } else {
                // Utwórz nowego użytkownika
                $user = User::create([
                    'name' => $googleUser->getName() ?? 'Użytkownik Google',
                    'first_name' => $googleUser->user['given_name'] ?? '',
                    'last_name' => $googleUser->user['family_name'] ?? '',
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                    'password' => Hash::make(Str::random(50)), // Random password
                    'email_verified_at' => now(), // Email from Google is already verified
                ]);

                // Assign default user role
                if (method_exists($user, 'assignRole')) {
                    $user->assignRole('user');
                }
            }

            // Zaloguj użytkownika
            Auth::login($user);

            // Pobierz uprawnienia użytkownika
            $permissions = [];
            if (method_exists($user, 'getAllPermissions')) {
                $permissions = $user->getAllPermissions()->pluck('name')->toArray();
            }

            // Prepare response data
            $userData = $user->toArray();
            $userData['permissions'] = $permissions;
            $userData['roles'] = method_exists($user, 'getRoleNames') ? $user->getRoleNames()->toArray() : [];

            return $this->successResponse([
                'message' => 'Successfully logged in with Google',
                'user' => $userData,
                'token' => $user->createToken('auth-token')->plainTextToken
            ]);

        } catch (\Exception $e) {
            Log::error('Google OAuth error: ' . $e->getMessage());
            
            return $this->serverErrorResponse('Błąd podczas logowania przez Google: ' . $e->getMessage(), $e);
        }
    }

    /**
     * Handle Google login from frontend (with access token)
     */
    public function loginWithGoogle(Request $request)
    {
        try {
            $validated = $request->validate([
                'access_token' => 'required|string',
            ]);

            // Get user data from Google using access token
            $googleUser = Socialite::driver('google')->userFromToken($request->access_token);
            
            if (!$googleUser || !$googleUser->getEmail()) {
                return $this->errorResponse('Nie udało się pobrać danych z Google', 400);
            }

            // Sprawdź czy użytkownik już istnieje
            $user = User::where('email', $googleUser->getEmail())->first();
            
            if ($user) {
                // Jeśli użytkownik istnieje, zaktualizuj dane Google
                $user->update([
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                    'email_verified_at' => $user->email_verified_at ?? now(),
                ]);
            } else {
                // Utwórz nowego użytkownika
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

                // Przypisz domyślną rolę użytkownika
                if (method_exists($user, 'assignRole')) {
                    $user->assignRole('user');
                }
            }

            // Zaloguj użytkownika
            Auth::login($user);

            // Pobierz uprawnienia użytkownika
            $permissions = [];
            if (method_exists($user, 'getAllPermissions')) {
                $permissions = $user->getAllPermissions()->pluck('name')->toArray();
            }

            // Prepare response data
            $userData = $user->toArray();
            $userData['permissions'] = $permissions;
            $userData['roles'] = method_exists($user, 'getRoleNames') ? $user->getRoleNames()->toArray() : [];

            return $this->successResponse([
                'message' => 'Pomyślnie zalogowano przez Google',
                'user' => $userData
            ]);

        } catch (\Exception $e) {
            Log::error('Google login error: ' . $e->getMessage());
            
            return $this->serverErrorResponse('Błąd podczas logowania przez Google: ' . $e->getMessage(), $e);
        }
    }

    /**
     * Force SSL configuration for this request
     * 
     * This method disables SSL verification for local development only.
     * In production, proper SSL certificates should be configured.
     * 
     * TODO: Remove this method when proper SSL certificates are configured
     * - Configure proper SSL certificates for development
     * - Use proper CA bundles instead of disabling verification
     * - Consider using mkcert for local development
     */
    private function forceSSLConfiguration()
    {
        if (app()->environment('local')) {
            // Disable SSL verification for local development only
            // This is a temporary solution and should be replaced with proper SSL configuration
            putenv("CURL_CA_BUNDLE=");
            putenv("GUZZLE_HTTP_VERIFY=false");
            ini_set('curl.cainfo', '');
            ini_set('openssl.cafile', '');
            ini_set('openssl.verify_peer', 'Off');
            ini_set('openssl.verify_peer_name', 'Off');
            
            // Rebind Guzzle Client in case Socialite uses different instance
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