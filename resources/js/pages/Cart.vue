<template>
  <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <h1 class="text-3xl font-extrabold text-gray-900 mb-8">Koszyk</h1>
    
    <div v-if="cartStore.loading && !updatingItemId" class="text-center py-10">
      <div class="w-12 h-12 border-4 border-indigo-500 border-t-transparent rounded-full animate-spin mx-auto"></div>
      <p class="mt-2 text-gray-500">Ładowanie koszyka...</p>
    </div>
    
    <div v-else-if="cartStore.error" class="text-center py-10">
      <p class="text-red-500">{{ cartStore.error }}</p>
      <button 
        @click="loadCart" 
        class="mt-4 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200"
      >
        Spróbuj ponownie
      </button>
    </div>
    
    <div v-else-if="cartStore.isEmpty" class="text-center py-10">
      <div class="text-center">
        <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
        </svg>
        <h3 class="text-lg font-medium text-gray-900 mb-2">Twój koszyk jest pusty</h3>
        <p class="text-gray-500 mb-6">Wygląda na to, że nie dodałeś jeszcze żadnych produktów do koszyka.</p>
        <router-link to="/products" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
          Przeglądaj produkty
        </router-link>
      </div>
    </div>
    
    <div v-else>
      <!-- Cart contents -->
      <div class="bg-white shadow-sm rounded-lg overflow-hidden">
        <!-- Cart header -->
        <div class="bg-gray-50 px-6 py-4 text-sm font-medium text-gray-700 border-b border-gray-200 hidden md:grid md:grid-cols-12 md:gap-3">
          <div class="md:col-span-6">Produkt</div>
          <div class="md:col-span-2 text-center">Cena</div>
          <div class="md:col-span-2 text-center">Ilość</div>
          <div class="md:col-span-2 text-right">Suma</div>
        </div>
        
        <!-- Cart items -->
        <div class="divide-y divide-gray-200">
          <div 
            v-for="item in cartStore.items" 
            :key="item.product_id" 
            class="px-6 py-4 flex flex-col md:grid md:grid-cols-12 md:gap-3 md:items-center"
          >
            <!-- Product info -->
            <div class="md:col-span-6 flex items-center">
              <div class="flex-shrink-0 w-20 h-20 bg-gray-100 rounded-md overflow-hidden">
                <img 
                  :src="item.product.image_url || 'https://via.placeholder.com/80x80/indigo/fff?text=' + item.product.name" 
                  :alt="item.product.name" 
                  class="w-full h-full object-center object-cover"
                >
              </div>
              <div class="ml-4 flex-1">
                <h3 class="text-base font-medium text-gray-900">
                  <router-link :to="`/products/${item.product_id}`" class="hover:text-indigo-600">
                    {{ item.product.name }}
                  </router-link>
                </h3>
                <button 
                  @click="removeItem(item.product_id)" 
                  class="mt-1 text-sm text-red-600 hover:text-red-800 flex items-center"
                  :disabled="isRemovingItem(item.product_id)"
                >
                  <template v-if="isRemovingItem(item.product_id)">
                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Usuwanie...
                  </template>
                  <template v-else>
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                    Usuń
                  </template>
                </button>
              </div>
            </div>
            
            <!-- Price -->
            <div class="md:col-span-2 text-center mt-4 md:mt-0">
              <span class="text-sm font-medium text-gray-900">{{ formatPrice(item.product.price) }} zł</span>
            </div>
            
            <!-- Quantity -->
            <div class="md:col-span-2 text-center mt-4 md:mt-0">
              <div class="flex items-center justify-center">
                <button 
                  @click="decreaseQuantity(item)" 
                  class="text-gray-500 hover:text-indigo-600 focus:outline-none disabled:opacity-50"
                  :disabled="isUpdatingItem(item.product_id) || item.quantity <= 1"
                >
                  <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                  </svg>
                </button>
                <span v-if="isUpdatingItem(item.product_id)" class="mx-2 w-8 text-center text-sm font-medium text-gray-700">
                  <svg class="animate-spin h-5 w-5 text-indigo-600 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                </span>
                <span v-else class="mx-2 w-8 text-center text-sm font-medium text-gray-700">{{ item.quantity }}</span>
                <button 
                  @click="increaseQuantity(item)" 
                  class="text-gray-500 hover:text-indigo-600 focus:outline-none disabled:opacity-50"
                  :disabled="isUpdatingItem(item.product_id)"
                >
                  <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                  </svg>
                </button>
              </div>
            </div>
            
            <!-- Subtotal -->
            <div class="md:col-span-2 text-right mt-4 md:mt-0">
              <span class="text-base font-medium text-gray-900">{{ formatPrice(item.product.price * item.quantity) }} zł</span>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Cart summary -->
      <div class="mt-10 grid grid-cols-1 lg:grid-cols-12 gap-8">
        <!-- Coupon code (could be implemented later) -->
        <div class="lg:col-span-7">
          <div class="bg-white shadow-sm rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Kod promocyjny</h3>
            <div class="flex">
              <input 
                type="text" 
                placeholder="Wpisz kod promocyjny" 
                class="flex-1 rounded-l-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
              >
              <button class="px-4 py-2 rounded-r-md bg-gray-200 text-gray-700 hover:bg-gray-300 transition-colors">
                Zastosuj
              </button>
            </div>
          </div>
        </div>
        
        <!-- Order summary -->
        <div class="lg:col-span-5">
          <div class="bg-white shadow-sm rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Podsumowanie</h3>
            <div class="space-y-3">
              <div class="flex justify-between text-base text-gray-700">
                <span>Suma częściowa</span>
                <span>{{ cartStore.formattedSubtotal }}</span>
              </div>
              <div v-if="cartStore.discount > 0" class="flex justify-between text-base text-green-600">
                <span>Zniżka</span>
                <span>-{{ cartStore.formattedDiscount }}</span>
              </div>
              <div class="pt-3 border-t border-gray-200 flex justify-between text-lg font-medium text-gray-900">
                <span>Suma</span>
                <span>{{ cartStore.formattedTotal }}</span>
              </div>
            </div>
            
            <div class="mt-6 grid grid-cols-2 gap-3">
              <router-link 
                to="/products" 
                class="inline-flex justify-center items-center px-4 py-3 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none"
              >
                Kontynuuj zakupy
              </router-link>
              <router-link 
                to="/checkout" 
                class="inline-flex justify-center items-center px-4 py-3 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none"
              >
                Przejdź do kasy
              </router-link>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { useCartStore } from '../stores/cartStore';

