<template>
  <div class="min-h-screen bg-gradient-to-br from-indigo-50 via-white to-purple-50 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
      <!-- Header -->
      <div class="text-center">
        <div class="mx-auto h-16 w-16 bg-red-100 rounded-full flex items-center justify-center mb-6">
          <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        </div>
        <h2 class="text-3xl font-bold text-gray-900 mb-2">Wypisz się z newslettera</h2>
        <p class="text-gray-600">Czy na pewno chcesz przestać otrzymywać nasze wiadomości?</p>
      </div>

      <!-- Unsubscribe Form -->
      <div class="bg-white rounded-xl shadow-xl p-8 border border-gray-200">
        <form @submit.prevent="unsubscribe" class="space-y-6">
          <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
              Adres email
            </label>
            <input
              id="email"
              v-model="email"
              type="email"
              required
              :disabled="loading"
              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 disabled:opacity-50 disabled:bg-gray-100 transition-all duration-200"
              :class="{ 'border-red-500': errorMessage }"
              placeholder="Wprowadź swój adres email"
            />
            <p v-if="errorMessage" class="mt-2 text-sm text-red-600">{{ errorMessage }}</p>
          </div>

          <button
            type="submit"
            :disabled="loading || !email || !isValidEmail"
            class="w-full bg-red-600 text-white font-semibold py-3 px-6 rounded-lg hover:bg-red-700 focus:ring-2 focus:ring-red-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200 flex items-center justify-center"
          >
            <span v-if="loading" class="flex items-center">
              <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              Wypisywanie...
            </span>
            <span v-else class="flex items-center">
              <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
              Wypisz się z newslettera
            </span>
          </button>
        </form>

        <!-- Info -->
        <div class="mt-6 p-4 bg-blue-50 rounded-lg">
          <div class="flex">
            <div class="flex-shrink-0">
              <svg class="h-5 w-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </div>
            <div class="ml-3">
              <p class="text-sm text-blue-700">
                Po wypisaniu się nie będziesz już otrzymywać naszych newsletterów. Możesz się ponownie zapisać w każdej chwili.
              </p>
            </div>
          </div>
        </div>
      </div>

      <!-- Back to Home -->
      <div class="text-center">
        <router-link 
          to="/" 
          class="text-indigo-600 hover:text-indigo-500 font-medium transition-colors duration-200"
        >
          ← Wróć do strony głównej
        </router-link>
      </div>
    </div>
  </div>
</template>

<script>
import newsletterService from '../services/newsletterService.ts';
import { useToast } from "vue-toastification";

export default {
  name: 'NewsletterUnsubscribe',
  setup() {
    const toast = useToast();
    return { toast };
  },
  data() {
    return {
      email: '',
      loading: false,
      errorMessage: ''
    };
  },
  computed: {
    isValidEmail() {
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      return this.email ? emailRegex.test(this.email) : true;
    }
  },
  methods: {
    async unsubscribe() {
      if (!this.email || !this.isValidEmail) {
        this.errorMessage = 'Proszę wprowadzić prawidłowy adres email';
        return;
      }

      this.loading = true;
      this.errorMessage = '';

      try {
        const response = await newsletterService.unsubscribe(this.email);
        
        if (response.success) {
          const message = response.data?.message || response.message || 'Zostałeś pomyślnie wypisany z newslettera';
          this.toast.success(message, {
            position: "top-center",
            timeout: 6000,
            closeOnClick: true,
            pauseOnFocusLoss: true,
            pauseOnHover: true,
            draggable: true,
            draggablePercent: 0.6,
            showCloseButtonOnHover: false,
            hideProgressBar: false,
            closeButton: "button",
            icon: "✅",
            rtl: false
          });
          
          this.email = '';
          
          // Redirect to home after a short delay
          setTimeout(() => {
            this.$router.push('/');
          }, 2000);
        } else {
          const message = response.data?.message || response.message || 'Wystąpił błąd podczas wypisywania z newslettera';
          this.errorMessage = message;
        }
      } catch (error) {
        console.error('Newsletter unsubscribe error:', error);
        
        if (error.response) {
          if (error.response.status === 404) {
            this.errorMessage = 'Nie znaleziono aktywnej subskrypcji dla tego adresu email';
          } else if (error.response.data && error.response.data.message) {
            this.errorMessage = error.response.data.message;
          } else {
            this.errorMessage = 'Wystąpił błąd podczas wypisywania z newslettera';
          }
        } else {
          this.errorMessage = 'Błąd połączenia. Sprawdź swoje połączenie internetowe i spróbuj ponownie.';
        }
      } finally {
        this.loading = false;
      }
    }
  }
};
</script> 