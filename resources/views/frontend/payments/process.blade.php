@extends('layouts.app')

@section('title', 'Płatność')

@section('content')
<div class="bg-white">
    <div class="max-w-7xl mx-auto px-4 py-16 sm:px-6 lg:px-8">
        <div class="max-w-xl mx-auto">
            <div class="bg-white rounded-lg shadow-lg p-6 md:p-10">
                <div class="text-center">
                    <h1 class="text-3xl font-extrabold text-gray-900">Płatność za zamówienie</h1>
                    <p class="mt-2 text-lg text-gray-600">Numer zamówienia: <strong>{{ $order->order_number }}</strong></p>
                    <p class="mt-2 text-lg text-gray-600">Kwota do zapłaty: <strong>{{ number_format($order->total, 2) }} zł</strong></p>
                </div>
                
                <!-- Przykładowy formularz płatności - w rzeczywistej implementacji należy zintegrować bramkę płatności -->
                <div class="mt-8 border-t border-gray-200 pt-8">
                    <h2 class="text-xl font-medium text-gray-900">Wybierz metodę płatności</h2>
                    
                    <div class="mt-6 space-y-4">
                        <div class="relative bg-white border rounded-md p-4 flex">
                            <div class="flex items-center h-5">
                                <input id="payment-card" name="payment-method" type="radio" checked class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300">
                            </div>
                            <div class="ml-3 flex flex-col">
                                <label for="payment-card" class="text-sm font-medium text-gray-900">Karta płatnicza</label>
                                <span class="text-sm text-gray-500">Visa, Mastercard, AmEx</span>
                                <div class="mt-1 flex space-x-2">
                                    <svg class="h-6 w-auto" viewBox="0 0 36 24" xmlns="http://www.w3.org/2000/svg" role="img" width="36" height="24" aria-labelledby="pi-visa"><title id="pi-visa">Visa</title><path opacity=".07" d="M35 0H1C.45 0 0 .45 0 1v22c0 .55.45 1 1 1h34c.55 0 1-.45 1-1V1c0-.55-.45-1-1-1z"/><path fill="#fff" d="M35 1c0-.55-.45-1-1-1H2c-.55 0-1 .45-1 1v22c0 .55.45 1 1 1h32c.55 0 1-.45 1-1V1z"/><path d="M28.883 10.806l-1.612 6.723h-1.924L27.19 10.8l1.693.006zm6.742 4.395h-1.631l.877-2.253h-2.783l-1.38 2.253H28.95l4.596-10.415a.794.794 0 0 1 .761-.448h1.755l-1.437 10.863zm-6.74-7.175l-.65 1.632-1.25 3.146h2.218l-.068-3.0-.25-1.778zm-6.054 7.175l-1.125-5.53c-.066-.343-.297-.653-.615-.814a7.7 7.7 0 0 0-1.825-.523l.034-.28h3.164c.405 0 .728.307.79.711l.728 3.794 1.973-4.504h1.964l-2.982 6.621-1.105.006zm-7.06-4.672c0-1.824 2.523-1.921 2.523-.446 0 .787-.684 1.082-1.516 1.082h-.652c-.08-.205-.101-.446-.067-.636zm-.365 1.696h.917c1.613 0 2.947-.48 2.947-1.973 0-1.473-1.468-1.605-2.557-1.605H18.1l-.694 3.578zm-.944 2.977L15.93 6.728h-2.047l-2.488 8.056h1.844l.33-1.1h2.13l.195 1.1h1.516zm-2.644-2.656h-1.42l.693-2.134.727 2.134z" fill="#142688"/><path d="M7.064 16.76L10.718 7.2l-1.812-.003c-.6 0-1.104.45-1.189 1.044L4.94 16.757l2.125.003z" fill="#103577"/><path d="M9.424 7.834a.696.696 0 0 0-.66-.636H6.5l-.182.642-.394 1.399 2.233 2.669 1.267-4.074z" fill="#142688"/></svg>
                                    <svg class="h-6 w-auto" viewBox="0 0 36 24" xmlns="http://www.w3.org/2000/svg" role="img" width="36" height="24" aria-labelledby="pi-master"><title id="pi-master">Mastercard</title><path opacity=".07" d="M35 0H1C.45 0 0 .45 0 1v22c0 .55.45 1 1 1h34c.55 0 1-.45 1-1V1c0-.55-.45-1-1-1z"/><path fill="#fff" d="M35 1c0-.55-.45-1-1-1H2c-.55 0-1 .45-1 1v22c0 .55.45 1 1 1h32c.55 0 1-.45 1-1V1z"/><path d="M22.83 11.7h-4.234v7.836h4.234V11.7z" fill="#FF5F00"/><path d="M19.063 15.068a4.973 4.973 0 0 1 1.905-3.924 4.973 4.973 0 0 0-7.752 0 4.973 4.973 0 0 0 0 7.848 4.973 4.973 0 0 0 7.752 0 4.973 4.973 0 0 1-1.905-3.924z" fill="#EB001B"/><path d="M30.767 15.068a4.973 4.973 0 0 1-1.905 3.924 4.973 4.973 0 0 1-3.876 0 4.971 4.971 0 0 0 0-7.848 4.973 4.973 0 0 1 3.876 0 4.973 4.973 0 0 1 1.905 3.924z" fill="#F79E1B"/></svg>
                                </div>
                            </div>
                        </div>
                        
                        <div class="relative bg-white border rounded-md p-4 flex">
                            <div class="flex items-center h-5">
                                <input id="payment-blik" name="payment-method" type="radio" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300">
                            </div>
                            <div class="ml-3 flex flex-col">
                                <label for="payment-blik" class="text-sm font-medium text-gray-900">BLIK</label>
                                <span class="text-sm text-gray-500">Szybka płatność mobilna</span>
                            </div>
                        </div>
                        
                        <div class="relative bg-white border rounded-md p-4 flex">
                            <div class="flex items-center h-5">
                                <input id="payment-transfer" name="payment-method" type="radio" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300">
                            </div>
                            <div class="ml-3 flex flex-col">
                                <label for="payment-transfer" class="text-sm font-medium text-gray-900">Szybki przelew</label>
                                <span class="text-sm text-gray-500">mTransfer, iPKO, Inteligo, Santander i inne</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-8">
                        <div id="card-payment-form" class="space-y-4">
                            <div>
                                <label for="card-number" class="block text-sm font-medium text-gray-700">Numer karty</label>
                                <input type="text" id="card-number" name="card-number" placeholder="0000 0000 0000 0000" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            </div>
                            
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="expiration" class="block text-sm font-medium text-gray-700">Data ważności</label>
                                    <input type="text" id="expiration" name="expiration" placeholder="MM/RR" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                </div>
                                
                                <div>
                                    <label for="cvc" class="block text-sm font-medium text-gray-700">Kod CVC</label>
                                    <input type="text" id="cvc" name="cvc" placeholder="123" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                </div>
                            </div>
                            
                            <div>
                                <label for="cardholder" class="block text-sm font-medium text-gray-700">Imię i nazwisko na karcie</label>
                                <input type="text" id="cardholder" name="cardholder" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="mt-8 border-t border-gray-200 pt-8">
                    <form action="{{ route('payment.complete', ['order' => $order->id]) }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full bg-indigo-600 border border-transparent rounded-md py-3 px-4 text-base font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Zapłać {{ number_format($order->total, 2) }} zł
                        </button>
                    </form>
                    
                    <div class="mt-4">
                        <form action="{{ route('payment.cancel', ['order' => $order->id]) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full bg-white border border-gray-300 rounded-md py-3 px-4 text-base font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Anuluj płatność
                            </button>
                        </form>
                    </div>
                </div>
                
                <div class="mt-8 text-center">
                    <p class="text-sm text-gray-500">
                        Płatności są bezpieczne i szyfrowane. Dane karty nie są przechowywane na naszych serwerach.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 