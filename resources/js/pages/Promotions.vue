<template>
  <div class="min-h-screen bg-gradient-to-br from-orange-50 via-red-50 to-pink-50">
    <!-- Hero Section -->
    <div class="relative overflow-hidden bg-gradient-to-r from-red-600 to-pink-600 py-16">
      <div class="absolute inset-0 bg-black opacity-20"></div>
      <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl md:text-5xl font-bold text-white mb-4">
          Promocje & Oferty Specjalne
        </h1>
        <p class="text-xl text-red-100 max-w-2xl mx-auto">
          Odkryj najlepsze okazje i promocje na profesjonalny sprzęt do dart. Oszczędzaj kupując wysokiej jakości produkty.
        </p>
      </div>
    </div>

    <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
      <!-- Loading state -->
      <div v-if="loading" class="flex justify-center items-center py-20">
        <div class="relative">
          <div class="w-20 h-20 border-4 border-red-200 rounded-full animate-spin border-t-red-600"></div>
          <div class="absolute inset-0 flex items-center justify-center">
            <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
            </svg>
          </div>
        </div>
      </div>

      <!-- Error state -->
      <div v-else-if="error" class="text-center py-20">
        <div class="bg-white rounded-2xl shadow-lg p-8 max-w-md mx-auto border border-red-100">
          <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
          </div>
          <h3 class="text-lg font-semibold text-gray-900 mb-2">Ups! Coś poszło nie tak</h3>
          <p class="text-gray-600 mb-6">{{ error }}</p>
          <button @click="fetchPromotions" 
                  class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-xl text-white bg-gradient-to-r from-red-600 to-pink-600 hover:from-red-700 hover:to-pink-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all duration-200 shadow-lg hover:shadow-xl">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
            </svg>
            Spróbuj ponownie
          </button>
        </div>
      </div>

      <!-- No promotions -->
      <div v-else-if="promotions.length === 0" class="text-center py-20">
        <div class="bg-white rounded-2xl shadow-lg p-8 max-w-md mx-auto">
          <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
            </svg>
          </div>
          <h3 class="text-lg font-semibold text-gray-900 mb-2">Aktualnie brak promocji</h3>
          <p class="text-gray-600 mb-6">Nie mamy obecnie żadnych promocji, ale sprawdź ponownie wkrótce!</p>
          <router-link to="/products" 
                       class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-xl text-white bg-gradient-to-r from-orange-600 to-red-600 hover:from-orange-700 hover:to-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition-all duration-200 shadow-lg hover:shadow-xl">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M8 11v6a2 2 0 002 2h4a2 2 0 002-2v-6M8 11h8"/>
            </svg>
            Przejdź do produktów
          </router-link>
        </div>
      </div>

      <!-- Promotions content -->
      <div v-else>
        <!-- Stats Section -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
          <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100 text-center">
            <div class="w-12 h-12 bg-gradient-to-r from-red-400 to-pink-500 rounded-xl flex items-center justify-center mx-auto mb-4">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
              </svg>
            </div>
            <h3 class="text-2xl font-bold text-gray-900 mb-1">{{ promotions.length }}</h3>
            <p class="text-gray-600">Aktywnych promocji</p>
          </div>
          
          <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100 text-center">
            <div class="w-12 h-12 bg-gradient-to-r from-orange-400 to-red-500 rounded-xl flex items-center justify-center mx-auto mb-4">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
              </svg>
            </div>
            <h3 class="text-2xl font-bold text-gray-900 mb-1">Do 50%</h3>
            <p class="text-gray-600">Maksymalna zniżka</p>
          </div>
          
          <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100 text-center">
            <div class="w-12 h-12 bg-gradient-to-r from-yellow-400 to-orange-500 rounded-xl flex items-center justify-center mx-auto mb-4">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
            </div>
            <h3 class="text-2xl font-bold text-gray-900 mb-1">24/7</h3>
            <p class="text-gray-600">Dostęp do ofert</p>
          </div>
        </div>

        <!-- Sample Promotions -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
          <!-- Sample promotion cards with modern design -->
          <div class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition-all duration-300 group border border-gray-100 transform hover:-translate-y-1">
            <div class="relative">
              <div class="h-48 bg-gradient-to-br from-red-400 to-pink-500 flex items-center justify-center">
                <div class="text-center text-white">
                  <div class="text-4xl font-bold mb-2">-30%</div>
                  <div class="text-lg">Wszystkie lotki</div>
                </div>
              </div>
              <div class="absolute top-4 right-4">
                <div class="bg-yellow-400 text-gray-900 px-3 py-1 rounded-full text-sm font-semibold">
                  PROMOCJA
                </div>
              </div>
            </div>
            <div class="p-6">
              <h3 class="text-xl font-bold text-gray-900 mb-2">Mega promocja na lotki</h3>
              <p class="text-gray-600 mb-4">Zniżka 30% na wszystkie lotki w naszej ofercie. Idealna okazja na wymianę sprzętu!</p>
              <div class="flex items-center justify-between text-sm text-gray-500 mb-4">
                <span>Ważne do: 31.12.2024</span>
                <span class="flex items-center">
                  <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                  </svg>
                  Ograniczona
                </span>
              </div>
              <router-link to="/products?category=lotki" 
                           class="inline-flex items-center justify-center w-full px-6 py-3 border border-transparent text-base font-medium rounded-xl text-white bg-gradient-to-r from-red-600 to-pink-600 hover:from-red-700 hover:to-pink-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all duration-200">
                Zobacz produkty
              </router-link>
            </div>
          </div>

          <div class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition-all duration-300 group border border-gray-100 transform hover:-translate-y-1">
            <div class="relative">
              <div class="h-48 bg-gradient-to-br from-blue-400 to-indigo-500 flex items-center justify-center">
                <div class="text-center text-white">
                  <div class="text-4xl font-bold mb-2">-20%</div>
                  <div class="text-lg">Tarcze elektroniczne</div>
                </div>
              </div>
              <div class="absolute top-4 right-4">
                <div class="bg-green-400 text-gray-900 px-3 py-1 rounded-full text-sm font-semibold">
                  NOWOŚĆ
                </div>
              </div>
            </div>
            <div class="p-6">
              <h3 class="text-xl font-bold text-gray-900 mb-2">Zniżka na tarcze elektroniczne</h3>
              <p class="text-gray-600 mb-4">20% taniej wszystkie tarcze elektroniczne. Unowocześnij swoje wyposażenie!</p>
              <div class="flex items-center justify-between text-sm text-gray-500 mb-4">
                <span>Ważne do: 15.01.2025</span>
                <span class="flex items-center">
                  <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                  </svg>
                  Bestseller
                </span>
              </div>
              <router-link to="/products?category=tarcze" 
                           class="inline-flex items-center justify-center w-full px-6 py-3 border border-transparent text-base font-medium rounded-xl text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200">
                Zobacz produkty
              </router-link>
            </div>
          </div>

          <div class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition-all duration-300 group border border-gray-100 transform hover:-translate-y-1">
            <div class="relative">
              <div class="h-48 bg-gradient-to-br from-green-400 to-teal-500 flex items-center justify-center">
                <div class="text-center text-white">
                  <div class="text-3xl font-bold mb-2">2+1</div>
                  <div class="text-lg">Akcesoria</div>
                </div>
              </div>
              <div class="absolute top-4 right-4">
                <div class="bg-orange-400 text-gray-900 px-3 py-1 rounded-full text-sm font-semibold">
                  PAKIET
                </div>
              </div>
            </div>
            <div class="p-6">
              <h3 class="text-xl font-bold text-gray-900 mb-2">Pakiet akcesoriów 2+1</h3>
              <p class="text-gray-600 mb-4">Kup 2 akcesoria, a trzecie otrzymasz gratis! Idealna oferta na kompletne wyposażenie.</p>
              <div class="flex items-center justify-between text-sm text-gray-500 mb-4">
                <span>Ważne do: 28.02.2025</span>
                <span class="flex items-center">
                  <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                  </svg>
                  Super oferta
                </span>
              </div>
              <router-link to="/products?category=akcesoria" 
                           class="inline-flex items-center justify-center w-full px-6 py-3 border border-transparent text-base font-medium rounded-xl text-white bg-gradient-to-r from-green-600 to-teal-600 hover:from-green-700 hover:to-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-200">
                Zobacz produkty
              </router-link>
            </div>
          </div>
        </div>

        <!-- Newsletter Section -->
        <div class="mt-16 bg-gradient-to-r from-orange-600 to-red-600 rounded-2xl shadow-xl p-8 text-center text-white">
          <h2 class="text-3xl font-bold mb-4">Nie przegap żadnej promocji!</h2>
          <p class="text-xl text-orange-100 mb-8 max-w-2xl mx-auto">
            Zapisz się do naszego newslettera i bądź pierwszym, który dowie się o nowych promocjach i ofertach specjalnych.
          </p>
          <div class="flex flex-col sm:flex-row gap-4 justify-center max-w-md mx-auto">
            <input type="email" 
                   placeholder="Twój adres e-mail" 
                   class="flex-1 px-4 py-3 rounded-xl border-0 text-gray-900 placeholder-gray-500 focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-orange-600">
            <button class="px-8 py-3 bg-white text-orange-600 font-semibold rounded-xl hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-orange-600 transition-all duration-200">
              Zapisz się
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue'
import axios from 'axios'

export default {
  name: 'Promotions',
  setup() {
    const promotions = ref([])
    const loading = ref(false)
    const error = ref(null)

    const fetchPromotions = async () => {
      try {
        loading.value = true
        error.value = null
        // In a real app, this would fetch promotions data from an API
        // const response = await axios.get('/api/promotions')
        // promotions.value = response.data
        
        // For now, we'll set some sample data
        setTimeout(() => {
          promotions.value = [
            { id: 1, title: 'Sample Promotion', discount: 30 },
            { id: 2, title: 'Another Promotion', discount: 20 },
            { id: 3, title: 'Special Offer', discount: 50 }
          ]
          loading.value = false
        }, 1000)
      } catch (err) {
        console.error('Error fetching promotions:', err)
        error.value = 'Wystąpił błąd podczas pobierania promocji. Spróbuj ponownie później.'
      } finally {
        loading.value = false
      }
    }

    onMounted(() => {
      fetchPromotions()
    })

    return {
      promotions,
      loading,
      error,
      fetchPromotions
    }
  }
}
</script> 