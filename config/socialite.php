<?php

return [
    /*
    |--------------------------------------------------------------------------
    | HTTP Client Configuration
    |--------------------------------------------------------------------------
    |
    | This sets the HTTP client options for Socialite providers.
    | In development, we need to specify the CA bundle for SSL verification.
    |
    */

    'guzzle' => [
        'verify' => env('APP_ENV') === 'local' && file_exists(__DIR__ . '/../cacert.pem') 
            ? __DIR__ . '/../cacert.pem'
            : true,
        
        'timeout' => 30,
        'connect_timeout' => 10,
    ],
]; 