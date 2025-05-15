@extends('layouts.app')

@section('title', 'Edycja recenzji')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Edycja recenzji</h1>
            <p class="mt-2 text-sm text-gray-700">Edytuj treść recenzji, zmień status lub ustal wyróżnienie.</p>
            <p class="mt-1 text-sm text-gray-500">
                Wyróżnione recenzje: <span class="font-medium {{ $featuredCount >= 5 ? 'text-red-600' : 'text-indigo-600' }}">{{ $featuredCount }}/5</span>
                @if($featuredCount >= 5 && !$review->is_featured)
                    <span class="ml-1 text-xs text-red-500">(osiągnięto limit)</span>
                @endif
            </p>
        </div>
        <a href="{{ route('admin.reviews.show', $review) }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            <svg class="-ml-1 mr-2 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
            </svg>
            Wróć do podglądu
        </a>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-green-100 border-l-4 border-green-500 text-green-700 p-4" role="alert">
            <p>{{ session('success') }}</p>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 bg-red-100 border-l-4 border-red-500 text-red-700 p-4" role="alert">
            <p>{{ session('error') }}</p>
        </div>
    @endif

    @if($errors->any())
        <div class="mb-6 bg-red-100 border-l-4 border-red-500 text-red-700 p-4" role="alert">
            <p class="font-bold">Wystąpiły błędy:</p>
            <ul class="mt-2 list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6 bg-gray-50">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Formularz edycji recenzji</h3>
            <p class="mt-1 max-w-2xl text-sm text-gray-500">ID recenzji: #{{ $review->id }}</p>
        </div>
        <div class="border-t border-gray-200 px-4 py-5 sm:p-6">
            <form action="{{ route('admin.reviews.update', $review) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                    <div class="sm:col-span-3">
                        <label for="product" class="block text-sm font-medium text-gray-700">Produkt</label>
                        <div class="mt-1">
                            <input type="text" id="product" value="{{ $review->product->name }}" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md bg-gray-50" disabled>
                        </div>
                    </div>

                    <div class="sm:col-span-3">
                        <label for="user" class="block text-sm font-medium text-gray-700">Użytkownik</label>
                        <div class="mt-1">
                            <input type="text" id="user" value="{{ $review->user->name }}" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md bg-gray-50" disabled>
                        </div>
                    </div>
                    
                    <div class="sm:col-span-6">
                        <label for="rating" class="block text-sm font-medium text-gray-700">Ocena</label>
                        <div class="mt-1">
                            <select id="rating" name="rating" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                <option value="1" {{ $review->rating == 1 ? 'selected' : '' }}>1 gwiazdka</option>
                                <option value="2" {{ $review->rating == 2 ? 'selected' : '' }}>2 gwiazdki</option>
                                <option value="3" {{ $review->rating == 3 ? 'selected' : '' }}>3 gwiazdki</option>
                                <option value="4" {{ $review->rating == 4 ? 'selected' : '' }}>4 gwiazdki</option>
                                <option value="5" {{ $review->rating == 5 ? 'selected' : '' }}>5 gwiazdek</option>
                            </select>
                        </div>
                    </div>

                    <div class="sm:col-span-6">
                        <label for="title" class="block text-sm font-medium text-gray-700">Tytuł</label>
                        <div class="mt-1">
                            <input type="text" name="title" id="title" value="{{ old('title', $review->title) }}" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                        </div>
                    </div>

                    <div class="sm:col-span-6">
                        <label for="content" class="block text-sm font-medium text-gray-700">Treść</label>
                        <div class="mt-1">
                            <textarea id="content" name="content" rows="5" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">{{ old('content', $review->content) }}</textarea>
                        </div>
                    </div>
                    
                    <div class="sm:col-span-3">
                        <div class="flex items-center">
                            <input id="is_approved" name="is_approved" type="checkbox" value="1" {{ $review->is_approved ? 'checked' : '' }} class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                            <label for="is_approved" class="ml-2 block text-sm text-gray-900">
                                Zatwierdzona
                            </label>
                        </div>
                        <p class="mt-1 text-sm text-gray-500">Zaznacz, aby recenzja była widoczna dla innych użytkowników.</p>
                    </div>
                    
                    <div class="sm:col-span-3">
                        <div class="flex items-center">
                            <input id="is_featured" name="is_featured" type="checkbox" value="1" {{ $review->is_featured ? 'checked' : '' }} class="h-4 w-4 text-amber-600 focus:ring-amber-500 border-gray-300 rounded">
                            <label for="is_featured" class="ml-2 block text-sm text-gray-900">
                                Wyróżniona
                            </label>
                        </div>
                        <p class="mt-1 text-sm text-gray-500">Zaznacz, aby recenzja była wyświetlana na stronie głównej.</p>
                    </div>
                </div>
                
                <div class="mt-6 flex justify-end space-x-3">
                    <a href="{{ route('admin.reviews.show', $review) }}" class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Anuluj
                    </a>
                    <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Zapisz zmiany
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection 