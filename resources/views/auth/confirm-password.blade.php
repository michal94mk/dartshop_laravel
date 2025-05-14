@extends('layouts.app')

@section('title', 'Potwierdź hasło')

@section('content')
<div class="max-w-7xl mx-auto py-12 sm:px-6 lg:px-8">
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg max-w-md mx-auto">
        <div class="p-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">{{ __('Potwierdź hasło') }}</h2>
            
            <div class="mb-4 text-sm text-gray-600">
                {{ __('To jest bezpieczny obszar aplikacji. Proszę potwierdź swoje hasło, aby kontynuować.') }}
            </div>

            <form method="POST" action="{{ route('password.confirm') }}">
                @csrf

                <div class="mb-4">
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Hasło') }}</label>
                    <input id="password" type="password" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('password') border-red-500 @enderror" name="password" required autocomplete="current-password">
                    @error('password')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-end">
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white py-2 px-4 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        {{ __('Potwierdź') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
