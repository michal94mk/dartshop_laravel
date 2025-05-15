@extends('layouts.app')

@section('title', 'Szczegóły zamówienia')

@section('content')
<div class="bg-gray-50 min-h-screen py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl mx-auto">
            <div class="mb-8">
                <h1 class="text-3xl font-bold tracking-tight text-gray-900">Szczegóły zamówienia</h1>
                <p class="mt-2 text-lg text-gray-600">Numer zamówienia: <span class="font-medium">{{ $order->order_number }}</span></p>
                <p class="text-sm text-gray-500">Złożone dnia: {{ $order->created_at->format('d.m.Y H:i') }}</p>
            </div>

            <div class="bg-white rounded-lg shadow-sm overflow-hidden border border-gray-200">
                <!-- Status zamówienia -->
                <div class="px-6 py-5 bg-gray-50 border-b border-gray-200">
                    <div class="flex justify-between items-center">
                        <div class="flex items-center">
                            @if($order->status == \App\Enums\OrderStatus::COMPLETED)
                                <span class="h-8 w-8 rounded-full bg-green-100 flex items-center justify-center mr-3">
                                    <svg class="h-5 w-5 text-green-600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                </span>
                                <h2 class="text-lg font-medium text-gray-900">Zamówienie zrealizowane</h2>
                            @elseif($order->status == \App\Enums\OrderStatus::PROCESSING)
                                <span class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                                    <svg class="h-5 w-5 text-blue-600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z" />
                                        <path d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H11a1 1 0 001-1v-5h2v5a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H19a1 1 0 001-1V9a3 3 0 00-3-3h-3V4a1 1 0 00-1-1H3z" />
                                    </svg>
                                </span>
                                <h2 class="text-lg font-medium text-gray-900">Zamówienie w realizacji</h2>
                            @elseif($order->status == \App\Enums\OrderStatus::PENDING)
                                <span class="h-8 w-8 rounded-full bg-yellow-100 flex items-center justify-center mr-3">
                                    <svg class="h-5 w-5 text-yellow-600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                                    </svg>
                                </span>
                                <h2 class="text-lg font-medium text-gray-900">Zamówienie oczekujące</h2>
                            @elseif($order->status == \App\Enums\OrderStatus::CANCELLED)
                                <span class="h-8 w-8 rounded-full bg-red-100 flex items-center justify-center mr-3">
                                    <svg class="h-5 w-5 text-red-600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                    </svg>
                                </span>
                                <h2 class="text-lg font-medium text-gray-900">Zamówienie anulowane</h2>
                            @else
                                <span class="h-8 w-8 rounded-full bg-gray-100 flex items-center justify-center mr-3">
                                    <svg class="h-5 w-5 text-gray-600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                                    </svg>
                                </span>
                                <h2 class="text-lg font-medium text-gray-900">Status: {{ $order->status }}</h2>
                            @endif
                        </div>
                        
                        <!-- Status płatności -->
                        @if($order->payment)
                            <div>
                                @if($order->payment->status == \App\Enums\PaymentStatus::COMPLETED)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                        Opłacone
                                    </span>
                                @elseif($order->payment->status == \App\Enums\PaymentStatus::PENDING)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                        Oczekuje na płatność
                                    </span>
                                @elseif($order->payment->status == \App\Enums\PaymentStatus::FAILED)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                        Płatność anulowana
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                                        {{ $order->payment->status }}
                                    </span>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
                
                <div class="p-6">
                    <!-- Sekcja płatności -->
                    @if($order->payment_method == 'online' && (!$order->payment || $order->payment->status !== \App\Enums\PaymentStatus::COMPLETED))
                        <div class="mb-6 bg-yellow-50 p-4 rounded-md">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-yellow-800">Płatność oczekująca</h3>
                                    <div class="mt-2 text-sm text-yellow-700">
                                        <p>Aby dokończyć zamówienie, przejdź do płatności.</p>
                                    </div>
                                    <div class="mt-4">
                                        <div class="-mx-2 -my-1.5 flex">
                                            <a href="{{ route('payment.process', ['order' => $order->id]) }}" class="bg-yellow-100 px-4 py-2 rounded-md text-sm font-medium text-yellow-800 hover:bg-yellow-200">
                                                Przejdź do płatności
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @elseif($order->payment && $order->payment->status == \App\Enums\PaymentStatus::FAILED)
                        <div class="mb-6 bg-red-50 p-4 rounded-md">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-red-800">Płatność anulowana</h3>
                                    <div class="mt-2 text-sm text-red-700">
                                        <p>Twoja płatność została anulowana. Możesz spróbować ponownie.</p>
                                    </div>
                                    <div class="mt-4">
                                        <div class="-mx-2 -my-1.5 flex">
                                            <a href="{{ route('payment.process', ['order' => $order->id]) }}" class="bg-red-100 px-4 py-2 rounded-md text-sm font-medium text-red-800 hover:bg-red-200">
                                                Spróbuj zapłacić ponownie
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @elseif($order->payment_method == 'bank_transfer')
                        <div class="mb-6 bg-blue-50 p-4 rounded-md">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-blue-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2h-1V9a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-blue-800">Dane do przelewu</h3>
                                    <div class="mt-2 text-sm text-blue-700">
                                        <p>Prosimy o przelew na następujące konto:</p>
                                        <p class="mt-1"><strong>Nazwa odbiorcy:</strong> DartShop Sp. z o.o.</p>
                                        <p><strong>Nr konta:</strong> 00 1111 2222 3333 4444 5555 6666</p>
                                        <p><strong>Tytuł przelewu:</strong> Zamówienie {{ $order->order_number }}</p>
                                        <p><strong>Kwota:</strong> {{ number_format($order->total, 2) }} zł</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @elseif($order->payment_method == 'cash_on_delivery')
                        <div class="mb-6 bg-green-50 p-4 rounded-md">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-green-800">Płatność przy dostawie</h3>
                                    <div class="mt-2 text-sm text-green-700">
                                        <p>Wybrałeś płatność przy odbiorze. Zamówienie zostało przekazane do realizacji.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                    
                    <!-- Lista produktów -->
                    <div class="mt-6 border rounded-lg overflow-hidden">
                        <div class="bg-gray-50 px-4 py-3 sm:px-6">
                            <div class="flex justify-between">
                                <h3 class="text-sm font-medium text-gray-900">Produkty</h3>
                                <h3 class="text-sm font-medium text-gray-900">Cena</h3>
                            </div>
                        </div>
                        <div class="border-t border-gray-200 divide-y divide-gray-200">
                            @foreach($order->items as $item)
                                <div class="px-4 py-4 sm:px-6 flex justify-between">
                                    <div class="flex items-center">
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">{{ $item->product_name }}</p>
                                            <p class="text-sm text-gray-500">Ilość: {{ $item->quantity }}</p>
                                        </div>
                                    </div>
                                    <div class="text-sm font-medium text-gray-900">{{ number_format($item->total, 2) }} zł</div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    
                    <!-- Podsumowanie kosztów -->
                    <div class="mt-6 bg-gray-50 p-6 rounded-lg">
                        <div class="flex justify-between text-sm">
                            <p class="text-gray-600">Wartość produktów</p>
                            <p class="font-medium text-gray-900">{{ number_format($order->subtotal, 2) }} zł</p>
                        </div>
                        
                        @if($order->discount > 0)
                            <div class="flex justify-between text-sm mt-3">
                                <p class="text-gray-600">Rabat</p>
                                <p class="font-medium text-gray-900">-{{ number_format($order->discount, 2) }} zł</p>
                            </div>
                        @endif
                        
                        <div class="flex justify-between text-sm mt-3">
                            <p class="text-gray-600">Dostawa</p>
                            <p class="font-medium text-gray-900">{{ number_format($order->shipping_cost, 2) }} zł</p>
                        </div>
                        
                        <div class="flex justify-between mt-6 border-t border-gray-200 pt-6">
                            <p class="text-base font-medium text-gray-900">Razem</p>
                            <p class="text-base font-medium text-gray-900">{{ number_format($order->total, 2) }} zł</p>
                        </div>
                    </div>
                    
                    <!-- Dane dostawy i kontaktowe -->
                    <div class="mt-8 grid grid-cols-1 gap-y-6 sm:grid-cols-2 sm:gap-x-4">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">Dane kontaktowe</h3>
                            <div class="mt-4 text-sm text-gray-600">
                                <p class="font-medium text-gray-900">{{ $order->first_name }} {{ $order->last_name }}</p>
                                <p>{{ $order->email }}</p>
                                @if($order->phone)
                                    <p>{{ $order->phone }}</p>
                                @endif
                            </div>
                        </div>
                        
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">Adres dostawy</h3>
                            <div class="mt-4 text-sm text-gray-600">
                                <p class="font-medium text-gray-900">{{ $order->first_name }} {{ $order->last_name }}</p>
                                <p>{{ $order->address }}</p>
                                <p>{{ $order->postal_code }} {{ $order->city }}</p>
                                <p>{{ $order->country }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Przyciski akcji -->
                    <div class="mt-8 border-t border-gray-200 pt-8">
                        <div class="flex justify-between">
                            <a href="{{ route('orders.history') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                Historia zamówień
                            </a>
                            
                            <a href="{{ route('home') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700">
                                Wróć do sklepu
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 