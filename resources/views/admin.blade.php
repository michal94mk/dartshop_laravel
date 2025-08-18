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
            isAdmin: true
        };
    </script>

    <title>Admin Panel | {{ config('app.name', 'DartShop') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.ts'])
    
    <style>
        /* Admin Fallback Loading Screen Styles */
        .admin-fallback-container {
            position: fixed;
            inset: 0;
            z-index: 9999;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(8px);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .admin-fallback-content {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 1.5rem;
            text-align: center;
            padding: 2rem;
        }

        .admin-fallback-spinner {
            width: 48px;
            height: 48px;
            border: 4px solid #f3f4f6;
            border-top: 4px solid #4f46e5;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        .admin-loading-label {
            font-size: 1.125rem;
            font-weight: 500;
            color: #374151;
        }

        .admin-subtitle {
            font-size: 0.875rem;
            color: #6b7280;
            margin-top: 0.5rem;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        @media (prefers-color-scheme: dark) {
            .admin-fallback-container {
                background: rgba(17, 24, 39, 0.9);
            }
            
            .admin-loading-label {
                color: #d1d5db;
            }
            
            .admin-subtitle {
                color: #9ca3af;
            }
            
            .admin-fallback-spinner {
                border-color: #374151;
                border-top-color: #6366f1;
            }
        }
    </style>
</head>
<body class="font-sans antialiased bg-gray-100 text-gray-800">
    <div id="app">
        <!-- Fallback content if Vue.js doesn't load -->
        <div id="admin-fallback-loader" class="admin-fallback-container">
            <div class="admin-fallback-content">
                <!-- Admin Spinner -->
                <div class="admin-fallback-spinner"></div>
                
                <!-- Loading Indicator -->
                <div class="admin-loading-label">Ładowanie panelu administracyjnego...</div>
                <div class="admin-subtitle">Inicjalizacja systemu zarządzania</div>
            </div>
        </div>
    </div>
    

</body>
</html> 