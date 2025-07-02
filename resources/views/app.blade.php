<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Add base URL information for the JavaScript -->
    <script>
        window.Laravel = {
            csrfToken: "{{ csrf_token() }}",
            baseUrl: "{{ url('/') }}",
            apiUrl: "{{ url('/api') }}",
            isAdmin: false
        };
    </script>

    <title>{{ config('app.name', 'DartShop') }}</title>

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('favicon.svg') }}">
    <meta name="theme-color" content="#1f2937">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        /* Fallback Loading Screen Styles */
        .fallback-loading-container {
            position: fixed;
            inset: 0;
            z-index: 9999;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(8px);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .fallback-content {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 1.5rem;
            text-align: center;
            padding: 2rem;
        }

        .fallback-spinner {
            width: 48px;
            height: 48px;
            border: 4px solid #f3f4f6;
            border-top: 4px solid #4f46e5;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        .fallback-text {
            font-size: 1.125rem;
            font-weight: 500;
            color: #374151;
        }

        .fallback-subtitle {
            font-size: 0.875rem;
            color: #6b7280;
            margin-top: 0.5rem;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        @media (prefers-color-scheme: dark) {
            .fallback-loading-container {
                background: rgba(17, 24, 39, 0.9);
            }
            
            .fallback-text {
                color: #d1d5db;
            }
            
            .fallback-subtitle {
                color: #9ca3af;
            }
            
            .fallback-spinner {
                border-color: #374151;
                border-top-color: #6366f1;
            }
        }
    </style>
</head>
<body class="font-sans antialiased bg-gray-50 text-gray-800">
    <div id="app">
        <!-- Fallback content if Vue.js doesn't load -->
        <div id="vue-fallback-loader" class="fallback-loading-container">
            <div class="fallback-content">
                <!-- Animated Logo -->
                <div class="fallback-spinner"></div>
                
                <!-- Progress -->
                <div class="fallback-text">Inicjalizacja DartShop...</div>
                <div class="fallback-subtitle">Ładowanie aplikacji sklepu</div>
            </div>
        </div>
    </div>
    

</body>
</html> 