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
            background: linear-gradient(135deg, 
                rgba(79, 70, 229, 0.1) 0%, 
                rgba(124, 58, 237, 0.1) 50%, 
                rgba(236, 72, 153, 0.1) 100%
            );
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .fallback-content {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 3rem;
            text-align: center;
            padding: 2rem;
        }

        .fallback-logo {
            position: relative;
            width: 120px;
            height: 120px;
        }

        .fallback-ring {
            position: absolute;
            border-radius: 50%;
            border: 2px solid transparent;
            border-top: 2px solid;
            border-right: 2px solid;
        }

        .fallback-ring-1 {
            inset: 0;
            border-image: linear-gradient(45deg, #4f46e5, #7c3aed) 1;
            animation: logoRotate 3s linear infinite;
        }

        .fallback-ring-2 {
            inset: 15px;
            border-image: linear-gradient(135deg, #7c3aed, #ec4899) 1;
            animation: logoRotate 2s linear infinite reverse;
        }

        .fallback-ring-3 {
            inset: 30px;
            border-image: linear-gradient(225deg, #ec4899, #f59e0b) 1;
            animation: logoRotate 4s linear infinite;
        }

        .fallback-center {
            position: absolute;
            inset: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            border-radius: 50%;
            box-shadow: 0 0 40px rgba(79, 70, 229, 0.3);
            animation: logoPulse 2s ease-in-out infinite;
        }

        .fallback-icon {
            width: 24px;
            height: 24px;
            color: white;
        }

        .fallback-progress {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 1.5rem;
            min-width: 300px;
        }

        .fallback-progress-track {
            width: 100%;
            height: 4px;
            background: rgba(79, 70, 229, 0.1);
            border-radius: 2px;
            overflow: hidden;
        }

        .fallback-progress-bar {
            height: 100%;
            width: 0%;
            background: linear-gradient(90deg, #4f46e5, #7c3aed, #ec4899, #f59e0b);
            border-radius: 2px;
            animation: progressLoad 3s ease-in-out infinite;
            box-shadow: 0 0 20px rgba(79, 70, 229, 0.4);
        }

        .fallback-text {
            font-size: 1.125rem;
            font-weight: 600;
            background: linear-gradient(45deg, #4f46e5, #7c3aed, #ec4899);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: textGlow 2s ease-in-out infinite;
        }

        .fallback-subtitle {
            font-size: 0.875rem;
            color: #64748b;
            margin-top: 0.5rem;
            animation: fadeInOut 3s ease-in-out infinite;
        }

        @keyframes logoRotate {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        @keyframes logoPulse {
            0%, 100% { 
                transform: scale(1);
                box-shadow: 0 0 40px rgba(79, 70, 229, 0.3);
            }
            50% { 
                transform: scale(1.05);
                box-shadow: 0 0 60px rgba(79, 70, 229, 0.5);
            }
        }

        @keyframes progressLoad {
            0% { width: 0%; }
            50% { width: 70%; }
            100% { width: 100%; }
        }

        @keyframes textGlow {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.8; }
        }

        @keyframes fadeInOut {
            0%, 100% { opacity: 0.6; }
            50% { opacity: 1; }
        }

        @media (max-width: 640px) {
            .fallback-logo {
                width: 80px;
                height: 80px;
            }
            
            .fallback-center {
                inset: 25px;
            }
            
            .fallback-icon {
                width: 18px;
                height: 18px;
            }
            
            .fallback-progress {
                min-width: 250px;
            }
            
            .fallback-text {
                font-size: 1rem;
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
                <div class="fallback-logo">
                    <div class="fallback-ring fallback-ring-1"></div>
                    <div class="fallback-ring fallback-ring-2"></div>
                    <div class="fallback-ring fallback-ring-3"></div>
                    <div class="fallback-center">
                        <svg class="fallback-icon" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M7 4V2C7 1.45 7.45 1 8 1H16C16.55 1 17 1.45 17 2V4H20C20.55 4 21 4.45 21 5S20.55 6 20 6H19V19C19 20.1 18.1 21 17 21H7C5.9 21 5 20.1 5 19V6H4C3.45 6 3 5.55 3 5S3.45 4 4 4H7ZM9 3V4H15V3H9ZM7 6V19H17V6H7Z"/>
                        </svg>
                    </div>
                </div>
                
                <!-- Progress -->
                <div class="fallback-progress">
                    <div class="fallback-progress-track">
                        <div class="fallback-progress-bar"></div>
                    </div>
                    <div class="fallback-text">Inicjalizacja DartShop...</div>
                    <div class="fallback-subtitle">Ładowanie aplikacji sklepu</div>
                </div>
            </div>
        </div>
    </div>
    

</body>
</html> 