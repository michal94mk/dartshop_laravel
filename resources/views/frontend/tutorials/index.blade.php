@extends('layouts.app')

@section('title', 'Poradniki')

@section('content')
    <!-- Hero Section - Enhanced with gradient -->
    <div class="relative bg-indigo-800">
        <div class="absolute inset-0">
            <img src="https://images.unsplash.com/photo-1610464526017-5f0a2c4c0819?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1920&q=80" alt="Dart background" class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-r from-indigo-900 to-indigo-700 mix-blend-multiply opacity-90" aria-hidden="true"></div>
        </div>
        <div class="relative max-w-7xl mx-auto py-28 px-4 sm:py-40 sm:px-6 lg:px-8 text-center">
            <h1 class="text-4xl font-extrabold tracking-tight text-white sm:text-5xl lg:text-6xl">Poradniki</h1>
            <p class="mt-6 text-xl text-indigo-100 max-w-3xl mx-auto">
                Materiały edukacyjne, porady i wskazówki, które pomogą Ci poprawić umiejętności gry w darta i wybrać odpowiedni sprzęt.
            </p>
            <div class="mt-10 flex justify-center">
                <a href="#tutorials" class="inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-indigo-700 bg-white hover:bg-indigo-50 transition-colors shadow-md">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                    Przeglądaj poradniki
                </a>
            </div>
        </div>
    </div>

    <!-- Filters Section - Enhanced with better styling -->
    <div class="bg-white py-8">
        <div class="max-w-7xl mx-auto pt-8 px-4 sm:px-6 lg:px-8">
            <div class="p-6 mb-8 shadow-sm border border-indigo-100 rounded-xl bg-gradient-to-r from-indigo-50/50 to-purple-50/50">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center space-y-4 md:space-y-0 mb-6">
                    <div class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-600 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                        </svg>
                        <h2 class="text-2xl font-bold text-gray-900">Filtruj poradniki</h2>
                    </div>
                    
                    <div class="flex items-center text-sm text-gray-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Wybierz filtry, aby znaleźć interesujące Cię poradniki
                    </div>
                </div>
                
                <form method="GET" action="{{ route('frontend.tutorials') }}" class="flex flex-wrap gap-4">
                    @if($categories->count() > 0)
                    <div class="w-full md:w-auto flex-1">
                        <label for="category" class="block text-sm font-medium text-gray-700 mb-1">Kategoria</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                </svg>
                            </div>
                            <select id="category" name="category" class="mt-1 block w-full pl-10 py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                <option value="">Wszystkie kategorie</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>{{ $category }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    @endif
                    
                    <div class="w-full md:w-auto flex-1">
                        <label for="difficulty" class="block text-sm font-medium text-gray-700 mb-1">Poziom trudności</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                            </div>
                            <select id="difficulty" name="difficulty" class="mt-1 block w-full pl-10 py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                <option value="">Wszystkie poziomy</option>
                                <option value="beginner" {{ request('difficulty') == 'beginner' ? 'selected' : '' }}>Początkujący</option>
                                <option value="intermediate" {{ request('difficulty') == 'intermediate' ? 'selected' : '' }}>Średniozaawansowany</option>
                                <option value="advanced" {{ request('difficulty') == 'advanced' ? 'selected' : '' }}>Zaawansowany</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="w-full md:w-auto self-end">
                        <button type="submit" class="w-full py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition shadow-md flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            Filtruj
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Tutorials Grid - Enhanced with better cards -->
    <div class="bg-white" id="tutorials">
        <div class="max-w-7xl mx-auto py-12 px-4 sm:py-16 sm:px-6 lg:px-8">
            @if($tutorials->count() > 0)
                <div class="grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-3">
                    @foreach($tutorials as $tutorial)
                        <div class="flex flex-col rounded-lg shadow-lg overflow-hidden bg-white transform transition duration-300 ease-in-out hover:-translate-y-2 hover:shadow-xl">
                            <div class="flex-shrink-0 overflow-hidden h-48">
                                <img class="w-full h-full object-cover transition-transform duration-500 hover:scale-105" src="{{ $tutorial->thumbnailImageUrl }}" alt="{{ $tutorial->title }}">
                            </div>
                            <div class="flex-1 bg-white p-6 flex flex-col justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-2 mb-3 flex-wrap gap-2">
                                        <span class="inline-flex items-center px-3 py-0.5 rounded-full text-sm font-medium transition transform hover:-translate-y-0.5 duration-200
                                            {{ $tutorial->difficulty === 'beginner' ? 'bg-green-100 text-green-800' : 
                                               ($tutorial->difficulty === 'intermediate' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                            @if($tutorial->difficulty === 'beginner')
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                                                </svg>
                                                Początkujący
                                            @elseif($tutorial->difficulty === 'intermediate')
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                                </svg>
                                                Średniozaawansowany
                                            @else
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                                                </svg>
                                                Zaawansowany
                                            @endif
                                        </span>
                                        @if($tutorial->category)
                                            <span class="inline-flex items-center px-3 py-0.5 rounded-full text-sm font-medium bg-indigo-100 text-indigo-800 transition transform hover:-translate-y-0.5 duration-200">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                                </svg>
                                                {{ $tutorial->category }}
                                            </span>
                                        @endif
                                    </div>
                                    <a href="{{ route('frontend.tutorials.show', $tutorial) }}" class="block mt-2 group">
                                        <p class="text-xl font-semibold text-gray-900 group-hover:text-indigo-600 transition-colors">{{ $tutorial->title }}</p>
                                        <p class="mt-3 text-base text-gray-500">{{ $tutorial->excerpt }}</p>
                                    </a>
                                </div>
                                <div class="mt-6 flex items-center">
                                    <div class="flex-shrink-0 text-gray-400">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <div class="ml-2 text-sm text-gray-500">
                                        <time datetime="{{ $tutorial->published_at->format('Y-m-d') }}">{{ $tutorial->published_at->format('d.m.Y') }}</time>
                                    </div>
                                    <div class="ml-auto">
                                        <a href="{{ route('frontend.tutorials.show', $tutorial) }}" class="text-base font-medium text-indigo-600 hover:text-indigo-500 inline-flex items-center group">
                                            Czytaj więcej 
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-1 transform group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                            </svg>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <div class="mt-12">
                    {{ $tutorials->links() }}
                </div>
            @else
                <div class="text-center py-16 bg-gray-50 rounded-lg shadow-sm border border-gray-100">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-indigo-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900">Brak poradników</h3>
                    <p class="mt-1 text-gray-500">Wkrótce dodamy nowe materiały. Sprawdź ponownie za jakiś czas.</p>
                </div>
            @endif
        </div>
    </div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Smooth scroll to tutorials section
        document.querySelector('a[href="#tutorials"]').addEventListener('click', function(e) {
            e.preventDefault();
            document.querySelector('#tutorials').scrollIntoView({
                behavior: 'smooth'
            });
        });
    });
</script>
@endsection 