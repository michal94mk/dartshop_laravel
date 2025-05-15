@extends('layouts.app')

@section('title', 'O nas')

@section('content')
    <!-- Hero Section - Enhanced with better overlay -->
    <div class="relative bg-indigo-800">
        <div class="absolute inset-0">
            <img src="https://images.unsplash.com/photo-1637627328577-651925a992cc?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1920&q=80" alt="Dart background" class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-r from-indigo-900 to-indigo-700 mix-blend-multiply opacity-90" aria-hidden="true"></div>
        </div>
        <div class="relative max-w-7xl mx-auto py-28 px-4 sm:py-40 sm:px-6 lg:px-8 text-center">
            <h1 class="text-4xl font-extrabold tracking-tight text-white sm:text-5xl lg:text-6xl">O nas</h1>
            <p class="mt-6 text-xl text-indigo-100 max-w-3xl mx-auto">
                Poznaj naszą historię, misję i wartości, które przyświecają nam w tworzeniu najlepszego sklepu z akcesoriami dart.
            </p>
        </div>
    </div>

    <!-- About Content - Redesigned with cards -->
    <div class="bg-white">
        <div class="max-w-7xl mx-auto py-16 px-4 sm:py-24 sm:px-6 lg:px-8">
            @if($aboutPages->count() > 0)
                <div class="grid grid-cols-1 gap-16">
                    @foreach($aboutPages as $aboutPage)
                        <div class="opacity-0 transform translate-y-5 transition-all duration-500 ease-out about-section rounded-lg overflow-hidden shadow-lg bg-white p-6 border border-gray-100" id="about-section-{{ $loop->index }}">
                            <div class="flex flex-col md:flex-row items-start md:items-center mb-6">
                                <div class="flex-shrink-0 mr-4 text-indigo-600 transform transition duration-300 group-hover:scale-125 group-hover:text-indigo-500">
                                    @switch($loop->index % 5)
                                        @case(0)
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                            </svg>
                                            @break
                                        @case(1)
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                            </svg>
                                            @break
                                        @case(2)
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                            </svg>
                                            @break
                                        @case(3)
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            @break
                                        @default
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                            </svg>
                                    @endswitch
                                </div>
                                <h2 class="text-3xl font-extrabold text-gray-900">{{ $aboutPage->title }}</h2>
                            </div>
                            <div class="prose prose-indigo prose-lg max-w-none">
                                {!! $aboutPage->content !!}
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center p-12 rounded-lg shadow-md bg-gray-50">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-indigo-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h2 class="text-3xl font-extrabold text-gray-900 mb-4">Wkrótce więcej informacji</h2>
                    <p class="text-lg text-gray-500">Jesteśmy w trakcie aktualizacji strony.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Call to Action Section - Enhanced with gradient -->
    <div class="bg-gradient-to-r from-indigo-50 to-purple-50">
        <div class="max-w-7xl mx-auto py-16 px-4 sm:py-24 sm:px-6 lg:flex lg:items-center lg:justify-between">
            <div>
                <h2 class="text-3xl font-extrabold tracking-tight text-gray-900 sm:text-4xl">
                    <span class="block">Gotowy do rozpoczęcia zakupów?</span>
                    <span class="block text-indigo-600">Sprawdź naszą bogatą ofertę już dziś.</span>
                </h2>
                <p class="mt-4 text-lg text-gray-500">
                    Tysiące produktów najwyższej jakości czeka na Ciebie w naszym sklepie. 
                    Skorzystaj z wiedzy naszych ekspertów.
                </p>
            </div>
            <div class="mt-8 flex flex-col sm:flex-row lg:mt-0 lg:flex-shrink-0 gap-3">
                <div class="inline-flex rounded-md shadow">
                    <a href="{{ route('frontend.categories.index') }}" class="inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                        Zobacz produkty
                    </a>
                </div>
                <div class="inline-flex rounded-md shadow">
                    <a href="{{ route('frontend.contact') }}" class="inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-indigo-600 bg-white hover:bg-indigo-50 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        Skontaktuj się z nami
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sections = document.querySelectorAll('.about-section');
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    setTimeout(() => {
                        entry.target.classList.remove('opacity-0', 'translate-y-5');
                        entry.target.classList.add('opacity-100', 'translate-y-0');
                    }, 100);
                }
            });
        }, {
            threshold: 0.1
        });
        
        sections.forEach(section => {
            observer.observe(section);
        });
    });
</script>
@endsection 