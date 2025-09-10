<template>
  <div class="min-h-screen bg-gradient-to-br from-indigo-50 via-blue-50 to-purple-50 flex items-center justify-center">
    <div class="text-center">
      <div class="inline-flex items-center px-6 py-3 bg-white rounded-full shadow-lg mb-4">
        <svg v-if="!error && !success" class="animate-spin -ml-1 mr-3 h-5 w-5 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <svg v-else-if="success" class="h-5 w-5 mr-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
        </svg>
        <svg v-else class="h-5 w-5 mr-3 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
        </svg>
        <span class="text-sm font-medium text-gray-700">
          {{ getStatusText() }}
        </span>
      </div>
      
      <p v-if="!error && !success" class="text-gray-600 text-sm">
        Please wait, Google authorization in progress
      </p>
      <p v-else-if="success" class="text-green-600 text-sm">
        Login successful! Redirecting...
      </p>
      <p v-else class="text-red-600 text-sm">
        {{ error }}
      </p>
      
      <div v-if="error" class="mt-4">
        <router-link 
          to="/login" 
          class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors"
        >
          Back to login
        </router-link>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../stores/authStore';
import { useCartStore } from '../stores/cartStore';
import axios from 'axios';

export default {
  name: 'GoogleCallback',
  
  setup() {
    const error = ref(null);
    const success = ref(false);
    const router = useRouter();
    const authStore = useAuthStore();
    const cartStore = useCartStore();
    
    const getStatusText = () => {
      if (success.value) return 'Login successful!';
      if (error.value) return 'Login error';
      return 'Verifying Google data...';
    };
    
    onMounted(async () => {
      try {
        console.log('GoogleCallback mounted, current URL:', window.location.href);
        
        // Get parameters from URL
        const urlParams = new URLSearchParams(window.location.search);
        const token = urlParams.get('token');
        const redirect = urlParams.get('redirect');
        const code = urlParams.get('code');
        const errorParam = urlParams.get('error');
        
        console.log('URL params:', { token: token?.substring(0, 20) + '...', redirect, code: code?.substring(0, 20) + '...', error: errorParam });
        
        if (errorParam) {
          throw new Error('Login was cancelled or an error occurred');
        }
        
        // If we have a token, use it directly (new flow)
        if (token) {
          console.log('Token received, setting up authentication...');
          
          // Set token in axios headers
          axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
          
          // Get user data with the token
          const response = await axios.get('/api/user');
          
          if (response.data) {
            console.log('Google auth successful, updating auth store...');
            
            // Update store with user data
            authStore.user = response.data;
            authStore.permissions = response.data.permissions || [];
            authStore.authInitialized = true;
            authStore.hasError = false;
            authStore.errorMessage = '';
            
            // Save data to localStorage
            authStore.saveUserToLocalStorage();
            
            // Sync cart after login
            await cartStore.syncCartAfterLogin();
            
            success.value = true;
            
            // Show success message based on action type
            const { useAlertStore } = await import('../stores/alertStore');
            const alertStore = useAlertStore();
            
            const authAction = localStorage.getItem('google_auth_action') || 'login';
            localStorage.removeItem('google_auth_action');
            
            if (authAction === 'register') {
              alertStore.success(`🎉 Konto utworzone przez Google! Witaj, ${authStore.user.name}!`, 4000);
            } else {
              alertStore.success(`👋 Witaj ponownie, ${authStore.user.name}!`, 3000);
            }
            
            // Get redirect path from localStorage or use profile as default
            const redirectPath = localStorage.getItem('google_auth_redirect') || '/profile';
            localStorage.removeItem('google_auth_redirect');
            
            console.log('User authenticated successfully, redirecting to:', redirectPath);
            console.log('Auth store state:', { 
              isLoggedIn: authStore.isLoggedIn, 
              authInitialized: authStore.authInitialized,
              user: authStore.user?.email 
            });
            
            // Make sure auth store is fully updated before redirecting
            setTimeout(() => {
              console.log('Final auth check before redirect:', {
                isLoggedIn: authStore.isLoggedIn,
                userName: authStore.user?.name
              });
              
              // Use router.push instead of window.location.href
              // Router guard has been fixed to handle Google Callback
              router.push(redirectPath).catch(err => {
                if (err.name === 'NavigationDuplicated') {
                  return;
                }
                console.error('Navigation error:', err);
                // Fallback to window.location if router has issues
                window.location.href = redirectPath;
              });
            }, 1500);
          }
          
        } else if (code) {
          // Old flow - handle with code parameter
          console.log('Sending request to API callback...');
          
          // First get CSRF token
          await axios.get('/sanctum/csrf-cookie');
          console.log('CSRF token acquired');
          
          // Send code to backend
          const response = await axios.get(`/api/auth/google/callback?code=${code}`);
          
          console.log('API Response:', response.data);
          
          if (response.data.success && response.data.data?.user) {
            console.log('Google auth successful, updating auth store...');
            
            // Set token from response if available
            if (response.data.data.token) {
              axios.defaults.headers.common['Authorization'] = `Bearer ${response.data.data.token}`;
            }
            
            // Update store with user data
            authStore.user = response.data.data.user;
            authStore.permissions = response.data.data.user.permissions || [];
            authStore.authInitialized = true;
            authStore.hasError = false;
            authStore.errorMessage = '';
            
            // Save data to localStorage
            authStore.saveUserToLocalStorage();
            
            // Sync cart after login
            await cartStore.syncCartAfterLogin();
            
            success.value = true;
            
            // Show success message based on action type
            const { useAlertStore } = await import('../stores/alertStore');
            const alertStore = useAlertStore();
            
            const authAction = localStorage.getItem('google_auth_action') || 'login';
            localStorage.removeItem('google_auth_action');
            
            if (authAction === 'register') {
              alertStore.success(`🎉 Konto utworzone przez Google! Witaj, ${authStore.user.name}!`, 4000);
            } else {
              alertStore.success(`👋 Witaj ponownie, ${authStore.user.name}!`, 3000);
            }
            
            // Get redirect path from localStorage or use profile as default
            const redirectPath = localStorage.getItem('google_auth_redirect') || '/profile';
            localStorage.removeItem('google_auth_redirect');
            
            console.log('User authenticated successfully, redirecting to:', redirectPath);
            console.log('Auth store state:', { 
              isLoggedIn: authStore.isLoggedIn, 
              authInitialized: authStore.authInitialized,
              user: authStore.user?.email 
            });
            
            // Make sure auth store is fully updated before redirecting
            setTimeout(() => {
              console.log('Final auth check before redirect:', {
                isLoggedIn: authStore.isLoggedIn,
                userName: authStore.user?.name
              });
              
              // Use router.push instead of window.location.href
              // Router guard has been fixed to handle Google Callback
              router.push(redirectPath).catch(err => {
                if (err.name === 'NavigationDuplicated') {
                  return;
                }
                console.error('Navigation error:', err);
                // Fallback to window.location if router has issues
                window.location.href = redirectPath;
              });
            }, 1500);
            
          } else {
            throw new Error(response.data.message || 'Error during login');
          }
        } else {
          throw new Error('Missing authorization code or token from Google');
        }
        
      } catch (err) {
        console.error('Google callback error:', err);
        error.value = err.response?.data?.message || err.message || 'An unexpected error occurred';
      }
    });
    
    return {
      error,
      success,
      getStatusText
    };
  }
};
</script> 