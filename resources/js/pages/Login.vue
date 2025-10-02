<template>
  <div class="min-h-screen bg-gradient-to-br from-indigo-50 via-blue-50 to-purple-50 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
      <!-- Header -->
      <div class="text-center">
        <div class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-indigo-100 to-purple-100 rounded-full text-indigo-700 font-semibold text-sm mb-4">
          <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
          </svg>
          Zaloguj się
        </div>
        <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-2">
          Witaj ponownie!
        </h2>
        <p class="text-gray-600">
          Zaloguj się do swojego konta, aby kontynuować zakupy
        </p>
      </div>

      <!-- Login Form Card -->
      <div class="bg-white shadow-xl rounded-2xl overflow-hidden border border-gray-100">
        <div class="px-8 py-8">
          <!-- Admin credentials info -->
          <div class="mb-6 bg-blue-50 border-l-4 border-blue-500 text-blue-700 p-4 rounded-md">
            <div class="flex">
              <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                </svg>
              </div>
              <div class="ml-3">
                <div class="text-sm">
                  <p class="font-medium mb-1">Dane logowania dla administratora:</p>
                  <p><strong>Email:</strong> admin@example.com</p>
                  <p><strong>Hasło:</strong> Password123</p>
                </div>
              </div>
            </div>
          </div>

          <div v-if="authStore.hasError" class="mb-6 bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-md">
            <div class="flex">
              <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-red-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                </svg>
              </div>
              <div class="ml-3">
                <p class="font-medium">{{ authStore.errorMessage || 'Błąd logowania. Spróbuj ponownie.' }}</p>
              </div>
            </div>
          </div>
          
          <form @submit.prevent="handleLogin" class="space-y-6">
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
                placeholder="Wprowadź hasło"
              />
              <div class="mt-2 flex items-center justify-between">
                <div class="flex items-center">
                  <input 
                    id="remember" 
                    type="checkbox" 
                    v-model="rememberMe"
                    class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                  />
                  <label for="remember" class="ml-2 block text-sm text-gray-700">
                    Zapamiętaj mnie
                  </label>
                </div>
                <router-link to="/forgot-password" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium transition-colors duration-200">
                  Zapomniałeś hasła?
                </router-link>
              </div>
            </div>
            
            <div class="space-y-4">
              <button 
                type="submit" 
                class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-semibold py-3 px-4 rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 flex items-center justify-center transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5"
                :disabled="authStore.isRegularLoading"
              >
                <svg v-if="authStore.isRegularLoading" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span v-if="authStore.isRegularLoading">Logowanie...</span>
                <span v-else>Zaloguj się</span>
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
                  @click="handleGoogleLogin"
                  :disabled="authStore.isGoogleLoading"
                >
                  <svg v-if="!authStore.isGoogleLoading" class="h-5 w-5 mr-3" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                    <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                    <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                    <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                  </svg>
                  <svg v-else class="animate-spin -ml-1 mr-3 h-5 w-5 text-gray-700" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  <span v-if="authStore.isGoogleLoading">Logowanie...</span>
                  <span v-else>Zaloguj z Google</span>
                </button>
              </div>
            </div>
          </form>
        </div>
        
        <!-- Footer -->
        <div class="px-8 py-6 bg-gray-50 border-t border-gray-200">
          <div class="text-center">
            <p class="text-gray-600 text-sm">Nie masz jeszcze konta?</p>
            <router-link 
              to="/register" 
              class="inline-block mt-2 font-semibold text-indigo-600 hover:text-indigo-800 transition-colors duration-200"
            >
              Zarejestruj się teraz &rarr;
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
import { useAlertStore } from '../stores/alertStore';

export default {
  name: 'LoginPage',
  
  setup() {
    const email = ref('');
    const password = ref('');
    const rememberMe = ref(false);
    const router = useRouter();
    const authStore = useAuthStore();
    const alertStore = useAlertStore();
    
    // Clear error messages when component is mounted
    onMounted(() => {
      authStore.hasError = false;
      authStore.errorMessage = '';
      
      // Check if redirected due to session expiration
      const expired = router.currentRoute.value.query.expired;
      if (expired) {
        authStore.hasError = true;
        authStore.errorMessage = 'Twoja sesja wygasła. Zaloguj się ponownie.';
      }
    });
    
    const handleLogin = async () => {
      try {
        // Clear any previous errors
        authStore.hasError = false;
        authStore.errorMessage = '';
        
        const success = await authStore.login(email.value, password.value, rememberMe.value);
        
        if (success) {
          // Show success message with shorter timeout for quick redirect
          // Show personalized message if user has a proper name, otherwise generic message
          const userName = authStore.userName;
          if (userName && userName !== 'Użytkownik' && userName !== 'Admin' && !userName.includes('@')) {
            alertStore.success(`👋 Witaj ponownie, ${userName}!`, 3000);
          } else {
            alertStore.success(`👋 Witaj ponownie!`, 3000);
          }
          
          // Check if redirect parameter exists
          const redirectPath = router.currentRoute.value.query.redirect || '/';
          const verified = router.currentRoute.value.query.verified;
          
          // If there's a verified parameter, add it to the redirect path
          let finalRedirectPath = redirectPath;
          if (verified && redirectPath.includes('/profile')) {
            const separator = redirectPath.includes('?') ? '&' : '?';
            finalRedirectPath = `${redirectPath}${separator}verified=${verified}`;
          }
          
          router.push(finalRedirectPath).catch(err => {
            // In case of error redirect to home page
            if (err.name === 'NavigationDuplicated') {
              // Ignore navigation duplicates (same URL)
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

    const handleGoogleLogin = async () => {
      try {
        // Save info that this is a login attempt (not registration)
        localStorage.setItem('google_auth_action', 'login');
        
        // authStore.loginWithGoogle() already handles redirect to profile for login page
        await authStore.loginWithGoogle();
        // Don't do router.push here, as Google OAuth redirects the entire page
      } catch (error) {
        console.error('Google login error:', error);
      }
    };
    
    return {
      email,
      password,
      rememberMe,
      authStore,
      handleLogin,
      handleGoogleLogin
    };
  }
};
</script> 