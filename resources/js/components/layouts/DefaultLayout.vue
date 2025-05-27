<template>
  <div class="flex flex-col min-h-screen bg-gray-50">
    <!-- Promotion Bar -->
    <promotion-bar />
    
    <!-- Header z lekkim cieniem i lepszą wizualną separacją -->
    <site-header class="sticky top-0 z-40 bg-white shadow-sm border-b border-gray-200" />
    
    <!-- Main Content z odpowiednim paddingiem i maksymalną szerokością -->
    <main class="flex-grow">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 flex flex-col">
        <!-- Komunikaty alertów -->
        <div v-if="successMessage" class="mb-6 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-md shadow-sm mx-auto" role="alert">
          <div class="flex">
            <div class="flex-shrink-0">
              <svg class="h-5 w-5 text-green-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
              </svg>
            </div>
            <div class="ml-3">
              <p class="font-medium">{{ successMessage }}</p>
            </div>
          </div>
        </div>
        
        <div v-if="errorMessage" class="mb-6 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md shadow-sm mx-auto" role="alert">
          <div class="flex">
            <div class="flex-shrink-0">
              <svg class="h-5 w-5 text-red-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
              </svg>
            </div>
            <div class="ml-3">
              <p class="font-medium">{{ errorMessage }}</p>
            </div>
          </div>
        </div>
        
        <!-- Router View z ładnymi przejściami między stronami -->
        <div class="bg-white shadow-sm rounded-lg overflow-hidden">
          <router-view v-slot="{ Component }">
            <transition 
              name="fade" 
              mode="out-in"
              enter-active-class="transition ease-out duration-200"
              enter-from-class="opacity-0 transform scale-95"
              enter-to-class="opacity-100 transform scale-100"
              leave-active-class="transition ease-in duration-150"
              leave-from-class="opacity-100 transform scale-100"
              leave-to-class="opacity-0 transform scale-95"
            >
              <component :is="Component" :key="$route.fullPath" />
            </transition>
          </router-view>
        </div>
      </div>
    </main>

    <!-- Footer z lepszym kontrastem i stylizacją -->
    <site-footer class="shadow-inner mt-auto" />
  </div>
</template>

<script>
import PromotionBar from '../layout/PromotionBar.vue';
import SiteHeader from '../layout/SiteHeader.vue';
import SiteFooter from '../layout/SiteFooter.vue';
import { computed } from 'vue';
import { useAlertStore } from '../../stores/alertStore';

export default {
  name: 'DefaultLayout',
  components: {
    PromotionBar,
    SiteHeader,
    SiteFooter
  },
  setup() {
    const alertStore = useAlertStore();
    
    // Alert messages
    const successMessage = computed(() => alertStore.successMessage);
    const errorMessage = computed(() => alertStore.errorMessage);
    
    return {
      successMessage,
      errorMessage
    };
  }
};
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style> 