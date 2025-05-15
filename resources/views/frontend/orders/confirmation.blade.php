@extends('layouts.app')

@section('title', 'Potwierdzenie zamówienia')

@section('content')
<div class="bg-white">
    <div class="max-w-7xl mx-auto px-4 py-16 sm:px-6 lg:px-8">
        <div class="max-w-3xl mx-auto">
            <div class="bg-white rounded-lg shadow-lg p-6 md:p-10">
                <div class="text-center">
                    <div class="flex justify-center mb-6">
                        <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center">
                            <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                    </div>
                    <h1 class="text-3xl font-extrabold text-gray-900">Dziękujemy za zamówienie!</h1>
                    <p class="mt-2 text-lg text-gray-600">Twoje zamówienie zostało przyjęte do realizacji.</p>
                    <p class="mt-1 text-lg text-gray-600">Numer zamówienia: <strong>{{ $order->order_number }}</strong></p>
                    
                    @if($order->payment_method == 'online' && (!$order->payment || $order->payment->status !== \App\Enums\PaymentStatus::COMPLETED))
                        <div class="mt-4 bg-yellow-50 p-4 rounded-md">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
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
                    @elseif($order->payment_method == 'bank_transfer')
                        <div class="mt-4 bg-blue-50 p-4 rounded-md">
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
                        <div class="mt-4 bg-green-50 p-4 rounded-md">
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
                </div>
                
                <div class="mt-8 border-t border-gray-200 pt-8">
                    <h2 class="text-xl font-medium text-gray-900">Szczegóły zamówienia</h2>
                    
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
                </div>
                
                <div class="mt-8 border-t border-gray-200 pt-8 grid grid-cols-1 gap-y-6 sm:grid-cols-2 sm:gap-x-4">
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
                
                <div class="mt-8 border-t border-gray-200 pt-8">
                    <div class="flex justify-between">
                        <a href="{{ route('orders.history') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Historia zamówień
                        </a>
                        
                        <a href="{{ route('home') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Wróć do sklepu
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 