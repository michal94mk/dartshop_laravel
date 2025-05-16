<template>
  <div class="container mx-auto py-8">
    <div class="max-w-md mx-auto bg-white rounded-lg overflow-hidden shadow-lg">
      <div class="p-6">
        <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">Ustaw nowe hasło</h2>
        
        <div v-if="status" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
          {{ status }}
        </div>
        
        <div v-if="errorMessage" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
          {{ errorMessage }}
        </div>
        
        <form @submit.prevent="handleSubmit">
          <input type="hidden" v-model="token">
          
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
          
          <div class="mb-4">
            <label for="password" class="block text-gray-700 text-sm font-bold mb-2">Nowe hasło</label>
            <input 
              id="password" 
              type="password" 
              v-model="password" 
              required
              class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
            />
          </div>
          
          <div class="mb-6">
            <label for="password_confirmation" class="block text-gray-700 text-sm font-bold mb-2">Powtórz hasło</label>
            <input 
              id="password_confirmation" 
              type="password" 
              v-model="passwordConfirmation" 
              required
              class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
            />
          </div>
          
          <div class="flex items-center justify-between">
            <button 
              type="submit" 
              class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
              :disabled="isLoading"
            >
              <span v-if="isLoading">Przetwarzanie...</span>
              <span v-else>Zresetuj hasło</span>
            </button>
            <router-link 
              to="/login" 
              class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800"
            >
              Powrót do logowania
            </router-link>
          </div>
        </form>
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
    const status = ref('');
    const errorMessage = ref('');
    const router = useRouter();
    const route = useRoute();
    
    onMounted(() => {
      // Pobierz token z parametrów URL
      token.value = route.params.token || '';
      email.value = route.query.email || '';
    });
    
    const handleSubmit = async () => {
      isLoading.value = true;
      errorMessage.value = '';
      status.value = '';
      
      try {
        // Uzyskaj CSRF token
        await axios.get('/sanctum/csrf-cookie');
        
        // Wyślij żądanie resetowania hasła
        const response = await axios.post('/api/reset-password', {
          token: token.value,
          email: email.value,
          password: password.value,
          password_confirmation: passwordConfirmation.value
        });
        
        status.value = response.data.message || 'Hasło zostało pomyślnie zresetowane.';
        
        // Przekieruj do strony logowania po 2 sekundach
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
      status,
      errorMessage,
      handleSubmit
    };
  }
};
</script> 