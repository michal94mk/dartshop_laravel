<template>
  <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-xl p-4 shadow-xl border border-indigo-500/20">
    <div class="max-w-md mx-auto text-center">
      <div class="mb-3">
        <div class="w-12 h-12 bg-white/10 rounded-full flex items-center justify-center mx-auto mb-2 backdrop-blur-sm">
          <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
          </svg>
        </div>
      </div>
      
      <h3 class="text-lg font-bold text-white mb-2">Newsletter DartShop</h3>
      <p class="text-indigo-100 mb-4 text-sm leading-relaxed">
        Zapisz się do naszego newslettera i bądź na bieżąco z najnowszymi produktami, promocjami i poradnikami dotyczącymi gry w dart!
      </p>
      
      <form @submit.prevent="subscribe" class="space-y-3">
        <div class="relative">
          <input
            v-model="email"
            type="email"
            placeholder="Twój adres email"
            required
            :disabled="loading"
            class="w-full px-4 py-3 text-gray-900 bg-white/95 backdrop-blur-sm border-2 border-white/20 rounded-lg focus:ring-2 focus:ring-white/30 focus:border-white focus:outline-none disabled:opacity-50 disabled:bg-gray-200 transition-all duration-200 text-center font-medium placeholder-gray-500 shadow-lg"
            :class="{ 'border-red-300 focus:border-red-400 focus:ring-red-400/30': localAlert && localAlert.class.includes('error') }"
          />
          <div v-if="loading" class="absolute right-3 top-1/2 transform -translate-y-1/2">
            <svg class="animate-spin h-4 w-4 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
          </div>
        </div>
        
        <button
          type="submit"
          :disabled="loading || !email || !isValidEmail"
          class="w-full bg-white text-indigo-600 font-semibold py-3 px-6 rounded-lg hover:bg-indigo-50 focus:ring-2 focus:ring-white/50 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200 transform hover:scale-105 disabled:hover:scale-100 shadow-lg backdrop-blur-sm"
        >
          <span v-if="loading" class="flex items-center justify-center">
            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Zapisywanie...
          </span>
          <span v-else class="flex items-center justify-center">
            <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Zapisz się do newslettera
          </span>
        </button>
      </form>
      
      <!-- Local Alert -->
      <div v-if="localAlert" class="mt-4 p-4 rounded-lg text-sm font-medium transition-all duration-300" :class="localAlert.class">
        {{ localAlert.message }}
      </div>
      
      <!-- Privacy Notice -->
      <p class="mt-3 text-xs text-indigo-200 leading-relaxed">
        Zapisując się do newslettera akceptujesz naszą 
        <router-link to="/privacy" class="text-white hover:text-indigo-100 underline font-medium">politykę prywatności</router-link>. 
        Możesz się wypisać w każdej chwili.
        <router-link to="/newsletter/unsubscribe" class="text-white hover:text-indigo-100 underline font-medium ml-1">
          Wypisz się
        </router-link>
      </p>
    </div>
  </div>
</template>

<script>
import newsletterService from '../../services/newsletterService.ts';

export default {
  name: 'NewsletterSubscription',
  mounted() {
    console.log('NewsletterSubscription component mounted successfully!');
    console.log('Initial data:', this.$data);
    console.log('Newsletter service available:', typeof newsletterService);
  },
  data() {
    return {
      email: '',
      loading: false,
      localAlert: null
    };
  },
  computed: {
    isValidEmail() {
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      return this.email ? emailRegex.test(this.email) : true;
    }
  },
    methods: {
    async subscribe() {
      console.log('Subscribe method called with email:', this.email);
      console.log('Email validation:', { isValidEmail: this.isValidEmail, email: this.email });
      
            // Prevent multiple submissions
      if (this.loading) {
        return;
      }
      
              if (!this.email || !this.isValidEmail) {
          this.showLocalAlert('❌ Proszę wprowadzić prawidłowy adres email', 'error', 4000);
          console.error('Email validation failed');
          return;
        }
      
      this.loading = true;
      
      try {
        console.log('Attempting to subscribe with email:', this.email);
        const response = await newsletterService.subscribe(this.email);
        console.log('Newsletter subscription response:', response);
        
        if (response.success) {
          // Success alert with same style as login alerts
          console.log('Showing success alert with timeout:', 6000);
          const message = response.data?.message || response.message || 'Zapisano do newslettera!';
          this.showLocalAlert(`🎯 ${message}`, 'success', 6000);
          
          this.email = '';
          
          // Additional celebration effect
          this.celebrateSuccess();
        } else {
          console.log('Showing warning alert with timeout:', 5000);
          const message = response.data?.message || response.message || 'Wystąpił błąd podczas zapisywania do newslettera';
          this.showLocalAlert(message, 'warning', 5000);
        }
      } catch (error) {
        console.error('Newsletter subscription error:', error);
        
                // Handle different types of errors
        if (error.response) {
          console.error('Error response:', error.response.data);
          if (error.response.status === 422 && error.response.data.errors) {
            const firstError = Object.values(error.response.data.errors)[0];
            const errorMessage = Array.isArray(firstError) ? firstError[0] : firstError;
            this.showLocalAlert(`❌ ${errorMessage}`, 'error', 5000);
          } else if (error.response.data.message) {
            this.showLocalAlert(`❌ ${error.response.data.message}`, 'error', 5000);
          } else {
            this.showLocalAlert('❌ Wystąpił błąd podczas zapisywania do newslettera', 'error', 5000);
          }
        } else if (error.request) {
          console.error('Network error:', error.request);
          this.showLocalAlert('🌐 Błąd połączenia. Sprawdź swoje połączenie internetowe i spróbuj ponownie.', 'error', 5000);
        } else {
          console.error('Error message:', error.message);
          this.showLocalAlert('❌ Wystąpił nieoczekiwany błąd. Spróbuj ponownie.', 'error', 5000);
        }
      } finally {
        this.loading = false;
      }
    },
    
    celebrateSuccess() {
      // Add a simple celebration effect
      this.$el.style.transform = 'scale(1.02)';
      setTimeout(() => {
        this.$el.style.transform = 'scale(1)';
      }, 300);
    },
    
    showLocalAlert(message, type = 'success', timeout = 5000) {
      // Clear any existing alert
      this.localAlert = null;
      
      // Set alert classes based on type
      const alertClasses = {
        success: 'bg-green-50 border border-green-200 text-green-800 shadow-sm',
        error: 'bg-red-50 border border-red-200 text-red-800 shadow-sm',
        warning: 'bg-yellow-50 border border-yellow-200 text-yellow-800 shadow-sm'
      };
      
      // Show new alert
      this.localAlert = {
        message,
        class: alertClasses[type] || alertClasses.success
      };
      
      // Auto-hide after timeout
      if (timeout > 0) {
        setTimeout(() => {
          this.localAlert = null;
        }, timeout);
      }
    }
  }
};
</script>

<style scoped>
/* Enhanced backdrop blur for better glass effect */
.backdrop-blur-sm {
  backdrop-filter: blur(4px);
}

/* Enhanced shadow and glow effects */
.shadow-2xl {
  box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25), 0 0 0 1px rgba(255, 255, 255, 0.1);
}

/* Smooth transitions for all interactive elements */
* {
  transition: all 0.2s ease;
}

/* Enhanced hover effects */
button:hover:not(:disabled) {
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15), 0 0 20px rgba(255, 255, 255, 0.2);
}

input:focus {
  box-shadow: 0 0 0 4px rgba(255, 255, 255, 0.2), 0 10px 20px rgba(0, 0, 0, 0.1);
}
</style> 