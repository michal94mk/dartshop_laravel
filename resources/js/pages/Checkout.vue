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
          
          <button
            type="submit"
            class="w-full bg-indigo-600 text-white py-2 px-4 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
          >
            Przejdź do płatności
          </button>
        </form>
      </div>

      <!-- Order Summary -->
      <div class="bg-white p-6 rounded-lg shadow">
        <h2 class="text-2xl font-semibold mb-6">Podsumowanie zamówienia</h2>
        <div v-if="loading" class="text-center py-4">
          <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600 mx-auto"></div>
        </div>
        <div v-else-if="error" class="text-red-600 text-center py-4">
          {{ error }}
        </div>
        <div v-else>
          <div class="space-y-4">
            <div v-for="item in cartItems" :key="item.id" class="flex justify-between items-center">
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
import axios from 'axios'

export default {
  name: 'Checkout',
  
  setup() {
    const cartItems = ref([])
    const loading = ref(true)
    const error = ref(null)
    
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
    
    const fetchCart = async () => {
      try {
        loading.value = true
        const response = await axios.get('/api/checkout')
        cartItems.value = response.data.cart_items
      } catch (err) {
        error.value = err.response?.data?.message || 'Nie udało się załadować koszyka'
      } finally {
        loading.value = false
      }
    }
    
    const processCheckout = async () => {
      try {
        loading.value = true
        const response = await axios.post('/api/checkout/process', {
          shipping: shippingDetails.value
        })
        // Handle successful checkout (will be implemented with Stripe)
      } catch (err) {
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
    
    onMounted(() => {
      fetchCart()
    })
    
    return {
      cartItems,
      loading,
      error,
      shippingDetails,
      total,
      processCheckout,
      formatPrice
    }
  }
}
</script> 