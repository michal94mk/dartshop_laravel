@extends('layouts.app')

@section('title', 'Zamówienie')

@section('content')
<div class="bg-white">
    <div class="max-w-7xl mx-auto px-4 py-16 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-extrabold tracking-tight text-gray-900">Zamówienie</h1>
        
        <div class="mt-12">
            <div class="lg:grid lg:grid-cols-12 lg:gap-x-12 xl:gap-x-16">
                <!-- Formularz zamówienia -->
                <div class="lg:col-span-7">
                    <form action="{{ route('order.place') }}" method="POST" class="mt-6">
                        @csrf
                        
                        <div class="border-t border-gray-200 pt-6">
                            <h2 class="text-lg font-medium text-gray-900">Dane kontaktowe</h2>
                            
                            <div class="mt-4 grid grid-cols-1 gap-y-6 sm:grid-cols-2 sm:gap-x-4">
                                <div>
                                    <label for="first_name" class="block text-sm font-medium text-gray-700">Imię</label>
                                    <input type="text" name="first_name" id="first_name" autocomplete="given-name" value="{{ old('first_name', auth()->user()->name ?? '') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    @error('first_name')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <div>
                                    <label for="last_name" class="block text-sm font-medium text-gray-700">Nazwisko</label>
                                    <input type="text" name="last_name" id="last_name" autocomplete="family-name" value="{{ old('last_name') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    @error('last_name')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <div class="sm:col-span-2">
                                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                                    <input type="email" name="email" id="email" autocomplete="email" value="{{ old('email', auth()->user()->email ?? '') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    @error('email')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <div class="sm:col-span-2">
                                    <label for="phone" class="block text-sm font-medium text-gray-700">Telefon</label>
                                    <input type="text" name="phone" id="phone" autocomplete="tel" value="{{ old('phone') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    @error('phone')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="border-t border-gray-200 pt-6 mt-6">
                            <h2 class="text-lg font-medium text-gray-900">Adres dostawy</h2>
                            
                            <div class="mt-4 grid grid-cols-1 gap-y-6 sm:grid-cols-2 sm:gap-x-4">
                                <div class="sm:col-span-2">
                                    <label for="address" class="block text-sm font-medium text-gray-700">Adres</label>
                                    <input type="text" name="address" id="address" autocomplete="street-address" value="{{ old('address') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    @error('address')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <div>
                                    <label for="city" class="block text-sm font-medium text-gray-700">Miasto</label>
                                    <input type="text" name="city" id="city" autocomplete="address-level2" value="{{ old('city') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    @error('city')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <div>
                                    <label for="postal_code" class="block text-sm font-medium text-gray-700">Kod pocztowy</label>
                                    <input type="text" name="postal_code" id="postal_code" autocomplete="postal-code" value="{{ old('postal_code') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    @error('postal_code')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="border-t border-gray-200 pt-6 mt-6">
                            <h2 class="text-lg font-medium text-gray-900">Metoda płatności</h2>
                            
                            <div class="mt-4 space-y-4">
                                <div class="flex items-center">
                                    <input id="payment_method_online" name="payment_method" value="online" type="radio" checked class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300">
                                    <label for="payment_method_online" class="ml-3 block text-sm font-medium text-gray-700">
                                        Płatność online (karta, BLIK, przelew)
                                    </label>
                                </div>
                                
                                <div class="flex items-center">
                                    <input id="payment_method_transfer" name="payment_method" value="bank_transfer" type="radio" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300">
                                    <label for="payment_method_transfer" class="ml-3 block text-sm font-medium text-gray-700">
                                        Przelew tradycyjny
                                    </label>
                                </div>
                                
                                <div class="flex items-center">
                                    <input id="payment_method_cod" name="payment_method" value="cash_on_delivery" type="radio" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300">
                                    <label for="payment_method_cod" class="ml-3 block text-sm font-medium text-gray-700">
                                        Płatność przy odbiorze
                                    </label>
                                </div>
                                
                                @error('payment_method')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="border-t border-gray-200 pt-6 mt-6">
                            <h2 class="text-lg font-medium text-gray-900">Uwagi do zamówienia</h2>
                            
                            <div class="mt-4">
                                <textarea name="notes" id="notes" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="Opcjonalne uwagi do zamówienia...">{{ old('notes') }}</textarea>
                            </div>
                        </div>
                        
                        <div class="mt-8">
                            <button type="submit" class="w-full bg-indigo-600 border border-transparent rounded-md py-3 px-4 text-base font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Zamów i zapłać
                            </button>
                        </div>
                    </form>
                </div>
                
                <!-- Podsumowanie zamówienia -->
                <div class="mt-10 lg:mt-0 lg:col-span-5">
                    <div class="bg-gray-50 rounded-lg px-6 py-6">
                        <h2 class="text-lg font-medium text-gray-900">Podsumowanie zamówienia</h2>
                        
                        <div class="mt-6 space-y-4">
                            <div class="flow-root">
                                <ul role="list" class="-my-6 divide-y divide-gray-200">
                                    @foreach($cart as $productId => $item)
                                        <li class="py-6 flex">
                                            <div class="flex-shrink-0 w-16 h-16 overflow-hidden rounded-md border border-gray-200">
                                                @if($item['product']->image)
                                                    <img src="{{ asset($item['product']->image) }}" alt="{{ $item['product']->name }}" class="w-full h-full object-center object-cover">
                                                @else
                                                    <div class="w-full h-full flex items-center justify-center bg-gray-200">
                                                        <span class="text-gray-400">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                            </svg>
                                                        </span>
                                                    </div>
                                                @endif
                                            </div>
                                            
                                            <div class="ml-4 flex-1 flex flex-col">
                                                <div>
                                                    <div class="flex justify-between text-base font-medium text-gray-900">
                                                        <h3>{{ $item['product']->name }}</h3>
                                                        <p class="ml-4">{{ number_format($item['product']->price * $item['quantity'], 2) }} zł</p>
                                                    </div>
                                                    <p class="mt-1 text-sm text-gray-500">
                                                        @if($item['product']->brand)
                                                            {{ $item['product']->brand->name }}
                                                        @endif
                                                    </p>
                                                </div>
                                                <div class="flex-1 flex items-end justify-between text-sm">
                                                    <p class="text-gray-500">Ilość {{ $item['quantity'] }}</p>
                                                </div>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                            
                            <div class="border-t border-gray-200 pt-4">
                                <div class="flex justify-between text-base font-medium text-gray-900">
                                    <p>Wartość produktów</p>
                                    <p>{{ number_format($subtotal, 2) }} zł</p>
                                </div>
                                
                                @if(isset($promotion))
                                <div class="flex justify-between text-sm text-gray-500 mt-1">
                                    <p>Kod promocyjny ({{ $promotion['code'] }})</p>
                                    <p>-{{ number_format($discountAmount, 2) }} zł</p>
                                </div>
                                @endif
                                
                                <div class="flex justify-between text-sm text-gray-500 mt-1">
                                    <p>Dostawa</p>
                                    <p>{{ number_format($shippingCost, 2) }} zł</p>
                                </div>
                                
                                <div class="flex justify-between text-base font-medium text-gray-900 mt-4">
                                    <p>Łącznie do zapłaty</p>
                                    <p>{{ number_format($finalTotal + $shippingCost, 2) }} zł</p>
                                </div>
                            </div>
                            
                            <div class="mt-6 text-sm text-center">
                                <p>
                                    <a href="{{ route('cart.view') }}" class="text-indigo-600 font-medium hover:text-indigo-500">
                                        <span aria-hidden="true">&larr;</span> Wróć do koszyka
                                    </a>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 