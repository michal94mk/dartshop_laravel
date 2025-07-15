<template>
  <div class="min-h-screen bg-gray-50">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Header -->
      <div class="text-center mb-8">
        <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mb-2">
          Finalizacja zamówienia
        </h1>
        <p class="text-gray-600">
          Uzupełnij dane i wybierz metodę płatności
        </p>
      </div>

      <form @submit.prevent="processCheckout">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
          <!-- Left Column - Forms -->
          <div class="lg:col-span-2 space-y-6">
            <!-- Dane do wysyłki -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
              <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                  <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                  </svg>
                  Dane do wysyłki
                </h2>
              </div>
              <div class="p-6 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <label for="first_name" class="block text-sm font-medium text-gray-700 mb-1">Imię</label>
                    <input
                      type="text"
                      id="first_name"
                      v-model="shippingDetails.first_name"
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                      placeholder="Jan"
                      required
                    />
                  </div>
                  
                  <div>
                    <label for="last_name" class="block text-sm font-medium text-gray-700 mb-1">Nazwisko</label>
                    <input
                      type="text"
                      id="last_name"
                      v-model="shippingDetails.last_name"
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                      placeholder="Kowalski"
                      required
                    />
                  </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Adres email</label>
                    <input
                      type="email"
                      id="email"
                      v-model="shippingDetails.email"
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                      placeholder="twoj@email.com"
                      required
                    />
                  </div>

                  <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Telefon</label>
                    <input
                      type="tel"
                      id="phone"
                      v-model="shippingDetails.phone"
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                      placeholder="123456789"
                    />
                  </div>
                </div>
                
                <div>
                  <label for="street" class="block text-sm font-medium text-gray-700 mb-1">Ulica i numer</label>
                  <input
                    type="text"
                    id="street"
                    v-model="shippingDetails.street"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                    placeholder="ul. Przykładowa 123"
                    required
                  />
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <label for="city" class="block text-sm font-medium text-gray-700 mb-1">Miasto</label>
                    <input
                      type="text"
                      id="city"
                      v-model="shippingDetails.city"
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                      placeholder="Warszawa"
                      required
                    />
                  </div>
                  
                  <div>
                    <label for="postal_code" class="block text-sm font-medium text-gray-700 mb-1">Kod pocztowy</label>
                    <input
                      type="text"
                      id="postal_code"
                      v-model="shippingDetails.postal_code"
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                      placeholder="00-000"
                      required
                    />
                  </div>
                </div>

                <div>
                  <label for="country" class="block text-sm font-medium text-gray-700 mb-1">Kraj</label>
                  <input
                    type="text"
                    id="country"
                    v-model="shippingDetails.country"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                    placeholder="Poland"
                    required
                  />
                </div>
              </div>
            </div>

            <!-- Metoda wysyłki -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200" v-if="shippingMethods && Object.keys(shippingMethods).length > 0">
              <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-700 flex items-center">
                  <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                  </svg>
                  Metoda wysyłki
                </h2>
              </div>
              <div class="p-6">
                <div class="space-y-2">
                  <div 
                    v-for="(method, key) in shippingMethods" 
                    :key="key"
                    class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer transition-colors"
                    :class="{ 'border-indigo-500 bg-indigo-50': selectedShippingMethod === key }"
                    @click="selectedShippingMethod = key"
                  >
                    <input
                      :id="key"
                      name="shipping_method"
                      type="radio"
                      :value="key"
                      v-model="selectedShippingMethod"
                      class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300"
                    />
                    <div class="ml-3 flex items-center justify-between flex-1">
                      <div class="flex items-center">
                        <svg class="w-4 h-4 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path v-if="method.icon === 'truck'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                          <path v-else-if="method.icon === 'archive'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8l6 6 6-6"/>
                          <path v-else-if="method.icon === 'store'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                          <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        <div>
                          <span class="text-sm font-medium text-gray-900">{{ method.name }}</span>
                          <p class="text-xs text-gray-500">{{ method.delivery_time }}</p>
                        </div>
                      </div>
                      <div class="text-right">
                        <span v-if="method.is_free" class="text-sm font-bold text-green-600">DARMOWA</span>
                        <span v-else class="text-sm font-bold text-gray-900">{{ formatPrice(method.calculated_cost) }}</span>
                      </div>
                    </div>
                  </div>
                </div>
                <div v-if="freeShippingThreshold && cartTotal < freeShippingThreshold" class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                  <p class="text-sm text-blue-700">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Dodaj produkty za {{ formatPrice(freeShippingThreshold - cartTotal) }}, aby otrzymać darmową dostawę!
                  </p>
                </div>
              </div>
            </div>

            <!-- Metoda płatności -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
              <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-700 flex items-center">
                  <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                  </svg>
                  Metoda płatności
                </h2>
              </div>
              <div class="p-6">
                <div class="space-y-2">
                  <!-- Stripe Payment Methods -->
                  <div 
                    class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer transition-colors"
                    :class="{ 'border-indigo-500 bg-indigo-50': paymentMethod === 'stripe' }"
                    @click="paymentMethod = 'stripe'"
                  >
                    <input
                      id="stripe"
                      name="payment_method"
                      type="radio"
                      value="stripe"
                      v-model="paymentMethod"
                      class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300"
                    />
                    <div class="ml-3 flex items-center">
                      <svg class="w-4 h-4 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                      </svg>
                                        <div class="flex flex-col">
                    <span class="text-sm font-medium text-gray-900">Płatność online</span>
                    <span class="text-xs text-gray-500">Karta płatnicza, BLIK, Przelewy24</span>
                    <span class="text-xs text-yellow-600 font-medium">🧪 Środowisko testowe - płatności symulowane</span>
                  </div>
                    </div>
                  </div>
                  
                  <!-- Cash on Delivery -->
                  <div 
                    class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer transition-colors"
                    :class="{ 'border-indigo-500 bg-indigo-50': paymentMethod === 'cod' }"
                    @click="paymentMethod = 'cod'"
                  >
                    <input
                      id="cod"
                      name="payment_method"
                      type="radio"
                      value="cod"
                      v-model="paymentMethod"
                      class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300"
                    />
                    <div class="ml-3 flex items-center">
                      <svg class="w-4 h-4 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                      </svg>
                      <span class="text-sm font-medium text-gray-900">Płatność przy odbiorze</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Right Column - Order Summary -->
          <div class="bg-white rounded-xl shadow-sm border border-gray-200 h-fit sticky top-8">
            <div class="px-6 py-4 border-b border-gray-200">
              <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
                Podsumowanie
              </h2>
            </div>
            <div class="p-6">
              <div v-if="loading && cartItems.length === 0" class="text-center py-8">
                <div class="w-8 h-8 border-4 border-indigo-500 border-t-transparent rounded-full animate-spin mx-auto"></div>
                <p class="mt-2 text-gray-500 text-sm">Ładowanie...</p>
              </div>
              <div v-else-if="error" class="text-center py-4">
                <div class="bg-red-50 border border-red-200 text-red-700 p-3 rounded-lg">
                  <p class="text-sm">{{ error }}</p>
                </div>
              </div>
              <div v-else-if="cartItems.length === 0" class="text-center py-8">
                <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <p class="text-gray-500 text-sm">Koszyk jest pusty</p>
              </div>
              <div v-else>
                <!-- Cart Items -->
                <div class="space-y-3 mb-6">
                  <div v-for="item in cartItems" :key="item.product_id || item.id" class="flex items-center space-x-3">
                    <div class="flex-shrink-0 w-12 h-12 bg-gray-100 rounded-lg overflow-hidden">
                      <img 
                        :src="getProductImageUrl(item.product.image_url, item.product.name, 48, 48)" 
                        :alt="item.product.name" 
                                                  class="w-full h-full object-cover"
                          @error="(e) => handleImageError(e, item.product.name, 48, 48)"
                      >
                    </div>
                    <div class="flex-1 min-w-0">
                      <h3 class="text-sm font-medium text-gray-900 truncate">{{ item.product.name }}</h3>
                      <p class="text-xs text-gray-500">Ilość: {{ item.quantity }}</p>
                    </div>
                    <div class="text-sm font-medium text-gray-900">
                      {{ formatPrice(getPromotionalPrice(item.product) * item.quantity) }}
                    </div>
                  </div>
                </div>
                
                <!-- Summary -->
                <div class="border-t border-gray-200 pt-4 space-y-2">
                  <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Produkty</span>
                    <span class="text-gray-900">{{ formatPrice(subtotal) }}</span>
                  </div>
                  <div v-if="selectedShippingMethod && shippingMethods[selectedShippingMethod]" class="flex justify-between text-sm">
                    <span class="text-gray-600">Wysyłka</span>
                    <span class="text-gray-900">
                      <span v-if="shippingMethods[selectedShippingMethod].is_free" class="text-green-600 font-medium">DARMOWA</span>
                      <span v-else>{{ formatPrice(shippingCost) }}</span>
                    </span>
                  </div>
                  <div class="border-t border-gray-200 pt-2">
                    <div class="flex justify-between">
                      <span class="text-base font-semibold text-gray-900">Suma</span>
                      <span class="text-lg font-bold text-indigo-600">{{ formatPrice(total) }}</span>
                    </div>
                  </div>
                </div>

                <!-- Privacy Policy Agreement -->
                <div v-if="!authStore.isLoggedIn || !authStore.user?.privacy_policy_accepted" class="mt-4 border-t border-gray-200 pt-4">
                  <div class="flex items-start">
                    <div class="flex items-center h-5">
                      <input 
                        id="checkout_privacy" 
                        type="checkbox" 
                        v-model="privacyPolicyAccepted" 
                        required
                        class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded"
                      />
                    </div>
                    <div class="ml-3 text-sm">
                      <label for="checkout_privacy" class="text-gray-700">
                        Akceptuję 
                        <router-link 
                          to="/privacy" 
                          target="_blank"
                          class="text-indigo-600 hover:text-indigo-800 underline font-medium"
                        >
                          politykę prywatności
                        </router-link>
                        <span class="text-red-500 ml-1">*</span>
                      </label>
                    </div>
                  </div>
                </div>

                <!-- Submit Button -->
                <div class="mt-6">
                  <button
                    type="submit"
                    :disabled="loading || cartItems.length === 0 || !isFormValid"
                    class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-3 px-4 rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-200 flex items-center justify-center"
                  >
                    <svg v-if="loading" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span v-if="loading">Przetwarzanie...</span>
                    <span v-else-if="paymentMethod === 'stripe'">Przejdź do płatności</span>
                    <span v-else>Złóż zamówienie</span>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/authStore'
