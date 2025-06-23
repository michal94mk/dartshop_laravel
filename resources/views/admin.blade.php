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
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        /* Admin Fallback Loading Screen Styles */
        .admin-fallback-container {
            position: fixed;
            inset: 0;
            z-index: 9999;
            background: linear-gradient(135deg, 
                rgba(79, 70, 229, 0.15) 0%, 
                rgba(99, 102, 241, 0.15) 50%, 
                rgba(124, 58, 237, 0.15) 100%
            );
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .admin-fallback-content {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 3rem;
            text-align: center;
            padding: 2rem;
        }

        .admin-fallback-spinner {
            position: relative;
            width: 80px;
            height: 80px;
        }

        .admin-outer-ring {
            position: absolute;
            inset: 0;
            border-radius: 50%;
            animation: adminRotate 3s linear infinite;
        }

        .admin-ring-segment {
            position: absolute;
            inset: 0;
            border-radius: 50%;
            border: 2px solid transparent;
        }

        .admin-segment-1 {
            border-top: 2px solid #4f46e5;
            border-right: 2px solid #4f46e5;
            opacity: 0.9;
            animation: adminFadeSegment 2s ease-in-out infinite;
        }

        .admin-segment-2 {
            border-right: 2px solid #6366f1;
            border-bottom: 2px solid #6366f1;
            opacity: 0.7;
            animation: adminFadeSegment 2s ease-in-out infinite 0.7s;
        }

        .admin-segment-3 {
            border-bottom: 2px solid #8b5cf6;
            border-left: 2px solid #8b5cf6;
            opacity: 0.5;
            animation: adminFadeSegment 2s ease-in-out infinite 1.4s;
        }

        .admin-middle-ring {
            position: absolute;
            inset: 8px;
            border-radius: 50%;
        }

        .admin-pulse-wave {
            position: absolute;
            inset: 0;
            border-radius: 50%;
            border: 1px solid rgba(79, 70, 229, 0.3);
            animation: adminPulseWave 2s ease-out infinite;
        }

        .admin-wave-1 { animation-delay: 0s; }
        .admin-wave-2 { animation-delay: 0.7s; }

        .admin-inner-core {
            position: absolute;
            top: 50%;
            left: 50%;
            width: 20px;
            height: 20px;
            margin: -10px 0 0 -10px;
            background: linear-gradient(135deg, #4f46e5, #6366f1);
            border-radius: 50%;
            box-shadow: 0 0 25px rgba(79, 70, 229, 0.4);
            animation: adminCoreBreath 2s ease-in-out infinite;
        }

        .admin-data-particles {
            position: absolute;
            inset: -15px;
        }

        .admin-particle {
            position: absolute;
            width: 3px;
            height: 3px;
            background: #4f46e5;
            border-radius: 50%;
            box-shadow: 0 0 8px rgba(79, 70, 229, 0.6);
        }

        .admin-particle-1 {
            top: 5px;
            left: 50%;
            margin-left: -1.5px;
            animation: adminOrbitParticle 4s linear infinite;
        }

        .admin-particle-2 {
            top: 50%;
            right: 5px;
            margin-top: -1.5px;
            animation: adminOrbitParticle 4s linear infinite 1.3s;
        }

        .admin-particle-3 {
            bottom: 5px;
            left: 50%;
            margin-left: -1.5px;
            animation: adminOrbitParticle 4s linear infinite 2.6s;
        }

        .admin-loading-indicator {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 1rem;
            min-width: 280px;
        }

        .admin-progress-bar {
            width: 100%;
            height: 4px;
            background: rgba(79, 70, 229, 0.1);
            border-radius: 2px;
            overflow: hidden;
            backdrop-filter: blur(10px);
        }

        .admin-progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #4f46e5, #6366f1, #8b5cf6);
            border-radius: 2px;
            animation: adminProgressFlow 2s ease-in-out infinite;
            box-shadow: 0 0 15px rgba(79, 70, 229, 0.4);
        }

        .admin-loading-label {
            font-size: 1rem;
            font-weight: 600;
            color: #374151;
            animation: adminLabelFade 2s ease-in-out infinite;
        }

        .admin-subtitle {
            font-size: 0.875rem;
            color: #64748b;
            margin-top: 0.5rem;
            animation: adminSubtitleFade 3s ease-in-out infinite;
        }

        /* Admin Animations */
        @keyframes adminRotate {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        @keyframes adminFadeSegment {
            0%, 100% { opacity: 0.3; }
            50% { opacity: 1; }
        }

        @keyframes adminPulseWave {
            0% {
                transform: scale(0.8);
                opacity: 0.8;
            }
            100% {
                transform: scale(1.4);
                opacity: 0;
            }
        }

        @keyframes adminCoreBreath {
            0%, 100% { 
                transform: scale(1);
                box-shadow: 0 0 25px rgba(79, 70, 229, 0.4);
            }
            50% { 
                transform: scale(1.1);
                box-shadow: 0 0 35px rgba(79, 70, 229, 0.6);
            }
        }

        @keyframes adminOrbitParticle {
            0% {
                transform: scale(1) translateX(0);
                opacity: 0.7;
            }
            25% {
                transform: scale(1.2) translateX(3px);
                opacity: 1;
            }
            50% {
                transform: scale(1) translateX(0);
                opacity: 0.7;
            }
            75% {
                transform: scale(0.8) translateX(-3px);
                opacity: 0.5;
            }
            100% {
                transform: scale(1) translateX(0);
                opacity: 0.7;
            }
        }

        @keyframes adminProgressFlow {
            0% {
                transform: translateX(-100%);
                opacity: 0;
            }
            50% {
                opacity: 1;
            }
            100% {
                transform: translateX(100%);
                opacity: 0;
            }
        }

        @keyframes adminLabelFade {
            0%, 100% { opacity: 0.8; }
            50% { opacity: 1; }
        }

        @keyframes adminSubtitleFade {
            0%, 100% { opacity: 0.6; }
            50% { opacity: 1; }
        }

        @media (max-width: 640px) {
            .admin-fallback-spinner {
                width: 60px;
                height: 60px;
            }
            
            .admin-inner-core {
                width: 16px;
                height: 16px;
                margin: -8px 0 0 -8px;
            }
            
            .admin-loading-indicator {
                min-width: 220px;
            }
            
            .admin-loading-label {
                font-size: 0.875rem;
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
                <div class="admin-fallback-spinner">
                    <!-- Outer Rotating Ring -->
                    <div class="admin-outer-ring">
                        <div class="admin-ring-segment admin-segment-1"></div>
                        <div class="admin-ring-segment admin-segment-2"></div>
                        <div class="admin-ring-segment admin-segment-3"></div>
                    </div>
                    
                    <!-- Middle Pulse Ring -->
                    <div class="admin-middle-ring">
                        <div class="admin-pulse-wave admin-wave-1"></div>
                        <div class="admin-pulse-wave admin-wave-2"></div>
                    </div>
                    
                    <!-- Inner Core -->
                    <div class="admin-inner-core"></div>
                    
                    <!-- Data Particles -->
                    <div class="admin-data-particles">
                        <div class="admin-particle admin-particle-1"></div>
                        <div class="admin-particle admin-particle-2"></div>
                        <div class="admin-particle admin-particle-3"></div>
                    </div>
                </div>
                
                <!-- Loading Indicator -->
                <div class="admin-loading-indicator">
                    <div class="admin-progress-bar">
                        <div class="admin-progress-fill"></div>
                    </div>
                    <div class="admin-loading-label">Ładowanie panelu administracyjnego...</div>
                    <div class="admin-subtitle">Inicjalizacja systemu zarządzania</div>
                </div>
            </div>
        </div>
    </div>
    

</body>
</html> 