<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'DartShop') }} - Panel Administratora - @yield('title')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Styles -->
</head>
<body class="font-sans antialiased bg-gray-100">
    <div class="min-h-screen bg-gray-100">
        <nav x-data="{ open: false }" class="bg-indigo-800 border-b border-indigo-700">
            <!-- Primary Navigation Menu -->
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <!-- Logo -->
                        <div class="shrink-0 flex items-center">
                            <a href="{{ route('home') }}" class="text-white font-bold text-xl">
                                <span>Dart</span><span class="text-indigo-200">Shop</span>
                            </a>
                        </div>

                        <!-- Navigation Links -->
                        <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                            <a href="{{ route('admin.products.index') }}" 
                               class="inline-flex items-center px-1 pt-1 border-b-2 {{ str_starts_with(request()->route()->getName(), 'admin.products') ? 'border-white text-white' : 'border-transparent text-indigo-200 hover:text-white hover:border-indigo-300' }} text-sm font-medium leading-5">
                                Produkty
                            </a>
                            <a href="{{ route('admin.categories.index') }}" 
                               class="inline-flex items-center px-1 pt-1 border-b-2 {{ str_starts_with(request()->route()->getName(), 'admin.categories') ? 'border-white text-white' : 'border-transparent text-indigo-200 hover:text-white hover:border-indigo-300' }} text-sm font-medium leading-5">
                                Kategorie
                            </a>
                            <a href="{{ route('admin.brands.index') }}" 
                               class="inline-flex items-center px-1 pt-1 border-b-2 {{ str_starts_with(request()->route()->getName(), 'admin.brands') ? 'border-white text-white' : 'border-transparent text-indigo-200 hover:text-white hover:border-indigo-300' }} text-sm font-medium leading-5">
                                Marki
                            </a>
                            <a href="{{ route('admin.promotions.index') }}" 
                               class="inline-flex items-center px-1 pt-1 border-b-2 {{ str_starts_with(request()->route()->getName(), 'admin.promotions') ? 'border-white text-white' : 'border-transparent text-indigo-200 hover:text-white hover:border-indigo-300' }} text-sm font-medium leading-5">
                                Promocje
                            </a>
                            <a href="{{ route('admin.reviews.index') }}" 
                               class="inline-flex items-center px-1 pt-1 border-b-2 {{ str_starts_with(request()->route()->getName(), 'admin.reviews') ? 'border-white text-white' : 'border-transparent text-indigo-200 hover:text-white hover:border-indigo-300' }} text-sm font-medium leading-5">
                                Recenzje
                            </a>
                            <a href="{{ route('admin.orders.index') }}" 
                               class="inline-flex items-center px-1 pt-1 border-b-2 {{ str_starts_with(request()->route()->getName(), 'admin.orders') ? 'border-white text-white' : 'border-transparent text-indigo-200 hover:text-white hover:border-indigo-300' }} text-sm font-medium leading-5">
                                Zamówienia
                            </a>
                            <a href="{{ route('admin.payments.index') }}" 
                               class="inline-flex items-center px-1 pt-1 border-b-2 {{ str_starts_with(request()->route()->getName(), 'admin.payments') ? 'border-white text-white' : 'border-transparent text-indigo-200 hover:text-white hover:border-indigo-300' }} text-sm font-medium leading-5">
                                Płatności
                            </a>
                            <a href="{{ route('admin.contact.index') }}" 
                               class="inline-flex items-center px-1 pt-1 border-b-2 {{ str_starts_with(request()->route()->getName(), 'admin.contact') ? 'border-white text-white' : 'border-transparent text-indigo-200 hover:text-white hover:border-indigo-300' }} text-sm font-medium leading-5">
                                Kontakt
                            </a>
                            <a href="{{ route('admin.users.index') }}" 
                               class="inline-flex items-center px-1 pt-1 border-b-2 {{ str_starts_with(request()->route()->getName(), 'admin.users') ? 'border-white text-white' : 'border-transparent text-indigo-200 hover:text-white hover:border-indigo-300' }} text-sm font-medium leading-5">
                                Użytkownicy
                            </a>
                        </div>
                    </div>

                    <div class="hidden sm:flex sm:items-center sm:ml-6">
                        <!-- Front Page Link -->
                        <a href="{{ route('home') }}" class="ml-3 text-indigo-200 hover:text-white text-sm">
                            <i class="fas fa-home mr-1"></i> Strona główna
                        </a>

                        <!-- Settings Dropdown -->
                        <div class="ml-3 relative">
                            <div>
                                <button id="user-menu-button" class="flex items-center text-sm font-medium text-white hover:text-indigo-100 focus:outline-none transition duration-150 ease-in-out">
                                    <div>{{ Auth::user()->name }}</div>
                                    <div class="ml-1">
                                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </button>
                            </div>

                            <div id="user-dropdown" class="hidden absolute right-0 z-10 mt-2 w-48 origin-top-right rounded-md bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none" role="menu" aria-orientation="vertical" aria-labelledby="user-menu-button" tabindex="-1">
                                <!-- Profile -->
                                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">
                                    {{ __('Profil') }}
                                </a>

                                <!-- Authentication -->
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <a href="{{ route('logout') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem" onclick="event.preventDefault(); this.closest('form').submit();">
                                        {{ __('Wyloguj') }}
                                    </a>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Hamburger -->
                    <div class="-mr-2 flex items-center sm:hidden">
                        <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-indigo-200 hover:text-white hover:bg-indigo-700 focus:outline-none focus:bg-indigo-700 focus:text-white transition duration-150 ease-in-out">
                            <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Responsive Navigation Menu -->
            <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
                <div class="pt-2 pb-3 space-y-1">
                    <a href="{{ route('admin.products.index') }}" class="block pl-3 pr-4 py-2 border-l-4 {{ str_starts_with(request()->route()->getName(), 'admin.products') ? 'border-white text-white bg-indigo-700' : 'border-transparent text-indigo-200 hover:text-white hover:bg-indigo-700 hover:border-indigo-300' }} text-base font-medium">
                        Produkty
                    </a>
                    <a href="{{ route('admin.categories.index') }}" class="block pl-3 pr-4 py-2 border-l-4 {{ str_starts_with(request()->route()->getName(), 'admin.categories') ? 'border-white text-white bg-indigo-700' : 'border-transparent text-indigo-200 hover:text-white hover:bg-indigo-700 hover:border-indigo-300' }} text-base font-medium">
                        Kategorie
                    </a>
                    <a href="{{ route('admin.brands.index') }}" class="block pl-3 pr-4 py-2 border-l-4 {{ str_starts_with(request()->route()->getName(), 'admin.brands') ? 'border-white text-white bg-indigo-700' : 'border-transparent text-indigo-200 hover:text-white hover:bg-indigo-700 hover:border-indigo-300' }} text-base font-medium">
                        Marki
                    </a>
                    <a href="{{ route('admin.promotions.index') }}" class="block pl-3 pr-4 py-2 border-l-4 {{ str_starts_with(request()->route()->getName(), 'admin.promotions') ? 'border-white text-white bg-indigo-700' : 'border-transparent text-indigo-200 hover:text-white hover:bg-indigo-700 hover:border-indigo-300' }} text-base font-medium">
                        Promocje
                    </a>
                    <a href="{{ route('admin.reviews.index') }}" class="block pl-3 pr-4 py-2 border-l-4 {{ str_starts_with(request()->route()->getName(), 'admin.reviews') ? 'border-white text-white bg-indigo-700' : 'border-transparent text-indigo-200 hover:text-white hover:bg-indigo-700 hover:border-indigo-300' }} text-base font-medium">
                        Recenzje
                    </a>
                    <a href="{{ route('admin.orders.index') }}" class="block pl-3 pr-4 py-2 border-l-4 {{ str_starts_with(request()->route()->getName(), 'admin.orders') ? 'border-white text-white bg-indigo-700' : 'border-transparent text-indigo-200 hover:text-white hover:bg-indigo-700 hover:border-indigo-300' }} text-base font-medium">
                        Zamówienia
                    </a>
                    <a href="{{ route('admin.payments.index') }}" class="block pl-3 pr-4 py-2 border-l-4 {{ str_starts_with(request()->route()->getName(), 'admin.payments') ? 'border-white text-white bg-indigo-700' : 'border-transparent text-indigo-200 hover:text-white hover:bg-indigo-700 hover:border-indigo-300' }} text-base font-medium">
                        Płatności
                    </a>
                    <a href="{{ route('admin.contact.index') }}" class="block pl-3 pr-4 py-2 border-l-4 {{ str_starts_with(request()->route()->getName(), 'admin.contact') ? 'border-white text-white bg-indigo-700' : 'border-transparent text-indigo-200 hover:text-white hover:bg-indigo-700 hover:border-indigo-300' }} text-base font-medium">
                        Kontakt
                    </a>
                    <a href="{{ route('admin.users.index') }}" class="block pl-3 pr-4 py-2 border-l-4 {{ str_starts_with(request()->route()->getName(), 'admin.users') ? 'border-white text-white bg-indigo-700' : 'border-transparent text-indigo-200 hover:text-white hover:bg-indigo-700 hover:border-indigo-300' }} text-base font-medium">
                        Użytkownicy
                    </a>
                    <a href="{{ route('home') }}" class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-indigo-200 hover:text-white hover:bg-indigo-700 hover:border-indigo-300 text-base font-medium">
                        Strona główna
                    </a>
                </div>

                <!-- Responsive Settings Options -->
                <div class="pt-4 pb-1 border-t border-indigo-700">
                    <div class="px-4">
                        <div class="font-medium text-base text-white">{{ Auth::user()->name }}</div>
                        <div class="font-medium text-sm text-indigo-200">{{ Auth::user()->email }}</div>
                    </div>

                    <div class="mt-3 space-y-1">
                        <a href="{{ route('profile.edit') }}" class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-indigo-200 hover:text-white hover:bg-indigo-700 hover:border-indigo-300 text-base font-medium">
                            {{ __('Profil') }}
                        </a>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <a href="{{ route('logout') }}" class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-indigo-200 hover:text-white hover:bg-indigo-700 hover:border-indigo-300 text-base font-medium" onclick="event.preventDefault(); this.closest('form').submit();">
                                {{ __('Wyloguj') }}
                            </a>
                        </form>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page Heading -->
        @if (isset($header))
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endif

        <!-- Page Content -->
        <main>
            <div class="py-6">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    @if (session('success'))
                        <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded" role="alert">
                            <strong class="font-bold">Sukces!</strong>
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif
                    
                    @if (session('error'))
                        <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded" role="alert">
                            <strong class="font-bold">Błąd!</strong>
                            <span class="block sm:inline">{{ session('error') }}</span>
                        </div>
                    @endif

                    @yield('content')
                </div>
            </div>
        </main>
    </div>

    <script>
        // Toggle user dropdown
        document.getElementById('user-menu-button')?.addEventListener('click', function() {
            document.getElementById('user-dropdown').classList.toggle('hidden');
        });
        
        // Close dropdowns when clicking outside
        window.addEventListener('click', function(e) {
            if (!document.getElementById('user-menu-button')?.contains(e.target)) {
                document.getElementById('user-dropdown')?.classList.add('hidden');
            }
        });
    </script>
    
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    
    @stack('scripts')
</body>
</html> 