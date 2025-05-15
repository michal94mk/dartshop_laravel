@extends('layouts.app')

@section('title', 'Poradniki')

@section('content')
    <!-- Hero Section -->
    <div class="relative bg-indigo-700">
        <div class="absolute inset-0">
            <img src="https://images.unsplash.com/photo-1610464526017-5f0a2c4c0819?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1024&q=80" alt="Dart background" class="w-full h-full object-cover opacity-30">
            <div class="absolute inset-0 bg-indigo-700 mix-blend-multiply" aria-hidden="true"></div>
        </div>
        <div class="relative max-w-7xl mx-auto py-24 px-4 sm:py-32 sm:px-6 lg:px-8">
            <h1 class="text-4xl font-extrabold tracking-tight text-white sm:text-5xl lg:text-6xl">Poradniki</h1>
            <p class="mt-6 text-xl text-indigo-100 max-w-3xl">
                Materiały edukacyjne, porady i wskazówki, które pomogą Ci poprawić umiejętności gry w darta i wybrać odpowiedni sprzęt.
            </p>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="bg-white">
        <div class="max-w-7xl mx-auto pt-10 px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center space-y-4 md:space-y-0">
                <h2 class="text-2xl font-bold text-gray-900">Filtruj poradniki</h2>
                
                <form method="GET" action="{{ route('frontend.tutorials') }}" class="flex flex-wrap gap-4">
                    @if($categories->count() > 0)
                    <div>
                        <label for="category" class="block text-sm font-medium text-gray-700">Kategoria</label>
                        <select id="category" name="category" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <option value="">Wszystkie kategorie</option>
                            @foreach($categories as $category)
                                <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>{{ $category }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                    
                    <div>
                        <label for="difficulty" class="block text-sm font-medium text-gray-700">Poziom trudności</label>
                        <select id="difficulty" name="difficulty" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <option value="">Wszystkie poziomy</option>
                            <option value="beginner" {{ request('difficulty') == 'beginner' ? 'selected' : '' }}>Początkujący</option>
                            <option value="intermediate" {{ request('difficulty') == 'intermediate' ? 'selected' : '' }}>Średniozaawansowany</option>
                            <option value="advanced" {{ request('difficulty') == 'advanced' ? 'selected' : '' }}>Zaawansowany</option>
                        </select>
                    </div>
                    
                    <div class="self-end">
                        <button type="submit" class="py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Filtruj
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Tutorials Grid -->
    <div class="bg-white">
        <div class="max-w-7xl mx-auto py-12 px-4 sm:py-16 sm:px-6 lg:px-8">
            @if($tutorials->count() > 0)
                <div class="grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-3">
                    @foreach($tutorials as $tutorial)
                        <div class="flex flex-col rounded-lg shadow-lg overflow-hidden transition-all duration-200 transform hover:-translate-y-1 hover:shadow-xl">
                            <div class="flex-shrink-0">
                                <img class="h-48 w-full object-cover" src="{{ $tutorial->thumbnailImageUrl }}" alt="{{ $tutorial->title }}">
                            </div>
                            <div class="flex-1 bg-white p-6 flex flex-col justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-2">
                                        <span class="inline-flex items-center px-3 py-0.5 rounded-full text-sm font-medium {{ $tutorial->difficulty === 'beginner' ? 'bg-green-100 text-green-800' : ($tutorial->difficulty === 'intermediate' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                            {{ $tutorial->difficulty === 'beginner' ? 'Początkujący' : ($tutorial->difficulty === 'intermediate' ? 'Średniozaawansowany' : 'Zaawansowany') }}
                                        </span>
                                        @if($tutorial->category)
                                            <span class="inline-flex items-center px-3 py-0.5 rounded-full text-sm font-medium bg-indigo-100 text-indigo-800">
                                                {{ $tutorial->category }}
                                            </span>
                                        @endif
                                    </div>
                                    <a href="{{ route('frontend.tutorials.show', $tutorial) }}" class="block mt-2">
                                        <p class="text-xl font-semibold text-gray-900">{{ $tutorial->title }}</p>
                                        <p class="mt-3 text-base text-gray-500">{{ $tutorial->excerpt }}</p>
                                    </a>
                                </div>
                                <div class="mt-6">
                                    <a href="{{ route('frontend.tutorials.show', $tutorial) }}" class="text-base font-medium text-indigo-600 hover:text-indigo-500">
                                        Czytaj więcej <span aria-hidden="true">→</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <div class="mt-12">
                    {{ $tutorials->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <h3 class="text-lg font-medium text-gray-900">Brak poradników</h3>
                    <p class="mt-1 text-gray-500">Wkrótce dodamy nowe materiały. Sprawdź ponownie za jakiś czas.</p>
                </div>
            @endif
        </div>
    </div>
@endsection 