export default {
  name: 'Cart',
  data() {
    return {
      updatingItemId: null,
      removingItemId: null
    }
  },
  created() {
    this.cartStore = useCartStore();
  },
  mounted() {
    this.loadCart();
  },
  computed: {
    
  },
  methods: {
    loadCart() {
      this.cartStore.fetchCart();
    },
    
    formatPrice(price) {
      return parseFloat(price).toFixed(2);
    },
    
    async increaseQuantity(item) {
      this.updatingItemId = item.product_id;
      try {
        await this.cartStore.updateCartItem(item.product_id, item.quantity + 1);
      } catch (error) {
        console.error('Error increasing quantity:', error);
      } finally {
        this.updatingItemId = null;
      }
    },
    
    async decreaseQuantity(item) {
      if (item.quantity <= 1) return;
      
      this.updatingItemId = item.product_id;
      try {
        await this.cartStore.updateCartItem(item.product_id, item.quantity - 1);
      } catch (error) {
        console.error('Error decreasing quantity:', error);
      } finally {
        this.updatingItemId = null;
      }
    },
    
    async removeItem(productId) {
      this.removingItemId = productId;
      try {
        await this.cartStore.removeFromCart(productId);
      } catch (error) {
        console.error('Error removing item:', error);
      } finally {
        this.removingItemId = null;
      }
    },
    
    isUpdatingItem(productId) {
      return this.updatingItemId === productId;
    },
    
    isRemovingItem(productId) {
      return this.removingItemId === productId;
    },
    
    clearCart() {
      if (confirm('Czy na pewno chcesz usunąć wszystkie produkty z koszyka?')) {
        this.cartStore.clearCart();
      }
    }
  }
}
</script>

<style scoped>
/* Additional scoped styles can be added here if needed */
</style> 