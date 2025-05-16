<template>
  <div class="min-h-screen flex flex-col">
    <!-- Wyświetl spinner ładowania podczas inicjalizacji -->
    <loading-spinner 
      v-if="isInitializing" 
      message="Inicjalizacja aplikacji..." 
      class="min-h-screen flex items-center justify-center"
    />
    
    <template v-else>
      <!-- Tylko renderuj główny layout, jeśli nie jesteśmy w panelu administracyjnym -->
      <template v-if="!isAdminRoute">
        <!-- Promotion Bar -->
        <promotion-bar />
        
        <!-- Header -->
        <site-header />
        
        <!-- Main Content -->
        <main class="flex-1">
          <div v-if="successMessage" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mx-auto max-w-7xl mt-4" role="alert">
            <strong class="font-bold">Sukces!</strong>
            <span class="block sm:inline">{{ successMessage }}</span>
          </div>
          
          <div v-if="errorMessage" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mx-auto max-w-7xl mt-4" role="alert">
            <strong class="font-bold">Błąd!</strong>
            <span class="block sm:inline">{{ errorMessage }}</span>
          </div>
          
          <!-- Router View for Page Content -->
          <router-view v-slot="{ Component }">
            <transition name="fade" mode="out-in">
              <component :is="Component" :key="$route.fullPath" />
            </transition>
          </router-view>
        </main>

        <!-- Footer -->
        <site-footer />
      </template>
      
      <!-- Jeśli jesteśmy w panelu administracyjnym, renderuj tylko RouterView bez głównego layoutu -->
      <template v-else>
        <router-view v-slot="{ Component }">
          <transition name="fade" mode="out-in">
            <component :is="Component" :key="$route.fullPath" />
          </transition>
        </router-view>
      </template>
    </template>
  </div>
</template>

<script>
import PromotionBar from './components/layout/PromotionBar.vue';
import SiteHeader from './components/layout/SiteHeader.vue';
import SiteFooter from './components/layout/SiteFooter.vue';
import LoadingSpinner from './components/ui/LoadingSpinner.vue';
import axios from 'axios';
import { computed, ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import { useAuthStore } from './stores/authStore';

export default {
  name: 'App',
  components: {
    PromotionBar,
    SiteHeader,
    SiteFooter,
    LoadingSpinner
  },
  setup() {
    const route = useRoute();
    const authStore = useAuthStore();
    const isInitializing = ref(true);
    
    // Sprawdź, czy bieżąca trasa należy do panelu administracyjnego
    const isAdminRoute = computed(() => {
      return route.meta.layout === 'admin';
    });
    
    // Po zamontowaniu sprawdź, czy authStore zostało zainicjalizowane
    onMounted(() => {
      // Jeśli flaga authInitialized jest już ustawiona, to nie pokazuj loadingu
      if (authStore.authInitialized) {
        isInitializing.value = false;
      } else {
        // Sprawdzaj co 100ms, czy inicjalizacja została zakończona
        const checkInterval = setInterval(() => {
          if (authStore.authInitialized) {
            isInitializing.value = false;
            clearInterval(checkInterval);
          }
        }, 100);
        
        // Na wszelki wypadek, ustaw timeout na 5 sekund
        setTimeout(() => {
          isInitializing.value = false;
          clearInterval(checkInterval);
        }, 5000);
      }
    });
    
    return {
      isAdminRoute,
      isInitializing
    };
  },
  data() {
    return {
      successMessage: '',
      errorMessage: ''
    };
  },
  created() {
    console.log('App component created');
    
    // Fetch CSRF cookie for API requests authentication
    this.getCsrfCookie();
  },
  methods: {
    showSuccess(message) {
      this.successMessage = message;
      setTimeout(() => {
        this.successMessage = '';
      }, 5000);
    },
    showError(message) {
      this.errorMessage = message;
      setTimeout(() => {
        this.errorMessage = '';
      }, 5000);
    },
    async getCsrfCookie() {
      try {
        await axios.get('/sanctum/csrf-cookie');
        console.log('CSRF cookie set successfully');
      } catch (error) {
        console.error('Failed to get CSRF cookie:', error);
      }
    }
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