<template>
  <div class="min-h-screen bg-gradient-to-br from-purple-50 via-blue-50 to-indigo-50">
    <!-- Loading indicator -->
    <div v-if="loading" class="flex justify-center items-center min-h-screen">
      <div class="relative">
        <div class="w-20 h-20 border-4 border-purple-200 rounded-full animate-spin border-t-purple-600"></div>
        <div class="absolute inset-0 flex items-center justify-center">
          <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
          </svg>
        </div>
      </div>
    </div>
    
    <!-- No content message -->
    <div v-else-if="!aboutUs" class="min-h-screen flex items-center justify-center">
      <div class="bg-white rounded-2xl shadow-xl p-8 max-w-md mx-auto text-center">
        <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
          <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
          </svg>
        </div>
        <h3 class="text-xl font-semibold text-gray-900 mb-2">Brak treści</h3>
        <p class="text-gray-600">Nie znaleziono informacji. Sprawdź ponownie później.</p>
      </div>
    </div>
    
    <!-- Content -->
    <div v-else>
      <!-- Hero Section -->
      <div class="relative overflow-hidden bg-gradient-to-r from-purple-600 to-indigo-600 py-16">
        <div class="absolute inset-0 bg-black opacity-20"></div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="text-center">
            <h1 class="text-4xl md:text-5xl font-bold text-white mb-4">
              {{ aboutUs.title || 'O nas' }}
            </h1>
            <p class="text-xl text-purple-100 max-w-2xl mx-auto">
              Poznaj naszą historię, wartości i misję, które kierują nami każdego dnia.
            </p>
          </div>
        </div>
      </div>

      <!-- Main Content Section -->
      <div class="py-16 px-4 sm:px-6 lg:px-8">
        <!-- Image and Content Layout -->
        <div v-if="aboutUs.image_position === 'left' || aboutUs.image_position === 'right'" 
             class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center h-full">
          
          <!-- Image Section -->
          <div v-if="aboutUs.image_path" 
               class="relative h-full"
               :class="{ 'lg:col-start-2': aboutUs.image_position === 'right' }">
            <div class="relative overflow-hidden rounded-2xl shadow-2xl h-full">
              <img 
                :src="getImageUrl(aboutUs.image_path)" 
                :alt="aboutUs.title"
                class="w-full h-full object-cover"
              >
              <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent"></div>
            </div>
            <!-- Decorative elements -->
            <div class="absolute -top-4 -right-4 w-24 h-24 bg-gradient-to-r from-purple-400 to-indigo-500 rounded-full opacity-20"></div>
            <div class="absolute -bottom-4 -left-4 w-16 h-16 bg-gradient-to-r from-indigo-400 to-purple-500 rounded-full opacity-30"></div>
          </div>
          
          <!-- Content Section -->
          <div class="h-full" :class="{ 'lg:col-start-1 lg:row-start-1': aboutUs.image_position === 'right' }">
            <div class="bg-white rounded-2xl shadow-xl p-8 border border-gray-100 h-full">
              <div class="prose prose-lg max-w-none h-full" v-html="aboutUs.content"></div>
            </div>
          </div>
        </div>

        <!-- Top/Bottom Image Layout -->
        <div v-else class="space-y-12">
          <!-- Image at the top -->
          <div v-if="aboutUs.image_path && aboutUs.image_position === 'top'" class="relative">
            <div class="relative overflow-hidden rounded-2xl shadow-2xl">
              <img 
                :src="getImageUrl(aboutUs.image_path)" 
                :alt="aboutUs.title"
                class="w-full h-96 object-cover"
              >
              <div class="absolute inset-0 bg-gradient-to-t from-black/30 to-transparent"></div>
            </div>
          </div>
          
          <!-- Content -->
          <div class="bg-white rounded-2xl shadow-xl p-8 lg:p-12 border border-gray-100">
            <div class="prose prose-lg max-w-none" v-html="aboutUs.content"></div>
          </div>
          
          <!-- Image at the bottom -->
          <div v-if="aboutUs.image_path && aboutUs.image_position === 'bottom'" class="relative">
            <div class="relative overflow-hidden rounded-2xl shadow-2xl">
              <img 
                :src="getImageUrl(aboutUs.image_path)" 
                :alt="aboutUs.title"
                class="w-full h-96 object-cover"
              >
              <div class="absolute inset-0 bg-gradient-to-t from-black/30 to-transparent"></div>
            </div>
          </div>
        </div>
      </div>

      <!-- Features Section -->
      <div class="bg-white py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Dlaczego warto nas wybrać?</h2>
            <p class="text-xl text-gray-600 max-w-2xl mx-auto">
              Oto kilka powodów, dla których jesteśmy wyjątkowi
            </p>
          </div>
          
          <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="text-center group">
              <div class="w-16 h-16 bg-gradient-to-r from-purple-400 to-indigo-500 rounded-2xl flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition-transform duration-300">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
              </div>
              <h3 class="text-xl font-semibold text-gray-900 mb-3">Najwyższa jakość</h3>
              <p class="text-gray-600">Oferujemy tylko produkty najwyższej jakości, które przechodzą rygorystyczne testy.</p>
            </div>
            
            <div class="text-center group">
              <div class="w-16 h-16 bg-gradient-to-r from-blue-400 to-purple-500 rounded-2xl flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition-transform duration-300">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
              </div>
              <h3 class="text-xl font-semibold text-gray-900 mb-3">Szybka dostawa</h3>
              <p class="text-gray-600">Gwarantujemy szybką i bezpieczną dostawę Twoich zamówień w całej Polsce.</p>
            </div>
            
            <div class="text-center group">
              <div class="w-16 h-16 bg-gradient-to-r from-green-400 to-blue-500 rounded-2xl flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition-transform duration-300">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                </svg>
              </div>
              <h3 class="text-xl font-semibold text-gray-900 mb-3">Wsparcie klienta</h3>
              <p class="text-gray-600">Nasz zespół ekspertów jest gotowy pomóc Ci w każdej sprawie w godzinach pracy.</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Combined Statistics & CTA Section -->
      <div class="bg-gradient-to-b from-purple-600 via-indigo-600 to-purple-700 py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <!-- Statistics Part -->
          <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">Nasze osiągnięcia w liczbach</h2>
            <p class="text-xl text-purple-100 mb-12">Zaufało nam już tysiące zadowolonych klientów</p>
            
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 mb-16">
              <div class="text-center group">
                <div class="text-4xl md:text-5xl font-bold text-white mb-2 group-hover:scale-110 transition-transform duration-300">10K+</div>
                <div class="text-purple-100">Zadowolonych klientów</div>
              </div>
              <div class="text-center group">
                <div class="text-4xl md:text-5xl font-bold text-white mb-2 group-hover:scale-110 transition-transform duration-300">500+</div>
                <div class="text-purple-100">Produktów w ofercie</div>
              </div>
              <div class="text-center group">
                <div class="text-4xl md:text-5xl font-bold text-white mb-2 group-hover:scale-110 transition-transform duration-300">5+</div>
                <div class="text-purple-100">Lat doświadczenia</div>
              </div>
              <div class="text-center group">
                <div class="text-4xl md:text-5xl font-bold text-white mb-2 group-hover:scale-110 transition-transform duration-300">99%</div>
                <div class="text-purple-100">Pozytywnych opinii</div>
              </div>
            </div>
            
            <!-- Decorative separator -->
            <div class="flex items-center justify-center mb-12">
              <div class="h-px bg-gradient-to-r from-transparent via-white/30 to-transparent w-full max-w-md"></div>
              <div class="mx-4">
                <div class="w-8 h-8 bg-white/20 rounded-full flex items-center justify-center">
                  <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                  </svg>
                </div>
              </div>
              <div class="h-px bg-gradient-to-r from-transparent via-white/30 to-transparent w-full max-w-md"></div>
            </div>
          </div>
          
          <!-- CTA Part -->
          <div class="text-center">
            <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">
              Gotowy na rozpoczęcie przygody z nami?
            </h2>
            <p class="text-xl text-indigo-100 mb-8 max-w-2xl mx-auto">
              Dołącz do tysięcy zadowolonych klientów i odkryj nasze wyjątkowe produkty już dziś.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
              <router-link to="/" 
                           class="inline-flex items-center px-8 py-4 border border-transparent text-base font-semibold rounded-xl text-indigo-600 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-white transition-all duration-200 shadow-lg hover:shadow-xl hover:scale-105">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M8 11v6a2 2 0 002 2h4a2 2 0 002-2v-6M8 11h8"/>
                </svg>
                Przejdź do sklepu
              </router-link>
              <router-link to="/contact" 
                           class="inline-flex items-center px-8 py-4 border-2 border-white text-base font-semibold rounded-xl text-white bg-transparent hover:bg-white hover:text-indigo-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-white transition-all duration-200 hover:scale-105">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 002 2z"/>
                </svg>
                Skontaktuj się z nami
              </router-link>
            </div>
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

<style scoped>
.prose {
  max-width: none;
}

.prose h1,
.prose h2,
.prose h3,
.prose h4,
.prose h5,
.prose h6 {
  color: #374151;
  font-weight: 700;
}

.prose p {
  color: #6b7280;
  line-height: 1.75;
}

.prose img {
  border-radius: 1rem;
  max-width: 100%;
  height: auto;
  box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
}

.prose a {
  color: #7c3aed;
  text-decoration: none;
  font-weight: 500;
}

.prose a:hover {
  color: #5b21b6;
  text-decoration: underline;
}

.break-words {
  overflow-wrap: break-word;
  word-wrap: break-word;
  word-break: break-word;
  hyphens: auto;
}
</style> 