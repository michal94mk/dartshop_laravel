<template>
  <div class="container mx-auto py-8">
    <div class="max-w-md mx-auto bg-white rounded-lg overflow-hidden shadow-lg">
      <div class="p-6">
        <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">Logowanie</h2>
        
        <div v-if="authStore.hasError" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
          {{ authStore.errorMessage || 'Błąd logowania. Spróbuj ponownie.' }}
        </div>
        
        <form @submit.prevent="handleLogin">
          <div class="mb-4">
            <label for="email" class="block text-gray-700 text-sm font-bold mb-2">Email</label>
            <input 
              id="email" 
              type="email" 
              v-model="email" 
              required
              class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
            />
          </div>
          
          <div class="mb-6">
            <label for="password" class="block text-gray-700 text-sm font-bold mb-2">Hasło</label>
            <input 
              id="password" 
              type="password" 
              v-model="password" 
              required
              class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
            />
            <div class="mt-1 text-right">
              <router-link to="/forgot-password" class="text-sm text-blue-500 hover:text-blue-800">
                Zapomniałeś hasła?
              </router-link>
            </div>
          </div>
          
          <div class="flex flex-col space-y-4">
            <button 
              type="submit" 
              class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-4 rounded focus:outline-none focus:shadow-outline flex items-center justify-center transition duration-150"
              :disabled="authStore.isLoading"
            >
              <svg v-if="authStore.isLoading" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              <span v-if="authStore.isLoading">Logowanie...</span>
              <span v-else>Zaloguj się</span>
            </button>
            
            <div class="text-center mt-2 text-gray-600">lub</div>
            
            <div class="flex flex-col space-y-2">
              <button 
                type="button" 
                class="w-full flex items-center justify-center bg-white border border-gray-300 hover:bg-gray-50 text-gray-800 font-medium py-2 px-4 rounded focus:outline-none focus:shadow-outline transition duration-150"
                disabled
              >
                <svg class="h-5 w-5 mr-2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                  <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                  <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                  <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                  <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                </svg>
                Zaloguj z Google
              </button>
              
              <button 
                type="button" 
                class="w-full flex items-center justify-center bg-[#1877F2] hover:bg-[#166FE5] text-white font-medium py-2 px-4 rounded focus:outline-none focus:shadow-outline transition duration-150"
                disabled
              >
                <svg class="h-5 w-5 mr-2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                  <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" fill="currentColor"/>
                </svg>
                Zaloguj z Facebook
              </button>
            </div>
          </div>
          
          <div class="mt-6 text-center">
            <p class="text-gray-600">Nie masz konta?</p>
            <router-link 
              to="/register" 
              class="inline-block mt-2 font-bold text-sm text-indigo-600 hover:text-indigo-800"
            >
              Zarejestruj się teraz
            </router-link>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../stores/authStore';

export default {
  name: 'LoginPage',
  
  setup() {
    const email = ref('');
    const password = ref('');
    const router = useRouter();
    const authStore = useAuthStore();
    
    const handleLogin = async () => {
      try {
        const success = await authStore.login(email.value, password.value);
        
        if (success) {
          // Sprawdź, czy istnieje parametr przekierowania
          const redirectPath = router.currentRoute.value.query.redirect || '/';
          router.push(redirectPath).catch(err => {
            // W przypadku błędu przekieruj na stronę główną
            if (err.name === 'NavigationDuplicated') {
              // Ignoruj duplikaty nawigacji (ten sam URL)
              return;
            }
            console.error('Navigation error:', err);
            router.push('/');
          });
        }
      } catch (error) {
        console.error('Login error:', error);
      }
    };
    
    return {
      email,
      password,
      authStore,
      handleLogin
    };
  }
};
</script> 