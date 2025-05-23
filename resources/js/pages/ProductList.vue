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
          <div v-for="product in productStore.products" :key="product.id" class="bg-white overflow-hidden shadow-sm rounded-lg transition-all hover:shadow-md flex flex-col h-full group">
            <div class="relative">
              <div class="aspect-square overflow-hidden">
                <img 
                  :src="product.image_url || 'https://via.placeholder.com/300x300/indigo/fff?text=' + product.name" 
                  :alt="product.name" 
                  class="h-full w-full object-cover group-hover:scale-105 transition-transform duration-300"
                  loading="lazy"
                >
                <div class="absolute inset-0 bg-black bg-opacity-10 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
              </div>
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
              <div v-if="product.id === favoriteMessageProductId" class="mt-2 p-2 rounded text-sm" :class="favoriteSuccess ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'">
                {{ favoriteMessage }}
              </div>
              <div class="mt-4 pt-3 border-t border-gray-100">
                <span class="text-indigo-600 font-bold text-xl block mb-3">{{ formatPrice(product.price) }} zł</span>
                <div class="mt-4 flex flex-col space-y-2">
                  <button 
                    @click="addToCart(product.id)"
                    :disabled="isCartLoading(product.id)" 
                    class="w-full h-10 inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 transition-colors duration-200"
                  >
                    <template v-if="isCartLoading(product.id)">
                      <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                      </svg>
                      Dodawanie...
                    </template>
                    <template v-else>
                      <i class="fas fa-shopping-cart mr-1"></i>
                      Dodaj do koszyka
                    </template>
                  </button>
                  <div class="flex space-x-2">
                    <router-link 
                      :to="`/products/${product.id}`" 
                      class="flex-1 h-10 inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 transition-colors duration-200"
                    >
                      Szczegóły produktu
                    </router-link>
                    <FavoriteButton 
                      :product="product"
                      buttonClasses="h-10 inline-flex items-center justify-center px-3 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition-colors duration-200"
                      @favorite-added="handleFavoriteAdded"
                      @favorite-removed="handleFavoriteRemoved"
                    />
                  </div>
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
      if (typeof page === 'number' && page !== productStore.pagination.currentPage) {
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