@props(['product'])

<div class="bg-white rounded-lg shadow-sm overflow-hidden hover:shadow-md transition-shadow duration-300">
    <a href="{{ route('frontend.products.show', ['id' => $product->id, 'tailwind' => request()->has('tailwind') ? 1 : null]) }}">
        <div class="relative aspect-square overflow-hidden bg-gray-100">
            @if ($product->image)
                <img src="{{ asset($product->image) }}" alt="{{ $product->name }}" class="h-full w-full object-cover object-center">
            @else
                <div class="flex items-center justify-center h-full bg-gray-200">
                    <span class="text-gray-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </span>
                </div>
            @endif
            
            @if($product->created_at > now()->subDays(7))
                <div class="absolute top-2 right-2">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                        Nowość
                    </span>
                </div>
            @endif
        </div>
    </a>
    
    <div class="p-4">
        @if($product->category)
            <p class="text-sm text-gray-500 uppercase tracking-wider">{{ $product->category->name }}</p>
        @endif
        
        <a href="{{ route('frontend.products.show', ['id' => $product->id, 'tailwind' => request()->has('tailwind') ? 1 : null]) }}" class="block">
            <h3 class="mt-1 text-lg font-medium text-gray-900 truncate hover:text-indigo-600">{{ $product->name }}</h3>
        </a>
        
        <div class="mt-2 flex items-center justify-between">
            <p class="text-xl font-bold text-indigo-600">{{ number_format($product->price, 2) }} zł</p>
            
            <div class="flex space-x-2">
                <button type="button" class="p-1.5 rounded-full text-gray-500 hover:text-gray-700 hover:bg-gray-100">
                    <span class="sr-only">Dodaj do ulubionych</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                </button>
                <a href="{{ route('frontend.products.show', ['id' => $product->id, 'tailwind' => request()->has('tailwind') ? 1 : null]) }}" class="p-1.5 rounded-full text-gray-500 hover:text-gray-700 hover:bg-gray-100" data-tooltip="Pokaż szczegóły">
                    <span class="sr-only">Pokaż szczegóły</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                </a>
            </div>
        </div>
        
        <div class="mt-4">
            <form action="{{ route('cart.add', $product->id) }}" method="post" class="add-to-cart-form">
                @csrf
                <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-2 px-4 rounded-md flex items-center justify-center text-sm font-medium transition-colors duration-150">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    Dodaj do koszyka
                </button>
            </form>
        </div>
    </div>
</div> 