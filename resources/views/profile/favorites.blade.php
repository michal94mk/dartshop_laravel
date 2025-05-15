@extends('layouts.app')

@section('title', 'Twoje ulubione produkty')

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
                <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Twoje ulubione produkty') }}</h3>
                
                @if($products->isEmpty())
                    <div class="bg-gray-50 p-6 rounded-lg text-center">
                        <p class="text-gray-500">{{ __('Nie masz jeszcze żadnych ulubionych produktów.') }}</p>
                        <a href="{{ route('home') }}" class="mt-4 inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            {{ __('Przeglądaj produkty') }}
                        </a>
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                        @foreach($products as $product)
                            <div class="bg-white border border-gray-200 rounded-md overflow-hidden shadow-sm hover:shadow-md transition-shadow duration-300">
                                @if($product->image)
                                    <a href="{{ route('frontend.products.show', $product->id) }}">
                                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-full h-48 object-cover">
                                    </a>
                                @endif
                                <div class="p-4">
                                    <h4 class="text-lg font-semibold text-gray-900 mb-2">
                                        <a href="{{ route('frontend.products.show', $product->id) }}" class="hover:text-blue-600">{{ $product->name }}</a>
                                    </h4>
                                    <p class="text-gray-500 text-sm mb-2">{{ Str::limit($product->description, 100) }}</p>
                                    <div class="flex justify-between items-center mt-4">
                                        <span class="text-lg font-bold text-gray-900">{{ number_format($product->price, 2) }} zł</span>
                                        <div class="flex space-x-2">
                                            <form method="POST" action="{{ route('profile.favorites.toggle', $product) }}">
                                                @csrf
                                                <button type="submit" class="text-red-500 hover:text-red-700" title="{{ __('Usuń z ulubionych') }}">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                                    </svg>
                                                </button>
                                            </form>
                                            <form method="POST" action="{{ route('cart.add', $product) }}">
                                                @csrf
                                                <input type="hidden" name="quantity" value="1">
                                                <button type="submit" class="text-blue-500 hover:text-blue-700" title="{{ __('Dodaj do koszyka') }}">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    <div class="mt-6">
                        {{ $products->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection 