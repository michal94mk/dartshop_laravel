import { defineStore } from 'pinia';
import { apiService } from '../services/apiService';
import { useAuthStore } from './authStore';
import type { Product, ApiResponse } from '@/types';

// Type definitions
interface FavoriteProduct {
  id: number;
  name: string;
  price: string;
  image_url?: string | null;
  category?: any;
  brand?: any;
  price_formatted?: string;
}

interface FavoriteState {
  favorites: FavoriteProduct[];
  loading: boolean;
  error: string | null;
  initialized: boolean;
}

interface ToggleFavoriteResponse {
  is_favorite: boolean;
  redirected?: boolean;
}

interface FavoriteStatusResponse {
  is_favorite: boolean;
}

interface FavoritesResponse {
  favorite_products: FavoriteProduct[];
}

export const useFavoriteStore = defineStore('favorites', {
  state: (): FavoriteState => ({
    favorites: [],
    loading: false,
    error: null,
    initialized: false
  }),
  
  getters: {
    totalFavorites: (state): number => {
      return state.favorites.length;
    },
    
    isInFavorites: (state) => (productId: number): boolean => {
      return state.favorites.some(item => item.id === productId);
    }
  },
  
  actions: {
    async initializeFavorites(): Promise<void> {
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
        const response = await apiService.get<FavoritesResponse>('/favorites');
        this.favorites = (response.favorite_products) || [];
        this.initialized = true;
      } catch (error: any) {
        this.error = 'Failed to load favorites';
        console.error('Error loading favorites:', error);
      } finally {
        this.loading = false;
      }
    },
    
    async toggleFavorite(product: Product): Promise<ToggleFavoriteResponse> {
      const authStore = useAuthStore();
      
      // Redirect to login if not authenticated
      if (!authStore.isLoggedIn) {
        // Store current path in localStorage for redirect after login
        localStorage.setItem('loginRedirect', window.location.pathname);
        // Redirect to login page
        window.location.href = '/login';
        return { is_favorite: false, redirected: true };
      }
      
      this.loading = true;
      this.error = null;
      
      try {
        const response = await apiService.post<ToggleFavoriteResponse>(`/favorites/${product.id}`);
        const isFavorite = response.is_favorite;
        if (isFavorite) {
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
        return { is_favorite: isFavorite };
      } catch (error: any) {
        this.error = 'Failed to update favorite status';
        console.error('Error toggling favorite:', error);
        throw error;
      } finally {
        this.loading = false;
      }
    },
    
    async checkFavoriteStatus(productId: number): Promise<boolean> {
      const authStore = useAuthStore();
      
      // Return false immediately if not authenticated
      if (!authStore.isLoggedIn) {
        return false;
      }
      
      try {
        const response = await apiService.get<FavoriteStatusResponse>(`/favorites/check/${productId}`);
        return response.is_favorite;
      } catch (error: any) {
        console.error('Error checking favorite status:', error);
        return false;
      }
    },
    
    async loadFavorites(): Promise<void> {
      const authStore = useAuthStore();
      
      // Only load favorites if user is authenticated
      if (!authStore.isLoggedIn) {
        this.favorites = [];
        return;
      }
      
      this.loading = true;
      this.error = null;
      
      try {
        const response = await apiService.get<FavoritesResponse>('/favorites');
        this.favorites = (response.favorite_products) || [];
        
        // Format price for display
        this.favorites.forEach(product => {
          if (!product.price_formatted && product.price) {
            product.price_formatted = this.formatPrice(product.price) + ' zł';
          }
        });
      } catch (error: any) {
        this.error = 'Failed to load favorites';
        console.error('Error loading favorites:', error);
      } finally {
        this.loading = false;
      }
    },
    
    // Format price helper
    formatPrice(price: string | number): string {
      if (price === null || price === undefined || isNaN(Number(price))) {
        return '0.00';
      }
      return parseFloat(price.toString()).toFixed(2);
    },
    
    // Reset store - useful for logout
    resetStore(): void {
      this.favorites = [];
      this.loading = false;
      this.error = null;
      this.initialized = false;
    }
  }
});
