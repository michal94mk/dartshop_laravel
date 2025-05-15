@extends('layouts.app')

@section('title', 'Zamówienie')

@section('content')
<div class="bg-gray-50 min-h-screen py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-8">
            <h1 class="text-3xl font-extrabold tracking-tight text-gray-900 sm:text-4xl">Finalizacja zamówienia</h1>
            <p class="mt-2 text-lg text-gray-600">Jesteś o krok od zakończenia zakupów</p>
        </div>
        
        <div class="lg:grid lg:grid-cols-12 lg:gap-x-12 xl:gap-x-16">
            <!-- Formularz zamówienia -->
            <div class="lg:col-span-7">
                <form action="{{ route('order.place') }}" method="POST" class="space-y-8">
                    @csrf
                    
                    <!-- Dane kontaktowe -->
                    <div class="bg-white shadow-sm rounded-lg overflow-hidden border border-gray-200">
                        <div class="px-6 py-5 bg-gray-50 border-b border-gray-200">
                            <div class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" />
                                </svg>
                                <h2 class="ml-2 text-lg font-medium text-gray-900">Dane kontaktowe</h2>
                            </div>
                        </div>
                        
                        <div class="px-6 py-6 space-y-6">
                            <div class="grid grid-cols-1 gap-y-6 sm:grid-cols-2 sm:gap-x-4">
                                <div>
                                    <label for="first_name" class="block text-sm font-medium text-gray-700">Imię</label>
                                    <div class="mt-1 relative rounded-md shadow-sm">
                                        <input type="text" name="first_name" id="first_name" autocomplete="given-name" value="{{ old('first_name', auth()->user()->name ?? '') }}" class="block w-full pr-10 focus:outline-none border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    </div>
                                    @error('first_name')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <div>
                                    <label for="last_name" class="block text-sm font-medium text-gray-700">Nazwisko</label>
                                    <div class="mt-1 relative rounded-md shadow-sm">
                                        <input type="text" name="last_name" id="last_name" autocomplete="family-name" value="{{ old('last_name') }}" class="block w-full pr-10 focus:outline-none border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    </div>
                                    @error('last_name')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <div class="sm:col-span-2">
                                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                                    <div class="mt-1 relative rounded-md shadow-sm">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                                                <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                                            </svg>
                                        </div>
                                        <input type="email" name="email" id="email" autocomplete="email" value="{{ old('email', auth()->user()->email ?? '') }}" class="block w-full pl-10 focus:outline-none border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    </div>
                                    @error('email')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <div class="sm:col-span-2">
                                    <label for="phone" class="block text-sm font-medium text-gray-700">Telefon</label>
                                    <div class="mt-1 relative rounded-md shadow-sm">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z" />
                                            </svg>
                                        </div>
                                        <input type="text" name="phone" id="phone" autocomplete="tel" value="{{ old('phone') }}" class="block w-full pl-10 focus:outline-none border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    </div>
                                    @error('phone')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Adres dostawy -->
                    <div class="bg-white shadow-sm rounded-lg overflow-hidden border border-gray-200">
                        <div class="px-6 py-5 bg-gray-50 border-b border-gray-200">
                            <div class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                                </svg>
                                <h2 class="ml-2 text-lg font-medium text-gray-900">Adres dostawy</h2>
                            </div>
                        </div>
                        
                        <div class="px-6 py-6 space-y-6">
                            <div class="grid grid-cols-1 gap-y-6 sm:grid-cols-2 sm:gap-x-4">
                                <div class="sm:col-span-2">
                                    <label for="address" class="block text-sm font-medium text-gray-700">Adres</label>
                                    <div class="mt-1 relative rounded-md shadow-sm">
                                        <input type="text" name="address" id="address" autocomplete="street-address" value="{{ old('address') }}" class="block w-full focus:outline-none border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    </div>
                                    @error('address')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <div>
                                    <label for="city" class="block text-sm font-medium text-gray-700">Miasto</label>
                                    <div class="mt-1 relative rounded-md shadow-sm">
                                        <input type="text" name="city" id="city" autocomplete="address-level2" value="{{ old('city') }}" class="block w-full focus:outline-none border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    </div>
                                    @error('city')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <div>
                                    <label for="postal_code" class="block text-sm font-medium text-gray-700">Kod pocztowy</label>
                                    <div class="mt-1 relative rounded-md shadow-sm">
                                        <input type="text" name="postal_code" id="postal_code" autocomplete="postal-code" value="{{ old('postal_code') }}" class="block w-full focus:outline-none border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="00-000">
                                    </div>
                                    @error('postal_code')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Metoda płatności -->
                    <div class="bg-white shadow-sm rounded-lg overflow-hidden border border-gray-200">
                        <div class="px-6 py-5 bg-gray-50 border-b border-gray-200">
                            <div class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z" />
                                    <path fill-rule="evenodd" d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z" clip-rule="evenodd" />
                                </svg>
                                <h2 class="ml-2 text-lg font-medium text-gray-900">Metoda płatności</h2>
                            </div>
                        </div>
                        
                        <div class="px-6 py-6 space-y-3">
                            <div class="relative border rounded-md px-4 py-3 flex items-center space-x-3 focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500 hover:border-gray-400 transition-colors">
                                <div class="flex-shrink-0">
                                    <span class="inline-flex items-center justify-center h-10 w-10 rounded-md bg-indigo-100 text-indigo-600">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                        </svg>
                                    </span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <label for="payment_method_online" class="cursor-pointer">
                                        <input id="payment_method_online" name="payment_method" value="online" type="radio" checked class="sr-only">
                                        <span class="block text-sm font-medium text-gray-900">Płatność online</span>
                                        <span class="block text-sm text-gray-500">Karta płatnicza, BLIK, szybki przelew</span>
                                    </label>
                                </div>
                                <div class="flex-shrink-0">
                                    <span class="h-6 w-6 text-indigo-600 group-hover:text-indigo-800" aria-hidden="true">
                                        <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                        </svg>
                                    </span>
                                </div>
                            </div>
                            
                            <div class="relative border rounded-md px-4 py-3 flex items-center space-x-3 focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500 hover:border-gray-400 transition-colors">
                                <div class="flex-shrink-0">
                                    <span class="inline-flex items-center justify-center h-10 w-10 rounded-md bg-gray-100 text-gray-600">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <label for="payment_method_transfer" class="cursor-pointer">
                                        <input id="payment_method_transfer" name="payment_method" value="bank_transfer" type="radio" class="sr-only">
                                        <span class="block text-sm font-medium text-gray-900">Przelew tradycyjny</span>
                                        <span class="block text-sm text-gray-500">Dane do przelewu otrzymasz w podsumowaniu zamówienia</span>
                                    </label>
                                </div>
                            </div>
                            
                            <div class="relative border rounded-md px-4 py-3 flex items-center space-x-3 focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500 hover:border-gray-400 transition-colors">
                                <div class="flex-shrink-0">
                                    <span class="inline-flex items-center justify-center h-10 w-10 rounded-md bg-gray-100 text-gray-600">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                    </span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <label for="payment_method_cod" class="cursor-pointer">
                                        <input id="payment_method_cod" name="payment_method" value="cash_on_delivery" type="radio" class="sr-only">
                                        <span class="block text-sm font-medium text-gray-900">Płatność przy odbiorze</span>
                                        <span class="block text-sm text-gray-500">Zapłać kurierowi gotówką przy dostawie</span>
                                    </label>
                                </div>
                            </div>
                            
                            @error('payment_method')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    
                    <!-- Uwagi do zamówienia -->
                    <div class="bg-white shadow-sm rounded-lg overflow-hidden border border-gray-200">
                        <div class="px-6 py-5 bg-gray-50 border-b border-gray-200">
                            <div class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M18 13V5a2 2 0 00-2-2H4a2 2 0 00-2 2v8a2 2 0 002 2h3l3 3 3-3h3a2 2 0 002-2zM5 7a1 1 0 011-1h8a1 1 0 110 2H6a1 1 0 01-1-1zm1 3a1 1 0 100 2h3a1 1 0 100-2H6z" clip-rule="evenodd" />
                                </svg>
                                <h2 class="ml-2 text-lg font-medium text-gray-900">Uwagi do zamówienia</h2>
                            </div>
                        </div>
                        
                        <div class="px-6 py-6">
                            <textarea name="notes" id="notes" rows="3" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md" placeholder="Opcjonalne uwagi do zamówienia, np. szczegóły dotyczące dostawy...">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                    
                    <div class="mt-8">
                        <button type="submit" class="w-full bg-indigo-600 border border-transparent rounded-lg py-4 px-4 text-base font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                            Zamów i zapłać
                        </button>
                    </div>
                </form>
            </div>
            
            <!-- Podsumowanie zamówienia -->
            <div class="mt-10 lg:mt-0 lg:col-span-5">
                <div class="bg-white shadow-sm rounded-lg overflow-hidden border border-gray-200 lg:sticky lg:top-6">
                    <div class="px-6 py-5 bg-gray-50 border-b border-gray-200">
                        <h2 class="text-lg font-medium text-gray-900">Podsumowanie zamówienia</h2>
                    </div>
                    
                    <div class="p-6 space-y-6">
                        <div class="flow-root">
                            <ul role="list" class="-my-6 divide-y divide-gray-200">
                                @foreach($cart as $productId => $item)
                                    <li class="py-6 flex">
                                        <div class="flex-shrink-0 w-20 h-20 overflow-hidden rounded-md border border-gray-200">
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
                                                <p class="text-gray-500">Ilość: {{ $item['quantity'] }}</p>
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        
                        <div class="border-t border-gray-200 pt-6">
                            <div class="flex justify-between text-sm text-gray-600">
                                <p>Wartość produktów</p>
                                <p class="font-medium">{{ number_format($subtotal, 2) }} zł</p>
                            </div>
                            
                            @if(isset($promotion))
                            <div class="flex justify-between text-sm text-gray-600 mt-3">
                                <div class="flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-green-500 mr-1.5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M5 2a2 2 0 00-2 2v14l3.5-2 3.5 2 3.5-2 3.5 2V4a2 2 0 00-2-2H5zm4.707 3.707a1 1 0 00-1.414-1.414l-3 3a1 1 0 000 1.414l3 3a1 1 0 001.414-1.414L8.414 9H10a3 3 0 013 3v1a1 1 0 102 0v-1a5 5 0 00-5-5H8.414l1.293-1.293z" clip-rule="evenodd" />
                                    </svg>
                                    <span>Kod promocyjny ({{ $promotion['code'] }})</span>
                                </div>
                                <p class="font-medium text-green-600">-{{ number_format($discountAmount, 2) }} zł</p>
                            </div>
                            @endif
                            
                            <div class="flex justify-between text-sm text-gray-600 mt-3">
                                <p>Dostawa</p>
                                <p class="font-medium">{{ number_format($shippingCost, 2) }} zł</p>
                            </div>
                            
                            <div class="flex justify-between text-base font-medium text-gray-900 mt-6">
                                <p>Łącznie do zapłaty</p>
                                <p>{{ number_format($finalTotal + $shippingCost, 2) }} zł</p>
                            </div>
                        </div>
                        
                        <div class="border-t border-gray-200 pt-6">
                            <div class="rounded-md bg-blue-50 p-4">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-blue-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="ml-3 flex-1 md:flex md:justify-between">
                                        <p class="text-sm text-blue-700">
                                            Wszystkie zamówienia są realizowane w ciągu 1-2 dni roboczych.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-6 text-center">
                            <a href="{{ route('cart.view') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-500 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                                </svg>
                                Wróć do koszyka
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 