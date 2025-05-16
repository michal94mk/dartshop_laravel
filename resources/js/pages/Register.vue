<template>
  <div class="container mx-auto py-8">
    <div class="max-w-md mx-auto bg-white rounded-lg overflow-hidden shadow-lg">
      <div class="p-6">
        <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">Rejestracja</h2>
        
        <div v-if="authStore.hasError" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
          {{ authStore.errorMessage || 'Błąd rejestracji. Spróbuj ponownie.' }}
        </div>
        
        <form @submit.prevent="handleRegister">
          <div class="mb-4">
            <label for="name" class="block text-gray-700 text-sm font-bold mb-2">Imię i nazwisko</label>
            <input 
              id="name" 
              type="text" 
              v-model="name" 
              required
              class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
            />
          </div>
          
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
            <label for="password" class="block text-gray-700 text-sm font-bold mb-2">Hasło</label>
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
              :disabled="authStore.isLoading"
            >
              <span v-if="authStore.isLoading">Przetwarzanie...</span>
              <span v-else>Zarejestruj się</span>
            </button>
            <router-link 
              to="/login" 
              class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800"
            >
              Masz już konto? Zaloguj się
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
  name: 'RegisterPage',
  
  setup() {
    const name = ref('');
    const email = ref('');
    const password = ref('');
    const passwordConfirmation = ref('');
    const router = useRouter();
    const authStore = useAuthStore();
    
    const handleRegister = async () => {
      try {
        const success = await authStore.register(
          name.value,
          email.value,
          password.value,
          passwordConfirmation.value
        );
        
        if (success) {
          router.push('/').catch(err => {
            if (err.name === 'NavigationDuplicated') {
              return;
            }
            console.error('Navigation error:', err);
            window.location.href = '/';
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