<template>
  <div class="min-h-screen bg-gradient-to-br from-indigo-50 via-blue-50 to-purple-50">
    <div class="max-w-4xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
      <!-- Header -->
      <div class="text-center mb-12">
        <div class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-indigo-100 to-purple-100 rounded-full text-indigo-700 font-semibold text-sm mb-4">
          <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
          </svg>
          Status płatności
        </div>
      </div>

      <!-- Loading state -->
      <div v-if="loading" class="text-center py-16">
        <div class="bg-white shadow-xl rounded-2xl p-12 max-w-md mx-auto border border-gray-100">
          <div class="w-16 h-16 border-4 border-indigo-500 border-t-transparent rounded-full animate-spin mx-auto mb-6"></div>
          <h2 class="text-xl font-bold text-gray-900 mb-3">Przetwarzanie płatności...</h2>
          <p class="text-gray-600">Proszę czekać, weryfikujemy Twoją płatność.</p>
        </div>
      </div>

      <!-- Error state -->
      <div v-else-if="error" class="text-center py-16">
        <div class="bg-white shadow-xl rounded-2xl p-12 max-w-md mx-auto border border-gray-100">
          <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-6">
            <svg class="w-10 h-10 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </div>
          <h2 class="text-2xl font-bold text-red-800 mb-4">Wystąpił problem z płatnością</h2>
          <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-lg mb-6">
            <p class="font-medium">{{ error }}</p>
          </div>
          <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <button 
              @click="$router.push('/checkout')" 
              class="inline-flex items-center px-6 py-3 border border-transparent text-sm font-medium rounded-lg text-white bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all duration-200 shadow-lg hover:shadow-xl"
            >
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
              </svg>
              Spróbuj ponownie
            </button>
            <button 
              @click="$router.push('/')" 
              class="inline-flex items-center px-6 py-3 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200"
            >
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
              </svg>
              Powrót do sklepu
            </button>
          </div>
        </div>
      </div>

      <!-- Success state -->
      <div v-else-if="success && order" class="text-center py-16">
        <div class="bg-white shadow-xl rounded-2xl overflow-hidden border border-gray-100 max-w-2xl mx-auto">
          <!-- Success header -->
          <div class="bg-gradient-to-r from-green-50 to-emerald-50 px-8 py-12 text-center border-b border-gray-200">
            <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
              <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
              </svg>
            </div>
            <h2 class="text-3xl font-bold text-green-800 mb-3">Zamówienie złożone pomyślnie!</h2>
            <p class="text-green-700 text-lg" v-if="order && order.payment_status === 'paid'">Twoje zamówienie zostało złożone i opłacone.</p>
            <p class="text-yellow-700 text-lg" v-else-if="order && order.payment_status === 'pending'">Twoje zamówienie zostało złożone. Płatność oczekuje na realizację.</p>
            <p class="text-red-700 text-lg" v-else-if="order && order.payment_status === 'failed'">Płatność nie powiodła się. Skontaktuj się z obsługą sklepu.</p>
            <p class="text-gray-700 text-lg" v-else>Twoje zamówienie zostało złożone. Status płatności: nieznany.</p>
          </div>
          
          <!-- Order details -->
          <div class="p-8">
            <div class="mb-8">
              <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Szczegóły zamówienia
              </h3>
              
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div class="bg-gray-50 rounded-lg p-4">
                  <p class="text-sm font-semibold text-gray-600 mb-1">Numer zamówienia</p>
                  <p class="text-lg font-bold text-gray-900">#{{ order.id }}</p>
                </div>
                <div class="bg-gray-50 rounded-lg p-4">
                  <p class="text-sm font-semibold text-gray-600 mb-1">Status</p>
                  <span v-if="order && order.payment_status === 'paid'" class="inline-flex items-center px-3 py-1 text-sm font-semibold rounded-full bg-green-100 text-green-800">
                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Opłacone
                  </span>
                  <span v-else-if="order && order.payment_status === 'pending'" class="inline-flex items-center px-3 py-1 text-sm font-semibold rounded-full bg-yellow-100 text-yellow-800">
                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Oczekuje na płatność
                  </span>
                  <span v-else-if="order && order.payment_status === 'failed'" class="inline-flex items-center px-3 py-1 text-sm font-semibold rounded-full bg-red-100 text-red-800">
                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Płatność nieudana
                  </span>
                  <span v-else class="inline-flex items-center px-3 py-1 text-sm font-semibold rounded-full bg-gray-100 text-gray-800">
                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Nieznany status
                  </span>
                </div>
                <div class="bg-gray-50 rounded-lg p-4">
                  <p class="text-sm font-semibold text-gray-600 mb-1">Email</p>
                  <p class="font-medium text-gray-900">{{ order.email }}</p>
                </div>
                <div class="bg-gray-50 rounded-lg p-4">
                  <p class="text-sm font-semibold text-gray-600 mb-1">Suma</p>
                  <p class="text-xl font-bold text-indigo-600">{{ formatPrice(order.total) }}</p>
                </div>
              </div>

              <div class="bg-gray-50 rounded-lg p-6 mb-6">
                <h4 class="text-sm font-semibold text-gray-600 mb-3 flex items-center">
                  <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                  </svg>
                  Adres dostawy
                </h4>
                <div class="text-gray-900">
                  <p class="font-semibold">{{ order.first_name }} {{ order.last_name }}</p>
                  <p>{{ order.address }}</p>
                  <p>{{ order.postal_code }} {{ order.city }}</p>
                </div>
              </div>

              <!-- Order items -->
              <div v-if="order.items && order.items.length > 0" class="border-t border-gray-200 pt-6">
                <h4 class="text-sm font-semibold text-gray-600 mb-4 flex items-center">
                  <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M8 11v6a2 2 0 002 2h4a2 2 0 002-2v-6M8 11h8"/>
                  </svg>
                  Zamówione produkty
                </h4>
                <div class="space-y-3">
                  <div v-for="item in order.items" :key="item.id" 
                       class="flex justify-between items-center py-3 px-4 bg-gray-50 rounded-lg">
                    <div>
                      <p class="font-semibold text-gray-900">{{ item.product_name }}</p>
                      <p class="text-sm text-gray-500">Ilość: {{ item.quantity }}</p>
                    </div>
                    <p class="font-bold text-indigo-600">{{ formatPrice(item.total) }}</p>
                  </div>
                </div>
              </div>
            </div>

            <div class="space-y-4">
              <button 
                @click="$router.push('/')" 
                class="w-full inline-flex justify-center items-center px-8 py-4 border border-transparent shadow-lg text-lg font-semibold rounded-lg text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 hover:shadow-xl transform hover:-translate-y-0.5"
              >
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M8 11v6a2 2 0 002 2h4a2 2 0 002-2v-6M8 11h8"/>
                </svg>
                Kontynuuj zakupy
              </button>
              <div class="bg-blue-50 border-l-4 border-blue-500 text-blue-700 p-4 rounded-lg">
                <div class="flex">
                  <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-blue-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                      <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                    </svg>
                  </div>
                  <div class="ml-3">
                    <p class="font-medium">Szczegóły zamówienia zostały wysłane na Twój adres email.</p>
                  </div>
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
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useCartStore } from '../stores/cartStore'
import { useAuthStore } from '../stores/authStore'
import axios from 'axios'

export default {
  name: 'PaymentSuccess',
  
  setup() {
    const route = useRoute()
    const router = useRouter()
    const cartStore = useCartStore()
    const authStore = useAuthStore()
    
    const loading = ref(true)
    const error = ref(null)
    const success = ref(false)
    const order = ref(null)
    
    const formatPrice = (price) => {
      return new Intl.NumberFormat('pl-PL', {
        style: 'currency',
        currency: 'PLN'
      }).format(price)
    }
    
    const processPaymentSuccess = async () => {
      try {
        const sessionId = route.query.session_id
        const orderId = route.query.order_id
        
        // Ensure auth store is initialized
        if (!authStore.authInitialized) {
          console.log('Auth not initialized, initializing...');
          await authStore.initAuth();
        }
        
        // Sprawdź, czy zamówienie zostało przekazane przez router state (gość)
        const stateOrder = window.history.state && window.history.state.order
        if (stateOrder) {
          order.value = stateOrder
          success.value = true
          // Clear cart for guest users
          await cartStore.clearCart();
        } else if (sessionId) {
          // Stripe payment
          // Odśwież dane użytkownika tylko jeśli jest zalogowany
          if (authStore.isLoggedIn) {
            console.log('Refreshing user data before processing Stripe payment...');
            await authStore.refreshUser();
          }
          
          const response = await axios.post('/api/stripe/success', {
            session_id: sessionId
          })
          order.value = response.data.data?.order
          success.value = true
          
          // Clear cart after successful Stripe payment
          await cartStore.clearCart();
          
          // Ponownie odśwież dane użytkownika po potwierdzeniu płatności (tylko dla zalogowanych)
          if (authStore.isLoggedIn) {
            console.log('Payment confirmed, refreshing user data again...');
            await authStore.refreshUser();
          }
          
          // Dodatkowe sprawdzenie czy dane użytkownika są poprawne
          if (!authStore.user) {
            console.log('User data still missing, forcing auth refresh...');
            await authStore.initAuth();
          }
        } else if (orderId) {
          // Cash on delivery - get order data (dla zalogowanych)
          const response = await axios.get(`/api/orders/${orderId}`)
          order.value = response.data.order
          success.value = true
          
          // Clear cart after successful COD order
          await cartStore.clearCart();
          
          // Odśwież dane użytkownika również dla płatności przy odbiorze
          if (authStore.isLoggedIn) {
            await authStore.refreshUser();
          }
        } else {
          throw new Error('Brak danych zamówienia')
        }
        // Clear cart locally (if guest)
        const savedCart = localStorage.getItem('cart')
        if (savedCart) {
          localStorage.removeItem('cart')
          cartStore.items = []
        }
        // Clear checkout data
        localStorage.removeItem('checkout_shipping')
      } catch (err) {
        console.error('Error processing payment success:', err)
        error.value = err.response?.data?.message || err.message || 'Nie udało się przetworzyć płatności'
      } finally {
        loading.value = false
      }
    }
    onMounted(() => {
      processPaymentSuccess()
    })
    
    return {
      loading,
      error,
      success,
      order,
      formatPrice
    }
  }
}
</script> 