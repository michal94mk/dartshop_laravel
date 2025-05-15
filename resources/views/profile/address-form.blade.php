@extends('layouts.app')

@section('title', $address->id ? 'Edytuj adres' : 'Dodaj nowy adres')

@section('content')
<div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
        <div class="p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800">{{ __('Twój profil') }}</h2>
            </div>
            
            <div class="mb-6 border-b border-gray-200">
                <nav class="flex space-x-6">
                    <a href="{{ route('profile.edit') }}" class="px-3 py-2 text-sm font-medium {{ request()->routeIs('profile.edit') ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-500 hover:text-gray-700' }}">
                        {{ __('Profil') }}
                    </a>
                    <a href="{{ route('profile.orders') }}" class="px-3 py-2 text-sm font-medium {{ request()->routeIs('profile.orders') ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-500 hover:text-gray-700' }}">
                        {{ __('Zamówienia') }}
                    </a>
                    <a href="{{ route('profile.payments') }}" class="px-3 py-2 text-sm font-medium {{ request()->routeIs('profile.payments') ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-500 hover:text-gray-700' }}">
                        {{ __('Płatności') }}
                    </a>
                    <a href="{{ route('profile.favorites') }}" class="px-3 py-2 text-sm font-medium {{ request()->routeIs('profile.favorites') ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-500 hover:text-gray-700' }}">
                        {{ __('Ulubione') }}
                    </a>
                    <a href="{{ route('profile.addresses') }}" class="px-3 py-2 text-sm font-medium {{ request()->routeIs('profile.addresses*') ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-500 hover:text-gray-700' }}">
                        {{ __('Adresy') }}
                    </a>
                </nav>
            </div>
            
            <div>
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-medium text-gray-900">
                        {{ $address->id ? __('Edytuj adres dostawy') : __('Dodaj nowy adres dostawy') }}
                    </h3>
                    <a href="{{ route('profile.addresses') }}" class="text-blue-600 hover:text-blue-900">
                        {{ __('Powrót do adresów') }}
                    </a>
                </div>
                
                <form method="POST" action="{{ $address->id ? route('profile.addresses.update', $address) : route('profile.addresses.store') }}" class="bg-gray-50 p-6 rounded-lg">
                    @csrf
                    @if($address->id)
                        @method('PUT')
                    @endif
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Imię i nazwisko') }} <span class="text-red-500">*</span></label>
                            <input type="text" name="name" id="name" value="{{ old('name', $address->name) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 @error('name') border-red-300 @enderror" required>
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Telefon') }}</label>
                            <input type="text" name="phone" id="phone" value="{{ old('phone', $address->phone) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 @error('phone') border-red-300 @enderror">
                            @error('phone')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="md:col-span-2">
                            <label for="address" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Adres') }} <span class="text-red-500">*</span></label>
                            <input type="text" name="address" id="address" value="{{ old('address', $address->address) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 @error('address') border-red-300 @enderror" required>
                            @error('address')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="city" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Miasto') }} <span class="text-red-500">*</span></label>
                            <input type="text" name="city" id="city" value="{{ old('city', $address->city) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 @error('city') border-red-300 @enderror" required>
                            @error('city')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="postal_code" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Kod pocztowy') }} <span class="text-red-500">*</span></label>
                            <input type="text" name="postal_code" id="postal_code" value="{{ old('postal_code', $address->postal_code) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 @error('postal_code') border-red-300 @enderror" required>
                            @error('postal_code')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="state" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Województwo/Stan') }}</label>
                            <input type="text" name="state" id="state" value="{{ old('state', $address->state) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 @error('state') border-red-300 @enderror">
                            @error('state')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="country" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Kraj') }} <span class="text-red-500">*</span></label>
                            <input type="text" name="country" id="country" value="{{ old('country', $address->country ?? 'Polska') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 @error('country') border-red-300 @enderror" required>
                            @error('country')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="mt-6">
                        <div class="flex items-center">
                            <input type="checkbox" name="is_default" id="is_default" value="1" {{ old('is_default', $address->is_default) ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            <label for="is_default" class="ml-2 block text-sm text-gray-700">{{ __('Ustaw jako domyślny adres dostawy') }}</label>
                        </div>
                    </div>
                    
                    <div class="mt-6 flex items-center justify-end">
                        <a href="{{ route('profile.addresses') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 active:bg-gray-300 focus:outline-none focus:border-gray-300 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150 mr-3">
                            {{ __('Anuluj') }}
                        </a>
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-700 focus:outline-none focus:border-blue-700 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                            {{ $address->id ? __('Zapisz zmiany') : __('Dodaj adres') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection 