<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
      <!-- Shipping Information -->
      <div class="bg-white p-6 rounded-lg shadow">
        <h2 class="text-2xl font-semibold mb-6">Dane do wysyłki</h2>
        <form @submit.prevent="processCheckout" class="space-y-4">
          <div>
            <label for="name" class="block text-sm font-medium text-gray-700">Imię i nazwisko</label>
            <input
              type="text"
              id="name"
              v-model="shippingDetails.name"
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
              required
            />
          </div>
          
          <div>
            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
            <input
              type="email"
              id="email"
              v-model="shippingDetails.email"
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
              required
            />
          </div>
          
          <div>
            <label for="address" class="block text-sm font-medium text-gray-700">Adres</label>
            <input
              type="text"
              id="address"
              v-model="shippingDetails.address"
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
              required
            />
          </div>
          
          <div>
            <label for="city" class="block text-sm font-medium text-gray-700">Miasto</label>
            <input
              type="text"
              id="city"
              v-model="shippingDetails.city"
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
              required
            />
          </div>
          
          <div>
            <label for="postal_code" class="block text-sm font-medium text-gray-700">Kod pocztowy</label>
            <input
              type="text"
              id="postal_code"
              v-model="shippingDetails.postalCode"
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
              required
            />
          </div>
          
          <!-- Wybór metody płatności -->
          <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-3">Metoda płatności</label>
            <div class="space-y-2">
              <div class="flex items-center">
                <input
                  id="stripe"
                  name="payment_method"
                  type="radio"
                  value="stripe"
                  v-model="paymentMethod"
                  class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300"
                />
                <label for="stripe" class="ml-3 block text-sm font-medium text-gray-700">
                  Karta płatnicza (Stripe)
                </label>
              </div>
              <div class="flex items-center">
                <input
                  id="cod"
                  name="payment_method"
                  type="radio"
                  value="cod"
                  v-model="paymentMethod"
                  class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300"
                />
                <label for="cod" class="ml-3 block text-sm font-medium text-gray-700">
                  Płatność przy odbiorze
                </label>
              </div>
            </div>
          </div>

          <!-- Submit button -->
          <button
            type="submit"
            :disabled="loading || cartItems.length === 0 || (paymentMethod === 'stripe' && !isFormValid)"
            class="w-full bg-indigo-600 text-white py-2 px-4 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <span v-if="loading">Przetwarzanie...</span>
            <span v-else-if="paymentMethod === 'stripe'">Przejdź do płatności</span>
            <span v-else>Złóż zamówienie</span>
          </button>
        </form>
      </div>

      <!-- Order Summary -->
      <div class="bg-white p-6 rounded-lg shadow">
        <h2 class="text-2xl font-semibold mb-6">Podsumowanie zamówienia</h2>
        <div v-if="loading && cartItems.length === 0" class="text-center py-4">
          <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600 mx-auto"></div>
        </div>
        <div v-else-if="error" class="text-red-600 text-center py-4">
          {{ error }}
        </div>
        <div v-else-if="cartItems.length === 0" class="text-center py-4">
          <p class="text-gray-500">Koszyk jest pusty</p>
        </div>
        <div v-else>
          <div class="space-y-4">
            <div v-for="item in cartItems" :key="item.product_id || item.id" class="flex justify-between items-center">
              <div>
                <h3 class="font-medium">{{ item.product.name }}</h3>
                <p class="text-sm text-gray-500">Ilość: {{ item.quantity }}</p>
              </div>
              <p class="font-medium">{{ formatPrice(item.product.price * item.quantity) }}</p>
            </div>
          </div>
          
          <div class="border-t border-gray-200 mt-6 pt-6">
            <div class="flex justify-between">
              <span class="font-medium">Suma</span>
              <span class="font-bold">{{ formatPrice(total) }}</span>
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