@extends('layouts.app')

@section('title', 'Resetowanie hasła')

@section('content')
<div class="max-w-7xl mx-auto py-12 sm:px-6 lg:px-8">
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg max-w-md mx-auto">
        <div class="p-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">{{ __('Resetowanie hasła') }}</h2>
            
            @if (session('status'))
                <div class="mb-4 bg-green-50 p-4 rounded-md">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-800">{{ session('status') }}</p>
                        </div>
                    </div>
                </div>
            @endif
            
            <div class="mb-4 text-sm text-gray-600">
                {{ __('Zapomniałeś hasła? Podaj swój adres e-mail, a wyślemy Ci link do zresetowania hasła.') }}
            </div>

            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Adres e-mail') }}</label>
                    <input id="email" type="email" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('email') border-red-500 @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                    @error('email')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-end">
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white py-2 px-4 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        {{ __('Wyślij link resetujący hasło') }}
                    </button>
                </div>
                
                <div class="mt-6 text-center">
                    <a href="{{ route('login') }}" class="text-sm text-indigo-600 hover:text-indigo-800">
                        {{ __('Powrót do logowania') }}
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
