<template>
  <div class="flex flex-col min-h-screen bg-gray-50">
    <!-- Promotion Bar -->
    <promotion-bar />
    
    <!-- Header z lekkim cieniem i lepszą wizualną separacją -->
    <site-header class="sticky top-0 z-40 bg-white shadow-sm border-b border-gray-200" />
    
    <!-- Main Content z odpowiednim paddingiem i maksymalną szerokością -->
    <main class="flex-grow">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 flex flex-col">
        <!-- Komunikaty alertów przeniesione do globalnego App.vue -->
        
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

export default {
  name: 'DefaultLayout',
  components: {
    PromotionBar,
    SiteHeader,
    SiteFooter
  },
  // No setup needed - alerts are now global in App.vue
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