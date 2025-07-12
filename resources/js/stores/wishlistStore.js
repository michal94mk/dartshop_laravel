import { defineStore } from 'pinia';

export const useWishlistStore = defineStore('wishlist', {
  state: () => ({
    wishlistItems: JSON.parse(localStorage.getItem('wishlist')) || [],
    loading: false,
    error: null,
  }),
  
  getters: {
    totalWishlistItems: (state) => {
      return state.wishlistItems.length;
    },
    
    isInWishlist: (state) => (productId) => {
      return state.wishlistItems.some(item => item.id === productId);
    },
    
    isEmpty: (state) => {
      return state.wishlistItems.length === 0;
    }
  },
  
  actions: {
    addToWishlist(product) {
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
    
    removeFromWishlist(productId) {
      this.wishlistItems = this.wishlistItems.filter(item => item.id !== productId);
      this.saveWishlistToLocalStorage();
    },
    
    toggleWishlistItem(product) {
      if (this.isInWishlist(product.id)) {
        this.removeFromWishlist(product.id);
      } else {
        this.addToWishlist(product);
      }
    },
    
    clearWishlist() {
      this.wishlistItems = [];
      this.saveWishlistToLocalStorage();
    },
    
    saveWishlistToLocalStorage() {
      localStorage.setItem('wishlist', JSON.stringify(this.wishlistItems));
    }
  }
}); 