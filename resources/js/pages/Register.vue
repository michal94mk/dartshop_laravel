<template>
  <div class="min-h-screen bg-gradient-to-br from-indigo-50 via-blue-50 to-purple-50 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
      <!-- Header -->
      <div class="text-center">
        <div class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-indigo-100 to-purple-100 rounded-full text-indigo-700 font-semibold text-sm mb-4">
          <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
          </svg>
          Utwórz konto
        </div>
        <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-2">
          Dołącz do nas!
        </h2>
        <p class="text-gray-600">
          Utwórz konto, aby rozpocząć zakupy w naszym sklepie
        </p>
      </div>

      <!-- Register Form Card -->
      <div class="bg-white shadow-xl rounded-2xl overflow-hidden border border-gray-100">
        <div class="px-8 py-8">
          <div v-if="authStore.hasError" class="mb-6 bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-md">
            <div class="flex">
              <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-red-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                </svg>
              </div>
              <div class="ml-3">
                <p class="font-medium">{{ authStore.errorMessage || 'Błąd rejestracji. Spróbuj ponownie.' }}</p>
              </div>
            </div>
          </div>
          
          <form @submit.prevent="handleRegister" class="space-y-6">
            <div>
              <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">Imię i Nazwisko</label>
              <input 
                id="name" 
                type="text" 
                v-model="name" 
                required
                class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200"
                placeholder="Jan Kowalski"
              />
            </div>
            
            <div>
              <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Adres email</label>
              <input 
                id="email" 
                type="email" 
                v-model="email" 
                required
                class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200"
                placeholder="twoj@email.com"
              />
            </div>
            
            <div>
              <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">Hasło</label>
              <input 
                id="password" 
                type="password" 
                v-model="password" 
                required
                class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200"
                placeholder="Minimum 8 znaków"
              />
              <p class="mt-1 text-xs text-gray-500">Hasło musi zawierać co najmniej 8 znaków</p>
            </div>
            
            <div>
              <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-2">Potwierdź hasło</label>
              <input 
                id="password_confirmation" 
                type="password" 
                v-model="passwordConfirmation" 
                required
                class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200"
                placeholder="Powtórz hasło"
              />
            </div>
            
            <div class="space-y-4">
              <button 
                type="submit" 
                class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-semibold py-3 px-4 rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 flex items-center justify-center transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5"
                :disabled="authStore.isLoading"
              >
                <svg v-if="authStore.isLoading" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span v-if="authStore.isLoading">Rejestracja...</span>
                <span v-else>Utwórz konto</span>
              </button>
              
              <div class="relative">
                <div class="absolute inset-0 flex items-center">
                  <div class="w-full border-t border-gray-300"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                  <span class="px-4 bg-white text-gray-500 font-medium">lub</span>
                </div>
              </div>
              
              <div class="grid grid-cols-1 gap-3">
                <button 
                  type="button" 
                  class="w-full flex items-center justify-center px-4 py-3 border border-gray-300 rounded-lg shadow-sm bg-white hover:bg-gray-50 text-gray-700 font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200"
                  disabled
                >
                  <svg class="h-5 w-5 mr-3" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                    <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                    <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                    <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                  </svg>
                  Zarejestruj z Google
                </button>
                
                <button 
                  type="button" 
                  class="w-full flex items-center justify-center px-4 py-3 border border-gray-300 rounded-lg shadow-sm bg-[#1877F2] hover:bg-[#166FE5] text-white font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200"
                  disabled
                >
                  <svg class="h-5 w-5 mr-3" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" fill="currentColor"/>
                  </svg>
                  Zarejestruj z Facebook
                </button>
              </div>
            </div>
          </form>
        </div>
        
        <!-- Footer -->
        <div class="px-8 py-6 bg-gray-50 border-t border-gray-200">
          <div class="text-center">
            <p class="text-gray-600 text-sm">Masz już konto?</p>
            <router-link 
              to="/login" 
              class="inline-block mt-2 font-semibold text-indigo-600 hover:text-indigo-800 transition-colors duration-200"
            >
              Zaloguj się tutaj &rarr;
            </router-link>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../stores/authStore';

export default {
  name: 'RegisterPage',
  
  setup() {
    const name = ref('');
    const email = ref('');
    const password = ref('');
    const passwordConfirmation = ref('');
    const router = useRouter();
    const authStore = useAuthStore();
    
    // Clear error messages when component is mounted
    onMounted(() => {
      authStore.hasError = false;
      authStore.errorMessage = '';
    });
    
    const handleRegister = async () => {
      try {
        // Clear any previous errors
        authStore.hasError = false;
        authStore.errorMessage = '';
        
        const success = await authStore.register(
          name.value,
          email.value,
          password.value,
          passwordConfirmation.value
        );
        
        if (success) {
          router.push('/').catch(err => {
            console.error('Navigation error:', err);
          });
        }
      } catch (error) {
        console.error('Registration error:', error);
      }
    };
    
    return {
      name,
      email,
      password,
      passwordConfirmation,
      authStore,
      handleRegister
    };
  }
};
</script> 