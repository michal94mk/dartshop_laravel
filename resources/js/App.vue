<template>
  <div class="min-h-screen flex flex-col">
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
          <component :is="Component" />
        </transition>
      </router-view>
    </main>

    <!-- Footer -->
    <site-footer />
  </div>
</template>

<script>
import PromotionBar from './components/layout/PromotionBar.vue';
import SiteHeader from './components/layout/SiteHeader.vue';
import SiteFooter from './components/layout/SiteFooter.vue';

export default {
  name: 'App',
  components: {
    PromotionBar,
    SiteHeader,
    SiteFooter
  },
  data() {
    return {
      successMessage: '',
      errorMessage: ''
    };
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