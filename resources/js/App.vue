<template>
  <div>
    <!-- Wyświetl spinner ładowania podczas inicjalizacji -->
    <loading-spinner 
      v-if="isInitializing" 
      message="Inicjalizacja aplikacji..." 
      class="min-h-screen flex items-center justify-center"
    />
    
    <template v-else>
      <!-- Renderuj layout w zależności od trasy -->
      <component 
        :is="currentLayout" 
        v-if="currentLayout"
      />
    </template>
  </div>
</template>

<script>
import DefaultLayout from './components/layouts/DefaultLayout.vue';
import AdminLayout from './components/layouts/AdminLayout.vue';
import LoadingSpinner from './components/ui/LoadingSpinner.vue';
import axios from 'axios';
import { computed, ref, onMounted, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useAuthStore } from './stores/authStore';
import { useFavoriteStore } from './stores/favoriteStore';

export default {
  name: 'App',
  components: {
    DefaultLayout,
    AdminLayout,
    LoadingSpinner
  },
  setup() {
    const route = useRoute();
    const router = useRouter();
    const authStore = useAuthStore();
    const favoriteStore = useFavoriteStore();
    const isInitializing = ref(true);
    
    // Sprawdź, czy bieżąca trasa należy do panelu administracyjnego (na podstawie meta)
    const isAdminRoute = computed(() => {
      return route.meta && route.meta.layout === 'admin';
    });
    
    // Sprawdź, czy używamy admin view na podstawie window.Laravel
    const isAdminView = computed(() => {
      return window.Laravel && window.Laravel.isAdmin === true;
    });
    
    // Wybierz właściwy layout na podstawie wszystkich warunków dostępu
    const currentLayout = computed(() => {
      // Sprawdź, czy użytkownik jest zalogowany i czy jest administratorem
      const isUserAdmin = authStore.isAdmin;
      
      // Jeśli ścieżka zaczyna się od /admin, używamy layoutu admin
      if (route.path.startsWith('/admin')) {
        // ...ale użytkownik nie jest adminem - przekieruj do głównego layoutu
        if (!isUserAdmin) {
          console.warn('User is not admin but tries to access admin layout, using default layout instead');
          setTimeout(() => {
            // Przekieruj na stronę główną, jeśli próbuje uzyskać dostęp do panelu admina
            router.push('/');
          }, 100);
          return 'DefaultLayout';
        }
        
        // Użytkownik jest adminem i ma dostęp do panelu admina
        return 'AdminLayout';
      }
      
      // Dla wszystkich innych ścieżek, nawet jeśli użytkownik jest adminem, 
      // używamy domyślnego layoutu aplikacji
      return 'DefaultLayout';
    });
    
    // Po zamontowaniu sprawdź, czy authStore zostało zainicjalizowane
    onMounted(() => {
      console.log('Admin view?', isAdminView.value);
      console.log('Admin route?', isAdminRoute.value);
      console.log('Selected layout:', currentLayout.value);
      
      // Jeśli flaga authInitialized jest już ustawiona, to nie pokazuj loadingu
      if (authStore.authInitialized) {
        isInitializing.value = false;
        // Initialize favorites if user is already authenticated
        if (authStore.isLoggedIn) {
          favoriteStore.initializeFavorites();
        }
      } else {
        // Sprawdzaj co 100ms, czy inicjalizacja została zakończona
        const checkInterval = setInterval(() => {
          if (authStore.authInitialized) {
            isInitializing.value = false;
            // Initialize favorites after auth is initialized
            if (authStore.isLoggedIn) {
              favoriteStore.initializeFavorites();
            }
            clearInterval(checkInterval);
          }
        }, 100);
        
        // Na wszelki wypadek, ustaw timeout na 5 sekund
        setTimeout(() => {
          if (isInitializing.value) {
            console.warn('Auth initialization timed out');
            isInitializing.value = false;
            clearInterval(checkInterval);
            
            // Initialize favorites anyway if user seems to be logged in
            if (authStore.isLoggedIn) {
              favoriteStore.initializeFavorites();
            }
            
            // Informuj użytkownika o potencjalnym problemie
            alert('Nie udało się w pełni zainicjalizować aplikacji. Niektóre funkcje mogą być niedostępne. Odśwież stronę lub spróbuj ponownie później.');
          }
        }, 5000);
      }
      
      // Fetch CSRF cookie for API requests authentication
      getCsrfCookie();
    });
    
    // Funkcja do pobierania tokena CSRF
    const getCsrfCookie = async () => {
      try {
        await axios.get('/sanctum/csrf-cookie');
        console.log('CSRF cookie set successfully');
      } catch (error) {
        console.error('Failed to get CSRF cookie:', error);
      }
    };
    
    return {
      isAdminRoute,
      isAdminView,
      isInitializing,
      currentLayout
    };
  }
};
</script>

<style>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style> 