@extends('layouts.app')

@section('title', 'Kontakt')

@section('content')
<div class="bg-white">
    <div class="max-w-7xl mx-auto py-16 px-4 sm:px-6 lg:py-24 lg:px-8">
        <div class="divide-y-2 divide-gray-200">
            <div class="lg:grid lg:grid-cols-3 lg:gap-8">
                <h2 class="text-2xl font-extrabold text-gray-900 sm:text-3xl">Skontaktuj się z nami</h2>
                <div class="mt-8 grid grid-cols-1 gap-12 sm:grid-cols-2 sm:gap-x-8 sm:gap-y-12 lg:mt-0 lg:col-span-2">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900">Obsługa klienta</h3>
                        <dl class="mt-2 text-base text-gray-500">
                            <div>
                                <dt class="sr-only">Email</dt>
                                <dd>kontakt@dartshop.pl</dd>
                            </div>
                            <div class="mt-1">
                                <dt class="sr-only">Numer telefonu</dt>
                                <dd>+48 123 456 789</dd>
                            </div>
                        </dl>
                    </div>
                    <div>
                        <h3 class="text-lg font-medium text-gray-900">Siedziba firmy</h3>
                        <dl class="mt-2 text-base text-gray-500">
                            <div>
                                <dt class="sr-only">Adres</dt>
                                <dd>ul. Sportowa 10</dd>
                            </div>
                            <div class="mt-1">
                                <dt class="sr-only">Miasto, Kod Pocztowy</dt>
                                <dd>00-001 Warszawa</dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>
            <div class="mt-16 pt-16 lg:grid lg:grid-cols-3 lg:gap-8">
                <h2 class="text-2xl font-extrabold text-gray-900 sm:text-3xl">Formularz kontaktowy</h2>
                <div class="mt-8 lg:mt-0 lg:col-span-2">
                    <form action="{{ route('contact.send') }}" method="POST" class="grid grid-cols-1 gap-y-6 sm:grid-cols-2 sm:gap-x-8">
                        @csrf
                        <div class="sm:col-span-2">
                            <label for="name" class="block text-sm font-medium text-gray-700">Imię i nazwisko</label>
                            <div class="mt-1">
                                <input type="text" name="name" id="name" autocomplete="name" class="py-3 px-4 block w-full shadow-sm focus:ring-indigo-500 focus:border-indigo-500 border-gray-300 rounded-md">
                            </div>
                        </div>
                        <div class="sm:col-span-2">
                            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                            <div class="mt-1">
                                <input id="email" name="email" type="email" autocomplete="email" class="py-3 px-4 block w-full shadow-sm focus:ring-indigo-500 focus:border-indigo-500 border-gray-300 rounded-md">
                            </div>
                        </div>
                        <div class="sm:col-span-2">
                            <label for="phone" class="block text-sm font-medium text-gray-700">Telefon</label>
                            <div class="mt-1">
                                <input type="text" name="phone" id="phone" autocomplete="tel" class="py-3 px-4 block w-full shadow-sm focus:ring-indigo-500 focus:border-indigo-500 border-gray-300 rounded-md">
                            </div>
                        </div>
                        <div class="sm:col-span-2">
                            <label for="subject" class="block text-sm font-medium text-gray-700">Temat</label>
                            <div class="mt-1">
                                <input type="text" name="subject" id="subject" class="py-3 px-4 block w-full shadow-sm focus:ring-indigo-500 focus:border-indigo-500 border-gray-300 rounded-md">
                            </div>
                        </div>
                        <div class="sm:col-span-2">
                            <label for="message" class="block text-sm font-medium text-gray-700">Wiadomość</label>
                            <div class="mt-1">
                                <textarea id="message" name="message" rows="4" class="py-3 px-4 block w-full shadow-sm focus:ring-indigo-500 focus:border-indigo-500 border border-gray-300 rounded-md"></textarea>
                            </div>
                        </div>
                        <div class="sm:col-span-2">
                            <button type="submit" class="w-full inline-flex items-center justify-center px-6 py-3 border border-transparent rounded-md shadow-sm text-base font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Wyślij wiadomość
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Google Maps iframe -->
    <div class="w-full h-96">
        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d156388.35438500522!2d20.92111263441581!3d52.233065479595244!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x471ecc669a869f01%3A0x72f0be2a88ead3fc!2sWarszawa!5e0!3m2!1spl!2spl!4v1683554534943!5m2!1spl!2spl" 
                width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
    </div>
</div>
@endsection 