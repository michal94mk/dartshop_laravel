@extends('layouts.app')

@section('content')
    <div class="max-w-md mx-auto my-10 bg-white p-6 rounded shadow-md">
        <form method="POST" action="{{ route('password.update') }}">
            @csrf

            <!-- Password Reset Token -->
            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <!-- Email Address -->
            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700">{{ __('Email') }}</label>
                <input id="email" class="mt-1 px-4 py-2 w-full border rounded-md" type="email" name="email"
                       value="{{ old('email', $request->email) }}" required autofocus autocomplete="username">

                @error('email')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password -->
            <div class="mb-4">
                <label for="password" class="block text-sm font-medium text-gray-700">{{ __('Password') }}</label>
                <input id="password" class="mt-1 px-4 py-2 w-full border rounded-md" type="password"
                       name="password" required autocomplete="new-password">

                @error('password')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Confirm Password -->
            <div class="mb-4">
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700">{{ __('Confirm Password') }}</label>
                <input id="password_confirmation" class="mt-1 px-4 py-2 w-full border rounded-md" type="password"
                       name="password_confirmation" required autocomplete="new-password">

                @error('password_confirmation')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-end mt-4">
                <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md">
                    {{ __('Reset Password') }}
                </button>
            </div>
        </form>
    </div>
@endsection
