@extends('layouts.app')

@section('title', 'Twoje adresy')

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
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">{{ __('Twoje adresy dostawy') }}</h3>
                    <a href="{{ route('profile.addresses.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        {{ __('Dodaj nowy adres') }}
                    </a>
                </div>
                
                @if(session('status'))
                    <div class="mb-4 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded">
                        {{ session('status') }}
                    </div>
                @endif
                
                @if($addresses->isEmpty())
                    <div class="bg-gray-50 p-6 rounded-lg text-center">
                        <p class="text-gray-500">{{ __('Nie masz jeszcze żadnych adresów.') }}</p>
                        <a href="{{ route('profile.addresses.create') }}" class="mt-4 inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            {{ __('Dodaj pierwszy adres') }}
                        </a>
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @foreach($addresses as $address)
                            <div class="bg-white border rounded-lg overflow-hidden {{ $address->is_default ? 'border-blue-400 ring-2 ring-blue-200' : 'border-gray-200' }}">
                                <div class="p-4 flex justify-between">
                                    <div>
                                        <div class="flex items-center mb-2">
                                            <h4 class="text-lg font-semibold text-gray-900">{{ $address->name }}</h4>
                                            @if($address->is_default)
                                                <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                    {{ __('Domyślny') }}
                                                </span>
                                            @endif
                                        </div>
                                        <p class="text-gray-600">{{ $address->address }}</p>
                                        <p class="text-gray-600">{{ $address->postal_code }} {{ $address->city }}</p>
                                        @if($address->state)
                                            <p class="text-gray-600">{{ $address->state }}</p>
                                        @endif
                                        <p class="text-gray-600">{{ $address->country }}</p>
                                        @if($address->phone)
                                            <p class="text-gray-600 mt-2">{{ __('Telefon') }}: {{ $address->phone }}</p>
                                        @endif
                                    </div>
                                    <div class="flex flex-col space-y-2">
                                        <a href="{{ route('profile.addresses.edit', $address) }}" class="text-blue-600 hover:text-blue-800">
                                            {{ __('Edytuj') }}
                                        </a>
                                        <form method="POST" action="{{ route('profile.addresses.destroy', $address) }}" onsubmit="return confirm('Czy na pewno chcesz usunąć ten adres?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800">
                                                {{ __('Usuń') }}
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection 