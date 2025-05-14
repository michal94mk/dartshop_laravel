@extends('layouts.app')

@section('title', 'Kategorie produktów')

@section('content')
<div class="bg-white">
    <div class="max-w-7xl mx-auto py-16 px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-extrabold tracking-tight text-gray-900">Produkty</h1>
        
        <div class="mt-8 lg:grid lg:grid-cols-5 lg:gap-x-8">
            <!-- Mobile filter dialog -->
            <div x-data="{ open: false }" class="relative z-40 lg:hidden">
                <div x-show="open" class="fixed inset-0 bg-black bg-opacity-25"></div>
                
                <div x-show="open" class="fixed inset-0 flex z-40">
                    <div x-show="open" 
                         x-transition:enter="transition-opacity ease-linear duration-300"
                         x-transition:enter-start="opacity-0"
                         x-transition:enter-end="opacity-100"
                         x-transition:leave="transition-opacity ease-linear duration-300"
                         x-transition:leave-start="opacity-100"
                         x-transition:leave-end="opacity-0"
                         class="fixed inset-0 bg-black bg-opacity-25" 
                         @click="open = false"
                         aria-hidden="true">
                    </div>

                    <div x-show="open"
                         x-transition:enter="transition ease-in-out duration-300 transform"
                         x-transition:enter-start="translate-x-full"
                         x-transition:enter-end="translate-x-0"
                         x-transition:leave="transition ease-in-out duration-300 transform"
                         x-transition:leave-start="translate-x-0"
                         x-transition:leave-end="translate-x-full"
                         class="ml-auto relative max-w-xs w-full h-full bg-white shadow-xl py-4 pb-12 flex flex-col overflow-y-auto">
                        <div class="px-4 flex items-center justify-between">
                            <h2 class="text-lg font-medium text-gray-900">Filtry</h2>
                            <button type="button" class="-mr-2 w-10 h-10 bg-white p-2 rounded-md flex items-center justify-center text-gray-400" @click="open = false">
                                <span class="sr-only">Zamknij menu</span>
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <!-- Filters -->
                        <form class="mt-4 border-t border-gray-200">
                            <div class="p-4 border-t border-gray-200">
                                <h3 class="font-medium text-gray-900">Kategorie</h3>
                                <div class="pt-6" id="filter-section-mobile">
                                    <div class="space-y-4">
                                        @foreach($categories as $category)
                                            <div class="flex items-center">
                                                <input id="mobile-category-{{ $category->id }}" 
                                                       name="categories[]" 
                                                       value="{{ $category->id }}" 
                                                       type="checkbox" 
                                                       class="h-4 w-4 border-gray-300 rounded text-indigo-600 focus:ring-indigo-500">
                                                <label for="mobile-category-{{ $category->id }}" class="ml-3 min-w-0 flex-1 text-gray-500">
                                                    {{ $category->name }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            
                            <div class="p-4 border-t border-gray-200">
                                <h3 class="font-medium text-gray-900">Marki</h3>
                                <div class="pt-6" id="filter-section-mobile-1">
                                    <div class="space-y-4">
                                        @foreach($brands as $brand)
                                            <div class="flex items-center">
                                                <input id="mobile-brand-{{ $brand->id }}" 
                                                       name="brands[]" 
                                                       value="{{ $brand->id }}" 
                                                       type="checkbox" 
                                                       class="h-4 w-4 border-gray-300 rounded text-indigo-600 focus:ring-indigo-500">
                                                <label for="mobile-brand-{{ $brand->id }}" class="ml-3 min-w-0 flex-1 text-gray-500">
                                                    {{ $brand->name }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            
                            <div class="p-4">
                                <button type="submit" class="w-full bg-indigo-600 border border-transparent rounded-md py-2 px-4 text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Filtruj
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Filters - Desktop -->
            <div class="hidden lg:block">
                <form class="divide-y divide-gray-200 space-y-10">
                    <div>
                        <fieldset>
                            <legend class="block text-sm font-medium text-gray-900">Kategorie</legend>
                            <div class="pt-6 space-y-3">
                                @foreach($categories as $category)
                                    <div class="flex items-center">
                                        <input id="category-{{ $category->id }}" 
                                               name="categories[]" 
                                               value="{{ $category->id }}" 
                                               type="checkbox" 
                                               class="h-4 w-4 border-gray-300 rounded text-indigo-600 focus:ring-indigo-500">
                                        <label for="category-{{ $category->id }}" class="ml-3 text-sm text-gray-600">
                                            {{ $category->name }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </fieldset>
                    </div>
                    
                    <div class="pt-10">
                        <fieldset>
                            <legend class="block text-sm font-medium text-gray-900">Marki</legend>
                            <div class="pt-6 space-y-3">
                                @foreach($brands as $brand)
                                    <div class="flex items-center">
                                        <input id="brand-{{ $brand->id }}" 
                                               name="brands[]" 
                                               value="{{ $brand->id }}" 
                                               type="checkbox" 
                                               class="h-4 w-4 border-gray-300 rounded text-indigo-600 focus:ring-indigo-500">
                                        <label for="brand-{{ $brand->id }}" class="ml-3 text-sm text-gray-600">
                                            {{ $brand->name }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </fieldset>
                    </div>
                    
                    <div class="pt-6">
                        <button type="submit" class="w-full bg-indigo-600 border border-transparent rounded-md py-2 px-4 text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Filtruj
                        </button>
                    </div>
                </form>
            </div>

            <!-- Product grid -->
            <div class="mt-6 lg:mt-0 lg:col-span-4">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-medium text-gray-900">{{ isset($filteredProducts) ? count($filteredProducts) : count($products) }} produktów</h2>
                    
                    <!-- Mobile filter button -->
                    <button type="button" class="inline-flex items-center lg:hidden" @click="open = true">
                        <span class="text-sm font-medium text-gray-700">Filtry</span>
                        <svg class="ml-1 h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                        </svg>
                    </button>
                </div>
                
                <div class="grid grid-cols-1 gap-y-10 gap-x-6 sm:grid-cols-2 lg:grid-cols-3 xl:gap-x-8">
                    @if(isset($filteredProducts) && count($filteredProducts) > 0)
                        @foreach($filteredProducts as $product)
                            <div class="group">
                                <x-product-card :product="$product" />
                            </div>
                        @endforeach
                    @elseif(isset($products) && count($products) > 0)
                        @foreach($products as $product)
                            <div class="group">
                                <x-product-card :product="$product" />
                            </div>
                        @endforeach
                    @else
                        <div class="col-span-full text-center py-10">
                            <p class="text-gray-500">Nie znaleziono produktów spełniających kryteria.</p>
                            <a href="{{ route('frontend.categories.index') }}" class="mt-4 inline-flex items-center text-sm font-medium text-indigo-600 hover:text-indigo-500">
                                Wyczyść filtry<span aria-hidden="true"> &rarr;</span>
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Filter form submission with AJAX
        const filterForms = document.querySelectorAll('form');
        
        filterForms.forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(form);
                
                fetch('{{ route("filter.products") }}', {
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
                        // Refresh with new data
                        window.location.reload();
                    }
                })
                .catch(error => console.error('Error:', error));
            });
        });
    });
</script>
@endpush 