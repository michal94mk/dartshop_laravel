<template>
  <div class="max-w-4xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <!-- Loading state -->
    <div v-if="loading" class="flex justify-center items-center py-12">
      <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-primary-600"></div>
    </div>

    <!-- Error state -->
    <div v-else-if="error" class="text-center py-12">
      <p class="text-red-600 text-lg">{{ error }}</p>
      <router-link 
        :to="{ name: 'tutorials' }" 
        class="inline-block mt-4 px-4 py-2 bg-primary-600 text-white rounded-md hover:bg-primary-700"
      >
        Wróć do listy poradników
      </router-link>
    </div>

    <!-- Tutorial content -->
    <article v-else class="bg-white rounded-lg shadow-lg overflow-hidden">
      <!-- Tutorial header -->
      <div class="relative">
        <img 
          :src="tutorial.featured_image_url" 
          :alt="tutorial.title"
          class="w-full h-[400px] object-cover"
        >
        <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
        <div class="absolute bottom-0 left-0 right-0 p-8">
          <div class="flex items-center space-x-2 mb-4">
            <span v-if="tutorial.category" class="px-3 py-1 text-sm bg-white/90 text-gray-900 rounded-full">
              {{ tutorial.category }}
            </span>
            <span v-if="tutorial.difficulty" class="px-3 py-1 text-sm bg-primary-500/90 text-white rounded-full">
              {{ tutorial.difficulty }}
            </span>
          </div>
          <h1 class="text-4xl font-bold text-white mb-2">{{ tutorial.title }}</h1>
          <div class="flex items-center text-white/90 space-x-6">
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
      </div>

      <!-- Tutorial content -->
      <div class="p-8">
        <div class="prose prose-lg max-w-none" v-html="tutorial.content"></div>
      </div>

      <!-- Navigation -->
      <div class="border-t border-gray-200 p-8">
        <router-link 
          :to="{ name: 'tutorials' }" 
          class="inline-flex items-center text-primary-600 hover:text-primary-700"
        >
          <i class="fas fa-arrow-left mr-2"></i>
          Wróć do listy poradników
        </router-link>
      </div>
    </article>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import axios from 'axios'

export default {
  name: 'Tutorial',
  setup() {
    const route = useRoute()
    const tutorial = ref(null)
    const loading = ref(true)
    const error = ref(null)

    const fetchTutorial = async () => {
      try {
        loading.value = true
        error.value = null
        const response = await axios.get(`/api/tutorials/${route.params.slug}`)
        tutorial.value = response.data
      } catch (err) {
        console.error('Error fetching tutorial:', err)
        error.value = 'Wystąpił błąd podczas pobierania poradnika. Spróbuj ponownie później.'
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
      fetchTutorial()
    })

    return {
      tutorial,
      loading,
      error,
      formatDate
    }
  }
}
</script> 