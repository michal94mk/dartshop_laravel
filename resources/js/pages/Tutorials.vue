<template>
  <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <div class="text-center mb-12">
      <h1 class="text-4xl font-bold text-gray-900 mb-4">Poradniki</h1>
      <p class="text-xl text-gray-600 max-w-2xl mx-auto">
        Odkryj nasze poradniki i wskazówki, które pomogą Ci w rozwijaniu umiejętności.
      </p>
    </div>

    <!-- Loading state -->
    <div v-if="loading" class="flex justify-center items-center py-12">
      <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-primary-600"></div>
    </div>

    <!-- Error state -->
    <div v-else-if="error" class="text-center py-12">
      <p class="text-red-600 text-lg">{{ error }}</p>
      <button @click="fetchTutorials" class="mt-4 px-4 py-2 bg-primary-600 text-white rounded-md hover:bg-primary-700">
        Spróbuj ponownie
      </button>
    </div>

    <!-- No tutorials -->
    <div v-else-if="tutorials.length === 0" class="text-center py-12">
      <p class="text-gray-600 text-lg">Aktualnie brak dostępnych poradników.</p>
    </div>

    <!-- Tutorials grid -->
    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
      <div v-for="tutorial in tutorials" :key="tutorial.id" class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300">
        <router-link :to="{ name: 'tutorial', params: { slug: tutorial.slug }}" class="block">
          <div class="relative pb-[56.25%]">
            <img 
              :src="tutorial.thumbnail_image_url" 
              :alt="tutorial.title"
              class="absolute inset-0 w-full h-full object-cover"
            >
          </div>
          <div class="p-6">
            <div class="flex items-center space-x-2 mb-3">
              <span v-if="tutorial.category" class="px-3 py-1 text-sm bg-gray-100 text-gray-700 rounded-full">
                {{ tutorial.category }}
              </span>
              <span v-if="tutorial.difficulty" class="px-3 py-1 text-sm bg-primary-100 text-primary-700 rounded-full">
                {{ tutorial.difficulty }}
              </span>
            </div>
            <h2 class="text-xl font-semibold text-gray-900 mb-2 line-clamp-2">{{ tutorial.title }}</h2>
            <p class="text-gray-600 mb-4 line-clamp-3">{{ tutorial.excerpt }}</p>
            <div class="flex items-center justify-between text-sm text-gray-500">
              <span class="flex items-center">
                <i class="fas fa-user mr-2"></i>
                {{ tutorial.author }}
              </span>
              <span class="flex items-center">
                <i class="fas fa-eye mr-2"></i>
                {{ tutorial.views }}
              </span>
              <span class="flex items-center">
                <i class="fas fa-calendar mr-2"></i>
                {{ formatDate(tutorial.published_at) }}
              </span>
            </div>
          </div>
        </router-link>
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
        tutorials.value = response.data
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