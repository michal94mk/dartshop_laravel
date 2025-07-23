<template>
  <div class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50">
    <!-- Hero Section -->
    <div class="relative overflow-hidden bg-gradient-to-r from-blue-600 to-indigo-600 py-16">
      <div class="absolute inset-0 bg-black opacity-20"></div>
      <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl md:text-5xl font-bold text-white mb-4">
          Poradniki & Tutoriale
        </h1>
        <p class="text-xl text-blue-100 max-w-2xl mx-auto">
          Odkryj nasze ekspertów poradniki i wskazówki, które pomogą Ci w rozwijaniu umiejętności i osiąganiu celów.
        </p>
      </div>
    </div>

    <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
      <!-- Loading state -->
      <div v-if="loading" class="flex justify-center items-center py-20">
        <div class="relative">
          <div class="w-20 h-20 border-4 border-blue-200 rounded-full animate-spin border-t-blue-600"></div>
          <div class="absolute inset-0 flex items-center justify-center">
            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
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
          <button @click="fetchTutorials" 
                  class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-xl text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 shadow-lg hover:shadow-xl">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
            </svg>
            Spróbuj ponownie
          </button>
        </div>
      </div>

      <!-- No tutorials -->
      <div v-else-if="tutorials.length === 0" class="text-center py-20">
        <div class="bg-white rounded-2xl shadow-lg p-8 max-w-md mx-auto">
          <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
            </svg>
          </div>
          <h3 class="text-lg font-semibold text-gray-900 mb-2">Brak poradników</h3>
          <p class="text-gray-600">Aktualnie brak dostępnych poradników. Sprawdź ponownie wkrótce!</p>
        </div>
      </div>

      <!-- Tutorials grid -->
      <div v-else>
        <!-- Stats Section -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
          <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100 text-center">
            <div class="w-12 h-12 bg-gradient-to-r from-blue-400 to-indigo-500 rounded-xl flex items-center justify-center mx-auto mb-4">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
              </svg>
            </div>
            <h3 class="text-2xl font-bold text-gray-900 mb-1">{{ tutorials.length }}</h3>
            <p class="text-gray-600">Dostępnych poradników</p>
          </div>
          
          <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100 text-center">
            <div class="w-12 h-12 bg-gradient-to-r from-green-400 to-blue-500 rounded-xl flex items-center justify-center mx-auto mb-4">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
              </svg>
            </div>
            <h3 class="text-2xl font-bold text-gray-900 mb-1">Darmowe</h3>
            <p class="text-gray-600">Wszystkie poradniki</p>
          </div>
          
          <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100 text-center">
            <div class="w-12 h-12 bg-gradient-to-r from-purple-400 to-pink-500 rounded-xl flex items-center justify-center mx-auto mb-4">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
            </div>
            <h3 class="text-2xl font-bold text-gray-900 mb-1">Online</h3>
            <p class="text-gray-600">Zawsze dostępne</p>
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
          <div v-for="tutorial in tutorials" :key="tutorial.id" 
               class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition-all duration-300 group border border-gray-100 transform hover:-translate-y-1">
            <router-link :to="{ name: 'tutorial', params: { slug: tutorial.slug }}" class="block">
              <div class="relative overflow-hidden">
                <div class="relative pb-[56.25%]">
                  <img 
                    :src="tutorial.thumbnail_image_url" 
                    :alt="tutorial.title"
                    class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                  >
                </div>
                <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                <div class="absolute top-4 left-4">
                  <div class="flex flex-wrap gap-2">
                    <span v-if="tutorial.category" class="px-3 py-1 text-xs font-semibold bg-white/90 text-gray-800 rounded-full backdrop-blur">
                      {{ tutorial.category }}
                    </span>
                    <span v-if="tutorial.difficulty" class="px-3 py-1 text-xs font-semibold bg-blue-500 text-white rounded-full">
                      {{ tutorial.difficulty }}
                    </span>
                  </div>
                </div>
              </div>
              
              <div class="p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-3 line-clamp-2 group-hover:text-blue-600 transition-colors">
                  {{ tutorial.title }}
                </h2>
                <p class="text-gray-600 mb-4 line-clamp-3 leading-relaxed">{{ tutorial.excerpt }}</p>
                
                <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                  <div class="flex items-center text-sm text-gray-500">
                    <div class="w-8 h-8 bg-gradient-to-r from-blue-400 to-indigo-500 rounded-full flex items-center justify-center mr-3">
                      <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                      </svg>
                    </div>
                    <span class="font-medium">{{ tutorial.author }}</span>
                  </div>
                  
                  <div class="flex items-center justify-end text-sm text-gray-500">
                    <span class="flex items-center">
                      <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                      </svg>
                      {{ formatDate(tutorial.published_at) }}
                    </span>
                  </div>
                </div>
              </div>
            </router-link>
          </div>
        </div>

        <!-- CTA Section -->
        <div class="mt-16 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-2xl shadow-xl p-8 text-center text-white">
          <h2 class="text-3xl font-bold mb-4">Nie możesz znaleźć tego, czego szukasz?</h2>
          <p class="text-xl text-blue-100 mb-8 max-w-2xl mx-auto">
            Skontaktuj się z nami, a pomożemy Ci znaleźć odpowiednie rozwiązanie lub stworzymy dedykowany poradnik.
          </p>
          <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <router-link to="/contact" 
                         class="inline-flex items-center px-8 py-4 border-2 border-white text-base font-semibold rounded-xl text-white bg-transparent hover:bg-white hover:text-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-white transition-all duration-200">
              <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
              </svg>
              Skontaktuj się z nami
            </router-link>
            <router-link to="/" 
                         class="inline-flex items-center px-8 py-4 border border-transparent text-base font-semibold rounded-xl text-blue-600 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 shadow-lg">
              <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M8 11v6a2 2 0 002 2h4a2 2 0 002-2v-6M8 11h8"/>
              </svg>
              Przejdź do sklepu
            </router-link>
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
  name: 'Tutorials',
  setup() {
    const tutorials = ref([])
    const loading = ref(true)
    const error = ref(null)

    const fetchTutorials = async () => {
      try {
        loading.value = true
        error.value = null
        const response = await axios.get('/api/tutorials')
        tutorials.value = response.data.data
      } catch (err) {
        console.error('Error fetching tutorials:', err)
        error.value = 'Wystąpił błąd podczas pobierania poradników. Spróbuj ponownie później.'
      } finally {
        loading.value = false
      }
    }

    const formatDate = (date) => {
      return new Date(date).toLocaleDateString('pl-PL', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
      })
    }

    onMounted(() => {
      fetchTutorials()
    })

    return {
      tutorials,
      loading,
      error,
      fetchTutorials,
      formatDate
    }
  }
}
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.line-clamp-3 {
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style> 