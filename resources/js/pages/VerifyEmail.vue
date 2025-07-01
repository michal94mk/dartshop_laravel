<template>
  <div class="min-h-screen bg-gradient-to-br from-indigo-50 via-blue-50 to-purple-50">
    <div class="max-w-md mx-auto py-12 px-4 sm:px-6 lg:px-8">
      <!-- Header -->
      <div class="text-center mb-12">
        <div class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-indigo-100 to-purple-100 rounded-full text-indigo-700 font-semibold text-sm mb-4">
          <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
          Weryfikacja email
        </div>
        <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
          Weryfikacja adresu e-mail
        </h1>
        <p class="text-gray-600 max-w-sm mx-auto">
          Potwierdź swój adres email, aby w pełni korzystać z naszego sklepu
        </p>
      </div>

      <div class="bg-white shadow-xl rounded-2xl overflow-hidden border border-gray-100">
        <div class="p-8">
          <div v-if="verified" class="text-center py-8">
            <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
              <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
            </div>
            <h3 class="text-2xl font-bold text-green-800 mb-3">Email zweryfikowany!</h3>
            <p class="text-green-700 mb-6">Twój adres e-mail został pomyślnie zweryfikowany.</p>
            <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 rounded-lg">
              <div class="flex">
                <div class="flex-shrink-0">
                  <svg class="h-5 w-5 text-green-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                  </svg>
                </div>
                <div class="ml-3">
                  <p class="font-medium">Za chwilę zostaniesz przekierowany do Twojego profilu.</p>
                </div>
              </div>
            </div>
          </div>
        
          <div v-else-if="!authStore.isLoggedIn" class="text-center py-8">
            <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
              <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
              </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-3">Wymagane logowanie</h3>
            <p class="text-gray-600 mb-6">Musisz być zalogowany, aby zweryfikować swój adres e-mail.</p>
            <router-link 
              to="/login" 
              class="inline-flex items-center px-6 py-3 border border-transparent text-sm font-medium rounded-lg text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 shadow-lg hover:shadow-xl"
            >
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
              </svg>
              Zaloguj się
            </router-link>
          </div>
          
          <div v-else-if="authStore.user?.email_verified_at" class="text-center py-8">
            <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
              <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
            </div>
            <h3 class="text-xl font-bold text-green-800 mb-3">Email już zweryfikowany</h3>
            <p class="text-green-700 mb-6">Twój adres e-mail został już zweryfikowany.</p>
            <router-link 
              to="/profile" 
              class="inline-flex items-center px-6 py-3 border border-transparent text-sm font-medium rounded-lg text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 shadow-lg hover:shadow-xl"
            >
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
              </svg>
              Przejdź do profilu
            </router-link>
          </div>
        
          <div v-else class="text-center py-8">
            <div class="w-20 h-20 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-6">
              <svg class="w-10 h-10 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-3">Weryfikacja wymagana</h3>
            <p class="text-gray-600 mb-6">
              Twój adres e-mail nie został jeszcze zweryfikowany. 
              Sprawdź swoją skrzynkę odbiorczą i kliknij link weryfikacyjny.
            </p>
            
            <div v-if="status" class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 rounded-lg mb-6">
              <div class="flex">
                <div class="flex-shrink-0">
                  <svg class="h-5 w-5 text-green-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                  </svg>
                </div>
                <div class="ml-3">
                  <p class="font-medium">{{ status }}</p>
                </div>
              </div>
            </div>
            
            <div v-if="errorMessage" class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-lg mb-6">
              <div class="flex">
                <div class="flex-shrink-0">
                  <svg class="h-5 w-5 text-red-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                  </svg>
                </div>
                <div class="ml-3">
                  <p class="font-medium">{{ errorMessage }}</p>
                </div>
              </div>
            </div>
            
            <button 
              @click="resendVerification" 
              class="inline-flex items-center px-6 py-3 border border-transparent text-sm font-medium rounded-lg text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 shadow-lg hover:shadow-xl disabled:opacity-50 disabled:cursor-not-allowed"
              :disabled="isLoading"
            >
              <template v-if="isLoading">
                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Wysyłanie...
              </template>
              <template v-else>
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                Wyślij ponownie link weryfikacyjny
              </template>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { useAuthStore } from '../stores/authStore';
import axios from 'axios';

export default {
  name: 'VerifyEmailPage',
  
  props: {
    id: String,
    hash: String
  },
  
  setup(props) {
    const verified = ref(false);
    const isLoading = ref(false);
    const status = ref('');
    const errorMessage = ref('');
    const router = useRouter();
    const route = useRoute();
    const authStore = useAuthStore();
    
    onMounted(async () => {
      // Initialize auth state if not yet initialized
      if (!authStore.user) {
        await authStore.initAuth();
      }
      
      // Check if this is a return from verification link
      const id = props.id || route.params.id;
      const hash = props.hash || route.params.hash; // Now hash is a URL parameter, not query
      const expires = route.query.expires;
      const signature = route.query.signature;
      
      if (id && hash && expires && signature) {
        await verifyEmail(id, hash, expires, signature);
      }
    });
    
    const verifyEmail = async (id, hash, expires, signature) => {
      isLoading.value = true;
      errorMessage.value = '';
      
      try {
        const response = await axios.get(`/api/email/verify/${id}/${hash}`, {
          params: {
            expires,
            signature
          }
        });
        
        verified.value = true;
        
        // Refresh user data
        await authStore.initAuth();
        
        // Redirect to profile page after 2 seconds
        setTimeout(() => {
          router.push('/profile');
        }, 2000);
      } catch (error) {
        console.error('Email verification failed:', error);
        errorMessage.value = error.response?.data?.message || 'Nie udało się zweryfikować adresu e-mail.';
      } finally {
        isLoading.value = false;
      }
    };
    
    const resendVerification = async () => {
      isLoading.value = true;
      errorMessage.value = '';
      status.value = '';
      
      try {
        // Get CSRF token
        await axios.get('/sanctum/csrf-cookie');
        
        // Send resend verification request
        const response = await axios.post('/api/email/verification-notification');
        
        status.value = response.data.message || 'Link weryfikacyjny został wysłany ponownie.';
      } catch (error) {
        console.error('Resend verification failed:', error);
        errorMessage.value = error.response?.data?.message || 'Nie udało się wysłać linku weryfikacyjnego.';
      } finally {
        isLoading.value = false;
      }
    };
    
    return {
      verified,
      isLoading,
      status,
      errorMessage,
      authStore,
      resendVerification
    };
  }
};
</script> 