<template>
  <div class="container mx-auto py-8">
    <div class="max-w-md mx-auto bg-white rounded-lg overflow-hidden shadow-lg">
      <div class="p-6">
        <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">Resetowanie hasła</h2>
        
        <div v-if="status" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
          {{ status }}
        </div>
        
        <div v-if="errorMessage" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
          {{ errorMessage }}
        </div>
        
        <form @submit.prevent="handleSubmit">
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
          
          <div class="flex items-center justify-between">
            <button 
              type="submit" 
              class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
              :disabled="isLoading"
            >
              <span v-if="isLoading">Wysyłanie...</span>
              <span v-else>Wyślij link resetujący</span>
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
import { ref } from 'vue';
import axios from 'axios';

export default {
  name: 'ForgotPasswordPage',
  
  setup() {
    const email = ref('');
    const isLoading = ref(false);
    const status = ref('');
    const errorMessage = ref('');
    
    const handleSubmit = async () => {
      isLoading.value = true;
      errorMessage.value = '';
      status.value = '';
      
      try {
        // Uzyskaj CSRF token
        await axios.get('/sanctum/csrf-cookie');
        
        // Wyślij żądanie resetowania hasła
        const response = await axios.post('/api/forgot-password', {
          email: email.value
        });
        
        status.value = response.data.message || 'Link do resetowania hasła został wysłany na twój adres email.';
        email.value = '';
      } catch (error) {
        console.error('Password reset request failed:', error);
        errorMessage.value = error.response?.data?.message || 'Nie udało się wysłać linku resetującego hasło.';
      } finally {
        isLoading.value = false;
      }
    };
    
    return {
      email,
      isLoading,
      status,
      errorMessage,
      handleSubmit
    };
  }
};
</script> 