@extends('layouts.app')

@section('title', $tutorial->meta_title ?? $tutorial->title)

@if($tutorial->meta_description)
@section('meta_description', $tutorial->meta_description)
@endif

@section('content')
    <!-- Tutorial Header -->
    <div class="relative">
        <div class="w-full h-64 sm:h-96 bg-indigo-700">
            @if($tutorial->featured_image)
                <img src="{{ $tutorial->featuredImageUrl }}" alt="{{ $tutorial->title }}" class="w-full h-full object-cover object-center opacity-70">
            @endif
            <div class="absolute inset-0 bg-indigo-700 mix-blend-multiply" aria-hidden="true"></div>
        </div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="relative -mt-12 sm:-mt-16 lg:-mt-24">
                <div class="bg-white rounded-lg shadow-lg px-6 py-8 md:px-10 md:py-12">
                    <div class="flex flex-wrap items-center mb-6 gap-2">
                        <span class="inline-flex items-center px-3 py-0.5 rounded-full text-sm font-medium {{ $tutorial->difficulty === 'beginner' ? 'bg-green-100 text-green-800' : ($tutorial->difficulty === 'intermediate' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                            {{ $tutorial->difficulty === 'beginner' ? 'Początkujący' : ($tutorial->difficulty === 'intermediate' ? 'Średniozaawansowany' : 'Zaawansowany') }}
                        </span>
                        @if($tutorial->category)
                            <span class="inline-flex items-center px-3 py-0.5 rounded-full text-sm font-medium bg-indigo-100 text-indigo-800">
                                {{ $tutorial->category }}
                            </span>
                        @endif
                        <span class="inline-flex items-center text-sm text-gray-500">
                            <time datetime="{{ $tutorial->published_at->format('Y-m-d') }}">{{ $tutorial->published_at->format('d.m.Y') }}</time>
                        </span>
                    </div>
                    <h1 class="text-3xl font-extrabold text-gray-900 sm:text-4xl lg:text-5xl">{{ $tutorial->title }}</h1>
                    @if($tutorial->excerpt)
                        <p class="mt-4 text-xl text-gray-500">{{ $tutorial->excerpt }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Tutorial Content -->
    <div class="bg-white">
        <div class="max-w-7xl mx-auto py-12 px-4 sm:py-16 sm:px-6 lg:px-8">
            <div class="prose prose-indigo prose-lg max-w-none">
                {!! $tutorial->content !!}
            </div>
        </div>
    </div>

    <!-- Related Tutorials -->
    @if($relatedTutorials->count() > 0)
    <div class="bg-gray-50">
        <div class="max-w-7xl mx-auto py-12 px-4 sm:py-16 sm:px-6 lg:px-8">
            <h2 class="text-2xl font-extrabold text-gray-900 mb-8">Podobne poradniki</h2>
            
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($relatedTutorials as $relatedTutorial)
                    <div class="flex flex-col rounded-lg shadow-lg overflow-hidden transition-all duration-200 transform hover:-translate-y-1 hover:shadow-xl bg-white">
                        <div class="flex-shrink-0">
                            <img class="h-48 w-full object-cover" src="{{ $relatedTutorial->thumbnailImageUrl }}" alt="{{ $relatedTutorial->title }}">
                        </div>
                        <div class="flex-1 p-6 flex flex-col justify-between">
                            <div class="flex-1">
                                <a href="{{ route('frontend.tutorials.show', $relatedTutorial) }}" class="block">
                                    <p class="text-xl font-semibold text-gray-900">{{ $relatedTutorial->title }}</p>
                                    <p class="mt-3 text-base text-gray-500">{{ Str::limit($relatedTutorial->excerpt, 100) }}</p>
                                </a>
                            </div>
                            <div class="mt-6">
                                <a href="{{ route('frontend.tutorials.show', $relatedTutorial) }}" class="text-base font-medium text-indigo-600 hover:text-indigo-500">
                                    Czytaj więcej <span aria-hidden="true">→</span>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- Call to Action -->
    <div class="bg-indigo-700">
        <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:py-16 lg:px-8 lg:flex lg:items-center lg:justify-between">
            <h2 class="text-3xl font-extrabold tracking-tight text-white sm:text-4xl">
                <span class="block">Chcesz ulepszył swój sprzęt?</span>
                <span class="block text-indigo-200">Sprawdź nasze produkty już teraz.</span>
            </h2>
            <div class="mt-8 flex lg:mt-0 lg:flex-shrink-0">
                <div class="inline-flex rounded-md shadow">
                    <a href="{{ route('frontend.categories.index') }}" class="inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-indigo-600 bg-white hover:bg-indigo-50">
                        Zobacz ofertę
                    </a>
                </div>
                <div class="ml-3 inline-flex rounded-md shadow">
                    <a href="{{ route('frontend.tutorials') }}" class="inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                        Wszystkie poradniki
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection 