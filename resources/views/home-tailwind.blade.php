@extends('layouts.tailwind')

@section('title', 'Strona główna')

@section('content')
    <!-- Hero Section -->
    <div class="relative bg-indigo-700">
        <div class="absolute inset-0">
            <img src="https://images.unsplash.com/photo-1557406966-8e0b531596e8?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1024&q=80" alt="Dart background" class="w-full h-full object-cover opacity-30">
            <div class="absolute inset-0 bg-indigo-700 mix-blend-multiply" aria-hidden="true"></div>
        </div>
        <div class="relative max-w-7xl mx-auto py-24 px-4 sm:py-32 sm:px-6 lg:px-8">
            <h1 class="text-4xl font-extrabold tracking-tight text-white sm:text-5xl lg:text-6xl">DartShop</h1>
            <p class="mt-6 text-xl text-indigo-100 max-w-3xl">
                Profesjonalny sklep z akcesoriami do gry w dart. Najwyższa jakość, najlepsze marki, konkurencyjne ceny.
            </p>
            <div class="mt-10">
                <a href="{{ route('frontend.categories.index') }}" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-indigo-700 bg-white hover:bg-indigo-50">
                    Sprawdź ofertę
                </a>
            </div>
        </div>
    </div>

    <!-- Newest Products Section -->
    <div class="bg-white">
        <div class="max-w-2xl mx-auto py-16 px-4 sm:py-24 sm:px-6 lg:max-w-7xl lg:px-8">
            <div class="flex items-center justify-between space-x-4">
                <h2 class="text-2xl font-extrabold tracking-tight text-gray-900">Najnowsze produkty</h2>
                <a href="{{ route('frontend.categories.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">
                    Zobacz wszystkie<span aria-hidden="true"> &rarr;</span>
                </a>
            </div>

            <div class="mt-6 grid grid-cols-1 gap-x-8 gap-y-8 sm:grid-cols-2 sm:gap-y-10 lg:grid-cols-4">
                @foreach($newestProducts as $product)
                    <div class="group">
                        <x-product-card :product="$product" />
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Discount Banner -->
    <div class="bg-indigo-700">
        <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:py-16 lg:px-8 lg:flex lg:items-center lg:justify-between">
            <h2 class="text-3xl font-extrabold tracking-tight text-white sm:text-4xl">
                <span class="block">Promocja tygodnia</span>
                <span class="block text-indigo-200">30% rabatu na wszystkie lotki</span>
            </h2>
            <div class="mt-8 flex lg:mt-0 lg:flex-shrink-0">
                <div class="inline-flex rounded-md shadow">
                    <a href="{{ route('frontend.categories.index') }}" class="inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-indigo-600 bg-white hover:bg-indigo-50">
                        Sprawdź teraz
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Featured Categories -->
    <div class="bg-gray-50">
        <div class="max-w-7xl mx-auto py-16 px-4 sm:py-24 sm:px-6 lg:px-8">
            <div class="sm:flex sm:items-baseline sm:justify-between">
                <h2 class="text-2xl font-extrabold tracking-tight text-gray-900">Kategorie produktów</h2>
                <a href="{{ route('frontend.categories.index') }}" class="hidden text-sm font-semibold text-indigo-600 hover:text-indigo-500 sm:block">
                    Zobacz wszystkie kategorie<span aria-hidden="true"> &rarr;</span>
                </a>
            </div>

            <div class="mt-6 grid grid-cols-1 gap-y-6 sm:grid-cols-2 sm:grid-rows-2 sm:gap-x-6 lg:gap-8">
                <div class="group aspect-h-1 aspect-w-2 overflow-hidden rounded-lg sm:aspect-h-1 sm:aspect-w-1 sm:row-span-2">
                    <img src="https://images.unsplash.com/photo-1637627328577-651925a992cc?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80" alt="Lotki" class="object-cover object-center group-hover:opacity-75">
                    <div aria-hidden="true" class="bg-gradient-to-b from-transparent to-black opacity-50"></div>
                    <div class="flex items-end p-6">
                        <div>
                            <h3 class="font-semibold text-white">
                                <a href="#">
                                    <span class="absolute inset-0"></span>
                                    Lotki
                                </a>
                            </h3>
                            <p aria-hidden="true" class="mt-1 text-sm text-white">Zobacz produkty</p>
                        </div>
                    </div>
                </div>
                <div class="group aspect-h-1 aspect-w-2 overflow-hidden rounded-lg sm:aspect-none sm:relative sm:h-full">
                    <img src="https://images.unsplash.com/photo-1615486780246-76d9ee78b221?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80" alt="Tarcze do darta" class="object-cover object-center group-hover:opacity-75 sm:absolute sm:inset-0 sm:h-full sm:w-full">
                    <div aria-hidden="true" class="bg-gradient-to-b from-transparent to-black opacity-50 sm:absolute sm:inset-0"></div>
                    <div class="flex items-end p-6 sm:absolute sm:inset-0">
                        <div>
                            <h3 class="font-semibold text-white">
                                <a href="#">
                                    <span class="absolute inset-0"></span>
                                    Tarcze
                                </a>
                            </h3>
                            <p aria-hidden="true" class="mt-1 text-sm text-white">Zobacz produkty</p>
                        </div>
                    </div>
                </div>
                <div class="group aspect-h-1 aspect-w-2 overflow-hidden rounded-lg sm:aspect-none sm:relative sm:h-full">
                    <img src="https://images.unsplash.com/photo-1610464526017-5f0a2c4c0819?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80" alt="Akcesoria do darta" class="object-cover object-center group-hover:opacity-75 sm:absolute sm:inset-0 sm:h-full sm:w-full">
                    <div aria-hidden="true" class="bg-gradient-to-b from-transparent to-black opacity-50 sm:absolute sm:inset-0"></div>
                    <div class="flex items-end p-6 sm:absolute sm:inset-0">
                        <div>
                            <h3 class="font-semibold text-white">
                                <a href="#">
                                    <span class="absolute inset-0"></span>
                                    Akcesoria
                                </a>
                            </h3>
                            <p aria-hidden="true" class="mt-1 text-sm text-white">Zobacz produkty</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-6 sm:hidden">
                <a href="{{ route('frontend.categories.index') }}" class="block text-sm font-semibold text-indigo-600 hover:text-indigo-500">
                    Zobacz wszystkie kategorie<span aria-hidden="true"> &rarr;</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Newsletter Section -->
    <div class="bg-white">
        <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:py-16 lg:px-8">
            <div class="bg-indigo-50 rounded-lg p-8">
                <div class="max-w-md mx-auto sm:max-w-xl lg:max-w-none lg:flex lg:items-center lg:justify-between">
                    <div>
                        <h3 class="text-xl font-extrabold text-gray-900 sm:text-2xl">Zapisz się do newslettera</h3>
                        <p class="mt-2 text-sm text-gray-500">
                            Bądź na bieżąco z nowościami, promocjami i wiedza o dartach.
                        </p>
                    </div>
                    <form class="mt-4 sm:flex sm:w-full sm:max-w-md lg:mt-0">
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
    // Script to handle add to cart AJAX
    document.addEventListener('DOMContentLoaded', function() {
        const forms = document.querySelectorAll('.add-to-cart-form');
        
        forms.forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(form);
                const url = form.getAttribute('action');
                
                fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    // Update cart count
                    const cartCount = document.getElementById('cart-count');
                    if (cartCount) {
                        cartCount.textContent = data.total_quantity || 0;
                    }
                    
                    // Show temporary success message
                    const button = form.querySelector('button');
                    const originalText = button.innerHTML;
                    button.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg> Dodano!';
                    button.classList.remove('bg-indigo-600', 'hover:bg-indigo-700');
                    button.classList.add('bg-green-600', 'hover:bg-green-700');
                    
                    setTimeout(() => {
                        button.innerHTML = originalText;
                        button.classList.remove('bg-green-600', 'hover:bg-green-700');
                        button.classList.add('bg-indigo-600', 'hover:bg-indigo-700');
                    }, 2000);
                })
                .catch(error => console.error('Error:', error));
            });
        });
    });
</script>
@endpush 