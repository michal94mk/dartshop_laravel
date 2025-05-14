@extends('layouts.app')

@section('title', 'Koszyk')

@section('content')
    <div class="bg-white">
        <div class="max-w-7xl mx-auto px-4 py-16 sm:px-6 lg:px-8">
            <h1 class="text-3xl font-extrabold tracking-tight text-gray-900">Twój koszyk</h1>

            @if(count($cart ?? []) > 0)
                <div class="mt-12">
                    <div class="lg:grid lg:grid-cols-12 lg:gap-x-12 xl:gap-x-16">
                        <div class="lg:col-span-7">
                            <h2 class="sr-only">Produkty w koszyku</h2>

                            <ul role="list" class="border-t border-b border-gray-200 divide-y divide-gray-200">
                                @foreach($cart as $productId => $item)
                                    <li class="flex py-6 sm:py-10">
                                        <div class="flex-shrink-0">
                                            <div class="h-24 w-24 sm:h-32 sm:w-32 rounded-md overflow-hidden">
                                                @if($item['product']->image)
                                                    <img src="{{ asset($item['product']->image) }}" alt="{{ $item['product']->name }}" class="h-full w-full object-cover object-center">
                                                @else
                                                    <div class="flex items-center justify-center h-full w-full bg-gray-200">
                                                        <span class="text-gray-400">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                            </svg>
                                                        </span>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="ml-4 flex-1 flex flex-col sm:ml-6">
                                            <div>
                                                <div class="flex justify-between">
                                                    <h3 class="text-lg font-medium text-gray-900">
                                                        {{ $item['product']->name }}
                                                    </h3>
                                                    <p class="ml-4 text-lg font-medium text-gray-900">{{ number_format($item['product']->price * $item['quantity'], 2) }} zł</p>
                                                </div>
                                                <p class="mt-1 text-sm text-gray-500">
                                                    @if($item['product']->category)
                                                        {{ $item['product']->category->name }}
                                                    @endif
                                                    @if($item['product']->brand)
                                                        | {{ $item['product']->brand->name }}
                                                    @endif
                                                </p>
                                            </div>

                                            <div class="mt-4 flex justify-between">
                                                <div class="flex items-center">
                                                    <form action="{{ route('cart.delete', $productId) }}" method="post" class="quantity-form mr-4">
                                                        @csrf
                                                        <button type="submit" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">
                                                            <span class="sr-only">Usuń</span>
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                            </svg>
                                                        </button>
                                                    </form>
                                                    
                                                    <div class="flex items-center border rounded-md">
                                                        <form action="{{ route('cart.delete', $productId) }}" method="post" class="quantity-form">
                                                            @csrf
                                                            <button type="submit" class="px-2 py-1 text-gray-600 hover:text-indigo-600">-</button>
                                                        </form>
                                                        
                                                        <span class="px-4 py-1 text-gray-900">{{ $item['quantity'] }}</span>
                                                        
                                                        <form action="{{ route('cart.add', $productId) }}" method="post" class="quantity-form">
                                                            @csrf
                                                            <button type="submit" class="px-2 py-1 text-gray-600 hover:text-indigo-600">+</button>
                                                        </form>
                                                    </div>
                                                </div>

                                                <p class="ml-4 text-sm text-gray-700">{{ number_format($item['product']->price, 2) }} zł / szt.</p>
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                        <!-- Order summary -->
                        <div class="mt-16 lg:mt-0 lg:col-span-5">
                            <div class="bg-gray-50 rounded-lg px-6 py-6">
                                <h2 class="text-lg font-medium text-gray-900">Podsumowanie zamówienia</h2>

                                <div class="mt-6 space-y-4">
                                    <div class="flex items-center justify-between">
                                        <p class="text-sm text-gray-600">Suma częściowa</p>
                                        <p class="text-sm font-medium text-gray-900">{{ number_format($total, 2) }} zł</p>
                                    </div>
                                    
                                    @if(isset($promotion))
                                    <div class="flex items-center justify-between border-t border-gray-200 pt-4">
                                        <div>
                                            <p class="text-sm text-gray-600">Kod promocyjny</p>
                                            <p class="text-xs text-indigo-600">{{ $promotion['code'] }} 
                                                @if($promotion['discount_type'] == 'percentage')
                                                    ({{ $promotion['discount_value'] }}%)
                                                @else
                                                    ({{ number_format($promotion['discount_value'], 2) }} zł)
                                                @endif
                                            </p>
                                        </div>
                                        <div class="flex items-center">
                                            <p class="text-sm font-medium text-red-600">-{{ number_format($discountAmount, 2) }} zł</p>
                                            <form action="{{ route('cart.promotion.remove') }}" method="post" class="promotion-remove-form ml-2">
                                                @csrf
                                                <button type="submit" class="text-gray-400 hover:text-red-600">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                    @else
                                    <div class="flex items-center justify-between border-t border-gray-200 pt-4">
                                        <form action="{{ route('cart.promotion.apply') }}" method="post" class="promotion-form w-full">
                                            @csrf
                                            <div class="flex flex-col space-y-2">
                                                <label for="promo-code" class="text-sm font-medium text-gray-700">Posiadasz kod promocyjny?</label>
                                                <div class="flex space-x-2">
                                                    <input type="text" id="promo-code" name="code" placeholder="Wpisz kod" class="text-sm border-gray-300 rounded-md flex-grow focus:ring-indigo-500 focus:border-indigo-500">
                                                    <button type="submit" class="text-sm px-3 py-1 bg-indigo-600 hover:bg-indigo-700 text-white rounded-md">
                                                        Zastosuj
                                                    </button>
                                                </div>
                                                <p class="text-xs text-gray-500">
                                                    <a href="{{ route('frontend.promotions') }}" class="text-indigo-600 hover:text-indigo-500">Zobacz dostępne promocje</a>
                                                </p>
                                            </div>
                                        </form>
                                    </div>
                                    @endif

                                    <div class="flex items-center justify-between border-t border-gray-200 pt-4">
                                        <p class="text-sm text-gray-600">Dostawa</p>
                                        <p class="text-sm font-medium text-gray-900">{{ number_format($shippingCost, 2) }} zł</p>
                                    </div>

                                    <div class="flex items-center justify-between border-t border-gray-200 pt-4">
                                        <p class="text-base font-medium text-gray-900">Razem do zapłaty</p>
                                        <p class="text-base font-medium text-gray-900">{{ number_format($finalTotal + $shippingCost, 2) }} zł</p>
                                    </div>
                                </div>

                                <div class="mt-6">
                                    <button type="button" class="w-full bg-indigo-600 border border-transparent rounded-md py-3 px-4 text-base font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        Przejdź do zamówienia
                                    </button>
                                </div>

                                <div class="mt-6 text-sm text-center">
                                    <p class="text-gray-600">
                                        lub 
                                        <a href="{{ route('frontend.categories.index') }}" class="text-indigo-600 font-medium hover:text-indigo-500">
                                            Kontynuuj zakupy<span aria-hidden="true"> &rarr;</span>
                                        </a>
                                    </p>
                                </div>
                                
                                <div class="mt-4">
                                    <form action="{{ route('cart.empty') }}" method="post" class="empty-cart-form">
                                        @csrf
                                        <button type="submit" class="w-full bg-red-600 border border-transparent rounded-md py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                            Opróżnij koszyk
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="mt-12 text-center py-12 px-4 sm:px-6 lg:px-8">
                    <div class="rounded-lg bg-gray-50 px-6 py-10">
                        <svg class="mx-auto h-20 w-20 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                        <h3 class="mt-4 text-xl font-medium text-gray-900">Twój koszyk jest pusty</h3>
                        <p class="mt-2 text-base text-gray-500">Zacznij zakupy i dodaj produkty do koszyka.</p>
                        <div class="mt-6">
                            <a href="{{ route('frontend.categories.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Przejdź do sklepu
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle quantity forms with AJAX
        const quantityForms = document.querySelectorAll('.quantity-form');
        
        quantityForms.forEach(form => {
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
                    // Refresh page to update cart
                    window.location.reload();
                })
                .catch(error => console.error('Error:', error));
            });
        });
        
        // Handle empty cart form with AJAX
        const emptyCartForm = document.querySelector('.empty-cart-form');
        if (emptyCartForm) {
            emptyCartForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                if (confirm('Czy na pewno chcesz opróżnić koszyk?')) {
                    const formData = new FormData(emptyCartForm);
                    const url = emptyCartForm.getAttribute('action');
                    
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
                        // Refresh page to update cart
                        window.location.reload();
                    })
                    .catch(error => console.error('Error:', error));
                }
            });
        }
        
        // Handle promotion form with AJAX
        const promotionForm = document.querySelector('.promotion-form');
        if (promotionForm) {
            promotionForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(promotionForm);
                const url = promotionForm.getAttribute('action');
                
                // Show loading state
                const submitButton = promotionForm.querySelector('button[type="submit"]');
                const originalText = submitButton.innerHTML;
                submitButton.innerHTML = 'Sprawdzanie...';
                submitButton.disabled = true;
                
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
                    if (data.success) {
                        // Show success message
                        const messageElement = document.createElement('div');
                        messageElement.className = 'mt-2 text-sm text-green-600';
                        messageElement.textContent = data.message;
                        
                        const formParent = promotionForm.parentElement;
                        formParent.appendChild(messageElement);
                        
                        // Remove message after 3 seconds and reload
                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                    } else {
                        // Show error message
                        const messageElement = document.createElement('div');
                        messageElement.className = 'mt-2 text-sm text-red-600';
                        messageElement.textContent = data.message;
                        
                        const formParent = promotionForm.parentElement;
                        formParent.appendChild(messageElement);
                        
                        // Reset button
                        submitButton.innerHTML = originalText;
                        submitButton.disabled = false;
                        
                        // Remove message after 5 seconds
                        setTimeout(() => {
                            messageElement.remove();
                        }, 5000);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    submitButton.innerHTML = originalText;
                    submitButton.disabled = false;
                });
            });
        }
        
        // Handle promotion remove form with AJAX
        const promotionRemoveForm = document.querySelector('.promotion-remove-form');
        if (promotionRemoveForm) {
            promotionRemoveForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(promotionRemoveForm);
                const url = promotionRemoveForm.getAttribute('action');
                
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
                    // Refresh page to update cart
                    window.location.reload();
                })
                .catch(error => console.error('Error:', error));
            });
        }
    });
</script>
@endpush 