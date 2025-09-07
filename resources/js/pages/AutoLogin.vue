<template>
  <div class="min-h-screen bg-gradient-to-br from-indigo-50 via-blue-50 to-purple-50 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full text-center">
      <div class="bg-white shadow-xl rounded-2xl overflow-hidden border border-gray-100 p-8">
        <div v-if="isLoading" class="space-y-4">
          <div class="w-16 h-16 border-4 border-indigo-500 border-t-transparent rounded-full animate-spin mx-auto"></div>
          <h2 class="text-xl font-bold text-gray-900">Logowanie...</h2>
          <p class="text-gray-600">Weryfikujemy Twój email i logujemy Cię automatycznie.</p>
        </div>
        
        <div v-else-if="hasError" class="space-y-4">
          <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto">
            <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
          </div>
          <h2 class="text-xl font-bold text-gray-900">Błąd logowania</h2>
          <p class="text-gray-600">{{ errorMessage }}</p>
          <router-link 
            to="/login"
            class="inline-flex items-center px-6 py-3 border border-transparent text-sm font-medium rounded-lg text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 shadow-lg hover:shadow-xl"
          >
            Przejdź do logowania
          </router-link>
        </div>

        <div v-else class="space-y-4">
          <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto">
            <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
          </div>
          <h2 class="text-xl font-bold text-gray-900">Zalogowano pomyślnie!</h2>
          <p class="text-gray-600">Przekierowywanie do profilu...</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useAuthStore } from '../stores/authStore';
import { useAlertStore } from '../stores/alertStore';
import axios from 'axios';

export default {
  name: 'AutoLogin',
  
  setup() {
    const isLoading = ref(true);
    const hasError = ref(false);
    const errorMessage = ref('');
    
    const route = useRoute();
    const router = useRouter();
    const authStore = useAuthStore();
    const alertStore = useAlertStore();
    
    const autoLogin = async () => {
      try {
        const token = route.query.token;
        const verified = route.query.verified;
        
        if (!token) {
          throw new Error('Brak tokenu autoryzacji');
        }
        
        console.log('Auto-login with token:', token.substring(0, 10) + '...');
        
        // Get user data with the token (set Authorization only for this request)
        const response = await axios.get('/api/user', {
          headers: {
            'Authorization': `Bearer ${token}`
          }
        });
        
        if (response.data) {
          // Update auth store
          authStore.user = response.data;
          if (response.data.permissions) {
            authStore.permissions = response.data.permissions;
          }
          
          // Save to localStorage
          authStore.saveUserToLocalStorage();
          authStore.authInitialized = true;
          
          console.log('Auto-login successful:', authStore.user.email);
          
          // Show success message if email was verified
          if (verified === 'success') {
            alertStore.success('🎉 Email został pomyślnie zweryfikowany!');
          }
          
          // Redirect to profile
          setTimeout(() => {
            router.push('/profile');
          }, 1000);
          
        } else {
          throw new Error('Nie udało się pobrać danych użytkownika');
        }
        
      } catch (error) {
        console.error('Auto-login failed:', error);
        hasError.value = true;
        errorMessage.value = error.response?.data?.message || error.message || 'Wystąpił błąd podczas automatycznego logowania';
        
        // Clean up auth headers
        delete axios.defaults.headers.common['Authorization'];
      } finally {
        isLoading.value = false;
      }
    };
    
    onMounted(() => {
      autoLogin();
    });
    
    return {
      isLoading,
      hasError,
      errorMessage
    };
  }
};
</script> 