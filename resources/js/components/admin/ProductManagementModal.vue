<template>
  <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full flex items-center justify-center z-50">
    <div class="relative mx-auto p-5 border w-full max-w-4xl shadow-lg rounded-md bg-white max-h-[90vh] overflow-y-auto">
      <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-medium text-gray-900">
          Zarządzaj produktami w promocji: {{ promotion.name }}
        </h3>
        <button @click="$emit('close')" class="text-gray-400 hover:text-gray-500">
          <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

      <!-- Promotion info -->
      <div class="mb-6 p-4 bg-gray-50 rounded-lg">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
          <div>
            <span class="font-medium text-gray-700">Typ:</span>
            <span class="ml-2">{{ promotion.discount_type === 'percentage' ? 'Procentowa' : 'Kwotowa' }}</span>
          </div>
          <div>
            <span class="font-medium text-gray-700">Wartość:</span>
            <span class="ml-2">{{ promotion.discount_type === 'percentage' ? `${promotion.discount_value}%` : `${promotion.discount_value} zł` }}</span>
          </div>
          <div>
            <span class="font-medium text-gray-700">Status:</span>
            <span :class="promotion.is_active ? 'text-green-600' : 'text-red-600'" class="ml-2 font-medium">
              {{ promotion.is_active ? 'Aktywna' : 'Nieaktywna' }}
            </span>
          </div>
        </div>
      </div>

      <!-- Current products in promotion -->
      <div class="mb-6">
        <h4 class="text-md font-medium text-gray-900 mb-3">
          Produkty w promocji ({{ currentProducts.length }})
        </h4>
        
        <div v-if="currentProducts.length === 0" class="text-center py-8 text-gray-500">
          <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2 2v-5m16 0h-2M4 13h2m13-8V4a1 1 0 00-1-1H7a1 1 0 00-1 1v1m8 0V4.5"/>
          </svg>
          <p>Brak produktów w tej promocji</p>
        </div>
        
        <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
          <div
            v-for="product in currentProducts"
            :key="product.id"
            class="border rounded-lg p-4 bg-white hover:shadow-md transition-shadow"
          >
            <div class="flex items-start space-x-3">
              <img
                :src="getProductImageUrl(product.image_url, product.name, 64, 64)"
                :alt="product.name"
                class="w-16 h-16 rounded object-cover flex-shrink-0"
                @error="(e) => handleImageError(e, product.name, 64, 64)"
              >
              <div class="flex-1 min-w-0">
                <h5 class="text-sm font-medium text-gray-900 truncate">{{ product.name }}</h5>
                <p class="text-xs text-gray-500 mb-2">{{ product.category?.name }}</p>
                <div class="flex items-center justify-between">
                  <div class="text-sm">
                    <span class="line-through text-gray-400">{{ product.price }} zł</span>
                    <span class="ml-2 font-medium text-green-600">
                      {{ calculatePromotionalPrice(product) }} zł
                    </span>
                  </div>
                  <button
                    @click="removeProduct(product)"
                    :disabled="removing.includes(product.id)"
                    class="text-red-600 hover:text-red-800 disabled:opacity-50"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Add products section -->
      <div class="border-t pt-6">
        <h4 class="text-md font-medium text-gray-900 mb-3">Dodaj produkty do promocji</h4>
        
        <!-- Search and filters -->
        <div class="flex flex-col sm:flex-row gap-4 mb-4">
          <div class="flex-1">
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Wyszukaj produkty..."
              class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
              @input="debouncedSearch"
            >
          </div>
          <div class="w-full sm:w-48">
            <select
              v-model="selectedCategory"
              @change="searchProducts"
              class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
            >
              <option value="">Wszystkie kategorie</option>
              <option v-for="category in categories" :key="category.id" :value="category.id">
                {{ category.name }}
              </option>
            </select>
          </div>
        </div>

        <!-- Available products -->
        <div class="border rounded-lg max-h-96 overflow-y-auto">
          <div v-if="loadingProducts" class="p-8 text-center">
            <div class="w-8 h-8 border-2 border-indigo-500 border-t-transparent rounded-full animate-spin mx-auto"></div>
            <p class="mt-2 text-sm text-gray-500">Ładowanie produktów...</p>
          </div>
          
          <div v-else-if="availableProducts.length === 0" class="p-8 text-center">
            <p class="text-sm text-gray-500">Brak dostępnych produktów</p>
          </div>
          
          <div v-else>
            <div
              v-for="product in availableProducts"
              :key="product.id"
              class="flex items-center justify-between p-4 border-b hover:bg-gray-50"
            >
              <div class="flex items-center space-x-3">
                <img
                  :src="getProductImageUrl(product.image_url, product.name, 48, 48)"
                  :alt="product.name"
                  class="w-12 h-12 rounded object-cover"
                  @error="(e) => handleImageError(e, product.name, 48, 48)"
                >
                <div>
                  <p class="text-sm font-medium text-gray-900">{{ product.name }}</p>
                  <p class="text-xs text-gray-500">{{ product.category?.name }} • {{ product.price }} zł</p>
                </div>
              </div>
              <button
                @click="addProduct(product)"
                :disabled="adding.includes(product.id)"
                class="px-3 py-1 text-sm bg-indigo-600 text-white rounded hover:bg-indigo-700 disabled:opacity-50"
              >
                {{ adding.includes(product.id) ? 'Dodawanie...' : 'Dodaj' }}
              </button>
            </div>
          </div>
        </div>

        <!-- Pagination for available products -->
        <div v-if="pagination && pagination.last_page > 1" class="mt-4 flex items-center justify-between">
          <button
            @click="goToPage(pagination.current_page - 1)"
            :disabled="pagination.current_page <= 1"
            class="px-3 py-1 text-sm border rounded disabled:opacity-50 hover:bg-gray-50"
          >
            Poprzednia
          </button>
          <span class="text-sm text-gray-600">
            Strona {{ pagination.current_page }} z {{ pagination.last_page }}
          </span>
          <button
            @click="goToPage(pagination.current_page + 1)"
            :disabled="pagination.current_page >= pagination.last_page"
            class="px-3 py-1 text-sm border rounded disabled:opacity-50 hover:bg-gray-50"
          >
            Następna
          </button>
        </div>
      </div>

      <!-- Close button -->
      <div class="flex justify-end mt-6 pt-4 border-t">
        <button
          @click="$emit('close')"
          class="px-4 py-2 bg-gray-500 text-white text-sm font-medium rounded-md hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-300"
        >
          Zamknij
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import { getProductImageUrl, handleImageError } from '../../utils/imageHelpers';

