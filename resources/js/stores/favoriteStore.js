import { defineStore } from 'pinia';
import axios from 'axios';
import { useAuthStore } from './authStore';

export const useFavoriteStore = defineStore('favorites', {
  state: () => ({
    favorites: [],
    loading: false,
    error: null,
    initialized: false
  }),
  
  getters: {
    totalFavorites: (state) => {
      return state.favorites.length;
    },
    
    isInFavorites: (state) => (productId) => {
      return state.favorites.some(item => item.id === productId);
    }
  },
  
  actions: {
    async initializeFavorites() {
      const authStore = useAuthStore();
      
      // Only load favorites if user is authenticated
      if (!authStore.isLoggedIn) {
        this.favorites = [];
        this.initialized = true;
        return;
      }
      
      if (this.initialized) return;
      
      this.loading = true;
      this.error = null;
      
      try {
        const response = await axios.get('/api/favorites');
        this.favorites = response.data.favorite_products || [];
        this.initialized = true;
      } catch (error) {
        this.error = 'Failed to load favorites';
        console.error('Error loading favorites:', error);
      } finally {
        this.loading = false;
      }
    },
    
    async toggleFavorite(product) {
      const authStore = useAuthStore();
      
      // Redirect to login if not authenticated
      if (!authStore.isLoggedIn) {
        // Store current path in localStorage for redirect after login
        localStorage.setItem('loginRedirect', window.location.pathname);
        // Redirect to login page
        window.location.href = '/login';
        return { is_favorite: false };
      }
      
      this.loading = true;
      this.error = null;
      
      try {
        const response = await axios.post(`/api/favorites/${product.id}`);
        
        if (response.data.is_favorite) {
          // Add to favorites if not already there
          if (!this.isInFavorites(product.id)) {
            this.favorites.push({
              id: product.id,
              name: product.name,
              price: product.price,
              image_url: product.image_url,
              category: product.category,
              brand: product.brand
            });
          }
        } else {
          // Remove from favorites
          this.favorites = this.favorites.filter(item => item.id !== product.id);
        }
        
        return response.data;
      } catch (error) {
        this.error = 'Failed to update favorite status';
        console.error('Error toggling favorite:', error);
        throw error;
      } finally {
        this.loading = false;
      }
    },
    
    async checkFavoriteStatus(productId) {
      const authStore = useAuthStore();
      
      // Return false immediately if not authenticated
      if (!authStore.isLoggedIn) {
        return false;
      }
      
      try {
        const response = await axios.get(`/api/favorites/check/${productId}`);
        return response.data.is_favorite;
      } catch (error) {
        console.error('Error checking favorite status:', error);
        return false;
      }
    },
    
    async loadFavorites() {
      const authStore = useAuthStore();
      
      // Only load favorites if user is authenticated
      if (!authStore.isLoggedIn) {
        this.favorites = [];
        return;
      }
      
      this.loading = true;
      this.error = null;
      
      try {
        const response = await axios.get('/api/favorites');
        this.favorites = response.data.favorite_products || [];
        
        // Format price for display
        this.favorites.forEach(product => {
          if (!product.price_formatted && product.price) {
            product.price_formatted = this.formatPrice(product.price) + ' zł';
          }
        });
      } catch (error) {
        this.error = 'Failed to load favorites';
        console.error('Error loading favorites:', error);
      } finally {
        this.loading = false;
      }
    },
    
    // Format price helper
    formatPrice(price) {
      if (price === null || price === undefined || isNaN(price)) {
        return '0.00';
      }
      return parseFloat(price).toFixed(2);
    },
    
    // Reset store - useful for logout
    resetStore() {
      this.favorites = [];
      this.loading = false;
      this.error = null;
      this.initialized = false;
    }
  }
}); 