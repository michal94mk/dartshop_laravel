<template>
  <button 
    @click.prevent="toggleFavorite"
    :class="['favorite-button', buttonClasses]"
    :disabled="loading"
    :aria-label="isFavorite ? 'Usuń z ulubionych' : 'Dodaj do ulubionych'"
  >
    <div v-if="loading" class="animate-pulse">
      <svg class="w-5 h-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
      </svg>
    </div>
    <template v-else>
      <svg 
        class="w-5 h-5" 
        :class="{ 'text-red-500 fill-current': isFavorite, 'text-gray-400': !isFavorite }"
        xmlns="http://www.w3.org/2000/svg" 
        viewBox="0 0 24 24" 
        stroke="currentColor"
      >
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
      </svg>
      <span v-if="showText" class="ml-2 text-sm">
        {{ isFavorite ? 'Usuń z ulubionych' : 'Dodaj do ulubionych' }}
      </span>
    </template>
  </button>
</template>

<script>
import { ref, onMounted } from 'vue';
import { useFavoriteStore } from '../../stores/favoriteStore';
import { useAuthStore } from '../../stores/authStore';

export default {
  name: 'FavoriteButton',
  props: {
    product: {
      type: Object,
      required: true
    },
    buttonClasses: {
      type: String,
      default: 'p-2 bg-white rounded-full shadow hover:bg-gray-100 transition-colors duration-200'
    },
    showText: {
      type: Boolean,
      default: false
    }
  },
  emits: ['toggled', 'favorite-added', 'favorite-removed'],
  setup(props, { emit }) {
    const favoriteStore = useFavoriteStore();
    const authStore = useAuthStore();
    const loading = ref(false);
    
    // Check initial favorite status
    const isFavorite = ref(false);
    
    // Check favorite status when component is mounted
    onMounted(async () => {
      if (authStore.isLoggedIn) {
        isFavorite.value = favoriteStore.isInFavorites(props.product.id);
        
        // If store is not initialized yet, check status directly
        if (!favoriteStore.initialized) {
          loading.value = true;
          try {
            const status = await favoriteStore.checkFavoriteStatus(props.product.id);
            isFavorite.value = status;
          } catch (error) {
            console.error('Error checking favorite status:', error);
          } finally {
            loading.value = false;
          }
        }
      }
    });
    
    const toggleFavorite = async () => {
      loading.value = true;
      try {
        // Handle redirect in the store if user is not logged in
        const result = await favoriteStore.toggleFavorite(props.product);
        if (result) {
          const wasAdded = result.is_favorite;
          isFavorite.value = wasAdded;
          
          // Emit general toggled event
          emit('toggled', { product: props.product, isFavorite: isFavorite.value });
          
          // Emit specific events for added or removed
          if (wasAdded) {
            emit('favorite-added', props.product);
          } else {
            emit('favorite-removed', props.product);
          }
        }
      } catch (error) {
        console.error('Error toggling favorite:', error);
      } finally {
        loading.value = false;
      }
    };
    
    return {
      isFavorite,
      loading,
      toggleFavorite
    };
  }
}
</script>

<style scoped>
.favorite-button {
  position: relative;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  background-color: rgba(255, 255, 255, 0.95);
  padding: 0.75rem;
  border-radius: 9999px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1), 0 4px 6px rgba(0, 0, 0, 0.05);
  z-index: 20;
  transition: all 0.2s ease;
}

.favorite-button:hover {
  background-color: white;
  transform: scale(1.1);
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1), 0 6px 8px rgba(0, 0, 0, 0.05);
}

.favorite-button svg {
  width: 1.75rem;
  height: 1.75rem;
  transition: transform 0.2s ease;
}

.favorite-button:hover svg {
  transform: scale(1.1);
}

.favorite-button[disabled] {
  opacity: 0.7;
  cursor: not-allowed;
}
</style> 