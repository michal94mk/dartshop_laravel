@extends('layouts.app')

@section('title', 'Dodaj recenzję')

@section('content')
<div class="bg-white">
    <div class="max-w-2xl mx-auto py-16 px-4 sm:py-24 sm:px-6 lg:max-w-7xl lg:px-8">
        <h1 class="text-3xl font-extrabold tracking-tight text-gray-900">Dodaj recenzję dla: {{ $product->name }}</h1>
        
        @if (session('error'))
            <div class="mt-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4" role="alert">
                <p>{{ session('error') }}</p>
            </div>
        @endif
        
        @if ($existingReview)
            <div class="mt-4 bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4" role="alert">
                <p class="font-bold">Uwaga!</p>
                <p>Już dodałeś recenzję dla tego produktu.</p>
            </div>
        @else
            <div class="mt-8">
                <form action="{{ route('frontend.reviews.store', $product->id) }}" method="POST" class="space-y-6">
                    @csrf
                    
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700">Tytuł recenzji</label>
                        <div class="mt-1">
                            <input type="text" name="title" id="title" value="{{ old('title') }}" required class="block w-full shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm border-gray-300 rounded-md">
                        </div>
                        @error('title')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="rating" class="block text-sm font-medium text-gray-700">Ocena</label>
                        <div class="mt-1 flex items-center space-x-2">
                            <div class="flex items-center">
                                <input type="radio" id="rating1" name="rating" value="1" {{ old('rating') == 1 ? 'checked' : '' }} class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300">
                                <label for="rating1" class="ml-2 text-sm text-gray-700">1</label>
                            </div>
                            <div class="flex items-center">
                                <input type="radio" id="rating2" name="rating" value="2" {{ old('rating') == 2 ? 'checked' : '' }} class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300">
                                <label for="rating2" class="ml-2 text-sm text-gray-700">2</label>
                            </div>
                            <div class="flex items-center">
                                <input type="radio" id="rating3" name="rating" value="3" {{ old('rating') == 3 ? 'checked' : '' }} class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300">
                                <label for="rating3" class="ml-2 text-sm text-gray-700">3</label>
                            </div>
                            <div class="flex items-center">
                                <input type="radio" id="rating4" name="rating" value="4" {{ old('rating') == 4 ? 'checked' : '' }} class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300">
                                <label for="rating4" class="ml-2 text-sm text-gray-700">4</label>
                            </div>
                            <div class="flex items-center">
                                <input type="radio" id="rating5" name="rating" value="5" {{ old('rating') == 5 || old('rating') === null ? 'checked' : '' }} class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300">
                                <label for="rating5" class="ml-2 text-sm text-gray-700">5</label>
                            </div>
                        </div>
                        @error('rating')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="content" class="block text-sm font-medium text-gray-700">Treść recenzji</label>
                        <div class="mt-1">
                            <textarea id="content" name="content" rows="5" required class="block w-full shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm border-gray-300 rounded-md">{{ old('content') }}</textarea>
                        </div>
                        <p class="mt-2 text-xs text-gray-500">Minimum 10 znaków.</p>
                        @error('content')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="mt-6 flex justify-between items-center">
                        <a href="{{ route('frontend.products.show', $product->id) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">Powrót do produktu</a>
                        <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Dodaj recenzję
                        </button>
                    </div>
                </form>
            </div>
        @endif
    </div>
</div>
@endsection 