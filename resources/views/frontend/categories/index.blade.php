@extends('layouts.app')

@section('title', 'Produkty')

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

                        <!-- Filters - Mobile -->
                        <form action="{{ route('filter.products') }}" method="GET" class="mt-4 border-t border-gray-200">
                            <!-- Sortowanie - Mobile -->
                            <div class="p-4 border-t border-gray-200">
                                <h3 class="font-medium text-gray-900">Sortowanie</h3>
                                <div class="pt-4">
                                    <select name="sort" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                        <option value="newest" {{ isset($sort) && $sort == 'newest' ? 'selected' : '' }}>Najnowsze</option>
                                        <option value="price-asc" {{ isset($sort) && $sort == 'price-asc' ? 'selected' : '' }}>Cena: rosnąco</option>
                                        <option value="price-desc" {{ isset($sort) && $sort == 'price-desc' ? 'selected' : '' }}>Cena: malejąco</option>
                                        <option value="name-asc" {{ isset($sort) && $sort == 'name-asc' ? 'selected' : '' }}>Nazwa: A-Z</option>
                                        <option value="name-desc" {{ isset($sort) && $sort == 'name-desc' ? 'selected' : '' }}>Nazwa: Z-A</option>
                                    </select>
                                </div>
                            </div>
                            
                            <!-- Zakres cen - Mobile -->
                            <div class="p-4 border-t border-gray-200">
                                <h3 class="font-medium text-gray-900">Zakres cenowy</h3>
                                <div class="pt-4 grid grid-cols-2 gap-4">
                                    <div>
                                        <label for="mobile-price-min" class="sr-only">Cena od</label>
                                        <input 
                                            type="number" 
                                            name="price_min" 
                                            id="mobile-price-min" 
                                            placeholder="Od" 
                                            min="0" 
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                            value="{{ $priceMin ?? '' }}"
                                        >
                                    </div>
                                    <div>
                                        <label for="mobile-price-max" class="sr-only">Cena do</label>
                                        <input 
                                            type="number" 
                                            name="price_max" 
                                            id="mobile-price-max" 
                                            placeholder="Do" 
                                            min="0" 
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                            value="{{ $priceMax ?? '' }}"
                                        >
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Kategorie - Mobile -->
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
                                                       class="h-4 w-4 border-gray-300 rounded text-indigo-600 focus:ring-indigo-500"
                                                       {{ isset($selectedCategories) && in_array($category->id, $selectedCategories) ? 'checked' : '' }}>
                                                <label for="mobile-category-{{ $category->id }}" class="ml-3 min-w-0 flex-1 text-gray-500">
                                                    {{ $category->name }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Marki - Mobile -->
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
                                                       class="h-4 w-4 border-gray-300 rounded text-indigo-600 focus:ring-indigo-500"
                                                       {{ isset($selectedBrands) && in_array($brand->id, $selectedBrands) ? 'checked' : '' }}>
                                                <label for="mobile-brand-{{ $brand->id }}" class="ml-3 min-w-0 flex-1 text-gray-500">
                                                    {{ $brand->name }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Przyciski akcji - Mobile -->
                            <div class="p-4 flex space-x-4">
                                <button type="submit" class="flex-1 bg-indigo-600 border border-transparent rounded-md py-2 px-4 text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Zastosuj
                                </button>
                                <a href="{{ route('frontend.categories.index') }}" class="flex-1 bg-gray-200 border border-transparent rounded-md py-2 px-4 text-sm font-medium text-gray-700 hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 text-center">
                                    Wyczyść
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Filters - Desktop -->
            <div class="hidden lg:block">
                <form action="{{ route('filter.products') }}" method="GET" class="divide-y divide-gray-200 space-y-10">
                    <!-- Sortowanie - Desktop -->
                    <div>
                        <fieldset>
                            <legend class="block text-sm font-medium text-gray-900">Sortowanie</legend>
                            <div class="pt-4">
                                <select name="sort" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <option value="newest" {{ isset($sort) && $sort == 'newest' ? 'selected' : '' }}>Najnowsze</option>
                                    <option value="price-asc" {{ isset($sort) && $sort == 'price-asc' ? 'selected' : '' }}>Cena: rosnąco</option>
                                    <option value="price-desc" {{ isset($sort) && $sort == 'price-desc' ? 'selected' : '' }}>Cena: malejąco</option>
                                    <option value="name-asc" {{ isset($sort) && $sort == 'name-asc' ? 'selected' : '' }}>Nazwa: A-Z</option>
                                    <option value="name-desc" {{ isset($sort) && $sort == 'name-desc' ? 'selected' : '' }}>Nazwa: Z-A</option>
                                </select>
                            </div>
                        </fieldset>
                    </div>
                    
                    <!-- Zakres cen - Desktop -->
                    <div class="pt-10">
                        <fieldset>
                            <legend class="block text-sm font-medium text-gray-900">Zakres cenowy</legend>
                            <div class="pt-4 grid grid-cols-2 gap-4">
                                <div>
                                    <label for="price-min" class="sr-only">Cena od</label>
                                    <input 
                                        type="number" 
                                        name="price_min" 
                                        id="price-min" 
                                        placeholder="Od" 
                                        min="0" 
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                        value="{{ $priceMin ?? '' }}"
                                    >
                                </div>
                                <div>
                                    <label for="price-max" class="sr-only">Cena do</label>
                                    <input 
                                        type="number" 
                                        name="price_max" 
                                        id="price-max" 
                                        placeholder="Do" 
                                        min="0" 
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                        value="{{ $priceMax ?? '' }}"
                                    >
                                </div>
                            </div>
                        </fieldset>
                    </div>
                    
                    <!-- Kategorie - Desktop -->
                    <div class="pt-10">
                        <fieldset>
                            <legend class="block text-sm font-medium text-gray-900">Kategorie</legend>
                            <div class="pt-6 space-y-3">
                                @foreach($categories as $category)
                                    <div class="flex items-center">
                                        <input id="category-{{ $category->id }}" 
                                               name="categories[]" 
                                               value="{{ $category->id }}" 
                                               type="checkbox" 
                                               class="h-4 w-4 border-gray-300 rounded text-indigo-600 focus:ring-indigo-500"
                                               {{ isset($selectedCategories) && in_array($category->id, $selectedCategories) ? 'checked' : '' }}>
                                        <label for="category-{{ $category->id }}" class="ml-3 text-sm text-gray-600">
                                            {{ $category->name }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </fieldset>
                    </div>
                    
                    <!-- Marki - Desktop -->
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
                                               class="h-4 w-4 border-gray-300 rounded text-indigo-600 focus:ring-indigo-500"
                                               {{ isset($selectedBrands) && in_array($brand->id, $selectedBrands) ? 'checked' : '' }}>
                                        <label for="brand-{{ $brand->id }}" class="ml-3 text-sm text-gray-600">
                                            {{ $brand->name }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </fieldset>
                    </div>
                    
                    <!-- Przyciski akcji - Desktop -->
                    <div class="pt-10 flex space-x-4">
                        <button type="submit" class="flex-1 bg-indigo-600 border border-transparent rounded-md py-2 px-4 text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Zastosuj filtry
                        </button>
                        <a href="{{ route('frontend.categories.index') }}" class="flex-1 bg-gray-200 border border-transparent rounded-md py-2 px-4 text-sm font-medium text-gray-700 hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 text-center">
                            Wyczyść filtry
                        </a>
                    </div>
                </form>
            </div>

            <!-- Product grid -->
            <div class="mt-6 lg:mt-0 lg:col-span-4">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-medium text-gray-900">
                        {{ isset($filteredProducts) ? $filteredProducts->total() : (isset($products) ? $products->total() : 0) }} produktów
                    </h2>
                    
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
                
                <!-- Pagination -->
                <div class="mt-8">
                    @if(isset($filteredProducts) && $filteredProducts->lastPage() > 1)
                        {{ $filteredProducts->appends(request()->query())->links() }}
                    @elseif(isset($products) && $products->lastPage() > 1)
                        {{ $products->links() }}
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 