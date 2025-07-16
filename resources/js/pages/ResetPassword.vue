<template>
  <div class="min-h-screen bg-gradient-to-br from-indigo-50 via-blue-50 to-purple-50">
    <div class="max-w-md mx-auto py-12 px-4 sm:px-6 lg:px-8">
      <!-- Header -->
      <div class="text-center mb-12">
        <div class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-indigo-100 to-purple-100 rounded-full text-indigo-700 font-semibold text-sm mb-4">
          <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
          </svg>
          Resetowanie hasła
        </div>
        <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
          Ustaw nowe hasło
        </h1>
        <p class="text-gray-600 max-w-sm mx-auto">
          Wprowadź nowe hasło dla swojego konta
        </p>
      </div>

      <div class="bg-white shadow-xl rounded-2xl overflow-hidden border border-gray-100">
        <div class="p-8">
          <!-- Loading state -->
          <div v-if="isValidating" class="text-center py-8">
            <div class="w-16 h-16 border-4 border-indigo-500 border-t-transparent rounded-full animate-spin mx-auto mb-4"></div>
            <p class="text-gray-600 font-medium">Sprawdzanie linku...</p>
          </div>
          
          <!-- Success message -->
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
          
          <!-- Error message -->
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
          
          <!-- Valid token form -->
          <form v-if="!isValidating && isValidToken" @submit.prevent="handleSubmit" class="space-y-6">
            <input type="hidden" v-model="token">
            
            <div>
              <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                Adres email
              </label>
              <input 
                id="email" 
                type="email" 
                v-model="email" 
                required
                placeholder="Wprowadź swój adres email"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200"
              />
            </div>
            
            <div>
              <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">
                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
                Nowe hasło
              </label>
              <input 
                id="password" 
                type="password" 
                v-model="password" 
                required
                minlength="8"
                placeholder="Wprowadź nowe hasło (min. 8 znaków)"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200"
              />
            </div>
            
            <div>
              <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-2">
                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Powtórz hasło
              </label>
              <input 
                id="password_confirmation" 
                type="password" 
                v-model="passwordConfirmation" 
                required
                minlength="8"
                placeholder="Powtórz nowe hasło"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200"
              />
            </div>
            
            <div class="flex flex-col space-y-4">
              <button 
                type="submit" 
                class="w-full inline-flex justify-center items-center px-6 py-3 border border-transparent text-sm font-medium rounded-lg text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 shadow-lg hover:shadow-xl disabled:opacity-50 disabled:cursor-not-allowed"
                :disabled="isLoading"
              >
                <template v-if="isLoading">
                  <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  Przetwarzanie...
                </template>
                <template v-else>
                  <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                  </svg>
                  Zresetuj hasło
                </template>
              </button>
              
              <div class="text-center">
                <router-link 
                  to="/login" 
                  class="inline-flex items-center text-sm font-medium text-indigo-600 hover:text-indigo-700 transition-colors duration-200"
                >
                  <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                  </svg>
                  Powrót do logowania
                </router-link>
              </div>
            </div>
          </form>
          
          <!-- Invalid token message -->
          <div v-if="!isValidating && !isValidToken" class="text-center py-8">
            <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-6">
              <svg class="w-10 h-10 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-3">Nieprawidłowy lub wygasły link</h3>
            <p class="text-gray-600 mb-6">Link do resetowania hasła jest nieprawidłowy lub wygasł. Wyślij nowy link.</p>
            <router-link 
              to="/forgot-password" 
              class="inline-flex items-center px-6 py-3 border border-transparent text-sm font-medium rounded-lg text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 shadow-lg hover:shadow-xl"
            >
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
              </svg>
              Wyślij nowy link
            </router-link>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import axios from 'axios';

export default {
  name: 'ResetPasswordPage',
  
  setup() {
    const email = ref('');
    const password = ref('');
    const passwordConfirmation = ref('');
    const token = ref('');
    const isLoading = ref(false);
    const isValidating = ref(true);
    const isValidToken = ref(false);
    const status = ref('');
    const errorMessage = ref('');
    const router = useRouter();
    const route = useRoute();
    
    onMounted(async () => {
      // Get token from URL parameters
      token.value = route.params.token || '';
      email.value = route.query.email || '';
      
      // Validate token
      await validateToken();
    });
    
    const validateToken = async () => {
      if (!token.value) {
        isValidToken.value = false;
        isValidating.value = false;
        return;
      }
      
      try {
        // Get CSRF token
        await axios.get('/sanctum/csrf-cookie');
        
        // Validate token by attempting to get user info
        const response = await axios.post('/api/validate-reset-token', {
          token: token.value,
          email: email.value
        });
        
        isValidToken.value = true;
        email.value = response.data.email || email.value;
      } catch (error) {
        console.error('Token validation failed:', error);
        isValidToken.value = false;
        errorMessage.value = 'Link do resetowania hasła jest nieprawidłowy lub wygasł.';
      } finally {
        isValidating.value = false;
      }
    };
    
    const handleSubmit = async () => {
      if (password.value !== passwordConfirmation.value) {
        errorMessage.value = 'Hasła nie są identyczne.';
        return;
      }
      
      if (password.value.length < 8) {
        errorMessage.value = 'Hasło musi mieć co najmniej 8 znaków.';
        return;
      }
      
      isLoading.value = true;
      errorMessage.value = '';
      status.value = '';
      
      try {
        // Get CSRF token
        await axios.get('/sanctum/csrf-cookie');
        
        // Send password reset request
        const response = await axios.post('/api/reset-password', {
          token: token.value,
          email: email.value,
          password: password.value,
          password_confirmation: passwordConfirmation.value
        });
        
        status.value = response.data.message || 'Hasło zostało pomyślnie zresetowane.';
        
        // Redirect to login page after 2 seconds
        setTimeout(() => {
          router.push('/login');
        }, 2000);
      } catch (error) {
        console.error('Password reset failed:', error);
        errorMessage.value = error.response?.data?.message || 'Nie udało się zresetować hasła.';
      } finally {
        isLoading.value = false;
      }
    };
    
    return {
      email,
      password,
      passwordConfirmation,
      token,
      isLoading,
      isValidating,
      isValidToken,
      status,
      errorMessage,
      handleSubmit
    };
  }
};
</script> 