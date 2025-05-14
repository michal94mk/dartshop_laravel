@extends('layouts.app')

@section('title', 'Logowanie')

@section('content')
<div class="max-w-7xl mx-auto py-12 sm:px-6 lg:px-8">
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg max-w-md mx-auto">
        <div class="p-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">{{ __('Logowanie') }}</h2>
            
            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Adres e-mail') }}</label>
                    <input id="email" type="email" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('email') border-red-500 @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                    @error('email')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Hasło') }}</label>
                    <input id="password" type="password" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('password') border-red-500 @enderror" name="password" required autocomplete="current-password">
                    @error('password')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <div class="flex items-center">
                        <input class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                        <label class="ml-2 text-sm text-gray-600" for="remember">
                            {{ __('Zapamiętaj mnie') }}
                        </label>
                    </div>
                </div>

                <div class="flex items-center justify-between mb-4">
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white py-2 px-4 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        {{ __('Zaloguj się') }}
                    </button>

                    @if (Route::has('password.request'))
                        <a class="text-sm text-indigo-600 hover:text-indigo-800" href="{{ route('password.request') }}">
                            {{ __('Zapomniałeś hasła?') }}
                        </a>
                    @endif
                </div>
                
                <div class="mt-6 text-center">
                    <p class="text-sm text-gray-600">
                        {{ __('Nie masz konta?') }} 
                        <a href="{{ route('register') }}" class="text-indigo-600 hover:text-indigo-800">
                            {{ __('Zarejestruj się') }}
                        </a>
                    </p>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
