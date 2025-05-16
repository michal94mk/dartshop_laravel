<template>
  <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <div v-if="loading" class="text-center py-10">
      <div class="w-12 h-12 border-4 border-indigo-500 border-t-transparent rounded-full animate-spin mx-auto"></div>
      <p class="mt-2 text-gray-500">Ładowanie produktu...</p>
    </div>
    
    <div v-else-if="error" class="text-center py-10">
      <p class="text-red-500">{{ error }}</p>
      <button @click="fetchProduct" class="mt-4 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200">
        Spróbuj ponownie
      </button>
    </div>
    
    <div v-else-if="product" class="bg-white shadow-lg rounded-lg overflow-hidden">
      <!-- Breadcrumbs -->
      <div class="px-6 py-4 border-b border-gray-200">
        <nav class="flex" aria-label="Breadcrumb">
          <ol role="list" class="flex items-center space-x-2">
            <li>
              <router-link to="/" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-home"></i>
                <span class="sr-only">Home</span>
              </router-link>
            </li>
            <li>
              <div class="flex items-center">
                <i class="fas fa-chevron-right text-gray-400 text-xs mx-1"></i>
                <router-link to="/products" class="text-gray-500 hover:text-gray-700">Produkty</router-link>
              </div>
            </li>
            <li v-if="product.category">
              <div class="flex items-center">
                <i class="fas fa-chevron-right text-gray-400 text-xs mx-1"></i>
                <router-link :to="`/products?category=${product.category.id}`" class="text-gray-500 hover:text-gray-700">
                  {{ product.category.name }}
                </router-link>
              </div>
            </li>
            <li>
              <div class="flex items-center">
                <i class="fas fa-chevron-right text-gray-400 text-xs mx-1"></i>
                <span class="text-gray-900 font-medium">{{ product.name }}</span>
              </div>
            </li>
          </ol>
        </nav>
      </div>
      
      <!-- Product details -->
      <div class="md:flex">
        <!-- Product image -->
        <div class="md:w-1/2">
          <div class="relative pb-3/4">
            <img 
              :src="product.image_url || `https://via.placeholder.com/800x600/indigo/fff?text=${product.name}`" 
              :alt="product.name" 
              class="absolute h-full w-full object-cover"
            >
          </div>
        </div>
        
        <!-- Product info -->
        <div class="md:w-1/2 p-6">
          <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900">{{ product.name }}</h1>
            <p v-if="product.brand" class="mt-1 text-sm text-gray-500">
              {{ product.brand.name }}
            </p>
          </div>
          
          <div class="mb-6">
            <div class="flex items-center">
              <div class="flex text-yellow-400">
                <span v-for="i in 5" :key="i" class="mr-1">
                  <i :class="i <= (product.rating || 0) ? 'fas fa-star' : 'far fa-star'"></i>
                </span>
              </div>
              <span class="ml-2 text-sm text-gray-500">
                {{ product.reviews_count || 0 }} opinii
              </span>
            </div>
          </div>
          
          <div class="mb-6">
            <p class="text-xl font-bold text-indigo-600">{{ formatPrice(product.price) }} zł</p>
            <p v-if="product.old_price" class="mt-1 text-sm text-gray-500">
              <span class="line-through">{{ formatPrice(product.old_price) }} zł</span>
              <span class="ml-2 text-green-600">{{ calculateDiscount(product.price, product.old_price) }}% taniej</span>
            </p>
          </div>
          
          <div class="mb-6">
            <h2 class="text-lg font-medium text-gray-900">Opis</h2>
            <div class="mt-2 text-gray-600 space-y-4">
              <p>{{ product.description }}</p>
            </div>
          </div>
          
          <div class="mb-6">
            <h2 class="text-lg font-medium text-gray-900">Dostępność</h2>
            <p class="mt-1 text-green-600">
              <i class="fas fa-check-circle mr-1"></i> Produkt dostępny
            </p>
          </div>
          
          <div class="pt-6 border-t border-gray-200">
            <div class="flex space-x-4">
              <div class="w-1/3">
                <label for="quantity" class="block text-sm font-medium text-gray-700">Ilość</label>
                <select 
                  id="quantity" 
                  v-model="quantity" 
                  class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                >
                  <option v-for="i in 10" :key="i" :value="i">{{ i }}</option>
                </select>
              </div>
              
              <div class="flex-1 flex items-end">
                <button 
                  @click="addToCart"
                  :disabled="cartLoading"
                  class="w-full bg-indigo-600 border border-transparent rounded-md py-3 px-8 flex items-center justify-center text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  <template v-if="cartLoading">
                    <svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Dodawanie...
                  </template>
                  <template v-else>
                    <i class="fas fa-shopping-cart mr-2"></i>
                    Dodaj do koszyka
                  </template>
                </button>
              </div>
            </div>
            <div v-if="cartMessage" class="mt-4 p-3 rounded" :class="cartSuccess ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'">
              <div v-if="cartSuccess" class="flex flex-col">
                <span>{{ cartMessage }}</span>
                <div class="mt-2 flex justify-between items-center">
                  <router-link to="/cart" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">
                    Przejdź do koszyka
                  </router-link>
                  <span class="text-sm">Ilość: {{ quantity }}</span>
                </div>
              </div>
              <div v-else>
                {{ cartMessage }}
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <div v-else class="text-center py-10">
      <p class="text-gray-500">Nie znaleziono produktu.</p>
      <router-link to="/products" class="mt-4 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200">
        Wróć do listy produktów
      </router-link>
    </div>
  </div>
