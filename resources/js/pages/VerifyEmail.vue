<template>
  <div class="container mx-auto py-8">
    <div class="max-w-md mx-auto bg-white rounded-lg overflow-hidden shadow-lg">
      <div class="p-6">
        <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">Weryfikacja adresu e-mail</h2>
        
        <div v-if="verified" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
          Twój adres e-mail został pomyślnie zweryfikowany.
        </div>
        
        <div v-else-if="!authStore.isLoggedIn" class="text-center py-4">
          <p class="text-gray-600 mb-4">Musisz być zalogowany, aby zweryfikować swój adres e-mail.</p>
          <router-link 
            to="/login" 
            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
          >
            Zaloguj się
          </router-link>
        </div>
        
        <div v-else-if="authStore.user?.email_verified_at" class="text-center py-4">
          <p class="text-gray-600 mb-4">Twój adres e-mail został już zweryfikowany.</p>
          <router-link 
            to="/" 
            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
          >
            Przejdź do strony głównej
          </router-link>
        </div>
        
        <div v-else class="text-center py-4">
          <p class="text-gray-600 mb-4">
            Twój adres e-mail nie został jeszcze zweryfikowany. 
            Sprawdź swoją skrzynkę odbiorczą i kliknij link weryfikacyjny.
          </p>
          
          <div v-if="status" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ status }}
          </div>
          
          <div v-if="errorMessage" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ errorMessage }}
          </div>
          
          <button 
            @click="resendVerification" 
            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline mt-4"
            :disabled="isLoading"
          >
            <span v-if="isLoading">Wysyłanie...</span>
            <span v-else>Wyślij ponownie link weryfikacyjny</span>
          </button>
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
  
  setup() {
    const verified = ref(false);
    const isLoading = ref(false);
    const status = ref('');
    const errorMessage = ref('');
    const router = useRouter();
    const route = useRoute();
    const authStore = useAuthStore();
    
    onMounted(async () => {
      // Inicjalizacja stanu autoryzacji, jeśli jeszcze nie został zainicjalizowany
      if (!authStore.user) {
        await authStore.initAuth();
      }
      
      // Sprawdź, czy jest to powrót z linka weryfikacyjnego
      const id = route.params.id;
      const hash = route.query.hash;
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
        const response = await axios.get(`/api/email/verify/${id}`, {
          params: {
            hash,
            expires,
            signature
          }
        });
        
        verified.value = true;
        
        // Odśwież dane użytkownika
        await authStore.initAuth();
        
        // Przekieruj do strony głównej po 2 sekundach
        setTimeout(() => {
          router.push('/');
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
        // Uzyskaj CSRF token
        await axios.get('/sanctum/csrf-cookie');
        
        // Wyślij żądanie ponownego wysłania weryfikacji
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