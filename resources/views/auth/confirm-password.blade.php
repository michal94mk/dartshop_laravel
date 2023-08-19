@extends('layouts.app')

@section('content')
    <div class="max-w-md mx-auto my-10 bg-white p-6 rounded shadow-md">
        <div class="mb-4 text-sm text-gray-600">
            {{ __('This is a secure area of the application. Please confirm your password before continuing.') }}
        </div>

        <form method="POST" action="{{ route('password.confirm') }}">
            @csrf

            <!-- Password -->
            <div class="mb-4">
                <label for="password" class="block text-sm font-medium text-gray-700">{{ __('Password') }}</label>

                <input id="password" type="password" class="mt-1 px-4 py-2 w-full border rounded-md"
                       name="password" required autocomplete="current-password">

                @error('password')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-end mt-4">
                <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md">
                    {{ __('Confirm') }}
                </button>
            </div>
        </form>
    </div>
@endsection
