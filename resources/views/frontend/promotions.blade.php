@extends('layouts.app')

@section('content')
<div class="bg-white">
    <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-extrabold tracking-tight text-gray-900 text-center mb-4">Promocje</h1>
        <p class="text-lg text-gray-500 text-center max-w-3xl mx-auto mb-8">
            Skorzystaj z dostępnych promocji i kodów rabatowych, aby zaoszczędzić na zakupach. Wybierz odpowiedni kod i użyj go w koszyku.
        </p>
        
        @if(session('success'))
            <div class="mb-8 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-8 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif
        
        <!-- Dynamic promotions from database -->
        @if($activePromotions->count() > 0)
        <div class="mb-16">
            <div class="flex items-center justify-between space-x-4 mb-6">
                <h2 class="text-2xl font-extrabold tracking-tight text-gray-900">Kody promocyjne</h2>
                <a href="{{ route('cart.view') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">
                    Przejdź do koszyka<span aria-hidden="true"> &rarr;</span>
                </a>
            </div>

            <div class="grid grid-cols-1 gap-y-10 sm:grid-cols-2 gap-x-6 lg:grid-cols-3 xl:grid-cols-4 xl:gap-x-8">
                @foreach($activePromotions as $promotion)
                    <x-promotion-card :promotion="$promotion" />
                @endforeach
            </div>
        </div>
        @endif
        
        <!-- Static promotional sections -->
        <div class="mb-16">
            <div class="flex items-center justify-between space-x-4 mb-6">
                <h2 class="text-2xl font-extrabold tracking-tight text-gray-900">Aktualne promocje</h2>
                <a href="{{ route('frontend.categories.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">
                    Przejdź do sklepu<span aria-hidden="true"> &rarr;</span>
                </a>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Promotion 1 -->
                <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                    <div class="bg-red-600 text-white p-4">
                        <h2 class="text-xl font-bold">20% zniżki na wszystkie lotki!</h2>
                        <p class="text-sm">Tylko do końca miesiąca</p>
                    </div>
                    <div class="p-6">
                        <p class="mb-4">Skorzystaj z wyjątkowej promocji i kup lotki premium z rabatem 20%.</p>
                        <a href="{{ route('frontend.categories.index') }}" class="inline-block bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 transition">Zobacz ofertę</a>
                    </div>
                </div>
                
                <!-- Promotion 2 -->
                <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                    <div class="bg-blue-600 text-white p-4">
                        <h2 class="text-xl font-bold">Promocja dla nowych klientów</h2>
                        <p class="text-sm">15% rabatu na pierwsze zamówienie</p>
                    </div>
                    <div class="p-6">
                        <p class="mb-4">Zarejestruj się i złóż pierwsze zamówienie, aby otrzymać 15% rabatu.</p>
                        <a href="{{ route('register') }}" class="inline-block bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">Zarejestruj się</a>
                    </div>
                </div>
                
                <!-- Promotion 3 -->
                <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                    <div class="bg-green-600 text-white p-4">
                        <h2 class="text-xl font-bold">Darmowa dostawa</h2>
                        <p class="text-sm">Przy zamówieniach powyżej 200 zł</p>
                    </div>
                    <div class="p-6">
                        <p class="mb-4">Złóż zamówienie na kwotę powyżej 200 zł i otrzymaj darmową dostawę.</p>
                        <a href="{{ route('frontend.categories.index') }}" class="inline-block bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition">Rozpocznij zakupy</a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Featured seasonal promotion -->
        <div class="bg-white rounded-lg shadow-lg overflow-hidden mb-8">
            <div class="md:flex">
                <div class="md:w-1/3 bg-gray-900 text-white p-6">
                    <h3 class="text-xl font-bold mb-2">Kompletny zestaw do gry w darta</h3>
                    <p class="text-xl font-bold text-yellow-400">Oszczędź 30%</p>
                    <p class="mt-4">Promocja ważna do: 31.12.2023</p>
                </div>
                <div class="md:w-2/3 p-6">
                    <p class="mb-4">
                        Przygotuj się do sezonu z profesjonalnym zestawem do gry w darta. W pakiecie otrzymasz:
                    </p>
                    <ul class="list-disc ml-6 mb-4">
                        <li>Profesjonalna tarcza turniejowa</li>
                        <li>Zestaw rzutek premium</li>
                        <li>Tablica do liczenia punktów</li>
                        <li>Dodatkowe lotki</li>
                    </ul>
                    <p class="mb-4">
                        Wszystko czego potrzebujesz, by rozpocząć przygodę z dartem lub podnieść swoje umiejętności na wyższy poziom.
                    </p>
                    <a href="{{ route('frontend.categories.index') }}" class="inline-block bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600 transition">Zobacz szczegóły</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 