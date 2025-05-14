@extends('layouts.app')

@section('title', 'Promocje')

@section('content')
<div class="bg-white">
    <div class="max-w-7xl mx-auto py-16 px-4 sm:px-6 lg:py-24 lg:px-8">
        <div class="text-center">
            <h1 class="text-4xl font-extrabold tracking-tight text-gray-900">Nasze promocje</h1>
            <p class="mt-4 max-w-3xl mx-auto text-xl text-gray-500">
                Sprawdź nasze aktualne promocje i korzystaj z wyjątkowych okazji.
            </p>
        </div>

        <!-- Current Promotion Banner -->
        <div class="mt-12 bg-indigo-700 rounded-lg shadow-xl overflow-hidden">
            <div class="px-6 py-12 md:px-12 md:py-16 lg:flex lg:items-center lg:justify-between">
                <div>
                    <h2 class="text-2xl font-extrabold tracking-tight text-white sm:text-3xl">
                        <span class="block">Promocja tygodnia</span>
                        <span class="block text-indigo-200">30% rabatu na wszystkie lotki</span>
                    </h2>
                    <p class="mt-4 text-lg text-indigo-100">
                        Tylko do końca tygodnia skorzystaj z wyjątkowej oferty na profesjonalne lotki do dart.
                    </p>
                    <div class="mt-6 flex space-x-4">
                        <div class="flex items-center text-white">
                            <div class="bg-white bg-opacity-25 rounded-lg px-3 py-2 mr-2">
                                <span class="text-xl" id="days">3</span>
                            </div>
                            <span>dni</span>
                        </div>
                        <div class="flex items-center text-white">
                            <div class="bg-white bg-opacity-25 rounded-lg px-3 py-2 mr-2">
                                <span class="text-xl" id="hours">12</span>
                            </div>
                            <span>godzin</span>
                        </div>
                        <div class="flex items-center text-white">
                            <div class="bg-white bg-opacity-25 rounded-lg px-3 py-2 mr-2">
                                <span class="text-xl" id="minutes">24</span>
                            </div>
                            <span>minut</span>
                        </div>
                    </div>
                </div>
                <div class="mt-8 flex lg:mt-0 lg:flex-shrink-0">
                    <div class="inline-flex rounded-md shadow">
                        <a href="{{ route('frontend.categories.index') }}?category=flights" class="inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-indigo-600 bg-white hover:bg-indigo-50">
                            Sprawdź teraz
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Other Promotions -->
        <div class="mt-16 grid gap-8 md:grid-cols-2 lg:grid-cols-3">
            <!-- Promotion 1 -->
            <div class="bg-gray-50 rounded-lg overflow-hidden shadow-md flex flex-col">
                <div class="px-6 py-8 flex-1">
                    <div class="bg-indigo-100 inline-block rounded-full px-3 py-1 text-sm font-semibold text-indigo-700 mb-4">
                        ZESTAW
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900">Zestaw startowy dla początkujących</h3>
                    <p class="mt-2 text-gray-600">
                        Kompletny zestaw dla początkujących w atrakcyjnej cenie. Zawiera tarcze, lotki i podstawowe akcesoria.
                    </p>
                    <div class="mt-4">
                        <span class="text-lg font-bold text-indigo-600">199,99 zł</span>
                        <span class="ml-2 text-sm line-through text-gray-500">269,99 zł</span>
                    </div>
                </div>
                <div class="px-6 pb-6">
                    <a href="{{ route('frontend.categories.index') }}" class="block w-full bg-indigo-600 text-white text-center py-2 rounded-md hover:bg-indigo-700 transition-colors">
                        Zobacz więcej
                    </a>
                </div>
            </div>

            <!-- Promotion 2 -->
            <div class="bg-gray-50 rounded-lg overflow-hidden shadow-md flex flex-col">
                <div class="px-6 py-8 flex-1">
                    <div class="bg-red-100 inline-block rounded-full px-3 py-1 text-sm font-semibold text-red-700 mb-4">
                        WYPRZEDAŻ
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900">Tarcze do dart - wyprzedaż kolekcji</h3>
                    <p class="mt-2 text-gray-600">
                        Wyprzedaż profesjonalnych tarcz do dart z poprzedniej kolekcji. Zniżki do 40%.
                    </p>
                    <div class="mt-4">
                        <span class="text-lg font-bold text-red-600">od 49,99 zł</span>
                    </div>
                </div>
                <div class="px-6 pb-6">
                    <a href="{{ route('frontend.categories.index') }}" class="block w-full bg-red-600 text-white text-center py-2 rounded-md hover:bg-red-700 transition-colors">
                        Zobacz więcej
                    </a>
                </div>
            </div>

            <!-- Promotion 3 -->
            <div class="bg-gray-50 rounded-lg overflow-hidden shadow-md flex flex-col">
                <div class="px-6 py-8 flex-1">
                    <div class="bg-amber-100 inline-block rounded-full px-3 py-1 text-sm font-semibold text-amber-700 mb-4">
                        2+1 GRATIS
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900">Kup 2 zestawy lotek, trzeci gratis</h3>
                    <p class="mt-2 text-gray-600">
                        Przy zakupie dwóch dowolnych zestawów lotek, trzeci (najtańszy) otrzymasz gratis.
                    </p>
                    <div class="mt-4">
                        <span class="text-lg font-bold text-amber-600">Oszczędzasz do 40 zł</span>
                    </div>
                </div>
                <div class="px-6 pb-6">
                    <a href="{{ route('frontend.categories.index') }}" class="block w-full bg-amber-600 text-white text-center py-2 rounded-md hover:bg-amber-700 transition-colors">
                        Zobacz więcej
                    </a>
                </div>
            </div>
        </div>

        <!-- Newsletter for Promotions -->
        <div class="mt-16 bg-gray-50 rounded-lg p-8">
            <div class="max-w-md mx-auto sm:max-w-xl lg:max-w-5xl lg:flex lg:items-center lg:justify-between">
                <div>
                    <h3 class="text-xl font-extrabold text-gray-900 sm:text-2xl">Zapisz się do newslettera</h3>
                    <p class="mt-2 text-sm text-gray-500">
                        Bądź na bieżąco z promocjami i zniżkami. Otrzymasz powiadomienie o nowych ofertach.
                    </p>
                </div>
                <form class="mt-4 sm:flex sm:w-full sm:max-w-lg lg:mt-0">
                    <label for="email-address" class="sr-only">Adres e-mail</label>
                    <input id="email-address" name="email" type="email" autocomplete="email" required class="w-full min-w-0 appearance-none rounded-md border border-gray-300 bg-white py-2 px-4 text-base text-gray-900 placeholder-gray-500 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500" placeholder="Wpisz swój adres e-mail">
                    <div class="mt-3 rounded-md sm:mt-0 sm:ml-3 sm:flex-shrink-0">
                        <button type="submit" class="flex w-full items-center justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-base font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                            Zapisz się
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Simple countdown timer
    document.addEventListener('DOMContentLoaded', function() {
        // Set the countdown date (3 days from now)
        const countdownDate = new Date();
        countdownDate.setDate(countdownDate.getDate() + 3);
        
        function updateCountdown() {
            const now = new Date().getTime();
            const distance = countdownDate - now;
            
            // Calculate days, hours, minutes
            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            
            // Update the countdown
            document.getElementById('days').textContent = days;
            document.getElementById('hours').textContent = hours;
            document.getElementById('minutes').textContent = minutes;
            
            // If the countdown is over
            if (distance < 0) {
                document.getElementById('days').textContent = '0';
                document.getElementById('hours').textContent = '0';
                document.getElementById('minutes').textContent = '0';
            }
        }
        
        // Update countdown every minute
        updateCountdown();
        setInterval(updateCountdown, 60000);
    });
</script>
@endpush 