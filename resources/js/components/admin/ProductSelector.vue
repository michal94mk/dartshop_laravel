<template>
  <div class="space-y-4">
    <!-- Search and filters -->
    <div class="flex flex-col sm:flex-row gap-4">
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

    <!-- Selected products -->
    <div v-if="selectedProducts.length > 0" class="border rounded-lg p-4 bg-gray-50">
      <h4 class="text-sm font-medium text-gray-900 mb-3">Wybrane produkty ({{ selectedProducts.length }})</h4>
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
        <div
          v-for="product in selectedProducts"
          :key="product.id"
          class="flex items-center justify-between bg-white p-3 rounded-lg border"
        >
          <div class="flex items-center space-x-3">
            <img
                              :src="getProductImageUrl(product.image_url, product.name, 40, 40)"
              :alt="product.name"
                              class="w-10 h-10 rounded object-cover"
                @error="(e) => handleImageError(e, product.name, 40, 40)"
            >
            <div>
              <p class="text-sm font-medium text-gray-900">{{ product.name }}</p>
              <p class="text-xs text-gray-500">{{ product.price }} zł</p>
            </div>
          </div>
          <button
            @click="removeProduct(product)"
            class="text-red-600 hover:text-red-800"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
      </div>
    </div>

    <!-- Available products -->
    <div class="border rounded-lg">
      <div class="p-4 border-b bg-gray-50">
        <h4 class="text-sm font-medium text-gray-900">Dostępne produkty</h4>
      </div>
      
      <div v-if="loading" class="p-8 text-center">
        <div class="w-8 h-8 border-2 border-indigo-500 border-t-transparent rounded-full animate-spin mx-auto"></div>
        <p class="mt-2 text-sm text-gray-500">Ładowanie produktów...</p>
      </div>
      
      <div v-else-if="availableProducts.length === 0" class="p-8 text-center">
        <p class="text-sm text-gray-500">Brak dostępnych produktów</p>
      </div>
      
      <div v-else class="max-h-96 overflow-y-auto">
        <div
          v-for="product in availableProducts"
          :key="product.id"
          class="flex items-center justify-between p-4 border-b hover:bg-gray-50 cursor-pointer"
          @click="addProduct(product)"
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
            class="text-indigo-600 hover:text-indigo-800 text-sm font-medium"
          >
            Dodaj
          </button>
        </div>
      </div>
      
      <!-- Pagination -->
      <div v-if="pagination && pagination.last_page > 1" class="p-4 border-t bg-gray-50">
        <div class="flex items-center justify-between">
          <button
            @click="goToPage(pagination.current_page - 1)"
            :disabled="pagination.current_page <= 1"
            class="px-3 py-1 text-sm border rounded disabled:opacity-50 hover:bg-white"
          >
            Poprzednia
          </button>
          <span class="text-sm text-gray-600">
            Strona {{ pagination.current_page }} z {{ pagination.last_page }}
          </span>
          <button
            @click="goToPage(pagination.current_page + 1)"
            :disabled="pagination.current_page >= pagination.last_page"
            class="px-3 py-1 text-sm border rounded disabled:opacity-50 hover:bg-white"
          >
            Następna
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import { getProductImageUrl, handleImageError } from '../../utils/imageHelpers';

export default {
  name: 'ProductSelector',
  props: {
    modelValue: {
      type: Array,
      default: () => []
    },
    existingProducts: {
      type: Array,
      default: () => []
    }
  },
  emits: ['update:modelValue', 'products-changed'],
  data() {
    return {
      availableProducts: [],
      categories: [],
      selectedProducts: [],
      loading: false,
      searchQuery: '',
      selectedCategory: '',
      pagination: null,
      debouncedSearch: null
    };
  },
  mounted() {
    this.selectedProducts = [...this.existingProducts];
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
  watch: {
    existingProducts: {
      handler(newProducts) {
        this.selectedProducts = [...newProducts];
      },
      deep: true
    }
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
      this.loading = true;
      try {
        const params = {
          page,
          per_page: 20,
          search: this.searchQuery,
          category_id: this.selectedCategory
        };
        
        const response = await axios.get('/api/admin/available-products', { params });
        this.availableProducts = response.data.data.filter(
          product => !this.selectedProducts.find(selected => selected.id === product.id)
        );
        this.pagination = {
          current_page: response.data.current_page,
          last_page: response.data.last_page,
          total: response.data.total
        };
      } catch (error) {
        console.error('Error searching products:', error);
      } finally {
        this.loading = false;
      }
    },

    addProduct(product) {
      if (!this.selectedProducts.find(p => p.id === product.id)) {
        this.selectedProducts.push(product);
        this.availableProducts = this.availableProducts.filter(p => p.id !== product.id);
        this.emitChange();
      }
    },

    removeProduct(product) {
      this.selectedProducts = this.selectedProducts.filter(p => p.id !== product.id);
      this.searchProducts();
      this.emitChange();
    },

    goToPage(page) {
      if (page >= 1 && page <= this.pagination.last_page) {
        this.searchProducts(page);
      }
    },

    emitChange() {
      this.$emit('update:modelValue', this.selectedProducts.map(p => p.id));
      this.$emit('products-changed', this.selectedProducts);
    },

    // Image helpers
    getProductImageUrl,
    handleImageError
  }
};
</script> 