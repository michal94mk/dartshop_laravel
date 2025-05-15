@extends('layouts.app')

@section('title', 'Płatność')

@section('content')
<div class="bg-gray-50 min-h-screen py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="px-6 py-8 bg-indigo-600 sm:p-10 sm:pb-8">
                <div class="flex justify-between items-center flex-wrap">
                    <div>
                        <h1 class="text-2xl font-extrabold text-white">Płatność za zamówienie</h1>
                        <p class="mt-2 text-base text-indigo-100">Numer zamówienia: <span class="font-medium">{{ $order->order_number }}</span></p>
                    </div>
                    <div class="mt-4 sm:mt-0">
                        <p class="text-xl font-bold text-white">{{ number_format($order->total, 2) }} zł</p>
                    </div>
                </div>
            </div>
            
            <div class="px-6 pt-6 pb-8 sm:p-10">
                <div class="space-y-6">
                    <fieldset>
                        <legend class="text-lg font-medium text-gray-900">Wybierz metodę płatności</legend>
                        <div class="mt-4 grid gap-y-4">
                            <label class="relative rounded-lg border bg-white p-4 shadow-sm cursor-pointer focus-within:ring-2 focus-within:ring-indigo-500 hover:border-gray-400 transition-colors">
                                <input type="radio" name="payment-method" value="card" class="sr-only" checked>
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <span class="inline-flex items-center justify-center h-12 w-12 rounded-md bg-indigo-100 text-indigo-600">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                            </svg>
                                        </span>
                                    </div>
                                    <div class="ml-4 flex-1">
                                        <span class="block text-sm font-medium text-gray-900">Karta płatnicza</span>
                                        <span class="block text-sm text-gray-500">Visa, Mastercard, American Express</span>
                                    </div>
                                    <div class="ml-4 flex-shrink-0">
                                        <span class="block text-sm font-medium text-indigo-600">
                                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                            </svg>
                                        </span>
                                    </div>
                                </div>
                                <div class="mt-4 flex space-x-2">
                                    <svg class="h-8 w-auto" viewBox="0 0 36 24" xmlns="http://www.w3.org/2000/svg" role="img" width="36" height="24" aria-labelledby="pi-visa"><title id="pi-visa">Visa</title><path opacity=".07" d="M35 0H1C.45 0 0 .45 0 1v22c0 .55.45 1 1 1h34c.55 0 1-.45 1-1V1c0-.55-.45-1-1-1z"/><path fill="#fff" d="M35 1c0-.55-.45-1-1-1H2c-.55 0-1 .45-1 1v22c0 .55.45 1 1 1h32c.55 0 1-.45 1-1V1z"/><path d="M28.883 10.806l-1.612 6.723h-1.924L27.19 10.8l1.693.006zm6.742 4.395h-1.631l.877-2.253h-2.783l-1.38 2.253H28.95l4.596-10.415a.794.794 0 0 1 .761-.448h1.755l-1.437 10.863zm-6.74-7.175l-.65 1.632-1.25 3.146h2.218l-.068-3.0-.25-1.778zm-6.054 7.175l-1.125-5.53c-.066-.343-.297-.653-.615-.814a7.7 7.7 0 0 0-1.825-.523l.034-.28h3.164c.405 0 .728.307.79.711l.728 3.794 1.973-4.504h1.964l-2.982 6.621-1.105.006zm-7.06-4.672c0-1.824 2.523-1.921 2.523-.446 0 .787-.684 1.082-1.516 1.082h-.652c-.08-.205-.101-.446-.067-.636zm-.365 1.696h.917c1.613 0 2.947-.48 2.947-1.973 0-1.473-1.468-1.605-2.557-1.605H18.1l-.694 3.578zm-.944 2.977L15.93 6.728h-2.047l-2.488 8.056h1.844l.33-1.1h2.13l.195 1.1h1.516zm-2.644-2.656h-1.42l.693-2.134.727 2.134z" fill="#142688"/><path d="M7.064 16.76L10.718 7.2l-1.812-.003c-.6 0-1.104.45-1.189 1.044L4.94 16.757l2.125.003z" fill="#103577"/><path d="M9.424 7.834a.696.696 0 0 0-.66-.636H6.5l-.182.642-.394 1.399 2.233 2.669 1.267-4.074z" fill="#142688"/></svg>
                                    <svg class="h-8 w-auto" viewBox="0 0 36 24" xmlns="http://www.w3.org/2000/svg" role="img" width="36" height="24" aria-labelledby="pi-master"><title id="pi-master">Mastercard</title><path opacity=".07" d="M35 0H1C.45 0 0 .45 0 1v22c0 .55.45 1 1 1h34c.55 0 1-.45 1-1V1c0-.55-.45-1-1-1z"/><path fill="#fff" d="M35 1c0-.55-.45-1-1-1H2c-.55 0-1 .45-1 1v22c0 .55.45 1 1 1h32c.55 0 1-.45 1-1V1z"/><path d="M22.83 11.7h-4.234v7.836h4.234V11.7z" fill="#FF5F00"/><path d="M19.063 15.068a4.973 4.973 0 0 1 1.905-3.924 4.973 4.973 0 0 0-7.752 0 4.973 4.973 0 0 0 0 7.848 4.973 4.973 0 0 0 7.752 0 4.973 4.973 0 0 1-1.905-3.924z" fill="#EB001B"/><path d="M30.767 15.068a4.973 4.973 0 0 1-1.905 3.924 4.973 4.973 0 0 1-3.876 0 4.971 4.971 0 0 0 0-7.848 4.973 4.973 0 0 1 3.876 0 4.973 4.973 0 0 1 1.905 3.924z" fill="#F79E1B"/></svg>
                                    <svg class="h-8 w-auto" viewBox="0 0 36 24" xmlns="http://www.w3.org/2000/svg" role="img" width="36" height="24" aria-labelledby="pi-amex"><title id="pi-amex">American Express</title><path fill="#000" d="M35 0H1C.45 0 0 .45 0 1v22c0 .55.45 1 1 1h34c.55 0 1-.45 1-1V1c0-.55-.45-1-1-1z" opacity=".07"/><path d="M35 1v22c0 .55-.45 1-1 1H2c-.55 0-1-.45-1-1V1c0-.55.45-1 1-1h32c.55 0 1 .45 1 1z" fill="#fff"/><path d="M22.012 6.703h2.97v9.5h-2.97v-9.5zm10.488 9.5h-3.498l-1.786-2.98-1.785 2.98h-8.32v-9.5h8.595l1.785 2.915L29.55 6.705H33l-4.623 4.617L33 16.203h-.5zm-10.82-2.26v-1.027h3.12v-1.834h-3.12V8.812h3.59l1.532 2.413-1.69 2.72h-3.43zm-5.53-3.13v5.39h5.132l1.67-2.544v-.008l-1.67-2.838h-5.134zm0-4.11v2.353h4.715v1.783h-4.713v2.344l5.946.008 1.487-2.353-1.487-2.348V6.705h-5.95z" fill="#006FCF"/></svg>
                                </div>
                            </label>
                            
                            <label class="relative rounded-lg border bg-white p-4 shadow-sm cursor-pointer focus-within:ring-2 focus-within:ring-indigo-500 hover:border-gray-400 transition-colors">
                                <input type="radio" name="payment-method" value="blik" class="sr-only">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <span class="inline-flex items-center justify-center h-12 w-12 rounded-md bg-yellow-100 text-yellow-700">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                            </svg>
                                        </span>
                                    </div>
                                    <div class="ml-4 flex-1">
                                        <span class="block text-sm font-medium text-gray-900">BLIK</span>
                                        <span class="block text-sm text-gray-500">Szybka płatność mobilna przez aplikację bankową</span>
                                    </div>
                                </div>
                            </label>
                            
                            <label class="relative rounded-lg border bg-white p-4 shadow-sm cursor-pointer focus-within:ring-2 focus-within:ring-indigo-500 hover:border-gray-400 transition-colors">
                                <input type="radio" name="payment-method" value="bank-transfer" class="sr-only">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <span class="inline-flex items-center justify-center h-12 w-12 rounded-md bg-green-100 text-green-700">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3" />
                                            </svg>
                                        </span>
                                    </div>
                                    <div class="ml-4 flex-1">
                                        <span class="block text-sm font-medium text-gray-900">Szybki przelew</span>
                                        <span class="block text-sm text-gray-500">mTransfer, iPKO, Inteligo, Santander i inne</span>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </fieldset>
                    
                    <!-- Formularz danych karty płatniczej -->
                    <div id="card-payment-form" class="mt-8 space-y-4">
                        <div>
                            <label for="card-number" class="block text-sm font-medium text-gray-700">Numer karty</label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                    </svg>
                                </div>
                                <input type="text" id="card-number" name="card-number" placeholder="0000 0000 0000 0000" class="pl-10 focus:ring-indigo-500 focus:border-indigo-500 block w-full border-gray-300 rounded-md">
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="expiration" class="block text-sm font-medium text-gray-700">Data ważności</label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <input type="text" id="expiration" name="expiration" placeholder="MM/RR" class="pl-10 focus:ring-indigo-500 focus:border-indigo-500 block w-full border-gray-300 rounded-md">
                                </div>
                            </div>
                            
                            <div>
                                <label for="cvc" class="block text-sm font-medium text-gray-700">Kod CVV/CVC</label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                        </svg>
                                    </div>
                                    <input type="text" id="cvc" name="cvc" placeholder="123" class="pl-10 focus:ring-indigo-500 focus:border-indigo-500 block w-full border-gray-300 rounded-md">
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <label for="cardholder" class="block text-sm font-medium text-gray-700">Imię i nazwisko na karcie</label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                                <input type="text" id="cardholder" name="cardholder" class="pl-10 focus:ring-indigo-500 focus:border-indigo-500 block w-full border-gray-300 rounded-md">
                            </div>
                        </div>
                    </div>
                    
                    <!-- BLIK kod - domyślnie ukryty -->
                    <div id="blik-payment-form" class="mt-8 hidden">
                        <div>
                            <label for="blik-code" class="block text-sm font-medium text-gray-700">Kod BLIK</label>
                            <div class="mt-1 flex space-x-2">
                                <input type="text" maxlength="1" class="block w-12 h-12 text-center text-xl font-bold border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                <input type="text" maxlength="1" class="block w-12 h-12 text-center text-xl font-bold border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                <input type="text" maxlength="1" class="block w-12 h-12 text-center text-xl font-bold border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                <input type="text" maxlength="1" class="block w-12 h-12 text-center text-xl font-bold border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                <input type="text" maxlength="1" class="block w-12 h-12 text-center text-xl font-bold border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                <input type="text" maxlength="1" class="block w-12 h-12 text-center text-xl font-bold border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                            <p class="mt-2 text-sm text-gray-500">Wpisz 6-cyfrowy kod z aplikacji bankowej</p>
                        </div>
                    </div>
                    
                    <!-- Przyciski akcji -->
                    <div class="mt-8 space-y-3">
                        <form action="{{ route('payment.complete', ['order' => $order->id]) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-base font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Zapłać {{ number_format($order->total, 2) }} zł
                            </button>
                        </form>
                        
                        <form action="{{ route('payment.cancel', ['order' => $order->id]) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full flex justify-center py-3 px-4 border border-gray-300 rounded-md shadow-sm text-base font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                                </svg>
                                Anuluj płatność
                            </button>
                        </form>
                    </div>
                    
                    <!-- Informacja o bezpieczeństwie -->
                    <div class="mt-8 border-t border-gray-200 pt-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-gray-600">
                                    Twoja płatność jest bezpieczna i szyfrowana. Dane karty nie są przechowywane na naszych serwerach.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Skrypt do obsługi przełączania między formularzami płatności
    document.addEventListener('DOMContentLoaded', function() {
        const radioButtons = document.querySelectorAll('input[name="payment-method"]');
        const cardForm = document.getElementById('card-payment-form');
        const blikForm = document.getElementById('blik-payment-form');
        
        radioButtons.forEach(radio => {
            radio.addEventListener('change', function() {
                if (this.value === 'card') {
                    cardForm.classList.remove('hidden');
                    blikForm.classList.add('hidden');
                } else if (this.value === 'blik') {
                    cardForm.classList.add('hidden');
                    blikForm.classList.remove('hidden');
                } else {
                    cardForm.classList.add('hidden');
                    blikForm.classList.add('hidden');
                }
            });
        });
    });
</script>
@endpush

@endsection