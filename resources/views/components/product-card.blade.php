@props(['product'])

<div class="relative bg-white border border-gray-200 rounded-lg shadow-sm flex flex-col overflow-hidden hover:shadow-lg transition-shadow duration-200">
    <!-- Product image -->
    <a href="{{ route('frontend.products.show', ['id' => $product->id]) }}">
        <div class="aspect-h-1 aspect-w-1 bg-gray-200 overflow-hidden">
            @if($product->image)
                <img src="{{ asset($product->image) }}" alt="{{ $product->name }}" class="object-center object-cover w-full h-full">
            @else
                <div class="flex items-center justify-center h-full bg-gray-200">
                    <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
            @endif
        </div>
    </a>
    
    <!-- Brand badge -->
    <div class="absolute top-0 right-0 mt-2 mr-2">
        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-indigo-100 text-indigo-800">
            {{ $product->brand->name }}
        </span>
    </div>
    
    <!-- Product details -->
    <div class="flex-1 p-4 space-y-2 flex flex-col">
        <h3 class="text-sm font-medium text-gray-900 line-clamp-2">
            <a href="{{ route('frontend.products.show', ['id' => $product->id]) }}" class="block">
                {{ $product->name }}
            </a>
        </h3>
        <p class="text-sm text-gray-500 line-clamp-2">{{ $product->description }}</p>
        <div class="mt-auto pt-2">
            <div class="flex items-center justify-between">
                <p class="text-base font-medium text-gray-900">${{ number_format($product->price, 2) }}</p>
                <p class="text-sm text-gray-500">{{ $product->category->name }}</p>
            </div>
            <div class="mt-3 flex space-x-2">
                <form action="{{ route('cart.add', $product->id) }}" method="POST" class="add-to-cart-form flex-1">
                    @csrf
                    <button type="submit" class="w-full flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        Dodaj
                    </button>
                </form>
                <a href="{{ route('frontend.products.show', ['id' => $product->id]) }}" class="p-1.5 rounded-full text-gray-500 hover:text-gray-700 hover:bg-gray-100" data-tooltip="Pokaż szczegóły">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                </a>
            </div>
        </div>
    </div>
</div> 