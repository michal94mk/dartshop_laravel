<template>
  <Teleport to="body">
    <Transition name="fade">
      <button
        v-if="showButton"
        @click="scrollToTop"
        class="fixed bottom-4 right-4 sm:bottom-5 sm:right-5 md:bottom-6 md:right-6 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 active:from-blue-700 active:to-blue-800 text-white shadow-lg hover:shadow-xl transition-all duration-300 z-[9999] w-12 h-12 sm:w-14 sm:h-14 md:w-16 md:h-16 rounded-full flex items-center justify-center touch-manipulation"
        style="position: fixed !important; z-index: 9999 !important; min-width: 48px !important; min-height: 48px !important;"
        aria-label="Przewiń do góry"
      >
        <svg class="w-5 h-5 sm:w-6 sm:h-6 md:w-7 md:h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
        </svg>
      </button>
    </Transition>
  </Teleport>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue'

const showButton = ref(false)

const checkScrollPosition = () => {
  // Check if we're in admin layout (has main element with specific class)
  const adminMain = document.querySelector('main.overflow-y-auto')
  
  if (adminMain) {
    // Admin layout - check main element scroll
    showButton.value = adminMain.scrollTop > 200
  } else {
    // Default layout - check window scroll
    // Use smaller threshold for mobile
    const threshold = window.innerWidth < 768 ? 200 : 300
    showButton.value = window.scrollY > threshold
  }
}

const scrollToTop = () => {
  // Check if we're in admin layout
  const adminMain = document.querySelector('main.overflow-y-auto')
  
  if (adminMain) {
    // Admin layout - scroll main element
    adminMain.scrollTo({
      top: 0,
      behavior: 'smooth'
    })
  } else {
    // Default layout - scroll window
    window.scrollTo({
      top: 0,
      behavior: 'smooth'
    })
  }
}

onMounted(() => {
  // Add scroll listeners for both window and potential admin main element
  window.addEventListener('scroll', checkScrollPosition, { passive: true })
  
  // Also listen to admin main element if it exists
  const adminMain = document.querySelector('main.overflow-y-auto')
  if (adminMain) {
    adminMain.addEventListener('scroll', checkScrollPosition, { passive: true })
  }
  
  // Listen for resize to adjust threshold
  window.addEventListener('resize', checkScrollPosition, { passive: true })
  
  // Check initial position
  checkScrollPosition()
})

onBeforeUnmount(() => {
  window.removeEventListener('scroll', checkScrollPosition)
  window.removeEventListener('resize', checkScrollPosition)
  
  const adminMain = document.querySelector('main.overflow-y-auto')
  if (adminMain) {
    adminMain.removeEventListener('scroll', checkScrollPosition)
  }
})
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

/* Enhanced responsive optimizations */
button {
  /* Ensure minimum touch target size for all screen sizes */
  min-width: 48px !important;
  min-height: 48px !important;
}

@media (max-width: 639px) {
  /* Small mobile */
  button {
    width: 48px !important;
    height: 48px !important;
    bottom: 16px !important;
    right: 16px !important;
  }
}

@media (min-width: 640px) and (max-width: 767px) {
  /* Large mobile / small tablet */
  button {
    width: 56px !important;
    height: 56px !important;
    bottom: 20px !important;
    right: 20px !important;
  }
}

@media (min-width: 768px) {
  /* Desktop */
  button {
    width: 64px !important;
    height: 64px !important;
    bottom: 24px !important;
    right: 24px !important;
  }
}
</style> 