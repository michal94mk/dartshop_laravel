<template>
  <div class="bg-white">
    <div class="max-w-7xl mx-auto py-16 px-4 sm:px-6 lg:px-8">
      <h1 class="text-3xl font-extrabold tracking-tight text-gray-900">Produkty</h1>
      
      <div class="mt-8 lg:grid lg:grid-cols-5 lg:gap-x-8">
        <!-- Mobile filter dialog -->
        <div class="relative z-40 lg:hidden">
          <button @click="mobileFiltersOpen = true" class="w-full bg-gray-100 p-3 rounded-md flex items-center justify-center text-gray-700 hover:bg-gray-200">
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
                <!-- Sort options - Mobile -->
                <div class="p-4 border-t border-gray-200">
                  <h3 class="font-medium text-gray-900">Sortowanie</h3>
                  <div class="pt-4">
                    <select 
                      v-model="productStore.filters.sort"
                      @change="applyFilters"
                      class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                    >
                      <option value="newest">Najnowsze</option>
                      <option value="price_asc">Cena: rosnąco</option>
                      <option value="price_desc">Cena: malejąco</option>
                      <option value="name_asc">Nazwa: A-Z</option>
                      <option value="name_desc">Nazwa: Z-A</option>
                    </select>
                  </div>
                </div>
                
                <!-- Price range - Mobile -->
                <div class="p-4 border-t border-gray-200">
                  <h3 class="font-medium text-gray-900">Zakres cenowy</h3>
                  <div class="pt-4 grid grid-cols-2 gap-4">
                    <div>
                      <label for="mobile-price-min" class="sr-only">Cena od</label>
                      <input 
                        type="number" 
                        id="mobile-price-min" 
                        v-model="priceRange[0]"
                        placeholder="Od" 
                        min="0" 
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                      >
                    </div>
                    <div>
                      <label for="mobile-price-max" class="sr-only">Cena do</label>
                      <input 
                        type="number" 
                        id="mobile-price-max" 
                        v-model="priceRange[1]"
                        placeholder="Do" 
                        min="0" 
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                      >
                    </div>
                  </div>
                </div>
                
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

        <!-- Filters - Desktop -->
        <div class="hidden lg:block">
          <div class="divide-y divide-gray-200 space-y-10">
            <!-- Sort options - Desktop -->
            <div>
              <fieldset>
                <legend class="block text-sm font-medium text-gray-900">Sortowanie</legend>
                <div class="pt-4">
                  <select 
                    v-model="productStore.filters.sort"
                    @change="applyFilters"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                  >
                    <option value="newest">Najnowsze</option>
                    <option value="price_asc">Cena: rosnąco</option>
                    <option value="price_desc">Cena: malejąco</option>
                    <option value="name_asc">Nazwa: A-Z</option>
                    <option value="name_desc">Nazwa: Z-A</option>
                  </select>
                </div>
              </fieldset>
            </div>
            
            <!-- Price range - Desktop -->
            <div class="pt-10">
              <fieldset>
                <legend class="block text-sm font-medium text-gray-900">Zakres cenowy</legend>
                <div class="pt-4 grid grid-cols-2 gap-4">
                  <div>
                    <label for="price-min" class="sr-only">Cena od</label>
                    <input 
                      type="number" 
                      id="price-min" 
                      v-model="priceRange[0]"
                      placeholder="Od" 
                      min="0" 
                      class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                    >
                  </div>
                  <div>
                    <label for="price-max" class="sr-only">Cena do</label>
                    <input 
                      type="number" 
                      id="price-max" 
                      v-model="priceRange[1]"
                      placeholder="Do" 
                      min="0" 
                      class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                    >
                  </div>
                </div>
              </fieldset>
            </div>
            
            <!-- Action buttons - Desktop -->
            <div class="pt-10">
              <button 
                @click="applyFilters"
                class="w-full bg-indigo-600 border border-transparent rounded-md py-2 px-4 text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
              >
                Zastosuj filtry
              </button>
              <button 
                @click="resetFilters"
                class="mt-4 w-full bg-gray-200 border border-transparent rounded-md py-2 px-4 text-sm font-medium text-gray-700 hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500"
              >
                Wyczyść filtry
              </button>
            </div>
          </div>
        </div>

        <!-- Product grid and sorting bar -->
        <div class="mt-8 lg:mt-0 lg:col-span-4">
          <div class="flex justify-between items-center mb-6">
            <div class="text-sm text-gray-600">
              Wyświetlanie {{ productStore.products.length }} z {{ productStore.pagination.total }} produktów
            </div>
            <div class="flex items-center">
              <span class="mr-2 text-sm text-gray-600">Sortuj według:</span>
              <select 
                v-model="productStore.filters.sort"
                @change="applyFilters"
                class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
              >
                <option value="newest">Najnowsze</option>
                <option value="price_asc">Cena: rosnąco</option>
                <option value="price_desc">Cena: malejąco</option>
                <option value="name_asc">Nazwa: A-Z</option>
                <option value="name_desc">Nazwa: Z-A</option>
              </select>
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
          
          <div v-else class="grid grid-cols-1 gap-y-8 gap-x-6 sm:grid-cols-2 lg:grid-cols-3">
            <div v-for="product in productStore.products" :key="product.id" class="bg-white overflow-hidden shadow rounded-lg">
              <div class="relative pb-7/12">
                <img 
                  :src="product.image_url || 'https://via.placeholder.com/300x300/indigo/fff?text=' + product.name" 
                  :alt="product.name" 
                  class="absolute h-full w-full object-cover"
                >
                <button 
                  @click.prevent="toggleFavorite(product)"
                  class="absolute top-2 right-2 p-2 bg-white rounded-full shadow hover:bg-gray-100"
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
              <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900">{{ product.name }}</h3>
                <p class="mt-1 text-sm text-gray-500">{{ product.short_description || product.description }}</p>
                <div v-if="product.id === cartMessageProductId" class="mt-2 p-2 rounded text-sm" :class="cartSuccess ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'">
                  {{ cartMessage }}
                </div>
                <div class="mt-4 flex items-center justify-between">
                  <span class="text-indigo-600 font-bold">{{ formatPrice(product.price) }} zł</span>
                  <div class="flex space-x-2">
                    <button 
                      @click="addToCart(product)"
                      class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700"
                      :disabled="cartStore.loading"
                    >
                      <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                      </svg>
                      Dodaj do koszyka
                    </button>
                    <router-link :to="`/products/${product.id}`" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200">
                      Zobacz
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
  </div>
