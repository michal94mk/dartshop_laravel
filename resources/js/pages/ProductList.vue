<template>
  <div class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50">
    <!-- Hero Section -->
    <div class="relative overflow-hidden bg-gradient-to-r from-blue-600 to-indigo-600 py-16">
      <div class="absolute inset-0 bg-black opacity-20"></div>
      <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl md:text-5xl font-bold text-white mb-4">
          Nasze Produkty
        </h1>
        <p class="text-xl text-blue-100 max-w-2xl mx-auto">
          Odkryj naszą pełną kolekcję profesjonalnego sprzętu do dart - od lotki po tarcze i akcesoria.
        </p>
      </div>
    </div>

    <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
      
      <!-- Breadcrumbs -->
      <nav class="mb-6" aria-label="Breadcrumb">
        <ol class="flex items-center space-x-2 text-sm text-gray-500">
          <li>
            <router-link to="/" class="text-gray-500 hover:text-gray-700 transition-colors duration-200">
              <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
              </svg>
            </router-link>
          </li>
          <li class="flex items-center">
            <svg class="w-4 h-4 text-gray-400 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <router-link to="/products" class="text-gray-500 hover:text-gray-700 transition-colors duration-200">
              Produkty
            </router-link>
          </li>
          <li v-if="selectedCategory" class="flex items-center">
            <svg class="w-4 h-4 text-gray-400 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <span class="text-indigo-600 font-medium">{{ getCategoryName(selectedCategory) }}</span>
          </li>
        </ol>
      </nav>
      
      <!-- Categories Filter Section -->
      <div class="mb-8">
        <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
          <div class="flex items-center mb-4">
            <svg class="w-5 h-5 text-indigo-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
            </svg>
            <h3 class="text-lg font-semibold text-gray-900">Filtruj według kategorii</h3>
          </div>
          
          <div class="flex flex-wrap gap-3">
            <button 
              @click="filterByCategory(null)" 
              class="px-4 py-2 rounded-xl text-sm font-medium transition-all duration-200 shadow-sm"
              :class="{'bg-gradient-to-r from-indigo-600 to-purple-600 text-white shadow-lg': !selectedCategory, 'bg-gray-100 text-gray-700 hover:bg-indigo-50 border border-gray-200': selectedCategory}"
            >
              <span class="flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
                Wszystkie kategorie
              </span>
            </button>
            
            <button 
              v-for="category in categoryStore.categoriesWithProducts"
              :key="category.id"
              @click="filterByCategory(category.id)" 
              class="px-4 py-2 rounded-xl text-sm font-medium transition-all duration-200 shadow-sm"
              :class="{'bg-gradient-to-r from-indigo-600 to-purple-600 text-white shadow-lg': selectedCategory === category.id, 'bg-gray-100 text-gray-700 hover:bg-indigo-50 border border-gray-200': selectedCategory !== category.id}"
            >
              <span class="flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
                {{ category.name }}
                <span class="ml-2 text-xs opacity-75">({{ category.products_count }})</span>
              </span>
            </button>
          </div>
          
          <!-- Active Category Info -->
          <div v-if="selectedCategory" class="mt-4 p-3 bg-indigo-50 rounded-lg border border-indigo-200">
            <div class="flex items-center">
              <svg class="h-5 w-5 text-indigo-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
              </svg>
              <span class="text-sm font-medium text-indigo-800">
                Filtrowanie według kategorii: <span class="font-semibold">{{ getCategoryName(selectedCategory) }}</span>
              </span>
              <button 
                @click="filterByCategory(null)"
                class="ml-auto text-indigo-600 hover:text-indigo-800 text-sm font-medium"
              >
                Usuń filtr
              </button>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Product grid and sorting bar -->
      <div>
        <!-- Filters and sorting interface -->
        <div class="bg-white rounded-2xl shadow-lg p-6 mb-8 border border-gray-100">
          <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
            <!-- Enhanced sorting interface -->
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-4">
              <div class="flex flex-col gap-4">
                <div class="flex items-center">
                  <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h9m5-4v12m0 0l-4-4m4 4l4-4"/>
                  </svg>
                  <span class="text-sm font-semibold text-gray-700">Sortuj według:</span>
                </div>
                
                <!-- Sortowanie ogólne -->
                <div class="space-y-3">
                  <div class="text-xs font-medium text-gray-600 uppercase tracking-wide">Ogólne</div>
                  <button 
                    @click="setSorting('newest')" 
                    class="w-full px-4 py-3 rounded-lg text-sm font-medium transition-all duration-200 shadow-sm"
                    :class="{'bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-lg': productStore.filters.sort === 'newest', 'bg-white text-gray-700 hover:bg-blue-50 border border-gray-200': productStore.filters.sort !== 'newest'}"
                  >
                    <span class="flex items-center justify-center">
                      <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                      </svg>
                      Najnowsze
                    </span>
                  </button>
                </div>

                <!-- Sortowanie po cenie -->
                <div class="space-y-3">
                  <div class="text-xs font-medium text-gray-600 uppercase tracking-wide">Cena</div>
                  <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                    <button 
                      @click="setSorting('price_asc')" 
                      class="px-4 py-3 rounded-lg text-sm font-medium transition-all duration-200 shadow-sm"
                      :class="{'bg-gradient-to-r from-green-600 to-emerald-600 text-white shadow-lg': productStore.filters.sort === 'price_asc', 'bg-white text-gray-700 hover:bg-green-50 border border-gray-200': productStore.filters.sort !== 'price_asc'}"
                    >
                      <span class="flex items-center justify-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4"/>
                        </svg>
                        Od najtańszych
                      </span>
                    </button>
                    <button 
                      @click="setSorting('price_desc')" 
                      class="px-4 py-3 rounded-lg text-sm font-medium transition-all duration-200 shadow-sm"
                      :class="{'bg-gradient-to-r from-red-600 to-rose-600 text-white shadow-lg': productStore.filters.sort === 'price_desc', 'bg-white text-gray-700 hover:bg-red-50 border border-gray-200': productStore.filters.sort !== 'price_desc'}"
                    >
                      <span class="flex items-center justify-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8V20m0 0l4-4m-4 4l-4-4"/>
                        </svg>
                        Od najdroższych
                      </span>
                    </button>
                  </div>
                </div>

                <!-- Sortowanie po nazwie -->
                <div class="space-y-3">
                  <div class="text-xs font-medium text-gray-600 uppercase tracking-wide">Nazwa</div>
                  <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                    <button 
                      @click="setSorting('name_asc')" 
                      class="px-4 py-3 rounded-lg text-sm font-medium transition-all duration-200 shadow-sm"
                      :class="{'bg-gradient-to-r from-purple-600 to-violet-600 text-white shadow-lg': productStore.filters.sort === 'name_asc', 'bg-white text-gray-700 hover:bg-purple-50 border border-gray-200': productStore.filters.sort !== 'name_asc'}"
                    >
                      <span class="flex items-center justify-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m-4 4l4 4"/>
                        </svg>
                        A-Z
                      </span>
                    </button>
                    <button 
                      @click="setSorting('name_desc')" 
                      class="px-4 py-3 rounded-lg text-sm font-medium transition-all duration-200 shadow-sm"
                      :class="{'bg-gradient-to-r from-orange-600 to-amber-600 text-white shadow-lg': productStore.filters.sort === 'name_desc', 'bg-white text-gray-700 hover:bg-orange-50 border border-gray-200': productStore.filters.sort !== 'name_desc'}"
                    >
                      <span class="flex items-center justify-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m-4 4l4 4"/>
                        </svg>
                        Z-A
                      </span>
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <!-- Price range filter -->
            <div class="bg-gradient-to-r from-indigo-50 to-purple-50 rounded-xl p-4">
              <div class="flex flex-col gap-4">
                <div class="flex items-center">
                  <svg class="w-5 h-5 text-indigo-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                  </svg>
                  <span class="text-sm font-semibold text-gray-700">Zakres cen:</span>
                </div>
                
                <!-- Pola wprowadzania cen -->
                <div class="space-y-3">
                  <div class="text-xs font-medium text-gray-600 uppercase tracking-wide">Filtruj po cenie</div>
                  <div class="flex flex-col sm:flex-row items-center gap-2">
                    <input 
                      type="number" 
                      v-model="priceRange[0]"
                      placeholder="Od" 
                      min="0" 
                      class="w-full sm:flex-1 px-2 py-2 rounded-lg border border-gray-200 focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-center text-sm min-w-0"
                    >
                    <span class="text-gray-400 font-medium text-xs sm:text-sm">-</span>
                    <input 
                      type="number" 
                      v-model="priceRange[1]"
                      placeholder="Do" 
                      min="0" 
                      class="w-full sm:flex-1 px-2 py-2 rounded-lg border border-gray-200 focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-center text-sm min-w-0"
                    >
                  </div>
                </div>

                <!-- Przyciski akcji -->
                <div class="space-y-3">
                  <div class="text-xs font-medium text-gray-600 uppercase tracking-wide">Akcje</div>
                  <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                    <button 
                      @click="applyPriceFilter"
                      class="px-4 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-lg hover:from-indigo-700 hover:to-purple-700 transition-all duration-200 shadow-lg hover:shadow-xl text-sm font-medium"
                    >
                      <svg class="w-4 h-4 mr-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                      </svg>
                      Filtruj
                    </button>
                    <button 
                      @click="resetFilters"
                      class="px-4 py-3 bg-white text-gray-700 rounded-lg hover:bg-gray-50 transition-all duration-200 border border-gray-200 text-sm font-medium"
                    >
                      <svg class="w-4 h-4 mr-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                      </svg>
                      Reset
                    </button>
                  </div>
                </div>

                <!-- Szybkie filtry cenowe -->
                <div class="space-y-3">
                  <div class="text-xs font-medium text-gray-600 uppercase tracking-wide">Szybkie filtry</div>
                  <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                    <button 
                      @click="setQuickPriceFilter(0, 50)"
                      class="px-3 py-2 bg-white text-gray-700 rounded-lg hover:bg-indigo-50 transition-all duration-200 border border-gray-200 text-xs font-medium"
                    >
                      0-50 zł
                    </button>
                    <button 
                      @click="setQuickPriceFilter(50, 100)"
                      class="px-3 py-2 bg-white text-gray-700 rounded-lg hover:bg-indigo-50 transition-all duration-200 border border-gray-200 text-xs font-medium"
                    >
                      50-100 zł
                    </button>
                    <button 
                      @click="setQuickPriceFilter(100, 200)"
                      class="px-3 py-2 bg-white text-gray-700 rounded-lg hover:bg-indigo-50 transition-all duration-200 border border-gray-200 text-xs font-medium"
                    >
                      100-200 zł
                    </button>
                    <button 
                      @click="setQuickPriceFilter(200, null)"
                      class="px-3 py-2 bg-white text-gray-700 rounded-lg hover:bg-indigo-50 transition-all duration-200 border border-gray-200 text-xs font-medium"
                    >
                      200+ zł
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Search Results Info -->
          <div v-if="productStore.filters.search" class="mt-4 p-3 bg-blue-50 rounded-lg border border-blue-200">
            <div class="flex items-center">
              <svg class="h-5 w-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
              </svg>
              <span class="text-sm font-medium text-blue-800">
                Wyniki wyszukiwania dla: "<span class="font-semibold">{{ productStore.filters.search }}</span>"
              </span>
              <button 
                @click="clearSearch"
                class="ml-auto text-blue-600 hover:text-blue-800 text-sm font-medium"
              >
                Wyczyść wyszukiwanie
              </button>
            </div>
          </div>
          
          <div class="mt-4 text-sm text-gray-600">
            Wyświetlanie {{ productStore.products.length }} z {{ productStore.pagination.total }} produktów
            <span class="ml-2 text-xs text-gray-500">
              <template v-if="productStore.filters.sort === 'newest'">
                (sortowanie: najnowsze najpierw)
              </template>
              <template v-else-if="productStore.filters.sort === 'price_asc'">
                (sortowanie: od najtańszych)
              </template>
              <template v-else-if="productStore.filters.sort === 'price_desc'">
                (sortowanie: od najdroższych)
              </template>
              <template v-else-if="productStore.filters.sort === 'name_asc'">
                (sortowanie: A-Z)
              </template>
              <template v-else-if="productStore.filters.sort === 'name_desc'">
                (sortowanie: Z-A)
              </template>
            </span>
          </div>
        </div>
        
        <div v-if="productStore.loading" class="text-center py-10">
          <div class="w-12 h-12 border-4 border-indigo-500 border-t-transparent rounded-full animate-spin mx-auto"></div>
          <p class="mt-2 text-gray-500">Ładowanie produktów...</p>
        </div>
        
        <div v-else-if="productStore.error" class="text-center py-10">
          <p class="text-red-500">{{ productStore.error }}</p>
          <button 
            @click="loadProducts" 
            class="mt-4 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200"
          >
            Spróbuj ponownie
          </button>
        </div>
        
        <div v-else-if="productStore.products.length === 0" class="text-center py-10">
          <p class="text-gray-500">Nie znaleziono produktów spełniających podane kryteria.</p>
          <button 
            @click="resetFilters" 
            class="mt-4 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200"
          >
            Wyczyść filtry
          </button>
        </div>
        
        <div v-else class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
          <div v-for="product in productStore.products" :key="product.id" class="bg-white overflow-hidden shadow-lg rounded-2xl transition-all hover:shadow-xl group transform hover:-translate-y-2 duration-300 border border-gray-100 flex flex-col" style="aspect-ratio: 1 / 1.5;">
            <div class="relative h-4/5 overflow-hidden">
              <img 
                :src="product.image_url || 'https://via.placeholder.com/400x400/indigo/fff?text=' + product.name" 
                :alt="product.name" 
                class="h-full w-full object-cover group-hover:scale-110 transition-transform duration-500"
                loading="lazy"
              >
              <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
              
              <!-- Promotion Badge -->
              <div v-if="hasPromotion(product)" class="absolute top-3 left-3">
                <div 
                  class="px-2 py-1 rounded-full text-xs font-bold text-white shadow-lg"
                  :style="{ backgroundColor: getPromotionBadgeColor(product) }"
                >
                  {{ getPromotionBadgeText(product) || `${getDiscountPercentage(product)}% OFF` }}
                </div>
              </div>
              
              <!-- Product badge -->
              <div v-else class="absolute top-3 right-3 bg-white/90 backdrop-blur-sm px-2 py-1 rounded-full text-xs font-semibold text-blue-600">
                PRODUKT
              </div>
            </div>
            
            <div class="p-4 flex-1 flex flex-col justify-between">
              <div>
                <h3 class="text-base font-bold text-gray-900 line-clamp-2 mb-2 leading-tight">{{ product.name }}</h3>
                
                <!-- Reviews rating -->
                <div v-if="product.reviews_count > 0" class="mb-2">
                  <StarRating 
                    :model-value="product.average_rating" 
                    :review-count="product.reviews_count"
                    size="sm"
                    show-text
                    :precision="0.1"
                  />
                </div>
                <div v-else class="mb-2">
                  <div class="flex items-center text-gray-400 text-sm">
                    <StarRating 
                      :model-value="0" 
                      size="sm"
                      :precision="0.1"
                    />
                    <span class="ml-2 text-xs">Brak recenzji</span>
                  </div>
                </div>
                
                <p class="text-xs text-gray-600 line-clamp-2 mb-3 leading-relaxed">{{ product.short_description || product.description }}</p>
              </div>
              
              <div>
                <div class="flex items-center justify-between mb-3">
                  <!-- Price section with promotion support -->
                  <div class="flex flex-col">
                    <div v-if="hasPromotion(product)" class="space-y-1">
                      <!-- Original price (crossed out) -->
                      <div class="flex items-center space-x-2">
                        <span class="text-sm text-gray-500 line-through">
                          {{ formatPrice(product.price) }} zł
                        </span>
                        <span class="text-xs text-red-600 font-medium bg-red-100 px-1.5 py-0.5 rounded">
                          -{{ getDiscountPercentage(product) }}%
                        </span>
                      </div>
                      <!-- Promotional price -->
                      <div class="text-lg font-bold text-red-600">
                        {{ formatPrice(product.promotion_price) }} zł
                      </div>
                    </div>
                    <!-- Regular price (no promotion) -->
                    <div v-else class="text-lg font-bold text-blue-600">
                      {{ formatPrice(product.price) }} zł
                    </div>
                  </div>
                  <FavoriteButton 
                    :product="product"
                    buttonClasses="p-1.5 rounded-full border border-gray-300 text-gray-400 hover:text-red-500 hover:border-red-300 transition-colors duration-200"
                    @favorite-added="handleFavoriteAdded"
                    @favorite-removed="handleFavoriteRemoved"
                  />
                </div>
                <div class="space-y-2">
                  <button 
                    @click="addToCart(product)"
                    :disabled="cartLoading"
                    class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-medium py-2.5 px-4 rounded-lg transition-all duration-200 text-sm"
                  >
                    <template v-if="cartLoading">
                      <svg class="animate-spin w-4 h-4 mr-2 inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                      </svg>
                      Dodawanie...
                    </template>
                    <template v-else>
                      <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 2.5M7 13l2.5 2.5m4.5-2.5V13"/>
                      </svg>
                      Dodaj do koszyka
                    </template>
                  </button>
                  <router-link 
                    :to="`/products/${product.id}`" 
                    class="w-full block text-center py-2 px-4 border border-blue-600 text-blue-600 font-medium rounded-lg hover:bg-blue-50 transition-colors duration-200 text-sm"
                  >
                    Zobacz szczegóły
                  </router-link>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Pagination -->
        <div v-if="productStore.pagination.totalPages > 1" class="mt-10 flex justify-center">
          <nav class="flex items-center justify-center space-x-2">
            <!-- Previous page button -->
            <button 
              @click="goToPage(productStore.pagination.currentPage - 1)"
              :disabled="productStore.pagination.currentPage === 1"
              class="relative inline-flex items-center px-4 py-2 text-sm font-medium rounded-md border"
              :class="[
                productStore.pagination.currentPage === 1 
                  ? 'bg-gray-100 text-gray-400 cursor-not-allowed' 
                  : 'bg-white text-gray-700 hover:bg-gray-50'
              ]"
            >
              &laquo; Poprzednia
            </button>

            <!-- Page numbers -->
            <div class="flex items-center space-x-2">
              <template v-for="(page, index) in paginationPages" :key="index">
                <button 
                  v-if="typeof page === 'number'"
                  @click="goToPage(page)"
                  class="relative inline-flex items-center px-4 py-2 text-sm font-medium rounded-md"
                  :class="[
                    page === productStore.pagination.currentPage 
                      ? 'z-10 bg-indigo-600 text-white' 
                      : 'bg-white text-gray-700 hover:bg-gray-50'
                  ]"
                >
                  {{ page }}
                </button>
                <span 
                  v-else 
                  class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700"
                >
                  ...
                </span>
              </template>
            </div>

            <!-- Next page button -->
            <button 
              @click="goToPage(productStore.pagination.currentPage + 1)"
              :disabled="productStore.pagination.currentPage === productStore.pagination.totalPages"
              class="relative inline-flex items-center px-4 py-2 text-sm font-medium rounded-md border"
              :class="[
                productStore.pagination.currentPage === productStore.pagination.totalPages 
                  ? 'bg-gray-100 text-gray-400 cursor-not-allowed' 
                  : 'bg-white text-gray-700 hover:bg-gray-50'
              ]"
            >
              Następna &raquo;
            </button>
          </nav>
        </div>

        <!-- Pagination info -->
        <div class="mt-4 text-sm text-gray-600 text-center">
          Wyświetlanie {{ (productStore.pagination.currentPage - 1) * productStore.pagination.perPage + 1 }} 
          - {{ Math.min(productStore.pagination.currentPage * productStore.pagination.perPage, productStore.pagination.total) }} 
          z {{ productStore.pagination.total }} produktów
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { computed, ref, onMounted, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useProductStore } from '../stores/productStore';
import { useCartStore } from '../stores/cartStore';
import { useFavoriteStore } from '../stores/favoriteStore';
import { useCategoryStore } from '../stores/categoryStore';
import FavoriteButton from '../components/ui/FavoriteButton.vue';
import StarRating from '../components/ui/StarRating.vue';
import { useToast } from 'vue-toastification';
import axios from 'axios';