</template>

<script>
import { useProductStore } from '../stores/productStore';
import { useCartStore } from '../stores/cartStore';

export default {
  name: 'ProductDetails',
  props: {
    id: {
      type: String,
      required: true
    }
  },
  data() {
    return {
      product: null,
      loading: true,
      error: null,
      quantity: 1,
      cartMessage: '',
      cartSuccess: false,
      cartLoading: false
    }
  },
  created() {
    this.productStore = useProductStore();
    this.cartStore = useCartStore();
  },
  mounted() {
    this.fetchProduct();
  },
  watch: {
    // Re-fetch product when the ID changes
    '$route.params.id': function() {
      this.fetchProduct();
    }
  },
  methods: {
    async fetchProduct() {
      this.loading = true;
      this.error = null;
      
      try {
        const product = await this.productStore.fetchProductById(this.$route.params.id);
        if (product) {
          this.product = product;
        } else {
          this.error = 'Nie udało się załadować produktu. Produkt nie został znaleziony.';
        }
      } catch (err) {
        this.error = 'Wystąpił błąd podczas ładowania produktu.';
        console.error('Error fetching product:', err);
      } finally {
        this.loading = false;
      }
    },
    
    formatPrice(price) {
      return parseFloat(price).toFixed(2);
    },
    
    calculateDiscount(price, oldPrice) {
      if (!oldPrice || oldPrice <= price) return 0;
      return Math.round(((oldPrice - price) / oldPrice) * 100);
    },
    
    async addToCart() {
      this.cartLoading = true;
      this.cartMessage = '';
      this.cartSuccess = false;
      
      try {
        const success = await this.cartStore.addToCart(this.product.id, this.quantity);
        if (success) {
          this.cartMessage = `Produkt "${this.product.name}" został dodany do koszyka.`;
          this.cartSuccess = true;
          
          // Reset the message after a longer time since we have a cart link
          setTimeout(() => {
            this.cartMessage = '';
          }, 5000);
        } else {
          this.cartMessage = 'Nie udało się dodać produktu do koszyka.';
          this.cartSuccess = false;
        }
      } catch (error) {
        this.cartMessage = 'Wystąpił błąd podczas dodawania produktu do koszyka.';
        this.cartSuccess = false;
        console.error('Error adding product to cart:', error);
      } finally {
        this.cartLoading = false;
      }
    }
  }
}
</script>

<style scoped>
.pb-3\/4 {
  padding-bottom: 75%;
}
</style> 