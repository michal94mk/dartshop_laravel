@extends('layouts.app')

@section('title', 'Weryfikacja e-mail')

@section('content')
<div class="max-w-7xl mx-auto py-12 sm:px-6 lg:px-8">
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg max-w-md mx-auto">
        <div class="p-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">{{ __('Weryfikacja adresu e-mail') }}</h2>
            
            <div class="mb-4 text-sm text-gray-600">
                {{ __('Dziękujemy za rejestrację! Zanim zaczniesz, czy mógłbyś zweryfikować swój adres e-mail, klikając na link, który właśnie wysłaliśmy na Twój adres e-mail? Jeśli nie otrzymałeś wiadomości e-mail, chętnie wyślemy Ci kolejną.') }}
            </div>

            @if (session('status') == 'verification-link-sent')
                <div class="mb-4 bg-green-50 p-4 rounded-md">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-800">{{ __('Link weryfikacyjny został wysłany na podany adres e-mail.') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <div class="mt-6 flex items-center justify-between">
                <form method="POST" action="{{ route('verification.send') }}">
                    @csrf
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white py-2 px-4 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        {{ __('Wyślij ponownie email weryfikacyjny') }}
                    </button>
                </form>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-sm text-indigo-600 hover:text-indigo-800">
                        {{ __('Wyloguj się') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
