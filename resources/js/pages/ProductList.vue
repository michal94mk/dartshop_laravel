<template>
  <div class="bg-gray-50">
    <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
      <h1 class="text-3xl font-extrabold tracking-tight text-gray-900 text-center mb-8">Produkty</h1>
      
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
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
          <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <!-- Enhanced sorting interface -->
            <div class="bg-gray-50 rounded-md p-4 flex-grow">
              <div class="flex flex-col sm:flex-row sm:items-center gap-2">
                <span class="text-sm font-medium text-gray-700">Sortuj według:</span>
                <div class="flex flex-wrap gap-2">
                  <button 
                    @click="setSorting('newest')" 
                    class="px-3 py-1.5 rounded-md text-sm transition-colors duration-200"
                    :class="{'bg-indigo-600 text-white hover:bg-indigo-700': productStore.filters.sort === 'newest', 'bg-white text-gray-700 hover:bg-gray-200': productStore.filters.sort !== 'newest'}"
                  >
                    Najnowsze
                  </button>
                  <button 
                    @click="setSorting('price_asc')" 
                    class="px-3 py-1.5 rounded-md text-sm transition-colors duration-200"
                    :class="{'bg-indigo-600 text-white hover:bg-indigo-700': productStore.filters.sort === 'price_asc', 'bg-white text-gray-700 hover:bg-gray-200': productStore.filters.sort !== 'price_asc'}"
                  >
                    <span class="hidden sm:inline">Cena: </span>↑
                  </button>
                  <button 
                    @click="setSorting('price_desc')" 
                    class="px-3 py-1.5 rounded-md text-sm transition-colors duration-200"
                    :class="{'bg-indigo-600 text-white hover:bg-indigo-700': productStore.filters.sort === 'price_desc', 'bg-white text-gray-700 hover:bg-gray-200': productStore.filters.sort !== 'price_desc'}"
                  >
                    <span class="hidden sm:inline">Cena: </span>↓
                  </button>
                  <button 
                    @click="setSorting('name_asc')" 
                    class="px-3 py-1.5 rounded-md text-sm transition-colors duration-200"
                    :class="{'bg-indigo-600 text-white hover:bg-indigo-700': productStore.filters.sort === 'name_asc', 'bg-white text-gray-700 hover:bg-gray-200': productStore.filters.sort !== 'name_asc'}"
                  >
                    <span class="hidden sm:inline">Nazwa: </span>A-Z
                  </button>
                  <button 
                    @click="setSorting('name_desc')" 
                    class="px-3 py-1.5 rounded-md text-sm transition-colors duration-200"
                    :class="{'bg-indigo-600 text-white hover:bg-indigo-700': productStore.filters.sort === 'name_desc', 'bg-white text-gray-700 hover:bg-gray-200': productStore.filters.sort !== 'name_desc'}"
                  >
                    <span class="hidden sm:inline">Nazwa: </span>Z-A
                  </button>
                </div>
              </div>
            </div>

            <!-- Price range filter -->
            <div class="bg-gray-50 rounded-md p-4">
              <div class="grid grid-cols-1 gap-2">
                <span class="text-sm font-medium text-gray-700">Zakres cen:</span>
                <div class="flex items-center gap-2">
                  <div class="flex">
                    <input 
                      type="number" 
                      v-model="priceRange[0]"
                      placeholder="Od" 
                      min="0" 
                      class="w-24 rounded-l-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                    >
                    <span class="px-2 inline-flex items-center bg-gray-100">-</span>
                    <input 
                      type="number" 
                      v-model="priceRange[1]"
                      placeholder="Do" 
                      min="0" 
                      class="w-24 rounded-r-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                    >
                  </div>
                  <button 
                    @click="applyPriceFilter"
                    class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition-colors duration-200"
                  >
                    Filtruj
                  </button>
                  <button 
                    @click="resetFilters"
                    class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition-colors duration-200"
                  >
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
        
        <div v-else class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
          <div v-for="product in productStore.products" :key="product.id" class="bg-white overflow-hidden shadow-sm rounded-lg transition-all hover:shadow-md flex flex-col h-full">
            <div class="relative aspect-square overflow-hidden">
              <img 
                :src="product.image_url || 'https://via.placeholder.com/300x300/indigo/fff?text=' + product.name" 
                :alt="product.name" 
                class="h-full w-full object-cover hover:scale-105 transition-transform duration-300"
                loading="lazy"
              >
              <button 
                @click.prevent="toggleFavorite(product)"
                class="absolute top-2 right-2 p-2 bg-white rounded-full shadow hover:bg-gray-100 transition-colors duration-200"
              >
                <svg 
                  class="w-5 h-5" 
                  :class="{ 'text-red-500 fill-current': isInFavorites(product.id), 'text-gray-400': !isInFavorites(product.id) }"
                  xmlns="http://www.w3.org/2000/svg" 
                  viewBox="0 0 24 24" 
                  stroke="currentColor"
                >
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                </svg>
              </button>
            </div>
            <div class="p-5 flex-grow flex flex-col">
              <h3 class="text-lg font-medium text-gray-900 line-clamp-2 min-h-[3.5rem]">{{ product.name }}</h3>
              <p class="mt-2 text-sm text-gray-500 line-clamp-2 flex-grow min-h-[2.5rem]">{{ product.short_description || product.description }}</p>
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
              <div class="mt-4 pt-3 border-t border-gray-100">
                <span class="text-indigo-600 font-bold text-xl block mb-3">{{ formatPrice(product.price) }} zł</span>
                <div class="grid grid-cols-2 gap-3">
                  <button 
                    @click="addToCart(product)"
                    class="h-10 inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 transition-colors duration-200"
                    :disabled="isCartLoading(product.id)"
                  >
                    <template v-if="isCartLoading(product.id)">
                      <svg class="animate-spin w-4 h-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                      </svg>
                      Dodaję...
                    </template>
                    <template v-else>
                      <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                      </svg>
                      Koszyk
                    </template>
                  </button>
                  <router-link :to="`/products/${product.id}`" class="h-10 inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 transition-colors duration-200">
                    Szczegóły
                  </router-link>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Pagination -->
        <div v-if="productStore.pagination.totalPages > 1" class="mt-10 flex justify-center">
          <nav class="flex items-center">
            <button 
              @click="goToPage(productStore.pagination.currentPage - 1)"
              :disabled="productStore.pagination.currentPage === 1"
              class="px-3 py-1 rounded-md mr-2 border border-gray-300 bg-white text-gray-700 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              &laquo; Poprzednia
            </button>
            
            <div v-for="page in paginationPages" :key="page" class="mx-1">
              <button 
                @click="goToPage(page)"
                :class="[
                  'px-3 py-1 rounded-md border',
                  page === productStore.pagination.currentPage 
                    ? 'bg-indigo-600 text-white border-indigo-600' 
                    : 'bg-white text-gray-700 border-gray-300'
                ]"
              >
                {{ page }}
              </button>
            </div>
            
            <button 
              @click="goToPage(productStore.pagination.currentPage + 1)"
              :disabled="productStore.pagination.currentPage === productStore.pagination.totalPages"
              class="px-3 py-1 rounded-md ml-2 border border-gray-300 bg-white text-gray-700 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              Następna &raquo;
            </button>
          </nav>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { computed } from 'vue';
