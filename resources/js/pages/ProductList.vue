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
      
      <!-- Mobile filter dialog -->
      <div class="relative z-40 lg:hidden mb-4">
        <button @click="mobileFiltersOpen = true" class="w-full bg-white p-3 rounded-md shadow-sm flex items-center justify-center text-gray-700 hover:bg-gray-100 transition duration-150">
          <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
          </svg>
          Filtry i sortowanie
        </button>
        
        <div v-if="mobileFiltersOpen" class="fixed inset-0 flex z-40">
          <div class="fixed inset-0 bg-black bg-opacity-25" @click="mobileFiltersOpen = false"></div>
          
          <div class="ml-auto relative max-w-xs w-full h-full bg-white shadow-xl py-4 pb-12 flex flex-col overflow-y-auto">
            <div class="px-4 flex items-center justify-between">
              <h2 class="text-lg font-medium text-gray-900">Filtry</h2>
              <button @click="mobileFiltersOpen = false" class="-mr-2 w-10 h-10 bg-white p-2 rounded-md flex items-center justify-center text-gray-400">
                <span class="sr-only">Zamknij menu</span>
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>
            
            <!-- Mobile filters -->
            <div class="mt-4 border-t border-gray-200">
              <!-- Action buttons - Mobile -->
              <div class="p-4 flex space-x-4">
                <button 
                  @click="applyFilters"
                  class="flex-1 bg-indigo-600 border border-transparent rounded-md py-2 px-4 text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                >
                  Zastosuj
                </button>
                <button 
                  @click="resetFilters"
                  class="flex-1 bg-gray-200 border border-transparent rounded-md py-2 px-4 text-sm font-medium text-gray-700 hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500"
                >
                  Wyczyść
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Product grid and sorting bar -->
      <div>
        <!-- Filters and sorting interface -->
        <div class="bg-white rounded-2xl shadow-lg p-6 mb-8 border border-gray-100">
          <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
            <!-- Enhanced sorting interface -->
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-4 flex-grow">
              <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                <div class="flex items-center">
                  <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h9m5-4v12m0 0l-4-4m4 4l4-4"/>
                  </svg>
                  <span class="text-sm font-semibold text-gray-700">Sortuj według:</span>
                </div>
                <div class="flex flex-wrap gap-2">
                  <button 
                    @click="setSorting('newest')" 
                    class="px-4 py-2 rounded-xl text-sm font-medium transition-all duration-200 shadow-sm"
                    :class="{'bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-lg': productStore.filters.sort === 'newest', 'bg-white text-gray-700 hover:bg-blue-50 border border-gray-200': productStore.filters.sort !== 'newest'}"
                  >
                    <span class="flex items-center">
                      <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                      </svg>
                      Najnowsze
                    </span>
                  </button>
                  <button 
                    @click="setSorting('price_asc')" 
                    class="px-4 py-2 rounded-xl text-sm font-medium transition-all duration-200 shadow-sm"
                    :class="{'bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-lg': productStore.filters.sort === 'price_asc', 'bg-white text-gray-700 hover:bg-blue-50 border border-gray-200': productStore.filters.sort !== 'price_asc'}"
                  >
                    <span class="flex items-center">
                      <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
                      </svg>
                      <span class="hidden sm:inline">Cena </span>↑
                    </span>
                  </button>
                  <button 
                    @click="setSorting('price_desc')" 
                    class="px-4 py-2 rounded-xl text-sm font-medium transition-all duration-200 shadow-sm"
                    :class="{'bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-lg': productStore.filters.sort === 'price_desc', 'bg-white text-gray-700 hover:bg-blue-50 border border-gray-200': productStore.filters.sort !== 'price_desc'}"
                  >
                    <span class="flex items-center">
                      <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8V20m0 0l4-4m-4 4l-4-4M7 4v12m0 0l4-4m-4 4L3 12"/>
                      </svg>
                      <span class="hidden sm:inline">Cena </span>↓
                    </span>
                  </button>
                  <button 
                    @click="setSorting('name_asc')" 
                    class="px-4 py-2 rounded-xl text-sm font-medium transition-all duration-200 shadow-sm"
                    :class="{'bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-lg': productStore.filters.sort === 'name_asc', 'bg-white text-gray-700 hover:bg-blue-50 border border-gray-200': productStore.filters.sort !== 'name_asc'}"
                  >
                    <span class="flex items-center">
                      <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m-4 4l4 4"/>
                      </svg>
                      A-Z
                    </span>
                  </button>
                  <button 
                    @click="setSorting('name_desc')" 
                    class="px-4 py-2 rounded-xl text-sm font-medium transition-all duration-200 shadow-sm"
                    :class="{'bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-lg': productStore.filters.sort === 'name_desc', 'bg-white text-gray-700 hover:bg-blue-50 border border-gray-200': productStore.filters.sort !== 'name_desc'}"
                  >
                    <span class="flex items-center">
                      <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m-4 4l4 4"/>
                      </svg>
                      Z-A
                    </span>
                  </button>
                </div>
              </div>
            </div>

            <!-- Price range filter -->
            <div class="bg-gradient-to-r from-indigo-50 to-purple-50 rounded-xl p-4">
              <div class="grid grid-cols-1 gap-3">
                <div class="flex items-center">
                  <svg class="w-5 h-5 text-indigo-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                  </svg>
                  <span class="text-sm font-semibold text-gray-700">Zakres cen:</span>
                </div>
                <div class="flex items-center gap-3">
                  <div class="flex items-center bg-white rounded-xl shadow-sm">
                    <input 
                      type="number" 
                      v-model="priceRange[0]"
                      placeholder="Od" 
                      min="0" 
                      class="w-20 px-3 py-2 rounded-l-xl border-0 focus:ring-2 focus:ring-indigo-500 text-center text-sm"
                    >
                    <span class="px-2 text-gray-400 bg-gray-50">-</span>
                    <input 
                      type="number" 
                      v-model="priceRange[1]"
                      placeholder="Do" 
                      min="0" 
                      class="w-20 px-3 py-2 rounded-r-xl border-0 focus:ring-2 focus:ring-indigo-500 text-center text-sm"
                    >
                  </div>
                  <button 
                    @click="applyPriceFilter"
                    class="px-4 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl hover:from-indigo-700 hover:to-purple-700 transition-all duration-200 shadow-lg hover:shadow-xl text-sm font-medium"
                  >
                    <svg class="w-4 h-4 mr-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                    </svg>
                    Filtruj
                  </button>
                  <button 
                    @click="resetFilters"
                    class="px-4 py-2 bg-white text-gray-700 rounded-xl hover:bg-gray-50 transition-all duration-200 border border-gray-200 text-sm font-medium"
                  >
                    <svg class="w-4 h-4 mr-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Reset
                  </button>
                </div>
              </div>
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
              <!-- Product badge -->
              <div class="absolute top-3 right-3 bg-white/90 backdrop-blur-sm px-2 py-1 rounded-full text-xs font-semibold text-blue-600">
                PRODUKT
              </div>
            </div>
            <div class="p-4 flex-1 flex flex-col justify-between">
              <div>
                <h3 class="text-base font-bold text-gray-900 line-clamp-2 mb-2 leading-tight">{{ product.name }}</h3>
                <p class="text-xs text-gray-600 line-clamp-2 mb-3 leading-relaxed">{{ product.short_description || product.description }}</p>
              </div>
              <div v-if="product.id === cartMessageProductId" class="mt-2 p-2 rounded text-sm" :class="cartSuccess ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'">
                <div v-if="cartSuccess" class="flex flex-col">
                  <span>{{ cartMessage }}</span>
                  <div class="mt-1 flex justify-end">
                    <router-link to="/cart" class="text-xs font-medium text-indigo-600 hover:text-indigo-500">
                      Przejdź do koszyka &rarr;
                    </router-link>
                  </div>
                </div>
                <div v-else>
                  {{ cartMessage }}
                </div>
              </div>
              <div v-if="product.id === favoriteMessageProductId" class="mt-2 p-2 rounded text-sm" :class="favoriteSuccess ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'">
                {{ favoriteMessage }}
              </div>
              <div>
                <div class="flex items-center justify-between mb-3">
                  <span class="text-lg font-bold text-blue-600">{{ formatPrice(product.price) }} zł</span>
                  <FavoriteButton 
                    :product="product"
                    buttonClasses="p-1.5 rounded-full border border-gray-300 text-gray-400 hover:text-red-500 hover:border-red-300 transition-colors duration-200"
                    @favorite-added="handleFavoriteAdded"
                    @favorite-removed="handleFavoriteRemoved"
                  />
                </div>
                <div class="space-y-2">
                  <button 
                    @click="addToCart(product.id)"
                    :disabled="isCartLoading(product.id)" 
                    class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-medium py-2.5 px-4 rounded-lg transition-all duration-200 text-sm"
                  >
                    <template v-if="isCartLoading(product.id)">
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
import { computed, ref, onMounted } from 'vue';
import { useProductStore } from '../stores/productStore';
import { useCartStore } from '../stores/cartStore';
import { useFavoriteStore } from '../stores/favoriteStore';
import FavoriteButton from '../components/ui/FavoriteButton.vue';
import axios from 'axios';

