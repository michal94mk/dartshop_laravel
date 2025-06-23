<template>
  <div id="app-container">
    <!-- Smooth transition when app is ready -->
    <transition name="app-fade" mode="out-in">
      <!-- Renderuj layout gdy gotowe -->
      <component 
        v-if="isReadyToShow"
        key="app"
        :is="currentLayout" 
      />
    </transition>
  </div>
</template>

<script>
import DefaultLayout from './components/layouts/DefaultLayout.vue';
import AdminLayout from './components/layouts/AdminLayout.vue';
import axios from 'axios';
import { computed, ref, onMounted, watch, nextTick } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useAuthStore } from './stores/authStore';
import { useFavoriteStore } from './stores/favoriteStore';

export default {
  name: 'App',
  components: {
    DefaultLayout,
    AdminLayout
  },
  setup() {
    const route = useRoute();
    const router = useRouter();
    const authStore = useAuthStore();
    const favoriteStore = useFavoriteStore();
    const isReadyToShow = ref(false);
    
    // Sprawdź, czy bieżąca trasa należy do panelu administracyjnego (na podstawie meta)
    const isAdminRoute = computed(() => {
      return route.meta && route.meta.layout === 'admin';
    });
    
    // Sprawdź, czy używamy admin view na podstawie window.Laravel
    const isAdminView = computed(() => {
      return window.Laravel && window.Laravel.isAdmin === true;
    });
    
    // Wybierz właściwy layout na podstawie trasy
    const currentLayout = computed(() => {
      // Don't show layout until we're ready
      if (!isReadyToShow.value) return null;
      
      // Sprawdź, czy użytkownik jest zalogowany i czy jest administratorem
      const isUserAdmin = authStore.isAdmin;
      
      // Jeśli ścieżka zaczyna się od /admin, używamy layoutu admin
      if (route.path.startsWith('/admin')) {
        // Sprawdź czy user jest adminem, ale nie rób przekierowań tutaj
        // (router guard powinien się tym zająć)
        if (!isUserAdmin && authStore.authInitialized) {
          console.warn('User is not admin but tries to access admin layout');
          return 'DefaultLayout';
        }
        
        // Użytkownik jest adminem i ma dostęp do panelu admina
        return 'AdminLayout';
      }
      
      // Dla wszystkich innych ścieżek, nawet jeśli użytkownik jest adminem, 
      // używamy domyślnego layoutu aplikacji
      return 'DefaultLayout';
    });

    // Simple initialization
    const initializeApp = async () => {
      try {
        console.log('Starting auth initialization...');
        
        // Initialize auth
        await authStore.initAuthWithRetry(2, 300);
        console.log('Auth initialized:', authStore.isLoggedIn ? 'User logged in' : 'User not logged in');
        
        // Initialize favorites if logged in
        if (authStore.isLoggedIn) {
          favoriteStore.initializeFavorites();
          setupSessionRefresh();
        }
        
        // Show app (blade loader will be hidden automatically by app.js)
        isReadyToShow.value = true;
        
      } catch (error) {
        console.error('Auth initialization failed:', error);
        // Show app anyway
        isReadyToShow.value = true;
      }
    };

    // Setup session refresh
    const setupSessionRefresh = () => {
      console.log('Setting up session refresh');
      setInterval(async () => {
        if (authStore.isLoggedIn) {
          await authStore.refreshSession();
        }
      }, 900000); // 15 minutes
    };
    
    // Po zamontowaniu sprawdź, czy authStore zostało zainicjalizowane
    onMounted(() => {
      console.log('App mounted');
      getCsrfCookie();
      initializeApp();
    });
    
    // Get CSRF cookie
    const getCsrfCookie = () => {
      axios.get('/sanctum/csrf-cookie')
        .then(() => console.log('CSRF cookie set'))
        .catch(error => console.error('CSRF cookie error:', error));
    };
    
    return {
      isAdminRoute,
      isAdminView,
      isReadyToShow,
      currentLayout
    };
  }
};
</script>

<style>
#app-container {
  min-height: 100vh;
  position: relative;
  /* Optimize for GPU acceleration */
  transform: translateZ(0);
  backface-visibility: hidden;
  perspective: 1000px;
}

/* Smooth app transition */
.app-fade-enter-active {
  transition: all 0.25s cubic-bezier(0.25, 0.46, 0.45, 0.94);
}

.app-fade-leave-active {
  transition: all 0.15s cubic-bezier(0.55, 0.055, 0.675, 0.19);
}

.app-fade-enter-from {
  opacity: 0;
  transform: translateY(8px) scale(0.98);
}

.app-fade-leave-to {
  opacity: 0;
  transform: translateY(-3px) scale(1.02);
}

.app-fade-enter-to,
.app-fade-leave-from {
  opacity: 1;
  transform: translateY(0) scale(1);
}

/* Legacy fade transitions */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style> 