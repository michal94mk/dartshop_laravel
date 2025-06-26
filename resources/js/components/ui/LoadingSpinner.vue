<template>
  <div :class="containerClass">
    <!-- Full Screen Loading -->
    <div v-if="fullScreen" class="fullscreen-overlay">
      <div class="spinner large"></div>
      <p class="loading-text large">{{ message || 'Ładowanie...' }}</p>
    </div>
    
    <!-- Regular Loading -->
    <div v-else>
      <div class="spinner"></div>
      <p v-if="message" class="loading-text">{{ message }}</p>
    </div>
  </div>
</template>

<script>
export default {
  name: 'LoadingSpinner',
  props: {
    message: {
      type: String,
      default: 'Ładowanie...'
    },
    fullScreen: {
      type: Boolean,
      default: false
    }
  },
  computed: {
    containerClass() {
      if (this.fullScreen) {
        return 'full-screen-container'
      }
      return 'loading-container'
    }
  }
}
</script>

<style scoped>
.loading-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  min-height: 300px;
  padding: 2rem;
}

.full-screen-container {
  position: fixed;
  inset: 0;
  z-index: 9999;
  background: rgba(255, 255, 255, 0.9);
  backdrop-filter: blur(8px);
}

.fullscreen-overlay {
  position: absolute;
  inset: 0;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 1.5rem;
}

.spinner {
  width: 32px;
  height: 32px;
  border: 3px solid #f3f4f6;
  border-top: 3px solid #4f46e5;
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
}

.spinner.large {
  width: 48px;
  height: 48px;
  border-width: 4px;
}

.loading-text {
  margin-top: 1rem;
  font-size: 0.875rem;
  color: #6b7280;
  text-align: center;
}

.loading-text.large {
  margin-top: 0;
  font-size: 1.125rem;
  font-weight: 500;
  color: #374151;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

@media (prefers-color-scheme: dark) {
  .full-screen-container {
    background: rgba(17, 24, 39, 0.9);
  }
  
  .loading-text {
    color: #9ca3af;
  }
  
  .loading-text.large {
    color: #d1d5db;
  }
  
  .spinner {
    border-color: #374151;
    border-top-color: #6366f1;
  }
}
</style> 