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
</head>
<body class="font-sans antialiased bg-gray-100 text-gray-800">
    <div id="app">
        <!-- Fallback content if Vue.js doesn't load -->
        <div class="min-h-screen flex items-center justify-center bg-gray-100 p-6">
            <div class="max-w-md w-full bg-white shadow-md rounded-lg p-6">
                <div class="text-center">
                    <h2 class="text-2xl font-bold text-gray-800 mb-2">Ładowanie panelu administracyjnego...</h2>
                    <p class="text-gray-600">Jeśli ta strona nie znika, mogą występować problemy z JavaScript. Sprawdź konsolę przeglądarki.</p>
                    <div class="mt-4">
                        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-700 mx-auto"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html> 