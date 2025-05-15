@extends('layouts.app')

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
            <div class="mt-10 flex flex-wrap gap-4">
                <a href="{{ route('frontend.categories.index') }}" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-indigo-700 bg-white hover:bg-indigo-50">
                    Sprawdź ofertę
                </a>
                @guest
                    <a href="{{ route('login') }}" class="inline-flex items-center px-6 py-3 border border-white text-base font-medium rounded-md text-white hover:bg-indigo-600">
                        Logowanie
                    </a>
                    <a href="{{ route('register') }}" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-indigo-700 bg-white hover:bg-indigo-50">
                        Rejestracja
                    </a>
                @endguest
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

    <!-- Top Rated Products Section -->
    @if(isset($topRatedProducts) && $topRatedProducts->count() > 0)
    <div class="bg-gray-50">
        <div class="max-w-2xl mx-auto py-16 px-4 sm:py-24 sm:px-6 lg:max-w-7xl lg:px-8">
            <div class="flex items-center justify-between space-x-4">
                <h2 class="text-2xl font-extrabold tracking-tight text-gray-900">Najlepiej oceniane produkty</h2>
                <a href="{{ route('frontend.categories.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">
                    Zobacz wszystkie<span aria-hidden="true"> &rarr;</span>
                </a>
            </div>

            <div class="mt-6 grid grid-cols-1 gap-x-8 gap-y-8 sm:grid-cols-2 sm:gap-y-10 lg:grid-cols-4">
                @foreach($topRatedProducts as $product)
                    <div class="group">
                        <x-product-card :product="$product" />
                        <div class="mt-2 flex items-center">
                            <div class="flex items-center">
                                @for ($i = 1; $i <= 5; $i++)
                                    @if ($i <= number_format($product->average_rating, 0))
                                        <svg class="h-5 w-5 text-yellow-500" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                        </svg>
                                    @else
                                        <svg class="h-5 w-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                        </svg>
                                    @endif
                                @endfor
                            </div>
                            <span class="ml-2 text-sm text-gray-500">
                                ({{ number_format($product->average_rating, 1) }}) z {{ $product->review_count }} {{ Str::plural('oceny', $product->review_count) }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- Active Promotions Section -->
    @if(isset($activePromotions) && $activePromotions->count() > 0)
    <div class="bg-gray-50">
        <div class="max-w-2xl mx-auto py-16 px-4 sm:py-24 sm:px-6 lg:max-w-7xl lg:px-8">
            <div class="flex items-center justify-between space-x-4">
                <h2 class="text-2xl font-extrabold tracking-tight text-gray-900">Aktualne promocje</h2>
                <a href="{{ route('frontend.promotions') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">
                    Zobacz wszystkie<span aria-hidden="true"> &rarr;</span>
                </a>
            </div>

            <div class="mt-6 grid grid-cols-1 gap-x-8 gap-y-8 sm:grid-cols-2 sm:gap-y-10 lg:grid-cols-4">
                @foreach($activePromotions as $promotion)
                    <x-promotion-card :promotion="$promotion" />
                @endforeach
            </div>
        </div>
    </div>
    @endif

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

    <!-- Customer Reviews -->
    @if(isset($featuredReviews) && $featuredReviews->count() > 0)
    <div class="bg-white">
        <div class="max-w-7xl mx-auto py-16 px-4 sm:py-24 sm:px-6 lg:px-8">
            <div class="text-center">
                <h2 class="text-3xl font-extrabold tracking-tight text-gray-900 sm:text-4xl">
                    Co mówią nasi klienci
                </h2>
                <p class="mt-3 max-w-2xl mx-auto text-xl text-gray-500 sm:mt-4">
                    Opinie prawdziwych graczy, którzy korzystają z produktów kupionych w naszym sklepie
                </p>
            </div>
            
            <div class="mt-12 max-w-lg mx-auto grid gap-5 lg:grid-cols-3 lg:max-w-none">
                @foreach($featuredReviews as $review)
                <div class="flex flex-col rounded-lg shadow-lg overflow-hidden transition-all duration-200 transform hover:-translate-y-1 hover:shadow-xl">
                    <div class="flex-1 bg-white p-6 flex flex-col justify-between">
                        <div class="flex-1">
                            <div class="flex items-center">
                                <div class="flex items-center">
                                    @for ($i = 1; $i <= 5; $i++)
                                        @if ($i <= $review->rating)
                                            <svg class="h-5 w-5 text-yellow-500" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                            </svg>
                                        @else
                                            <svg class="h-5 w-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                            </svg>
                                        @endif
                                    @endfor
                                </div>
                            </div>
                            <a href="{{ route('frontend.products.show', $review->product_id) }}" class="block mt-2">
                                <p class="text-xl font-semibold text-gray-900">{{ $review->title }}</p>
                                <p class="mt-3 text-base text-gray-500">{{ $review->content }}</p>
                            </a>
                        </div>
                        <div class="mt-6 flex items-center">
                            <div class="flex-shrink-0">
                                <span class="sr-only">{{ $review->user->name }}</span>
                                <div class="h-10 w-10 rounded-full bg-indigo-600 flex items-center justify-center">
                                    <span class="text-white font-semibold">{{ strtoupper(substr($review->user->name, 0, 1)) }}</span>
                                </div>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900">
                                    {{ $review->user->name }}
                                </p>
                                <div class="flex space-x-1 text-sm text-gray-500">
                                    <span>{{ $review->created_at->diffForHumans() }}</span>
                                    <span aria-hidden="true">&middot;</span>
                                    <a href="{{ route('frontend.products.show', $review->product_id) }}" class="text-indigo-600 hover:text-indigo-500">
                                        {{ Str::limit($review->product->name, 20) }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            
            <div class="mt-10 text-center">
                <a href="{{ route('frontend.categories.index') }}" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Sprawdź wszystkie produkty
                </a>
            </div>
        </div>
    </div>
    @endif

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
        
        if (forms.length === 0) {
            console.error('No add to cart forms found on the page');
            return;
        }
        
        console.log('Found', forms.length, 'add to cart forms');
        
        forms.forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                console.log('Form submit intercepted');
                
                const formData = new FormData(form);
                const url = form.getAttribute('action');
                const button = form.querySelector('button');
                const originalText = button.innerHTML;
                
                // Disable button while processing
                button.disabled = true;
                button.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';
                
                // Przygotuj nagłówki
                const headers = {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                };
                
                // Dodaj nagłówek X-Requested-With tylko gdy używamy AJAX
                if (typeof XMLHttpRequest !== 'undefined') {
                    headers['X-Requested-With'] = 'XMLHttpRequest';
                }
                
                fetch(url, {
                    method: 'POST',
                    headers: headers,
                    body: formData
                })
                .then(response => {
                    console.log('Response received', response);
                    return response.json();
                })
                .then(data => {
                    console.log('Data received', data);
                    // Update cart count
                    const cartCount = document.getElementById('cart-count');
                    if (cartCount && data.total_quantity) {
                        cartCount.textContent = data.total_quantity;
                    }
                    
                    // Show temporary success message
                    button.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>';
                    button.classList.remove('bg-indigo-600', 'hover:bg-indigo-700');
                    button.classList.add('bg-green-600', 'hover:bg-green-700');
                    
                    // Create a notification
                    const notification = document.createElement('div');
                    notification.className = 'fixed top-4 right-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded max-w-md shadow-lg z-50 transition-opacity';
                    notification.innerHTML = `
                        <div class="flex items-center">
                            <svg class="h-6 w-6 text-green-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <strong class="font-bold mr-2">Sukces!</strong>
                            <span class="block">Produkt został dodany do koszyka!</span>
                        </div>
                    `;
                    document.body.appendChild(notification);
                    
                    // Remove notification after 3 seconds
                    setTimeout(() => {
                        notification.style.opacity = '0';
                        setTimeout(() => {
                            notification.remove();
                        }, 300);
                    }, 3000);
                    
                    // Reset button after 2 seconds
                    setTimeout(() => {
                        button.innerHTML = originalText;
                        button.classList.remove('bg-green-600', 'hover:bg-green-700');
                        button.classList.add('bg-indigo-600', 'hover:bg-indigo-700');
                        button.disabled = false;
                    }, 2000);
                })
                .catch(error => {
                    console.error('Error:', error);
                    
                    // Reset button
                    button.innerHTML = originalText;
                    button.disabled = false;
                    
                    // Show error notification
                    const notification = document.createElement('div');
                    notification.className = 'fixed top-4 right-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded max-w-md shadow-lg z-50';
                    notification.innerHTML = `
                        <div class="flex items-center">
                            <svg class="h-6 w-6 text-red-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            <strong class="font-bold mr-2">Błąd!</strong>
                            <span class="block">Nie udało się dodać produktu do koszyka.</span>
                        </div>
                    `;
                    document.body.appendChild(notification);
                    
                    // Remove notification after 3 seconds
                    setTimeout(() => {
                        notification.style.opacity = '0';
                        setTimeout(() => {
                            notification.remove();
                        }, 300);
                    }, 3000);
                });
            });
        });
    });
</script>
@endpush 