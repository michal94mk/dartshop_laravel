import { defineStore } from 'pinia';
import type { Product } from '@/types';

// Type definitions
interface WishlistItem {
  id: number;
  name: string;
  price: string;
  image_url?: string | null;
  added_at: string;
}

interface WishlistState {
  wishlistItems: WishlistItem[];
  loading: boolean;
  error: string | null;
}

export const useWishlistStore = defineStore('wishlist', {
  state: (): WishlistState => ({
    wishlistItems: JSON.parse(localStorage.getItem('wishlist') || '[]'),
    loading: false,
    error: null,
  }),
  
  getters: {
    totalWishlistItems: (state): number => {
      return state.wishlistItems.length;
    },
    
    isInWishlist: (state) => (productId: number): boolean => {
      return state.wishlistItems.some(item => item.id === productId);
    },
    
    isEmpty: (state): boolean => {
      return state.wishlistItems.length === 0;
    }
  },
  
  actions: {
    addToWishlist(product: Product): void {
      if (!this.isInWishlist(product.id)) {
        this.wishlistItems.push({
          id: product.id,
          name: product.name,
          price: product.price,
          image_url: product.image_url || null,
          added_at: new Date().toISOString()
        });
        this.saveWishlistToLocalStorage();
      }
    },
    
    removeFromWishlist(productId: number): void {
      this.wishlistItems = this.wishlistItems.filter(item => item.id !== productId);
      this.saveWishlistToLocalStorage();
    },
    
    toggleWishlistItem(product: Product): void {
      if (this.isInWishlist(product.id)) {
        this.removeFromWishlist(product.id);
      } else {
        this.addToWishlist(product);
      }
    },
    
    clearWishlist(): void {
      this.wishlistItems = [];
      this.saveWishlistToLocalStorage();
    },
    
    saveWishlistToLocalStorage(): void {
      localStorage.setItem('wishlist', JSON.stringify(this.wishlistItems));
    }
  }
});
