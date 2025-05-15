import { defineStore } from 'pinia';
import api from '../services/api';

export const useCartStore = defineStore('cart', {
  state: () => ({
    items: [],
    loading: false,
    error: null,
    subtotal: 0,
    discount: 0,
    total: 0,
    promoCode: '',
    appliedPromo: null
  }),
  
  getters: {
    totalItems: (state) => {
      return state.items.reduce((sum, item) => sum + item.quantity, 0);
    },
    
    formattedSubtotal: (state) => {
      return state.subtotal.toFixed(2) + ' zł';
    },
    
    formattedTotal: (state) => {
      return state.total.toFixed(2) + ' zł';
    },
    
    formattedDiscount: (state) => {
      return state.discount.toFixed(2) + ' zł';
    },
    
    isEmpty: (state) => {
      return state.items.length === 0;
    }
  },
  
  actions: {
    async fetchCart() {
      this.loading = true;
      this.error = null;
      
      try {
        const response = await api.getCart();
        this.items = response.data.items || [];
        this.calculateTotals(response.data);
      } catch (error) {
        this.error = error.message || 'Failed to fetch cart';
        console.error('Error fetching cart:', error);
      } finally {
        this.loading = false;
      }
    },
    
    async addToCart(productId, quantity = 1) {
      this.loading = true;
      this.error = null;
      
      try {
        const response = await api.addToCart(productId, quantity);
        this.items = response.data.items || [];
        this.calculateTotals(response.data);
        return true;
      } catch (error) {
        this.error = error.message || 'Failed to add item to cart';
        console.error('Error adding item to cart:', error);
        return false;
      } finally {
        this.loading = false;
      }
    },
    
    async removeFromCart(productId) {
      this.loading = true;
      this.error = null;
      
      try {
        const response = await api.removeFromCart(productId);
        this.items = response.data.items || [];
        this.calculateTotals(response.data);
        return true;
      } catch (error) {
        this.error = error.message || 'Failed to remove item from cart';
        console.error('Error removing item from cart:', error);
        return false;
      } finally {
        this.loading = false;
      }
    },
    
    async updateCartItem(productId, quantity) {
      this.loading = true;
      this.error = null;
      
      try {
        const cartItems = this.items.map(item => {
          if (item.product_id === productId) {
            return { product_id: productId, quantity };
          }
          return { product_id: item.product_id, quantity: item.quantity };
        });
        
        const response = await api.updateCart(cartItems);
        this.items = response.data.items || [];
        this.calculateTotals(response.data);
        return true;
      } catch (error) {
        this.error = error.message || 'Failed to update cart';
        console.error('Error updating cart:', error);
        return false;
      } finally {
        this.loading = false;
      }
    },
    
    async clearCart() {
      this.loading = true;
      this.error = null;
      
      try {
        await api.clearCart();
        this.items = [];
        this.subtotal = 0;
        this.discount = 0;
        this.total = 0;
        this.appliedPromo = null;
        return true;
      } catch (error) {
        this.error = error.message || 'Failed to clear cart';
        console.error('Error clearing cart:', error);
        return false;
      } finally {
        this.loading = false;
      }
    },
    
    async applyPromoCode() {
      if (!this.promoCode) {
        return false;
      }
      
      this.loading = true;
      this.error = null;
      
      try {
        const response = await api.applyPromoCode(this.promoCode);
        this.appliedPromo = response.data.promo;
        this.calculateTotals(response.data);
        this.promoCode = '';
        return true;
      } catch (error) {
        this.error = error.message || 'Invalid promotion code';
        console.error('Error applying promo code:', error);
        return false;
      } finally {
        this.loading = false;
      }
    },
    
    calculateTotals(cartData) {
      this.subtotal = cartData.subtotal || 0;
      this.discount = cartData.discount || 0;
      this.total = cartData.total || 0;
      this.appliedPromo = cartData.applied_promo || null;
    }
  }
}); 