@extends('layouts.admin-tailwind')

@section('title', isset($category) ? 'Edytuj kategorię: ' . $category->name : 'Dodaj nową kategorię')

@section('content')
<div class="bg-white shadow rounded-lg overflow-hidden">
    <div class="p-6 bg-white border-b border-gray-200">
        <div class="mb-6">
            <h2 class="text-2xl font-semibold text-gray-800">
                {{ isset($category) ? 'Edytuj kategorię: ' . $category->name : 'Dodaj nową kategorię' }}
            </h2>
        </div>

        <form action="{{ isset($category) ? route('admin.categories.update', $category->id) : route('admin.categories.store') }}" 
              method="POST" 
              class="space-y-6">
            @csrf
            @if(isset($category))
                @method('PUT')
            @endif

            <!-- Name -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Nazwa kategorii</label>
                <input type="text" name="name" id="name" value="{{ old('name', $category->name ?? '') }}" 
                       class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Description (optional) -->
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700">Opis (opcjonalnie)</label>
                <textarea name="description" id="description" rows="3" 
                          class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">{{ old('description', $category->description ?? '') }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-end pt-5">
                <a href="{{ route('admin.categories.index') }}" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Anuluj
                </a>
                <button type="submit" class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    {{ isset($category) ? 'Zapisz zmiany' : 'Dodaj kategorię' }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection 