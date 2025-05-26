<template>
  <div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Loading state -->
      <div v-if="loading" class="text-center py-12">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600 mx-auto mb-4"></div>
        <h2 class="text-xl font-semibold text-gray-900 mb-2">Przetwarzanie płatności...</h2>
        <p class="text-gray-600">Proszę czekać, weryfikujemy Twoją płatność.</p>
      </div>

      <!-- Error state -->
      <div v-else-if="error" class="text-center py-12">
        <div class="bg-red-50 border border-red-200 rounded-lg p-6">
          <div class="text-red-600 mb-4">
            <svg class="w-16 h-16 mx-auto" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
            </svg>
          </div>
          <h2 class="text-xl font-semibold text-red-800 mb-2">Wystąpił problem z płatnością</h2>
          <p class="text-red-700 mb-4">{{ error }}</p>
          <div class="space-y-2">
            <button 
              @click="$router.push('/checkout')" 
              class="bg-red-600 text-white px-6 py-2 rounded-md hover:bg-red-700 mr-2"
            >
              Spróbuj ponownie
            </button>
            <button 
              @click="$router.push('/')" 
              class="bg-gray-200 text-gray-800 px-6 py-2 rounded-md hover:bg-gray-300"
            >
              Powrót do sklepu
            </button>
          </div>
        </div>
      </div>

      <!-- Success state -->
      <div v-else-if="success && order" class="text-center py-12">
        <div class="bg-green-50 border border-green-200 rounded-lg p-6">
          <div class="text-green-600 mb-4">
            <svg class="w-16 h-16 mx-auto" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
          </div>
          <h2 class="text-2xl font-bold text-green-800 mb-2">Płatność zakończona pomyślnie!</h2>
          <p class="text-green-700 mb-6">Twoje zamówienie zostało złożone i opłacone.</p>
          
          <!-- Order details -->
          <div class="bg-white rounded-lg p-6 mb-6 text-left">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Szczegóły zamówienia</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
              <div>
                <p class="text-sm text-gray-600">Numer zamówienia:</p>
                <p class="font-medium">#{{ order.id }}</p>
              </div>
              <div>
                <p class="text-sm text-gray-600">Status:</p>
                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                  Opłacone
                </span>
              </div>
              <div>
                <p class="text-sm text-gray-600">Email:</p>
                <p class="font-medium">{{ order.email }}</p>
              </div>
              <div>
                <p class="text-sm text-gray-600">Suma:</p>
                <p class="font-medium text-lg">{{ formatPrice(order.total) }}</p>
              </div>
            </div>

            <div class="border-t pt-4">
              <p class="text-sm text-gray-600 mb-2">Adres dostawy:</p>
              <p class="font-medium">{{ order.first_name }} {{ order.last_name }}</p>
              <p>{{ order.address }}</p>
              <p>{{ order.postal_code }} {{ order.city }}</p>
            </div>

            <!-- Order items -->
            <div v-if="order.items && order.items.length > 0" class="border-t pt-4 mt-4">
              <p class="text-sm text-gray-600 mb-2">Zamówione produkty:</p>
              <div class="space-y-2">
                <div v-for="item in order.items" :key="item.id" 
                     class="flex justify-between items-center py-2 border-b border-gray-100">
                  <div>
                    <p class="font-medium">{{ item.product_name }}</p>
                    <p class="text-sm text-gray-500">Ilość: {{ item.quantity }}</p>
                  </div>
                  <p class="font-medium">{{ formatPrice(item.total) }}</p>
                </div>
              </div>
            </div>
          </div>

          <div class="space-y-2">
            <button 
              @click="$router.push('/')" 
              class="bg-indigo-600 text-white px-8 py-3 rounded-md hover:bg-indigo-700 text-lg font-medium"
            >
              Kontynuuj zakupy
            </button>
            <p class="text-sm text-gray-600">
              Szczegóły zamówienia zostały wysłane na Twój adres email.
            </p>
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
import axios from 'axios'

export default {
  name: 'PaymentSuccess',
  
  setup() {
    const route = useRoute()
    const router = useRouter()
    const cartStore = useCartStore()
    
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
        
        if (!sessionId) {
          throw new Error('Brak ID sesji płatności')
        }
        
        // Wywołaj endpoint do przetworzenia sukcesu płatności
        const response = await axios.post('/api/stripe/success', {
          session_id: sessionId
        })
        
        order.value = response.data.order
        success.value = true
        
        // Wyczyść koszyk lokalnie (jeśli to gość)
        const savedCart = localStorage.getItem('cart')
        if (savedCart) {
          localStorage.removeItem('cart')
          cartStore.items = []
        }
        
        // Wyczyść dane checkout
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