export default {
  name: 'ProductList',
  components: {
    FavoriteButton,
    StarRating
  },
  setup() {
    const route = useRoute();
    const router = useRouter();
    const productStore = useProductStore();
    const cartStore = useCartStore();
    const favoriteStore = useFavoriteStore();
    const categoryStore = useCategoryStore();
    const toast = useToast();
    const priceRange = ref([null, null]);
    const cartLoading = ref(false);
    const selectedCategory = ref(null);
    
    // Define loadProducts function first
    const loadProducts = async () => {
      await productStore.fetchProducts();
    };
    
    // Load categories on mount
    onMounted(async () => {
      // Load categories first
      try {
        await categoryStore.fetchCategories();
      } catch (error) {
        console.error('Error loading categories in ProductList.vue:', error);
      }
      
      // Initialize favorites when component is mounted
      await favoriteStore.initializeFavorites();
      
      // Check for search parameter in URL
      if (route.query.search) {
        productStore.filters.search = route.query.search;
      }
      
      // Check for category parameter in URL
      if (route.query.category) {
        selectedCategory.value = parseInt(route.query.category);
        productStore.filters.category = parseInt(route.query.category);
      }
      
      // Then load products
      await loadProducts();
    });
    
    // Watch for route changes to update filters  
    watch(() => route.query, (newQuery, oldQuery) => {
      // Only proceed if this is not the initial load
      if (oldQuery) {
        // Update productStore filters from route query
        if (newQuery.search !== productStore.filters.search) {
          productStore.filters.search = newQuery.search || '';
        }
        
        if (newQuery.category && newQuery.category !== productStore.filters.category) {
          productStore.filters.category = newQuery.category;
          selectedCategory.value = parseInt(newQuery.category);
        } else if (!newQuery.category) {
          productStore.filters.category = null;
          selectedCategory.value = null;
        }
        
        if (newQuery.sort && newQuery.sort !== productStore.filters.sort) {
          productStore.filters.sort = newQuery.sort;
        }
        
        // Load products with new filters
        loadProducts();
      }
    }, { deep: true });

    const paginationPages = computed(() => {
      const totalPages = productStore.pagination.totalPages;
      const currentPage = productStore.pagination.currentPage;
      
      if (totalPages <= 7) {
        // If 7 or fewer pages, show all
        return Array.from({ length: totalPages }, (_, i) => i + 1);
      }
      
      if (currentPage <= 3) {
        // If near the start, show first 5, ellipsis, and last
        return [1, 2, 3, 4, 5, '...', totalPages];
      }
      
      if (currentPage >= totalPages - 2) {
        // If near the end, show first, ellipsis, and last 5
        return [1, '...', totalPages - 4, totalPages - 3, totalPages - 2, totalPages - 1, totalPages];
      }
      
      // Otherwise, show first, ellipsis, current-1, current, current+1, ellipsis, last
      return [
        1, 
        '...', 
        currentPage - 1, 
        currentPage, 
        currentPage + 1, 
        '...', 
        totalPages
      ];
    });

    const applyFilters = () => {
              // Before applying filters, convert priceRange values to numbers
      const minPrice = priceRange.value[0] !== '' && priceRange.value[0] !== undefined ? parseFloat(priceRange.value[0]) : null;
      const maxPrice = priceRange.value[1] !== '' && priceRange.value[1] !== undefined ? parseFloat(priceRange.value[1]) : null;
      
              // Pass price range to filters
      productStore.filters.priceRange = [minPrice, maxPrice];
      
              // Get products with applied filters
      productStore.fetchProducts();
    };

    const resetFilters = () => {
      productStore.filters = {
        category: null,
        brand: null,
        search: '',
        priceRange: [0, 1000],
        sort: 'newest'
      };
      priceRange.value = [0, 1000];
      productStore.fetchProducts();
    };

    const goToPage = (page) => {
      if (
        typeof page === 'number' && 
        page >= 1 && 
        page <= productStore.pagination.totalPages && 
        page !== productStore.pagination.currentPage
      ) {
        window.scrollTo({ top: 0, behavior: 'smooth' });
        productStore.setPage(page);
      }
    };

    const formatPrice = (price) => {
      if (price === null || price === undefined || isNaN(price)) {
        return '0.00';
      }
      return parseFloat(price).toFixed(2);
    };

    const addToCart = async (product) => {
      // Show loading indicator for this product
      cartLoading.value = true;
      
      try {
        const success = await cartStore.addToCart(product.id, 1);
        if (success) {
          toast.success(`🛒 Dodano do koszyka: "${product.name}"`, {
            position: "top-center",
            timeout: 4000,
            closeOnClick: true,
            pauseOnFocusLoss: true,
            pauseOnHover: true,
            draggable: true,
            showCloseButtonOnHover: false,
            hideProgressBar: false,
            toastClassName: "cart-success-toast",
            bodyClassName: "cart-success-body",
            progressClassName: "cart-success-progress"
          });
        } else {
          toast.error('❌ Nie udało się dodać produktu do koszyka', {
            position: "top-center",
            timeout: 5000,
            closeOnClick: true,
            pauseOnHover: true,
            toastClassName: "cart-error-toast",
            bodyClassName: "cart-error-body"
          });
        }
      } catch (error) {
        toast.error('⚠️ Wystąpił błąd podczas dodawania produktu do koszyka', {
          position: "top-center",
          timeout: 5000,
          closeOnClick: true,
          pauseOnHover: true,
          toastClassName: "cart-error-toast",
          bodyClassName: "cart-error-body"
        });
        console.error('Error adding product to cart:', error);
      } finally {
        cartLoading.value = false;
      }
    };

    const isCartLoading = (productId) => {
      return cartStore.isLoadingProduct(productId);
    };

    const setSorting = (sort) => {
      productStore.filters.sort = sort;
      applyFilters();
    };

    const applyPriceFilter = () => {
      productStore.filters.priceRange = priceRange.value;
      applyFilters();
    };

    const handleFavoriteAdded = (product) => {
      toast.success(`💖 Dodano do ulubionych: "${product.name}"`, {
        position: "top-center",
        timeout: 3500,
        closeOnClick: true,
        pauseOnHover: true,
        toastClassName: "favorite-success-toast",
        bodyClassName: "favorite-success-body",
        progressClassName: "favorite-success-progress"
      });
    };
    
    const handleFavoriteRemoved = (product) => {
      toast.info(`💔 Usunięto z ulubionych: "${product.name}"`, {
        position: "top-center",
        timeout: 3000,
        closeOnClick: true,
        pauseOnHover: true,
        toastClassName: "favorite-info-toast",
        bodyClassName: "favorite-info-body",
        progressClassName: "favorite-info-progress"
      });
    };

    const clearSearch = () => {
      productStore.filters.search = '';
      productStore.fetchProducts();
      // Also update the URL to remove search parameter
      router.push({ path: '/products' });
    };

    // Promotion helper functions
    const hasPromotion = (product) => {
      return product.promotion_price && parseFloat(product.promotion_price) < parseFloat(product.price);
    };

    const getDiscountPercentage = (product) => {
      if (!hasPromotion(product)) return 0;
      const originalPrice = parseFloat(product.price);
      const promotionalPrice = parseFloat(product.promotion_price);
      return Math.round(((originalPrice - promotionalPrice) / originalPrice) * 100);
    };

    const getPromotionBadgeColor = (product) => {
      if (hasPromotion(product)) {
        const discountPercentage = getDiscountPercentage(product);
        if (discountPercentage > 50) return 'bg-red-500';
        if (discountPercentage > 30) return 'bg-orange-500';
        if (discountPercentage > 10) return 'bg-yellow-500';
        return 'bg-green-500';
      }
      return 'bg-gray-500';
    };

    const getPromotionBadgeText = (product) => {
      if (hasPromotion(product)) {
        const discountPercentage = getDiscountPercentage(product);
        if (discountPercentage > 50) return 'Duża zniżka';
        if (discountPercentage > 30) return 'Zniżka';
        if (discountPercentage > 10) return 'Niska zniżka';
        return 'Brak zniżki';
      }
      return null;
    };

    const filterByCategory = (category) => {
      selectedCategory.value = category;
      productStore.filters.category = category;
      
      // Update URL
      const query = { ...route.query };
      if (category) {
        query.category = category;
      } else {
        delete query.category;
      }
      
      router.push({ path: route.path, query });
    };

    const getCategoryName = (categoryId) => {
      const category = categoryStore.getCategoryById(categoryId);
      return category ? category.name : 'Inne';
    };

    const setQuickPriceFilter = (min, max) => {
      priceRange.value = [min, max];
      applyPriceFilter();
    };

    return {
      productStore,
      priceRange,
      cartLoading,
      paginationPages,
      loadProducts,
      applyFilters,
      resetFilters,
      goToPage,
      formatPrice,
      addToCart,
      isCartLoading,
      setSorting,
      applyPriceFilter,
      handleFavoriteAdded,
      handleFavoriteRemoved,
      clearSearch,
      hasPromotion,
      getDiscountPercentage,
      getPromotionBadgeColor,
      getPromotionBadgeText,
      filterByCategory,
      getCategoryName,
      selectedCategory,
      categoryStore,
      setQuickPriceFilter
    };
  }
}
</script>

<style scoped>
.pb-7\/12 {
  padding-bottom: 58.333333%;
}
</style> 