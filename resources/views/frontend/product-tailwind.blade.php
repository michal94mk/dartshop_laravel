@extends('layouts.tailwind')

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
                        <a href="{{ route('frontend.categories.index', ['tailwind' => 1]) }}" class="text-gray-700 hover:text-indigo-600 ml-1 md:ml-2 text-sm font-medium">Produkty</a>
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

                <div class="mt-8 flex">
                    <form method="POST" action="{{ route('cart.add', $product->id) }}" class="add-to-cart-form">
                        @csrf
                        <button type="submit" class="flex max-w-xs flex-1 items-center justify-center rounded-md border border-transparent bg-indigo-600 px-8 py-3 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:ring-offset-gray-50 sm:w-full">
                            <svg class="-ml-1 mr-2 h-5 w-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 000-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 3H6.28l-.31-1.243A1 1 0 005 1H3zM16 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM6.5 18a1.5 1.5 0 100-3 1.5 1.5 0 000 3z"></path>
                            </svg>
                            Dodaj do koszyka
                        </button>
                    </form>
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
                        </ul>
                    </div>
                </div>
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
                // Show success message
                alert('Produkt został dodany do koszyka!');
            })
            .catch(error => console.error('Error:', error));
        });
    });
</script>
@endpush 