</template>

<script>
import { computed } from 'vue';
import { useProductStore } from '../stores/productStore';
import { useCartStore } from '../stores/cartStore';
import { useWishlistStore } from '../stores/wishlistStore';

export default {
  name: 'ProductList',
  data() {
    return {
      mobileFiltersOpen: false,
      priceRange: [0, 1000],
      cartMessage: '',
      cartSuccess: false,
      cartMessageProductId: null,
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
      this.productStore.fetchProducts()
        .then(() => {
          console.log('Products loaded successfully:', this.productStore.products);
        })
        .catch(error => {
          console.error('Failed to load products:', error);
          // Don't load fallback products anymore as we have real data
          // this.loadFallbackProducts();
        });
    },
    applyFilters() {
      this.productStore.filters.priceRange = this.priceRange;
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
      this.cartStore.addToCart(product.id)
        .then(() => {
          this.cartMessage = 'Produkt został dodany do koszyka';
          this.cartSuccess = true;
          
          // Clear message after 3 seconds
          setTimeout(() => {
            if (this.cartMessageProductId === product.id) {
              this.cartMessage = '';
              this.cartMessageProductId = null;
            }
          }, 3000);
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
    }
  }
}
</script>

<style scoped>
.pb-7\/12 {
  padding-bottom: 58.333333%;
}
</style> 