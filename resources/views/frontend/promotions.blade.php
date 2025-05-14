@extends('layouts.app')

@section('content')
<div class="container mx-auto py-8 px-4">
    <h1 class="text-3xl font-bold mb-8 text-center">Promocje</h1>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
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
    
    <div class="mt-12">
        <h2 class="text-2xl font-bold mb-6">Aktualne promocje sezonowe</h2>
        
        <div class="bg-white rounded-lg shadow-lg overflow-hidden mb-8">
            <div class="md:flex">
                <div class="md:w-1/3 bg-gray-800 text-white p-6">
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