export default {
  name: 'ProductList',
  components: {
    FavoriteButton
  },
  setup() {
    const productStore = useProductStore();
    const cartStore = useCartStore();
    const favoriteStore = useFavoriteStore();
    const mobileFiltersOpen = ref(false);
    const priceRange = ref([null, null]);
    const cartMessageProductId = ref(null);
    const cartMessage = ref('');
    const cartSuccess = ref(false);
    const cartLoadingItems = ref(new Set());
    const favoriteMessageProductId = ref(null);
    const favoriteMessage = ref('');
    const favoriteSuccess = ref(false);
    
    // Debugging information
    console.log('ProductList component setup started');
    
    onMounted(async () => {
      console.log('ProductList component mounted');
      // Initialize favorites when component is mounted
      await favoriteStore.initializeFavorites();
      // Then load products
      await loadProducts();
    });

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

    const loadProducts = async () => {
      console.log('Loading products method called');
      
      // Get the Laravel configuration
      console.log('Laravel config:', window.Laravel);
      
      // Add diagnostic request to check products API
      try {
        const response = await axios.get('/api/debug/products');
        console.log('Debug info:', response.data);
      } catch (error) {
        console.error('Debug API error:', error);
      }
      
      await productStore.fetchProducts();
      console.log('Products loaded successfully:', productStore.products);
    };

    const applyFilters = () => {
      // Zanim zastosujemy filtry, konwertujemy wartości priceRange na liczby
      const minPrice = priceRange.value[0] !== '' && priceRange.value[0] !== undefined ? parseFloat(priceRange.value[0]) : null;
      const maxPrice = priceRange.value[1] !== '' && priceRange.value[1] !== undefined ? parseFloat(priceRange.value[1]) : null;
      
      console.log('Applying filters with price range:', [minPrice, maxPrice]);
      
      // Przekazujemy zakres cen do filtrów
      productStore.filters.priceRange = [minPrice, maxPrice];
      
      // Pobieramy produkty z zastosowanymi filtrami
      productStore.fetchProducts();
      mobileFiltersOpen.value = false;
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
      mobileFiltersOpen.value = false;
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

    const addToCart = async (productId) => {
      // Show loading indicator for this product
      cartLoadingItems.value.add(productId);
      cartMessageProductId.value = null;
      cartMessage.value = '';
      cartSuccess.value = false;
      
      try {
        const success = await cartStore.addToCart(productId, 1);
        if (success) {
          cartSuccess.value = true;
          cartMessageProductId.value = productId;
          const product = productStore.products.find(p => p.id === productId);
          cartMessage.value = `Produkt "${product?.name || 'wybrany'}" został dodany do koszyka.`;
          
          // Hide the message after a few seconds
          setTimeout(() => {
            if (cartMessageProductId.value === productId) {
              cartMessageProductId.value = null;
              cartMessage.value = '';
            }
          }, 3000);
        } else {
          cartSuccess.value = false;
          cartMessageProductId.value = productId;
          cartMessage.value = 'Nie udało się dodać produktu do koszyka.';
        }
      } catch (error) {
        cartSuccess.value = false;
        cartMessageProductId.value = productId;
        cartMessage.value = 'Wystąpił błąd podczas dodawania produktu do koszyka.';
        console.error('Error adding product to cart:', error);
      } finally {
        cartLoadingItems.value.delete(productId);
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
      favoriteMessage.value = `Produkt "${product.name}" został dodany do ulubionych.`;
      favoriteSuccess.value = true;
      favoriteMessageProductId.value = product.id;
      
      // Clear the message after 3 seconds
      setTimeout(() => {
        if (favoriteMessageProductId.value === product.id) {
          favoriteMessageProductId.value = null;
        }
      }, 3000);
    };
    
    const handleFavoriteRemoved = (product) => {
      favoriteMessage.value = `Produkt "${product.name}" został usunięty z ulubionych.`;
      favoriteSuccess.value = false;
      favoriteMessageProductId.value = product.id;
      
      // Clear the message after 3 seconds
      setTimeout(() => {
        if (favoriteMessageProductId.value === product.id) {
          favoriteMessageProductId.value = null;
        }
      }, 3000);
    };

    return {
      productStore,
      mobileFiltersOpen,
      priceRange,
      cartMessageProductId,
      cartMessage,
      cartSuccess,
      cartLoadingItems,
      favoriteMessageProductId,
      favoriteMessage,
      favoriteSuccess,
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
      handleFavoriteRemoved
    };
  }
}
</script>

<style scoped>
.pb-7\/12 {
  padding-bottom: 58.333333%;
}
</style> 