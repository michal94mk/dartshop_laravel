@extends('layouts.app')

@section('content')
<div class="bg-white">
    <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
        <nav class="flex mb-8" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('home') }}" class="text-gray-700 hover:text-indigo-600 inline-flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                        </svg>
                        Strona główna
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <a href="{{ route('frontend.categories.index') }}" class="text-gray-700 hover:text-indigo-600 ml-1 md:ml-2 text-sm font-medium">Produkty</a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-gray-500 ml-1 md:ml-2 text-sm font-medium">{{ $product->name }}</span>
                    </div>
                </li>
            </ol>
        </nav>

        <div class="lg:grid lg:grid-cols-2 lg:gap-x-8 lg:items-start">
            <!-- Product image -->
            <div class="flex flex-col-reverse">
                <div class="w-full aspect-w-1 aspect-h-1">
                    <div class="relative h-96 w-full overflow-hidden rounded-lg">
                        @if($product->image)
                            <img src="{{ asset($product->image) }}" alt="{{ $product->name }}" class="h-full w-full object-cover object-center">
                        @else
                            <div class="h-full w-full bg-gray-200 flex items-center justify-center">
                                <svg class="h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Product details -->
            <div class="mt-10 px-4 sm:px-0 sm:mt-16 lg:mt-0">
                <div class="flex items-center">
                    <span class="bg-indigo-100 text-indigo-800 inline-flex items-center px-3 py-0.5 rounded-full text-sm font-medium">
                        {{ $product->category->name }}
                    </span>
                    @if($product->brand)
                        <span class="ml-2 bg-gray-100 text-gray-800 inline-flex items-center px-3 py-0.5 rounded-full text-sm font-medium">
                            {{ $product->brand->name }}
                        </span>
                    @endif
                </div>
                
                <h1 class="mt-4 text-3xl font-extrabold tracking-tight text-gray-900">{{ $product->name }}</h1>

                <div class="mt-4">
                    <h2 class="sr-only">Opis produktu</h2>
                    <div class="text-base text-gray-700 space-y-6">
                        <p>{{ $product->description }}</p>
                    </div>
                </div>

                <div class="mt-6">
                    <h3 class="sr-only">Cena</h3>
                    <p class="text-3xl font-bold text-gray-900">{{ number_format($product->price, 2) }} zł</p>
                </div>

                <div class="mt-8 flex space-x-4">
                    <form method="POST" action="{{ route('cart.add', $product->id) }}" class="add-to-cart-form" data-product-id="{{ $product->id }}">
                        @csrf
                        <button type="submit" class="flex max-w-xs flex-1 items-center justify-center rounded-md border border-transparent bg-indigo-600 px-8 py-3 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:ring-offset-gray-50 sm:w-full">
                            <svg class="-ml-1 mr-2 h-5 w-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 000-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 3H6.28l-.31-1.243A1 1 0 005 1H3zM16 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM6.5 18a1.5 1.5 0 100-3 1.5 1.5 0 000 3z"></path>
                            </svg>
                            Dodaj do koszyka
                        </button>
                    </form>
                    
                    @auth
                        <form method="POST" action="{{ route('profile.favorites.toggle', $product->id) }}" class="favorite-form">
                            @csrf
                            <button type="submit" class="flex items-center justify-center rounded-md border border-gray-300 bg-white px-4 py-3 text-base font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:ring-offset-gray-50">
                                <svg class="h-6 w-6 {{ Auth::user()->favoriteProducts->contains($product->id) ? 'text-red-500' : 'text-gray-400' }}" fill="currentColor" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                </svg>
                                <span class="sr-only">{{ Auth::user()->favoriteProducts->contains($product->id) ? 'Usuń z ulubionych' : 'Dodaj do ulubionych' }}</span>
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="flex items-center justify-center rounded-md border border-gray-300 bg-white px-4 py-3 text-base font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:ring-offset-gray-50">
                            <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                            </svg>
                            <span class="sr-only">Zaloguj się, aby dodać do ulubionych</span>
                        </a>
                    @endauth
                </div>

                <div class="mt-10 border-t border-gray-200 pt-10">
                    <h3 class="text-sm font-medium text-gray-900">Szczegóły</h3>
                    <div class="mt-4 prose prose-sm text-gray-500">
                        <ul role="list">
                            <li>ID: {{ $product->id }}</li>
                            <li>Kategoria: {{ $product->category->name }}</li>
                            @if($product->brand)
                                <li>Marka: {{ $product->brand->name }}</li>
                            @endif
                            <li>Cena: {{ number_format($product->price, 2) }} zł</li>
                            @if($product->weight && (Str::contains($product->category->name, 'Darts') || Str::contains($product->category->name, 'Lotki')))
                                <li>Waga: {{ number_format($product->weight, 2) }} g</li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reviews section -->
        <div class="mt-16 border-t border-gray-200 pt-10">
            <div class="flex items-center justify-between">
                <h2 class="text-2xl font-extrabold tracking-tight text-gray-900">Recenzje klientów</h2>
                @auth
                    <a href="{{ route('frontend.reviews.create', $product->id) }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Dodaj recenzję
                    </a>
                @else
                    <a href="{{ route('login') }}" class="inline-flex items-center text-sm font-medium text-indigo-600 hover:text-indigo-500">
                        Zaloguj się, aby dodać recenzję
                        <svg class="ml-1 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                    </a>
                @endauth
            </div>
            
            <div class="mt-6 space-y-10 divide-y divide-gray-200 border-b border-gray-200 pb-10">
                @if($product->reviews()->where('is_approved', true)->count() > 0)
                    @foreach($product->reviews()->where('is_approved', true)->orderBy('created_at', 'desc')->get() as $review)
                        <div class="pt-10">
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
                                <p class="ml-2 text-sm font-medium text-gray-900">{{ $review->title }}</p>
                            </div>
                            
                            <div class="mt-4 text-sm text-gray-600">
                                <p>{{ $review->content }}</p>
                            </div>
                            
                            <div class="mt-4 flex items-center">
                                <div class="flex-shrink-0">
                                    <span class="sr-only">{{ $review->user->name }}</span>
                                    <div class="h-8 w-8 rounded-full bg-indigo-600 flex items-center justify-center">
                                        <span class="text-white font-semibold text-xs">{{ strtoupper(substr($review->user->name, 0, 1)) }}</span>
                                    </div>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-900">{{ $review->user->name }}</p>
                                    <time class="text-sm text-gray-500" datetime="{{ $review->created_at->format('Y-m-d') }}">{{ $review->created_at->format('d.m.Y') }}</time>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="py-10 text-center">
                        <p class="text-gray-500">Ten produkt nie ma jeszcze recenzji.</p>
                        <p class="mt-2 text-gray-500">Bądź pierwszy i podziel się swoją opinią!</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Script to handle add to cart AJAX
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('.add-to-cart-form');
        
        if (!form) {
            console.error('Form not found!');
            return;
        }
        
        console.log('Form found, setting up event listener');
        
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            console.log('Form submit intercepted');
            
            const formData = new FormData(form);
            
            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                credentials: 'same-origin'
            })
            .then(response => response.json())
            .then(data => {
                console.log('Response:', data);
                if (data.success) {
                    // Show success notification
                    Swal.fire({
                        title: 'Sukces!',
                        text: 'Produkt został dodany do koszyka',
                        icon: 'success',
                        showConfirmButton: false,
                        timer: 1500
                    });
                    
                    // Update cart count
                    const cartCountElement = document.querySelector('.cart-count');
                    if (cartCountElement && data.cart_count) {
                        cartCountElement.textContent = data.cart_count;
                        cartCountElement.classList.remove('hidden');
                    }
                } else {
                    // Show error notification
                    Swal.fire({
                        title: 'Błąd!',
                        text: data.message || 'Wystąpił błąd podczas dodawania produktu do koszyka',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    title: 'Błąd!',
                    text: 'Wystąpił błąd podczas dodawania produktu do koszyka',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            });
        });
        
        // Handle favorite toggle AJAX
        const favoriteForm = document.querySelector('.favorite-form');
        if (favoriteForm) {
            favoriteForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(favoriteForm);
                
                fetch(favoriteForm.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    credentials: 'same-origin'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update heart icon
                        const heartIcon = favoriteForm.querySelector('svg');
                        if (data.status === 'added') {
                            heartIcon.classList.remove('text-gray-400');
                            heartIcon.classList.add('text-red-500');
                        } else {
                            heartIcon.classList.remove('text-red-500');
                            heartIcon.classList.add('text-gray-400');
                        }
                        
                        // Show notification
                        Swal.fire({
                            text: data.message,
                            icon: 'success',
                            showConfirmButton: false,
                            timer: 1500
                        });
                    } else {
                        Swal.fire({
                            text: data.message || 'Wystąpił błąd',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        text: 'Wystąpił błąd podczas przetwarzania żądania',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                });
            });
        }
    });
</script>
@endpush 