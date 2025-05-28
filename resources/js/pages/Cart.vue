<template>
  <div class="min-h-screen bg-gradient-to-br from-indigo-50 via-blue-50 to-purple-50">
    <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
      <!-- Header -->
      <div class="text-center mb-12">
        <div class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-indigo-100 to-purple-100 rounded-full text-indigo-700 font-semibold text-sm mb-4">
          <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
          </svg>
          Twój koszyk
        </div>
        <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
          Koszyk zakupowy
        </h1>
        <p class="text-gray-600 max-w-2xl mx-auto">
          Sprawdź wybrane produkty i przejdź do finalizacji zamówienia
        </p>
      </div>
      
      <div v-if="cartStore.loading && !updatingItemId" class="text-center py-16">
        <div class="w-16 h-16 border-4 border-indigo-500 border-t-transparent rounded-full animate-spin mx-auto"></div>
        <p class="mt-4 text-gray-500 font-medium">Ładowanie koszyka...</p>
      </div>
      
      <div v-else-if="cartStore.error" class="text-center py-16">
        <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-6 rounded-lg max-w-md mx-auto">
          <div class="flex">
            <div class="flex-shrink-0">
              <svg class="h-5 w-5 text-red-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
              </svg>
            </div>
            <div class="ml-3">
              <p class="font-medium">{{ cartStore.error }}</p>
            </div>
          </div>
        </div>
        <button 
          @click="loadCart" 
          class="mt-6 inline-flex items-center px-6 py-3 border border-transparent text-sm font-medium rounded-lg text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 shadow-lg hover:shadow-xl"
        >
          Spróbuj ponownie
        </button>
      </div>
      
      <div v-else-if="cartStore.isEmpty" class="text-center py-16">
        <div class="bg-white shadow-xl rounded-2xl p-12 max-w-md mx-auto border border-gray-100">
          <svg class="w-20 h-20 text-gray-300 mx-auto mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
          </svg>
          <h3 class="text-xl font-bold text-gray-900 mb-3">Twój koszyk jest pusty</h3>
          <p class="text-gray-500 mb-8">Wygląda na to, że nie dodałeś jeszcze żadnych produktów do koszyka.</p>
          <router-link to="/products" class="inline-flex items-center px-6 py-3 border border-transparent text-sm font-medium rounded-lg text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 shadow-lg hover:shadow-xl">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M8 11v6a2 2 0 002 2h4a2 2 0 002-2v-6M8 11h8"/>
            </svg>
            Przeglądaj produkty
          </router-link>
        </div>
      </div>
      
      <div v-else>
        <!-- Cart contents -->
        <div class="bg-white shadow-xl rounded-2xl overflow-hidden border border-gray-100 mb-8">
          <!-- Cart header -->
          <div class="bg-gradient-to-r from-indigo-50 to-purple-50 px-6 py-4 text-sm font-semibold text-gray-700 border-b border-gray-200 hidden md:grid md:grid-cols-12 md:gap-3">
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
              class="px-6 py-6 flex flex-col md:grid md:grid-cols-12 md:gap-3 md:items-center hover:bg-gray-50 transition-colors duration-200"
            >
              <!-- Product info -->
              <div class="md:col-span-6 flex items-center">
                <div class="flex-shrink-0 w-24 h-24 bg-gray-100 rounded-xl overflow-hidden shadow-sm">
                  <img 
                    :src="item.product && item.product.image_url 
                      ? item.product.image_url 
                      : `https://via.placeholder.com/96x96/indigo/fff?text=${item.product?.name || 'Produkt'}`" 
                    :alt="item.product?.name || 'Produkt'" 
                    class="w-full h-full object-center object-cover"
                  >
                </div>
                <div class="ml-4 flex-1">
                  <h3 class="text-base font-semibold text-gray-900">
                    <router-link :to="`/products/${item.product_id}`" class="hover:text-indigo-600 transition-colors duration-200">
                      {{ item.product?.name || 'Produkt' }}
                    </router-link>
                  </h3>
                  <button 
                    @click="removeItem(item.product_id)" 
                    class="mt-2 text-sm text-red-600 hover:text-red-800 flex items-center font-medium transition-colors duration-200"
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
                <div v-if="hasPromotion(item.product)" class="space-y-1">
                  <!-- Original price (crossed out) -->
                  <div class="text-xs text-gray-500 line-through">
                    {{ formatPrice(item.product.price) }} zł
                  </div>
                  <!-- Promotional price -->
                  <div class="text-sm font-semibold text-red-600">
                    {{ formatPrice(getPromotionalPrice(item.product)) }} zł
                  </div>
                </div>
                <!-- Regular price (no promotion) -->
                <div v-else class="text-sm font-semibold text-gray-900">
                  {{ formatPrice(item.product.price) }} zł
                </div>
              </div>
              
              <!-- Quantity -->
              <div class="md:col-span-2 text-center mt-4 md:mt-0">
                <div class="flex items-center justify-center">
                  <button 
                    @click="decreaseQuantity(item)" 
                    class="text-gray-500 hover:text-indigo-600 focus:outline-none disabled:opacity-50 p-2 rounded-lg hover:bg-indigo-50 transition-all duration-200"
                    :disabled="isUpdatingItem(item.product_id) || item.quantity <= 1"
                  >
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                    </svg>
                  </button>
                  <span v-if="isUpdatingItem(item.product_id)" class="mx-3 w-12 text-center text-sm font-semibold text-gray-700">
                    <svg class="animate-spin h-5 w-5 text-indigo-600 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                  </span>
                  <span v-else class="mx-3 w-12 text-center text-sm font-semibold text-gray-700 bg-gray-100 rounded-lg py-2">{{ item.quantity }}</span>
                  <button 
                    @click="increaseQuantity(item)" 
                    class="text-gray-500 hover:text-indigo-600 focus:outline-none disabled:opacity-50 p-2 rounded-lg hover:bg-indigo-50 transition-all duration-200"
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
                <span class="text-base font-bold text-indigo-600">{{ formatPrice(getPromotionalPrice(item.product) * item.quantity) }} zł</span>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Cart summary -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
          <!-- Coupon code -->
          <div class="lg:col-span-7">
            <div class="bg-white shadow-xl rounded-2xl p-8 border border-gray-100">
              <h3 class="text-lg font-bold text-gray-900 mb-6 flex items-center">
                <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                </svg>
                Kod promocyjny
              </h3>
              <div class="flex">
                <input 
                  type="text" 
                  placeholder="Wpisz kod promocyjny" 
                  class="flex-1 px-4 py-3 border border-gray-300 rounded-l-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200"
                >
                <button class="px-6 py-3 rounded-r-lg bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-medium transition-all duration-200 shadow-lg hover:shadow-xl">
                  Zastosuj
                </button>
              </div>
            </div>
          </div>
          
          <!-- Order summary -->
          <div class="lg:col-span-5">
            <div class="bg-white shadow-xl rounded-2xl p-8 border border-gray-100">
              <h3 class="text-lg font-bold text-gray-900 mb-6 flex items-center">
                <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
                Podsumowanie
              </h3>
              <div class="space-y-4">
                <div class="flex justify-between text-base text-gray-700">
                  <span>Suma częściowa</span>
                  <span class="font-semibold">{{ cartStore.formattedSubtotal }}</span>
                </div>
                <div v-if="cartStore.discount > 0" class="flex justify-between text-base text-green-600">
                  <span>Zniżka</span>
                  <span class="font-semibold">-{{ cartStore.formattedDiscount }}</span>
                </div>
                <div class="pt-4 border-t border-gray-200 flex justify-between text-xl font-bold text-gray-900">
                  <span>Suma</span>
                  <span class="text-indigo-600">{{ cartStore.formattedTotal }}</span>
                </div>
              </div>
              
              <div class="mt-8 space-y-3">
                <router-link 
                  to="/checkout" 
                  class="w-full inline-flex justify-center items-center px-6 py-4 border border-transparent shadow-lg text-base font-semibold rounded-lg text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 hover:shadow-xl transform hover:-translate-y-0.5"
                >
                  <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                  </svg>
                  Przejdź do kasy
                </router-link>
                <router-link 
                  to="/products" 
                  class="w-full inline-flex justify-center items-center px-6 py-3 border border-gray-300 shadow-sm text-base font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200"
                >
                  <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16l-4-4m0 0l4-4m-4 4h18"/>
                  </svg>
                  Kontynuuj zakupy
                </router-link>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { useCartStore } from '../stores/cartStore';
