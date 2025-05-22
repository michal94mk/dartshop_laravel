<template>
  <div>
    <!-- Loading indicator -->
    <div v-if="loading" class="flex justify-center my-12">
      <svg class="animate-spin h-10 w-10 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
      </svg>
    </div>
    
    <!-- No content message -->
    <div v-else-if="!aboutUs" class="my-6 text-center">
      <p class="text-xl text-gray-600">Nie znaleziono informacji. Sprawdź ponownie później.</p>
    </div>
    
    <!-- Content - Left/Right Layout -->
    <div v-else-if="aboutUs.image_position === 'left' || aboutUs.image_position === 'right'" 
         class="flex flex-col md:flex-row max-w-7xl mx-auto px-4 py-12 sm:px-6 lg:px-8" 
         :class="{ 'md:flex-row-reverse': aboutUs.image_position === 'right' }">
      
      <!-- Image section -->
      <div v-if="aboutUs.image_path" class="w-full md:w-2/5 lg:w-1/3 flex-shrink-0 mb-8 md:mb-0" 
           :class="{ 
             'md:mr-12 lg:mr-16': aboutUs.image_position === 'left', 
             'md:ml-12 lg:ml-16': aboutUs.image_position === 'right' 
           }">
        <img 
          :src="getImageUrl(aboutUs.image_path)" 
          :alt="aboutUs.title"
          class="w-full h-auto object-cover rounded-lg shadow-md"
        >
      </div>
      
      <!-- Content section -->
      <div class="flex-1">
        <h1 :class="[aboutUs.header_style || 'text-3xl font-bold text-gray-900', aboutUs.header_margin || 'mb-6']">{{ aboutUs.title }}</h1>
        <div :class="['break-words', aboutUs.content_layout || 'prose-lg']" v-html="aboutUs.content"></div>
      </div>
    </div>
    
    <!-- Content - Top/Bottom Layout -->
    <div v-else class="max-w-7xl mx-auto px-4 py-12 sm:px-6 lg:px-8 space-y-6">
      <!-- Image at the top -->
      <div v-if="aboutUs.image_path && aboutUs.image_position === 'top'" class="mb-8">
        <img 
          :src="getImageUrl(aboutUs.image_path)" 
          :alt="aboutUs.title"
          class="w-full h-auto max-h-96 object-cover rounded-lg shadow-md"
        >
      </div>
      
      <h1 :class="[aboutUs.header_style || 'text-3xl font-bold text-gray-900', aboutUs.header_margin || 'mb-6']">{{ aboutUs.title }}</h1>
      <div :class="['break-words', aboutUs.content_layout || 'prose-lg']" v-html="aboutUs.content"></div>
      
      <!-- Image at the bottom -->
      <div v-if="aboutUs.image_path && aboutUs.image_position === 'bottom'" class="mt-10">
        <img 
          :src="getImageUrl(aboutUs.image_path)" 
          :alt="aboutUs.title"
          class="w-full h-auto max-h-96 object-cover rounded-lg shadow-md"
        >
      </div>
    </div>
    
    <!-- Footer section with CTA -->
    <div class="bg-indigo-50 mt-12">
      <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:py-16 lg:px-8 lg:flex lg:items-center lg:justify-between">
        <h2 class="text-3xl font-extrabold tracking-tight text-gray-900 sm:text-4xl">
          <span class="block">Zainteresowany naszymi produktami?</span>
          <span class="block text-indigo-600">Sprawdź nasz sklep już dziś.</span>
        </h2>
        <div class="mt-8 flex lg:mt-0 lg:flex-shrink-0">
          <div class="inline-flex rounded-md shadow">
            <router-link to="/" class="inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
              Przejdź do sklepu
            </router-link>
          </div>
          <div class="ml-3 inline-flex rounded-md shadow">
            <router-link to="/contact" class="inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-indigo-600 bg-white hover:bg-indigo-50">
              Skontaktuj się
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
  name: 'About',
  setup() {
    const aboutUs = ref(null)
    const loading = ref(true)
    
    // Fetch about page data
    const fetchAboutUs = async () => {
      try {
        loading.value = true
        const response = await axios.get('/api/about')
        aboutUs.value = response.data
      } catch (error) {
        console.error('Error fetching about page content:', error)
      } finally {
        loading.value = false
      }
    }
    
    // Get the full URL for an image path
    const getImageUrl = (path) => {
      if (!path) return null
      
      // Check if it's already a full URL
      if (path.startsWith('http')) {
        return path
      }
      
      // Otherwise, construct URL from storage path
      return `/storage/${path}`
    }
    
    onMounted(() => {
      fetchAboutUs()
    })
    
    return {
      aboutUs,
      loading,
      getImageUrl
    }
  }
}
</script>

<style>
.prose img {
  border-radius: 0.375rem;
  max-width: 100%;
  height: auto;
}

.break-words {
  overflow-wrap: break-word;
  word-wrap: break-word;
  word-break: break-word;
  hyphens: auto;
}
</style> 