import { useProductStore } from '../stores/productStore';
import { useCartStore } from '../stores/cartStore';
import { useWishlistStore } from '../stores/wishlistStore';
import axios from 'axios';

export default {
  name: 'ProductList',
  data() {
    return {
      mobileFiltersOpen: false,
      priceRange: [0, 1000],
      cartMessage: '',
      cartSuccess: false,
      cartMessageProductId: null,
      loadingProductId: null,
      fallbackProducts: [
        {
          id: 1,
          name: 'Lotki Target Agora A30',
          description: 'Profesjonalne lotki ze stali wolframowej 90%',
          price: 149.99,
          image_url: 'https://via.placeholder.com/300x300/indigo/fff?text=Lotki+Target'
        },
        {
          id: 2,
          name: 'Tarcza elektroniczna Winmau Blade 6',
          description: 'Zaawansowana tarcza dla profesjonalistów',
          price: 299.99,
          image_url: 'https://via.placeholder.com/300x300/indigo/fff?text=Tarcza+Winmau'
        },
        {
          id: 3,
          name: 'Zestaw punktowy XQ Max',
          description: 'Zestaw do zapisywania punktów z kredą i ścierką',
          price: 49.99,
          image_url: 'https://via.placeholder.com/300x300/indigo/fff?text=Zestaw+XQ+Max'
        },
        {
          id: 4,
          name: 'Lotki Red Dragon Razor Edge',
          description: 'Lotki z wysokiej jakości stali wolframowej',
          price: 129.99,
          image_url: 'https://via.placeholder.com/300x300/indigo/fff?text=Lotki+Red+Dragon'
        },
        {
          id: 5,
          name: 'Oche treningowe',
          description: 'Profesjonalne oche do treningu',
          price: 89.99,
          image_url: 'https://via.placeholder.com/300x300/indigo/fff?text=Oche'
        },
        {
          id: 6,
          name: 'Zestaw dart Unicorn',
          description: 'Kompletny zestaw do gry w dart',
          price: 199.99,
          image_url: 'https://via.placeholder.com/300x300/indigo/fff?text=Zestaw+Unicorn'
        }
      ]
    }
  },
  computed: {
    paginationPages() {
      const totalPages = this.productStore.pagination.totalPages;
      const currentPage = this.productStore.pagination.currentPage;
      
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
    }
  },
  created() {
    this.productStore = useProductStore();
    this.cartStore = useCartStore();
    this.wishlistStore = useWishlistStore();
  },
  mounted() {
    console.log('ProductList component mounted');
    console.log('Starting to load products...');
    this.loadProducts();
  },
  methods: {
    loadProducts() {
      console.log('Loading products method called');
      
      // Add diagnostic request to check products API
      axios.get('/api/debug/products')
        .then(response => {
          console.log('Debug info:', response.data);
        })
        .catch(error => {
          console.error('Debug API error:', error);
        });
        
      this.productStore.fetchProducts()
        .then(() => {
          console.log('Products loaded successfully:', this.productStore.products);
        })
        .catch(error => {
          console.error('Failed to load products:', error);
        });
    },
    applyFilters() {
      // Zanim zastosujemy filtry, konwertujemy wartości priceRange na liczby
      const minPrice = this.priceRange[0] !== '' && this.priceRange[0] !== undefined ? parseFloat(this.priceRange[0]) : null;
      const maxPrice = this.priceRange[1] !== '' && this.priceRange[1] !== undefined ? parseFloat(this.priceRange[1]) : null;
      
      console.log('Applying filters with price range:', [minPrice, maxPrice]);
      
      // Przekazujemy zakres cen do filtrów
      this.productStore.filters.priceRange = [minPrice, maxPrice];
      
      // Pobieramy produkty z zastosowanymi filtrami
      this.productStore.fetchProducts();
      this.mobileFiltersOpen = false;
    },
    resetFilters() {
      this.productStore.filters = {
        category: null,
        brand: null,
        search: '',
        priceRange: [0, 1000],
        sort: 'newest'
      };
      this.priceRange = [0, 1000];
      this.productStore.fetchProducts();
      this.mobileFiltersOpen = false;
    },
    goToPage(page) {
      if (typeof page === 'number' && page !== this.productStore.pagination.currentPage) {
        this.productStore.setPage(page);
      }
    },
    formatPrice(price) {
      if (price === null || price === undefined || isNaN(price)) {
        return '0.00';
      }
      return parseFloat(price).toFixed(2);
    },
    addToCart(product) {
      this.cartMessageProductId = product.id;
      this.loadingProductId = product.id;
      this.cartMessage = '';
      this.cartSuccess = false;
      
      this.cartStore.addToCart(product.id)
        .then(() => {
          this.cartMessage = `Produkt "${product.name}" został dodany do koszyka.`;
          this.cartSuccess = true;
          
          // Clear message after 5 seconds
          setTimeout(() => {
            if (this.cartMessageProductId === product.id) {
              this.cartMessage = '';
              this.cartMessageProductId = null;
            }
          }, 5000);
        })
        .catch(error => {
          console.error('Failed to add to cart:', error);
          this.cartMessage = 'Nie udało się dodać produktu do koszyka';
          this.cartSuccess = false;
          
          // Clear message after 3 seconds
          setTimeout(() => {
            if (this.cartMessageProductId === product.id) {
              this.cartMessage = '';
              this.cartMessageProductId = null;
            }
          }, 3000);
        });
    },
    toggleFavorite(product) {
      this.wishlistStore.toggleWishlistItem(product);
    },
    isInFavorites(productId) {
      return this.wishlistStore.isInWishlist(productId);
    },
    setSorting(sort) {
      this.productStore.filters.sort = sort;
      this.applyFilters();
    },
    applyPriceFilter() {
      this.productStore.filters.priceRange = this.priceRange;
      this.applyFilters();
    },
    isCartLoading(productId) {
      return this.cartStore.isLoadingProduct(productId);
    }
  }
}
</script>

<style scoped>
.pb-7\/12 {
  padding-bottom: 58.333333%;
}
</style> 