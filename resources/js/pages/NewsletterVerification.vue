<template>
  <div class="min-h-screen bg-gray-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
      <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
        <!-- Loading State -->
        <div v-if="loading" class="text-center">
          <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto"></div>
          <h2 class="mt-4 text-lg font-medium text-gray-900">Weryfikacja adresu email...</h2>
          <p class="mt-2 text-sm text-gray-600">Proszę czekać, trwa weryfikacja Twojego adresu email.</p>
        </div>

        <!-- Success State -->
        <div v-else-if="verified" class="text-center">
          <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100">
            <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
          </div>
          <h2 class="mt-4 text-lg font-medium text-gray-900">Email zweryfikowany!</h2>
          <p class="mt-2 text-sm text-gray-600">{{ message }}</p>
          <div class="mt-6">
            <router-link
              to="/"
              class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
            >
              Wróć do strony głównej
            </router-link>
          </div>
        </div>


        <!-- Error State -->
        <div v-else class="text-center">
          <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
            <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </div>
          <h2 class="mt-4 text-lg font-medium text-gray-900">Błąd weryfikacji</h2>
          <p class="mt-2 text-sm text-gray-600">{{ message }}</p>
          <div class="mt-6 space-y-3">
            <router-link
              to="/"
              class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
            >
              Wróć do strony głównej
            </router-link>
            <button
              @click="retryVerification"
              class="w-full flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
            >
              Spróbuj ponownie
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import newsletterService from '../services/newsletterService';

export default {
  name: 'NewsletterVerification',
  data() {
    return {
      loading: true,
      verified: false,
      message: '',
      token: null
    };
  },
  mounted() {
    try {
      // Check if we have status/message from redirect
      const status = this.$route.query.status;
      const message = this.$route.query.message;
      
      if (status === 'error') {
        this.loading = false;
        this.verified = false;
        this.message = decodeURIComponent(message || 'Wystąpił błąd podczas weryfikacji adresu email.');
        return;
      }
      
      if (message) {
        this.loading = false;
        this.verified = true;
        this.message = decodeURIComponent(message);
        return;
      }
      
      // Fallback to token verification
      this.token = this.$route.query.token;
      
      if (!this.token) {
        this.loading = false;
        this.message = 'Brak tokenu weryfikacyjnego w linku.';
        return;
      }
      
      // Only try API if no redirect params
      this.verifyEmail();
    } catch (error) {
      this.loading = false;
      this.message = 'Błąd inicjalizacji komponentu.';
    }
  },
  methods: {
    async verifyEmail() {
      try {
        this.loading = true;
        const response = await newsletterService.verify(this.token);
        
        if (response && response.success) {
          this.verified = true;
          this.message = response.message || 'Email został pomyślnie zweryfikowany!';
        } else {
          this.verified = false;
          this.message = (response && response.message) || 'Wystąpił błąd podczas weryfikacji adresu email.';
        }
      } catch (error) {
        this.verified = false;
        
        // Handle API error response
        if (error.response && error.response.data) {
          this.message = error.response.data.message || 'Wystąpił błąd podczas weryfikacji adresu email.';
        } else {
          this.message = 'Wystąpił błąd podczas weryfikacji adresu email.';
        }
      } finally {
        this.loading = false;
      }
    },
    
    async retryVerification() {
      await this.verifyEmail();
    }
  }
};
</script> 