export default {
  name: 'ProductManagementModal',
  props: {
    promotion: {
      type: Object,
      required: true
    }
  },
  emits: ['close', 'updated'],
  data() {
    return {
      currentProducts: [],
      availableProducts: [],
      categories: [],
      loadingProducts: false,
      searchQuery: '',
      selectedCategory: '',
      pagination: null,
      adding: [],
      removing: [],
      debouncedSearch: null
    };
  },
  mounted() {
    this.currentProducts = [...(this.promotion.products || [])];
    this.fetchCategories();
    this.searchProducts();
    
    // Create debounced search function
    let timeout;
    this.debouncedSearch = () => {
      clearTimeout(timeout);
      timeout = setTimeout(() => {
        this.searchProducts();
      }, 300);
    };
  },
  methods: {
    async fetchCategories() {
      try {
        const response = await axios.get('/api/categories');
        // Handle new API response format (BaseApiController)
        if (response.data && response.data.success && response.data.data) {
          this.categories = response.data.data;
        } else if (response.data && response.data.data) {
          this.categories = response.data.data;
        } else if (Array.isArray(response.data)) {
          this.categories = response.data;
        } else {
          this.categories = [];
        }
      } catch (error) {
        console.error('Error fetching categories:', error);
        this.categories = [];
      }
    },

    async searchProducts(page = 1) {
      this.loadingProducts = true;
      try {
        const params = {
          page,
          per_page: 20,
          search: this.searchQuery,
          category_id: this.selectedCategory
        };
        
        const response = await axios.get('/api/admin/available-products', { params });
        
        this.availableProducts = response.data.data.filter(
          product => !this.currentProducts.find(current => current.id === product.id)
        );
        
        this.pagination = {
          current_page: response.data.current_page,
          last_page: response.data.last_page,
          total: response.data.total
        };
      } catch (error) {
        console.error('Error searching products:', error);
      } finally {
        this.loadingProducts = false;
      }
    },

    async addProduct(product) {
      this.adding.push(product.id);
      try {
        await axios.post(`/api/admin/promotions/${this.promotion.id}/attach-products`, {
          product_ids: [product.id]
        });
        
        this.currentProducts.push(product);
        this.availableProducts = this.availableProducts.filter(p => p.id !== product.id);
        
        this.$emit('updated');
      } catch (error) {
        console.error('Error adding product to promotion:', error);
      } finally {
        this.adding = this.adding.filter(id => id !== product.id);
      }
    },

    async removeProduct(product) {
      this.removing.push(product.id);
      try {
        await axios.post(`/api/admin/promotions/${this.promotion.id}/detach-products`, {
          product_ids: [product.id]
        });
        
        this.currentProducts = this.currentProducts.filter(p => p.id !== product.id);
        this.searchProducts();
        
        this.$emit('updated');
      } catch (error) {
        console.error('Error removing product from promotion:', error);
      } finally {
        this.removing = this.removing.filter(id => id !== product.id);
      }
    },

    goToPage(page) {
      if (page >= 1 && page <= this.pagination.last_page) {
        this.searchProducts(page);
      }
    },

    calculatePromotionalPrice(product) {
      const price = parseFloat(product.price);
      if (this.promotion.discount_type === 'percentage') {
        return (price * (1 - this.promotion.discount_value / 100)).toFixed(2);
      } else {
        return Math.max(0, price - this.promotion.discount_value).toFixed(2);
      }
    },

    // Image helpers
    getProductImageUrl,
    handleImageError
  }
};
</script> 