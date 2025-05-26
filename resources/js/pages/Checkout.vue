<template>
  <div class="min-h-screen bg-gradient-to-br from-indigo-50 via-blue-50 to-purple-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
      <!-- Header -->
      <div class="text-center mb-12">
        <div class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-indigo-100 to-purple-100 rounded-full text-indigo-700 font-semibold text-sm mb-4">
          <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
          </svg>
          Finalizacja zamówienia
        </div>
        <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
          Checkout
        </h1>
        <p class="text-gray-600 max-w-2xl mx-auto">
          Uzupełnij dane wysyłki i wybierz metodę płatności, aby sfinalizować zamówienie
        </p>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Shipping Information -->
        <div class="bg-white shadow-xl rounded-2xl overflow-hidden border border-gray-100">
          <div class="bg-gradient-to-r from-indigo-50 to-purple-50 px-8 py-6 border-b border-gray-200">
            <h2 class="text-xl font-bold text-gray-900 flex items-center">
              <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
              </svg>
              Dane do wysyłki
            </h2>
          </div>
          <div class="p-8">
            <form @submit.prevent="processCheckout" class="space-y-6">
              <div>
                <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">Imię i nazwisko</label>
                <input
                  type="text"
                  id="name"
                  v-model="shippingDetails.name"
                  class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200"
                  placeholder="Jan Kowalski"
                  required
                />
              </div>
              
              <div>
                <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Adres email</label>
                <input
                  type="email"
                  id="email"
                  v-model="shippingDetails.email"
                  class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200"
                  placeholder="twoj@email.com"
                  required
                />
              </div>
              
              <div>
                <label for="address" class="block text-sm font-semibold text-gray-700 mb-2">Adres</label>
                <input
                  type="text"
                  id="address"
                  v-model="shippingDetails.address"
                  class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200"
                  placeholder="ul. Przykładowa 123"
                  required
                />
              </div>
              
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label for="city" class="block text-sm font-semibold text-gray-700 mb-2">Miasto</label>
                  <input
                    type="text"
                    id="city"
                    v-model="shippingDetails.city"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200"
                    placeholder="Warszawa"
                    required
                  />
                </div>
                
                <div>
                  <label for="postal_code" class="block text-sm font-semibold text-gray-700 mb-2">Kod pocztowy</label>
                  <input
                    type="text"
                    id="postal_code"
                    v-model="shippingDetails.postalCode"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200"
                    placeholder="00-000"
                    required
                  />
                </div>
              </div>
              
              <!-- Wybór metody płatności -->
              <div class="pt-6 border-t border-gray-200">
                <label class="block text-sm font-semibold text-gray-700 mb-4 flex items-center">
                  <svg class="w-4 h-4 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                  </svg>
                  Metoda płatności
                </label>
                <div class="space-y-3">
                  <div class="flex items-center p-4 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors duration-200">
                    <input
                      id="stripe"
                      name="payment_method"
                      type="radio"
                      value="stripe"
                      v-model="paymentMethod"
                      class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300"
                    />
                    <label for="stripe" class="ml-3 flex items-center cursor-pointer flex-1">
                      <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                      </svg>
                      <span class="text-sm font-medium text-gray-700">Karta płatnicza (Stripe)</span>
                    </label>
                  </div>
                  <div class="flex items-center p-4 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors duration-200">
                    <input
                      id="cod"
                      name="payment_method"
                      type="radio"
                      value="cod"
                      v-model="paymentMethod"
                      class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300"
                    />
                    <label for="cod" class="ml-3 flex items-center cursor-pointer flex-1">
                      <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                      </svg>
                      <span class="text-sm font-medium text-gray-700">Płatność przy odbiorze</span>
                    </label>
                  </div>
                </div>
              </div>

              <!-- Submit button -->
              <div class="pt-6">
                <button
                  type="submit"
                  :disabled="loading || cartItems.length === 0 || (paymentMethod === 'stripe' && !isFormValid)"
                  class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-semibold py-4 px-6 rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 flex items-center justify-center"
                >
                  <svg v-if="loading" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  <span v-if="loading">Przetwarzanie...</span>
                  <span v-else-if="paymentMethod === 'stripe'">Przejdź do płatności</span>
                  <span v-else>Złóż zamówienie</span>
                </button>
              </div>
            </form>
          </div>
        </div>

        <!-- Order Summary -->
        <div class="bg-white shadow-xl rounded-2xl overflow-hidden border border-gray-100">
          <div class="bg-gradient-to-r from-indigo-50 to-purple-50 px-8 py-6 border-b border-gray-200">
            <h2 class="text-xl font-bold text-gray-900 flex items-center">
              <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
              </svg>
              Podsumowanie zamówienia
            </h2>
          </div>
          <div class="p-8">
            <div v-if="loading && cartItems.length === 0" class="text-center py-8">
              <div class="w-12 h-12 border-4 border-indigo-500 border-t-transparent rounded-full animate-spin mx-auto"></div>
              <p class="mt-3 text-gray-500 font-medium">Ładowanie...</p>
            </div>
            <div v-else-if="error" class="text-center py-8">
              <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-lg">
                <div class="flex">
                  <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                      <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                  </div>
                  <div class="ml-3">
                    <p class="font-medium">{{ error }}</p>
                  </div>
                </div>
              </div>
            </div>
            <div v-else-if="cartItems.length === 0" class="text-center py-8">
              <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
              </svg>
              <p class="text-gray-500 font-medium">Koszyk jest pusty</p>
            </div>
            <div v-else>
              <div class="space-y-4">
                <div v-for="item in cartItems" :key="item.product_id || item.id" class="flex justify-between items-center p-4 bg-gray-50 rounded-lg">
                  <div class="flex items-center">
                    <div class="flex-shrink-0 w-12 h-12 bg-gray-200 rounded-lg overflow-hidden mr-3">
                      <img 
                        :src="item.product.image_url || `https://via.placeholder.com/48x48/indigo/fff?text=${item.product.name}`" 
                        :alt="item.product.name" 
                        class="w-full h-full object-cover"
                      >
                    </div>
                    <div>
                      <h3 class="font-semibold text-gray-900">{{ item.product.name }}</h3>
                      <p class="text-sm text-gray-500">Ilość: {{ item.quantity }}</p>
                    </div>
                  </div>
                  <p class="font-bold text-indigo-600">{{ formatPrice(item.product.price * item.quantity) }}</p>
                </div>
              </div>
              
              <div class="border-t border-gray-200 mt-6 pt-6">
                <div class="flex justify-between items-center">
                  <span class="text-lg font-bold text-gray-900">Suma</span>
                  <span class="text-xl font-bold text-indigo-600">{{ formatPrice(total) }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/authStore'
import { useCartStore } from '../stores/cartStore'
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
    const paymentMethod = ref('cod')
    
    const shippingDetails = ref({
      name: '',
      email: '',
      address: '',
      city: '',
      postalCode: ''
    })
    
    const total = computed(() => {
      if (!cartItems.value?.length) return 0
      return cartItems.value.reduce((sum, item) => {
        return sum + (item.product.price * item.quantity)
      }, 0)
    })
    
    const isFormValid = computed(() => {
      return shippingDetails.value.name &&
             shippingDetails.value.email &&
             shippingDetails.value.address &&
             shippingDetails.value.city &&
             shippingDetails.value.postalCode
    })
    
    const fetchCart = async () => {
      try {
        loading.value = true
        error.value = null
        
        if (authStore.isLoggedIn) {
          // Dla zalogowanych użytkowników - użyj standardowego API
          const response = await axios.get('/api/checkout')
          cartItems.value = response.data.cart_items
        } else {
          // Dla gości - pobierz z localStorage i wyślij do guest API
          const savedCart = localStorage.getItem('cart')
          if (savedCart) {
            const localCartItems = JSON.parse(savedCart)
            if (localCartItems.length > 0) {
              // Przekształć dane z localStorage do formatu oczekiwanego przez API
              const cartData = localCartItems.map(item => ({
                product_id: item.product_id,
                quantity: item.quantity
              }))
              
              const response = await axios.post('/api/guest-checkout', {
                cart_items: cartData
              })
              cartItems.value = response.data.cart_items
            } else {
              cartItems.value = []
            }
          } else {
            cartItems.value = []
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
        
        // Jeśli wybrano płatność Stripe, utwórz sesję i przekieruj
        if (paymentMethod.value === 'stripe') {
          await processStripeCheckout()
          return
        }
        
        // Dla płatności przy odbiorze - przetwórz zamówienie
        if (authStore.isLoggedIn) {
          // Dla zalogowanych użytkowników
          const response = await axios.post('/api/checkout/process', {
            shipping: shippingDetails.value
          })
          
          // Wyczyść koszyk po udanym zamówieniu
          await cartStore.clearCart()
          
          alert('Zamówienie zostało złożone pomyślnie!')
          router.push('/')
          
        } else {
          // Dla gości - wyślij dane koszyka wraz z danymi wysyłki
          const savedCart = localStorage.getItem('cart')
          if (!savedCart) {
            throw new Error('Koszyk jest pusty')
          }
          
          const localCartItems = JSON.parse(savedCart)
          const cartData = localCartItems.map(item => ({
            product_id: item.product_id,
            quantity: item.quantity
          }))
          
          const response = await axios.post('/api/guest-checkout/process', {
            shipping: shippingDetails.value,
            cart_items: cartData
          })
          
          // Wyczyść localStorage po udanym zamówieniu
          localStorage.removeItem('cart')
          cartStore.items = []
          
          alert('Zamówienie zostało złożone pomyślnie!')
          router.push('/')
        }
        
      } catch (err) {
        console.error('Error processing checkout:', err)
        error.value = err.response?.data?.message || 'Nie udało się przetworzyć zamówienia'
      } finally {
        loading.value = false
      }
    }
    
    const formatPrice = (price) => {
      return new Intl.NumberFormat('pl-PL', {
        style: 'currency',
        currency: 'PLN'
      }).format(price)
    }
    
    const processStripeCheckout = async () => {
      try {
        loading.value = true
        
        const endpoint = authStore.isLoggedIn 
          ? '/api/stripe/create-checkout-session'
          : '/api/guest-stripe/create-checkout-session'
        
        const payload = {
          shipping: shippingDetails.value,
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
    })
    
    return {
      authStore,
      cartItems,
      loading,
      error,
      paymentMethod,
      shippingDetails,
      total,
      isFormValid,
      processCheckout,
      processStripeCheckout,
      formatPrice
    }
  }
}
</script> 