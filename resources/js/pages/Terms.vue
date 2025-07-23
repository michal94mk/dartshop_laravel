<template>
  <div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Loading State -->
      <div v-if="loading" class="text-center py-12">
        <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div>
        <p class="mt-4 text-gray-600">Ładowanie regulaminu...</p>
      </div>

      <!-- Error State -->
      <div v-else-if="error" class="text-center py-12">
        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
          <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.728-.833-2.498 0L3.356 16.5c-.77.833.192 2.5 1.732 2.5z" />
          </svg>
        </div>
        <h3 class="mt-2 text-sm font-medium text-gray-900">Błąd ładowania</h3>
        <p class="mt-1 text-sm text-gray-500">{{ error }}</p>
        <div class="mt-6">
          <button @click="fetchTermsOfService" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            Spróbuj ponownie
          </button>
        </div>
      </div>

      <!-- Terms of Service Content -->
      <div v-else-if="termsOfService" class="bg-white shadow-sm rounded-lg">
        <!-- Header -->
        <div class="px-6 py-8 border-b border-gray-200 bg-gradient-to-r from-blue-600 to-indigo-600">
          <div class="text-center">
            <h1 class="text-3xl font-bold text-white">{{ termsOfService.title }}</h1>
            <div class="mt-4 flex justify-center items-center space-x-6 text-blue-100">
              <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <span class="text-sm">Wersja {{ termsOfService.version }}</span>
              </div>
              <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a4 4 0 118 0v4m-4 6v6m-7 0h14a2 2 0 002-2v-8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                </svg>
                <span class="text-sm">Obowiązuje od {{ formatDate(termsOfService.effective_date) }}</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Content -->
        <div class="px-6 py-8">
          <div class="prose prose-lg max-w-none">
            <div v-html="termsOfService.content" class="terms-content"></div>
          </div>
        </div>

        <!-- Footer Actions -->
        <div v-if="authStore.isLoggedIn && !authStore.user?.terms_of_service_accepted" class="px-6 py-6 bg-gray-50 border-t border-gray-200">
          <div class="flex items-center justify-between">
            <div class="flex items-start">
              <div class="flex items-center h-5">
                <input 
                  id="accept_terms" 
                  type="checkbox" 
                  v-model="acceptTerms" 
                  class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded"
                />
              </div>
              <div class="ml-3 text-sm">
                <label for="accept_terms" class="font-medium text-gray-700">
                  Akceptuję niniejszy regulamin
                </label>
                <p class="text-gray-500">Zaakceptowanie jest wymagane do korzystania z serwisu</p>
              </div>
            </div>
            <button 
              @click="submitAcceptance"
              :disabled="!acceptTerms || submitting"
              class="ml-6 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <svg v-if="submitting" class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              {{ submitting ? 'Zapisywanie...' : 'Akceptuj' }}
            </button>
          </div>
        </div>

        <!-- Success Message for Logged Users -->
        <div v-else-if="authStore.isLoggedIn && authStore.user?.terms_of_service_accepted" class="px-6 py-4 bg-green-50 border-t border-green-200">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <svg class="h-5 w-5 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
            <div class="ml-3">
              <p class="text-sm font-medium text-green-800">
                Zaakceptowałeś już regulamin dnia {{ formatDate(authStore.user.terms_of_service_accepted_at) }}
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue'
import { useAuthStore } from '../stores/authStore'
import axios from 'axios'
import { useToast } from 'vue-toastification'

export default {
  name: 'TermsPage',
  
  setup() {
    const authStore = useAuthStore()
    const toast = useToast()
    
    const loading = ref(true)
    const error = ref(null)
    const termsOfService = ref(null)
    const acceptTerms = ref(false)
    const submitting = ref(false)
    
    const fetchTermsOfService = async () => {
      try {
        loading.value = true
        error.value = null
        
        const response = await axios.get('/api/terms-of-service')
        termsOfService.value = response.data.data
      } catch (err) {
        error.value = 'Nie udało się załadować regulaminu'
        console.error('Error fetching terms of service:', err)
      } finally {
        loading.value = false
      }
    }
    
    const submitAcceptance = async () => {
      try {
        submitting.value = true
        
        const response = await axios.post('/api/terms-of-service/accept')
        
        // Update user data in store
        authStore.user.terms_of_service_accepted = true
        authStore.user.terms_of_service_accepted_at = new Date().toISOString()
        authStore.saveUserToLocalStorage()
        
        toast.success('Regulamin został zaakceptowany')
      } catch (err) {
        toast.error('Nie udało się zaakceptować regulaminu')
        console.error('Error accepting terms of service:', err)
      } finally {
        submitting.value = false
      }
    }
    
    const formatDate = (dateString) => {
      if (!dateString) return ''
      const date = new Date(dateString)
      return date.toLocaleDateString('pl-PL', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
      })
    }
    
    onMounted(() => {
      fetchTermsOfService()
    })
    
    return {
      authStore,
      loading,
      error,
      termsOfService,
      acceptTerms,
      submitting,
      fetchTermsOfService,
      submitAcceptance,
      formatDate
    }
  }
}
</script>

<style scoped>
/* Style dla treści regulaminu */
:deep(.terms-content) {
  line-height: 1.8;
}

:deep(.terms-content h2) {
  @apply text-xl font-semibold text-gray-900 mt-8 mb-4 first:mt-0;
}

:deep(.terms-content h3) {
  @apply text-lg font-medium text-gray-800 mt-6 mb-3;
}

:deep(.terms-content p) {
  @apply mb-4 text-gray-700;
}

:deep(.terms-content ul) {
  @apply mb-4 pl-6 space-y-2;
}

:deep(.terms-content li) {
  @apply text-gray-700;
}

:deep(.terms-content strong) {
  @apply font-semibold text-gray-900;
}
</style> 