import { useCartStore } from '../stores/cartStore'
import { getProductImageUrl, handleImageError } from '../utils/imageHelpers'
import axios from 'axios'

export default {
  name: 'Checkout',
  
  setup() {
    const router = useRouter()
    const authStore = useAuthStore()
    const cartStore = useCartStore()
    const cartItems = ref([])
    const loading = ref(false)
    const error = ref(null)
    const paymentMethod = ref('stripe')
    const shippingMethods = ref({})
    const selectedShippingMethod = ref('courier')  // Zmienione z 'standard' na 'courier'
    const cartTotal = ref(0)
    const freeShippingThreshold = ref(0)
    const privacyPolicyAccepted = ref(false)
    
    const shippingDetails = ref({
      first_name: '',
      last_name: '',
      email: '',
      street: '',
      city: '',
      postal_code: '',
      phone: '',
      country: 'Poland'
    })
    
    const subtotal = computed(() => {
      if (!cartItems.value?.length) return 0
      return Number(cartItems.value.reduce((sum, item) => {
        return sum + (getPromotionalPrice(item.product) * item.quantity)
      }, 0).toFixed(2))
    })

    const shippingCost = computed(() => {
      if (!selectedShippingMethod.value || !shippingMethods.value[selectedShippingMethod.value]) return 0
      return Number(shippingMethods.value[selectedShippingMethod.value].calculated_cost || 0)
    })

    const total = computed(() => {
      return Number((subtotal.value + shippingCost.value).toFixed(2))
    })
    
    const isFormValid = computed(() => {
      const basicFormValid = shippingDetails.value.first_name &&
             shippingDetails.value.last_name &&
             shippingDetails.value.email &&
             shippingDetails.value.street &&
             shippingDetails.value.city &&
             shippingDetails.value.postal_code &&
             shippingDetails.value.country;
             
      // Sprawdź czy wybrano metodę wysyłki
      const shippingMethodValid = !!selectedShippingMethod.value;
      
      // Sprawdź czy wybrano metodę płatności
      const paymentMethodValid = !!paymentMethod.value;
      
      // Sprawdź politykę prywatności dla gości lub użytkowników, którzy jej nie zaakceptowali
      const privacyPolicyValid = authStore.isLoggedIn && authStore.user?.privacy_policy_accepted ? true : privacyPolicyAccepted.value;
      
      return basicFormValid && shippingMethodValid && paymentMethodValid && privacyPolicyValid;
    })
    
    const fetchCart = async () => {
      try {
        loading.value = true
        error.value = null
        
        if (authStore.isLoggedIn) {
          // Pobierz koszyk dla zalogowanych użytkowników
          const cartResponse = await axios.get('/api/cart')
          cartItems.value = cartResponse.data.items
          cartTotal.value = cartResponse.data.total || 0

          // Pobierz metody wysyłki dla zalogowanych użytkowników
          const shippingResponse = await axios.get('/api/user/shipping-methods', {
            params: {
              cart_total: cartTotal.value
            }
          })
          shippingMethods.value = shippingResponse.data.methods || {}
          freeShippingThreshold.value = shippingResponse.data.free_shipping_threshold || 0
          
          // Ustaw domyślną metodę wysyłki, jeśli nie jest jeszcze wybrana
          if (!selectedShippingMethod.value && Object.keys(shippingMethods.value).length > 0) {
            selectedShippingMethod.value = 'courier'
          }
        } else {
          // Dla gości - pobierz z localStorage
          const savedCart = localStorage.getItem('cart')
          if (savedCart) {
            console.log('Saved cart from localStorage:', savedCart)
            const localCartItems = JSON.parse(savedCart)
            console.log('Parsed cart items:', localCartItems)
            cartItems.value = localCartItems
            
            // Oblicz sumę koszyka
            const calculatedTotal = localCartItems.reduce((total, item) => {
              console.log('Item:', item)
              const price = item.product?.promotion_price && parseFloat(item.product.promotion_price) < parseFloat(item.product.price)
                ? parseFloat(item.product.promotion_price)
                : parseFloat(item.product.price)
              console.log('Calculated price:', price, 'Quantity:', item.quantity)
              return total + (price * item.quantity)
            }, 0)
            console.log('Calculated total before Number conversion:', calculatedTotal)
            cartTotal.value = Number(calculatedTotal.toFixed(2))
            console.log('Final cartTotal:', cartTotal.value)
            
            // Pobierz metody wysyłki dla gości
            const shippingResponse = await axios.get('/api/shipping-methods', {
              params: {
                cart_total: cartTotal.value
              }
            })
            shippingMethods.value = shippingResponse.data.methods || {}
            freeShippingThreshold.value = shippingResponse.data.free_shipping_threshold || 0
            
            // Ustaw domyślną metodę wysyłki, jeśli nie jest jeszcze wybrana
            if (!selectedShippingMethod.value && Object.keys(shippingMethods.value).length > 0) {
              selectedShippingMethod.value = 'courier'
            }
          } else {
            cartItems.value = []
            cartTotal.value = 0
          }
        }
      } catch (err) {
        console.error('Error fetching cart:', err)
        error.value = err.response?.data?.message || 'Nie udało się załadować koszyka'
        cartItems.value = []
      } finally {
        loading.value = false
      }
    }
    
    const processCheckout = async () => {
      try {
        loading.value = true
        error.value = null

        // Sprawdź czy formularz jest wypełniony
        if (!isFormValid.value) {
          error.value = 'Uzupełnij wszystkie wymagane pola'
          return
        }

        // Sprawdź czy wybrano metodę wysyłki
        if (!selectedShippingMethod.value) {
          error.value = 'Wybierz metodę wysyłki'
          return
        }

        // Jeśli wybrano płatność online, przekieruj do Stripe
        if (paymentMethod.value === 'stripe') {
          await processStripeCheckout()
          return
        }

        // Dla płatności przy odbiorze, kontynuuj standardowy proces
        const checkoutData = {
          shipping_address: {
            first_name: shippingDetails.value.first_name,
            last_name: shippingDetails.value.last_name,
            email: shippingDetails.value.email,
            street: shippingDetails.value.street,
            city: shippingDetails.value.city,
            postal_code: shippingDetails.value.postal_code,
            phone: shippingDetails.value.phone,
            country: shippingDetails.value.country
          },
          shipping_method: selectedShippingMethod.value,
          payment_method: paymentMethod.value,
          notes: shippingDetails.value.notes
        }

        console.log('Wysyłanie danych zamówienia:', checkoutData)

        // Wybierz odpowiedni endpoint w zależności od stanu logowania
        const endpoint = authStore.isLoggedIn ? '/api/checkout' : '/api/guest-checkout'
        console.log('Używany endpoint:', endpoint)

        const response = await axios.post(endpoint, checkoutData)
        console.log('Odpowiedź z serwera:', response.data)

        // Obsługa odpowiedzi
        if (response.data.order) {
          // Wyczyść koszyk
          await cartStore.clearCart()
          
          // Przekieruj do strony potwierdzenia
          router.push({
            name: 'payment-success',
            query: { order_id: response.data.order.id }
          })
        }

      } catch (err) {
        console.error('Błąd podczas przetwarzania zamówienia:', err.response || err)
        error.value = err.response?.data?.message || 'Wystąpił błąd podczas tworzenia zamówienia'
        loading.value = false
      }
    }
    
    const formatPrice = (price) => {
      return new Intl.NumberFormat('pl-PL', {
        style: 'currency',
        currency: 'PLN'
      }).format(price)
    }

    const getPromotionalPrice = (product) => {
      if (!product) return 0
      const hasPromotion = product.promotion_price && 
                          parseFloat(product.promotion_price) < parseFloat(product.price)
      return Number((hasPromotion ? parseFloat(product.promotion_price) : parseFloat(product.price)).toFixed(2))
    }

    const processStripeCheckout = async () => {
      try {
        loading.value = true
        
        const endpoint = authStore.isLoggedIn 
          ? '/api/stripe/create-checkout-session'
          : '/api/guest-stripe/create-checkout-session'
        
        const payload = {
          shipping: {
            name: `${shippingDetails.value.first_name} ${shippingDetails.value.last_name}`,
            email: shippingDetails.value.email,
            address: shippingDetails.value.street,
            city: shippingDetails.value.city,
            postalCode: shippingDetails.value.postal_code,
            phone: shippingDetails.value.phone,
            country: shippingDetails.value.country
          },
          shipping_method: selectedShippingMethod.value,
          ...(authStore.isLoggedIn ? {} : {
            cart_items: cartItems.value.map(item => ({
              product_id: item.product_id || item.product.id,
              quantity: item.quantity
            }))
          })
        }
        
        const response = await axios.post(endpoint, payload)
        
        // Przekieruj do Stripe Checkout
        window.location.href = response.data.checkout_url
        
      } catch (err) {
        console.error('Error creating Stripe checkout session:', err)
        error.value = err.response?.data?.message || 'Nie udało się utworzyć sesji płatności'
        loading.value = false
      }
    }
    
    onMounted(() => {
      fetchCart()
      
      // Auto-fill shipping details if user is logged in
      if (authStore.isLoggedIn && authStore.user) {
        const user = authStore.user
        shippingDetails.value.email = user.email || ''
        
        // Combine first_name and last_name if available, otherwise use name
        if (user.first_name && user.last_name) {
          shippingDetails.value.name = `${user.first_name} ${user.last_name}`
        } else if (user.name) {
          shippingDetails.value.name = user.name
        }
      }
    })
    
    return {
      authStore,
      cartItems,
      loading,
      error,
      paymentMethod,
      shippingDetails,
      shippingMethods,
      selectedShippingMethod,
      cartTotal,
      freeShippingThreshold,
      subtotal,
      shippingCost,
      total,
      isFormValid,
      processCheckout,
      processStripeCheckout,
      formatPrice,
      getPromotionalPrice,
      getProductImageUrl,
      handleImageError,
      privacyPolicyAccepted
    }
  }
}
</script> 