import { useAuthStore } from '../stores/authStore';

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
    this.authStore = useAuthStore();
  },
  mounted() {
    this.cartStore.initCart();
  },
  computed: {
    
  },
  methods: {
    loadCart() {
      this.cartStore.fetchCart();
    },
    
    formatPrice(price) {
      if (price === undefined || price === null) {
        return '0.00';
      }
      return parseFloat(price).toFixed(2);
    },
    
    async increaseQuantity(item) {
      this.updatingItemId = item.product_id;
      try {
        const itemId = this.authStore.isLoggedIn ? item.id : item.product_id;
        await this.cartStore.updateCartItem(itemId, item.quantity + 1);
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
        const itemId = this.authStore.isLoggedIn ? item.id : item.product_id;
        await this.cartStore.updateCartItem(itemId, item.quantity - 1);
      } catch (error) {
        console.error('Error decreasing quantity:', error);
      } finally {
        this.updatingItemId = null;
      }
    },
    
    async removeItem(productId) {
      this.removingItemId = productId;
      try {
        const itemToRemove = this.cartStore.items.find(item => item.product_id === productId);
        const itemId = this.authStore.isLoggedIn && itemToRemove ? itemToRemove.id : productId;
        await this.cartStore.removeFromCart(itemId);
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
    },
    
    // Promotion helper functions
    hasPromotion(product) {
      return product.promotion_price && parseFloat(product.promotion_price) < parseFloat(product.price);
    },
    
    getPromotionalPrice(product) {
      return this.hasPromotion(product) ? parseFloat(product.promotion_price) : parseFloat(product.price);
    }
  }
}
</script>

<style scoped>
/* Additional scoped styles can be added here if needed */
</style> 