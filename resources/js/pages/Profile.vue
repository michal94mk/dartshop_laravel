<template>
  <div class="container mx-auto py-8">
    <div class="max-w-lg mx-auto bg-white rounded-lg overflow-hidden shadow-lg">
      <div class="p-6">
        <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">Twój Profil</h2>
        
        <div v-if="authStore.isLoading" class="text-center py-4">
          <p class="text-gray-600">Ładowanie danych...</p>
        </div>
        
        <div v-else-if="!authStore.isLoggedIn" class="text-center py-4">
          <p class="text-gray-600 mb-4">Nie jesteś zalogowany.</p>
          <router-link 
            to="/login" 
            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
          >
            Zaloguj się
          </router-link>
        </div>
        
        <div v-else>
          <!-- Dane profilu -->
          <div v-if="!showChangePassword">
            <div class="flex items-center justify-center mb-6">
              <div class="w-20 h-20 rounded-full bg-blue-500 flex items-center justify-center text-white text-2xl font-bold">
                {{ authStore.userInitial }}
              </div>
            </div>
            
            <div class="mb-4">
              <p class="text-gray-600 text-sm">Imię i nazwisko</p>
              <p class="text-gray-900 font-medium">{{ authStore.userName }}</p>
            </div>
            
            <div class="mb-4">
              <p class="text-gray-600 text-sm">Email</p>
              <p class="text-gray-900 font-medium">{{ authStore.userEmail }}</p>
              
              <!-- Weryfikacja emaila -->
              <div v-if="authStore.isEmailVerified" class="mt-1 text-xs text-green-500 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                Zweryfikowany
              </div>
              <div v-else class="mt-1 text-xs text-red-500 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                </svg>
                Niezweryfikowany
                <button 
                  @click="resendVerification" 
                  class="ml-2 text-blue-500 hover:text-blue-700"
                  :disabled="isVerificationLoading"
                >
                  {{ isVerificationLoading ? 'Wysyłanie...' : 'Wyślij ponownie' }}
                </button>
              </div>
            </div>
            
            <div v-if="status" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
              {{ status }}
            </div>
            
            <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-2 mt-6">
              <button 
                @click="showChangePassword = true" 
                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
              >
                Zmień hasło
              </button>
              
              <button 
                @click="handleLogout" 
                class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
                :disabled="isLoggingOut"
              >
                <span v-if="isLoggingOut">Wylogowywanie...</span>
                <span v-else>Wyloguj się</span>
              </button>
            </div>
          </div>
          
          <!-- Formularz zmiany hasła -->
          <div v-else>
            <h3 class="text-xl font-medium text-gray-800 mb-4">Zmiana hasła</h3>
            
            <div v-if="authStore.hasError" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
              {{ authStore.errorMessage || 'Wystąpił błąd podczas zmiany hasła.' }}
            </div>
            
            <div v-if="passwordChangeStatus" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
              {{ passwordChangeStatus }}
            </div>
            
            <form @submit.prevent="handlePasswordChange">
              <div class="mb-4">
                <label for="current-password" class="block text-gray-700 text-sm font-bold mb-2">Aktualne hasło</label>
                <input 
                  id="current-password" 
                  type="password" 
                  v-model="currentPassword" 
                  required
                  class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                />
              </div>
              
              <div class="mb-4">
                <label for="new-password" class="block text-gray-700 text-sm font-bold mb-2">Nowe hasło</label>
                <input 
                  id="new-password" 
                  type="password" 
                  v-model="newPassword" 
                  required
                  class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                />
              </div>
              
              <div class="mb-6">
                <label for="new-password-confirmation" class="block text-gray-700 text-sm font-bold mb-2">Powtórz nowe hasło</label>
                <input 
                  id="new-password-confirmation" 
                  type="password" 
                  v-model="newPasswordConfirmation" 
                  required
                  class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                />
              </div>
              
              <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-2">
                <button 
                  type="submit" 
                  class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
                  :disabled="authStore.isLoading"
                >
                  <span v-if="authStore.isLoading">Zapisywanie...</span>
                  <span v-else>Zapisz nowe hasło</span>
                </button>
                
                <button 
                  type="button"
                  @click="showChangePassword = false" 
                  class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
                >
                  Anuluj
                </button>
              </div>
            </form>
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

export default {
  name: 'ProfilePage',
  
  setup() {
    const isLoggingOut = ref(false);
    const isVerificationLoading = ref(false);
    const showChangePassword = ref(false);
    const currentPassword = ref('');
    const newPassword = ref('');
    const newPasswordConfirmation = ref('');
    const status = ref('');
    const passwordChangeStatus = ref('');
    const router = useRouter();
    const authStore = useAuthStore();
    
    onMounted(() => {
      // Inicjalizacja stanu autoryzacji, jeśli jeszcze nie został zainicjalizowany
      if (!authStore.user) {
        authStore.initAuth();
      }
    });
    
    const handleLogout = async () => {
      isLoggingOut.value = true;
      
      try {
        const success = await authStore.logout();
        
        if (success) {
          router.push('/');
        }
      } finally {
        isLoggingOut.value = false;
      }
    };
    
    const resendVerification = async () => {
      isVerificationLoading.value = true;
      
      try {
        const message = await authStore.resendVerificationEmail();
        if (message) {
          status.value = message;
          setTimeout(() => {
            status.value = '';
          }, 5000);
        }
      } finally {
        isVerificationLoading.value = false;
      }
    };
    
    const handlePasswordChange = async () => {
      passwordChangeStatus.value = '';
      
      if (newPassword.value !== newPasswordConfirmation.value) {
        authStore.hasError = true;
        authStore.errorMessage = 'Nowe hasła nie są identyczne.';
        return;
      }
      
      const success = await authStore.updatePassword(
        currentPassword.value,
        newPassword.value,
        newPasswordConfirmation.value
      );
      
      if (success) {
        passwordChangeStatus.value = 'Hasło zostało pomyślnie zmienione.';
        currentPassword.value = '';
        newPassword.value = '';
        newPasswordConfirmation.value = '';
        
        setTimeout(() => {
          showChangePassword.value = false;
          passwordChangeStatus.value = '';
        }, 2000);
      }
    };
    
    return {
      authStore,
      isLoggingOut,
      isVerificationLoading,
      showChangePassword,
      currentPassword,
      newPassword,
      newPasswordConfirmation,
      status,
      passwordChangeStatus,
      handleLogout,
      resendVerification,
      handlePasswordChange
    };
  }
};
</script> 