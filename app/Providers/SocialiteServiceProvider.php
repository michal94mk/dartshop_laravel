<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\SocialiteManager;
use Laravel\Socialite\Two\GoogleProvider;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class SocialiteServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Globally disable SSL verification for development
        if ($this->app->environment('local')) {
            $this->configureDevelopmentSSL();
        }
    }

    public function boot()
    {
        // Configure Socialite with custom HTTP client
        if ($this->app->environment('local')) {
            $this->configureSocialiteForDevelopment();
        }
    }

    /**
     * SSL configuration for development at PHP level
     */
    private function configureDevelopmentSSL()
    {
        // Disable SSL verification globally for development
        putenv("CURL_CA_BUNDLE=");
        putenv("GUZZLE_HTTP_VERIFY=false");
        ini_set('curl.cainfo', '');
        ini_set('openssl.cafile', '');
        ini_set('openssl.verify_peer', 'Off');
        ini_set('openssl.verify_peer_name', 'Off');
        
        // Additional cURL settings
        $defaultOptions = [
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSLVERSION => CURL_SSLVERSION_TLSv1_2,
        ];
        
        // Set default cURL options
        if (function_exists('curl_setopt_array')) {
            foreach ($defaultOptions as $option => $value) {
                curl_setopt(curl_init(), $option, $value);
            }
        }
        
        Log::info('SocialiteServiceProvider: SSL verification disabled globally for development');
    }

    /**
     * Socialite configuration for development
     */
    private function configureSocialiteForDevelopment()
    {
        // Extend Socialite with custom Google provider
        Socialite::extend('google', function ($app) {
            $config = $app['config']['services.google'];
            
            // Create HTTP client with disabled SSL
            $httpClient = new Client([
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
            
            // Create Google provider with custom HTTP client
            $provider = new GoogleProvider(
                $app['request'],
                $config['client_id'],
                $config['client_secret'],
                $config['redirect']
            );
            
            // Set HTTP client
            $provider->setHttpClient($httpClient);
            
            Log::info('SocialiteServiceProvider: Custom Google provider with disabled SSL created');
            
            return $provider;
        });
        
        // Replace default Guzzle Client binding
        $this->app->bind(\GuzzleHttp\Client::class, function ($app) {
            $options = [
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
            ];
            
            return new Client($options);
        });
    }
} 