<template>
  <div class="min-h-screen bg-gradient-to-br from-indigo-50 via-blue-50 to-purple-50">
    <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
      <!-- Header -->
      <div class="text-center mb-12">
        <div class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-indigo-100 to-purple-100 rounded-full text-indigo-700 font-semibold text-sm mb-4">
          <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
          </svg>
          Poradnik
        </div>
      </div>

      <!-- Loading state -->
      <div v-if="loading" class="text-center py-16">
        <div class="w-16 h-16 border-4 border-indigo-500 border-t-transparent rounded-full animate-spin mx-auto"></div>
        <p class="mt-4 text-gray-500 font-medium">Ładowanie poradnika...</p>
      </div>

      <!-- Error state -->
      <div v-else-if="error" class="text-center py-16">
        <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-6 rounded-lg max-w-md mx-auto">
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
        <router-link 
          :to="{ name: 'tutorials' }" 
          class="inline-flex items-center mt-6 px-6 py-3 border border-transparent text-sm font-medium rounded-lg text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 shadow-lg hover:shadow-xl"
        >
          <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16l-4-4m0 0l4-4m-4 4h18"/>
          </svg>
          Wróć do listy poradników
        </router-link>
      </div>

      <!-- Tutorial content -->
      <article v-else class="bg-white shadow-xl rounded-2xl overflow-hidden border border-gray-100">
        <!-- Tutorial header -->
        <div class="relative">
          <img 
            :src="getTutorialImageUrl(tutorial.featured_image_url, tutorial.title)"
            :alt="tutorial.title"
            class="w-full h-[400px] object-cover"
            @error="(e) => handleTutorialImageError(e, tutorial.title)"
          >
          <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent"></div>
          <div class="absolute bottom-0 left-0 right-0 p-8">
            <div class="flex items-center space-x-3 mb-4">
              <span v-if="tutorial.category" class="inline-flex items-center px-3 py-1 text-sm bg-white/90 text-gray-900 rounded-full font-medium">
                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                </svg>
                {{ tutorial.category }}
              </span>
              <span v-if="tutorial.difficulty" class="inline-flex items-center px-3 py-1 text-sm bg-gradient-to-r from-indigo-500 to-purple-500 text-white rounded-full font-medium">
                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
                {{ tutorial.difficulty }}
              </span>
            </div>
            <h1 class="text-4xl md:text-5xl font-bold text-white mb-4 leading-tight">{{ tutorial.title }}</h1>
            <div class="flex flex-wrap items-center text-white/90 space-x-6 text-sm">
              <span class="flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                {{ tutorial.author }}
              </span>

              <span class="flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                {{ formatDate(tutorial.published_at) }}
              </span>
            </div>
          </div>
        </div>

        <!-- Tutorial content -->
        <div class="p-8 md:p-12">
          <div class="prose prose-lg prose-indigo max-w-none" v-html="tutorial.content"></div>
        </div>

        <!-- Navigation footer -->
        <div class="bg-gradient-to-r from-indigo-50 to-purple-50 border-t border-gray-200 p-8">
          <div class="flex flex-col sm:flex-row justify-between items-center space-y-4 sm:space-y-0">
            <router-link 
              :to="{ name: 'tutorials' }" 
              class="inline-flex items-center px-6 py-3 border border-gray-300 shadow-sm text-base font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200"
            >
              <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16l-4-4m0 0l4-4m-4 4h18"/>
              </svg>
              Wróć do listy poradników
            </router-link>
            
            <div class="flex items-center space-x-4">
              <button class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"/>
                </svg>
                Udostępnij
              </button>
              <button class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                </svg>
                Polub
              </button>
            </div>
          </div>
        </div>
      </article>
    </div>
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
        month: 'long',
        day: 'numeric'
      })
    }

    const generateTutorialPlaceholder = (title, width = 800, height = 400) => {
      const encodedTitle = encodeURIComponent(title || 'Tutorial');
      const svg = `
        <svg width="${width}" height="${height}" xmlns="http://www.w3.org/2000/svg">
          <rect width="100%" height="100%" fill="#4f46e5"/>
          <text x="50%" y="50%" text-anchor="middle" dy=".3em" font-family="Arial, sans-serif" font-size="24" fill="white">${encodedTitle}</text>
        </svg>
      `;
      return `data:image/svg+xml;base64,${btoa(svg)}`;
    };

    const getTutorialImageUrl = (imageUrl, title) => {
      if (imageUrl && !imageUrl.includes('via.placeholder.com')) {
        return imageUrl;
      }
      return generateTutorialPlaceholder(title, 800, 400);
    };

    const handleTutorialImageError = (event, title) => {
      event.target.src = generateTutorialPlaceholder(title, 800, 400);
    };

    onMounted(() => {
      fetchTutorial()
    })

    return {
      tutorial,
      loading,
      error,
      formatDate,
      getTutorialImageUrl,
      handleTutorialImageError
    }
  }
}
</script>

<style scoped>
/* Custom prose styling for better integration with the design system */
:deep(.prose) {
  @apply text-gray-700;
}

:deep(.prose h1) {
  @apply text-gray-900 font-bold;
}

:deep(.prose h2) {
  @apply text-gray-900 font-bold;
}

:deep(.prose h3) {
  @apply text-gray-900 font-semibold;
}

:deep(.prose a) {
  @apply text-indigo-600 hover:text-indigo-800 transition-colors duration-200;
}

:deep(.prose blockquote) {
  @apply border-l-4 border-indigo-200 bg-indigo-50 rounded-r-lg;
}

:deep(.prose code) {
  @apply bg-gray-100 text-gray-800 px-2 py-1 rounded;
}

:deep(.prose pre) {
  @apply bg-gray-900 text-gray-100 rounded-lg;
}
</style> 