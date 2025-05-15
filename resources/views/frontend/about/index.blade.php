@extends('layouts.app')

@section('title', 'O nas')

@section('content')
    <!-- Hero Section -->
    <div class="relative bg-indigo-700">
        <div class="absolute inset-0">
            <img src="https://images.unsplash.com/photo-1637627328577-651925a992cc?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1024&q=80" alt="Dart background" class="w-full h-full object-cover opacity-30">
            <div class="absolute inset-0 bg-indigo-700 mix-blend-multiply" aria-hidden="true"></div>
        </div>
        <div class="relative max-w-7xl mx-auto py-24 px-4 sm:py-32 sm:px-6 lg:px-8">
            <h1 class="text-4xl font-extrabold tracking-tight text-white sm:text-5xl lg:text-6xl">O nas</h1>
            <p class="mt-6 text-xl text-indigo-100 max-w-3xl">
                Poznaj naszą historię, misję i wartości, które przyświecają nam w tworzeniu najlepszego sklepu z akcesoriami dart.
            </p>
        </div>
    </div>

    <!-- About Content -->
    <div class="bg-white">
        <div class="max-w-7xl mx-auto py-16 px-4 sm:py-24 sm:px-6 lg:px-8">
            @if($aboutPages->count() > 0)
                @foreach($aboutPages as $aboutPage)
                    <div class="mb-16 last:mb-0">
                        <h2 class="text-3xl font-extrabold text-gray-900 mb-6">{{ $aboutPage->title }}</h2>
                        <div class="prose prose-indigo prose-lg max-w-none">
                            {!! $aboutPage->content !!}
                        </div>
                    </div>
                @endforeach
            @else
                <div class="text-center">
                    <h2 class="text-3xl font-extrabold text-gray-900 mb-4">Wkrótce więcej informacji</h2>
                    <p class="text-lg text-gray-500">Jesteśmy w trakcie aktualizacji strony.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Call to Action Section -->
    <div class="bg-indigo-50">
        <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:py-16 lg:px-8 lg:flex lg:items-center lg:justify-between">
            <h2 class="text-3xl font-extrabold tracking-tight text-gray-900 sm:text-4xl">
                <span class="block">Gotowy do rozpoczęcia zakupów?</span>
                <span class="block text-indigo-600">Sprawdź naszą bogatą ofertę już dziś.</span>
            </h2>
            <div class="mt-8 flex lg:mt-0 lg:flex-shrink-0">
                <div class="inline-flex rounded-md shadow">
                    <a href="{{ route('frontend.categories.index') }}" class="inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                        Zobacz produkty
                    </a>
                </div>
                <div class="ml-3 inline-flex rounded-md shadow">
                    <a href="{{ route('frontend.contact') }}" class="inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-indigo-600 bg-white hover:bg-indigo-50">
                        Skontaktuj się z nami
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection 