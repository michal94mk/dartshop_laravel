@extends('layouts.app')

@section('title', 'O nas')

@section('content')
    <!-- Nagłówek strony -->
    <div class="bg-gradient-to-r from-indigo-800 to-indigo-700 py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-4xl font-extrabold tracking-tight text-white sm:text-5xl">O nas</h1>
            <p class="mt-4 text-xl font-light text-indigo-100 max-w-3xl mx-auto">
                Poznaj naszą historię, misję i wartości
            </p>
        </div>
    </div>

    <!-- Treść strony -->
    <div class="bg-white py-12">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            @if(isset($aboutPages) && $aboutPages->count() > 0)
                <div class="bg-white p-8 rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300">
                    @foreach($aboutPages as $aboutPage)
                        <div class="mb-16 last:mb-0" id="about-section-{{ $loop->index }}">
                            <h2 class="text-3xl font-bold text-indigo-900 mb-6 border-b border-indigo-100 pb-3 text-center">{{ $aboutPage->title }}</h2>
                            <div class="prose prose-lg prose-indigo max-w-none">
                                {!! $aboutPage->content !!}
                            </div>
                        </div>
                        @if(!$loop->last)
                            <div class="my-8 border-b border-gray-100"></div>
                        @endif
                    @endforeach
                </div>
            @else
                <div class="text-center p-12 bg-gray-50 rounded-lg border border-gray-100">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">Wkrótce więcej informacji</h2>
                    <p class="text-gray-600">Jesteśmy w trakcie aktualizacji strony.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Wezwanie do działania -->
    <div class="bg-gray-50 py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl font-bold text-gray-900">Gotowy do rozpoczęcia zakupów?</h2>
            <p class="mt-4 text-xl text-gray-600 max-w-2xl mx-auto">
                Tysiące produktów najwyższej jakości czeka na Ciebie w naszym sklepie.
            </p>
            <div class="mt-8 flex flex-wrap justify-center gap-4">
                <a href="{{ route('frontend.categories.index') }}" class="inline-block bg-indigo-600 text-white px-8 py-4 rounded-md font-medium hover:bg-indigo-700 transition-colors duration-300">
                    Zobacz produkty
                </a>
                <a href="{{ route('frontend.contact') }}" class="inline-block bg-white text-indigo-600 px-8 py-4 rounded-md font-medium border border-indigo-600 hover:bg-indigo-50 transition-colors duration-300">
                    Skontaktuj się z nami
                </a>
            </div>
        </div>
    </div>
@endsection 