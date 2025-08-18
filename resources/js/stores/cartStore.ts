import { defineStore } from 'pinia';
import { apiService } from '../services/apiService';
import { useAuthStore } from './authStore';

// Type definitions
import type { Product, CartItem } from '@/types';

interface CartState {
  items: CartItem[];
  isLoading: boolean;
  loadingProductIds: number[];
  hasError: boolean;
  errorMessage: string;
}

interface AddToCartRequest {
  product_id: number;
  quantity: number;
}

interface UpdateCartItemRequest {
  quantity: number;
}

interface SyncCartRequest {
  items: Array<{
    product_id: number;
    quantity: number;
  }>;
}

interface CartResponse {
  items: CartItem[];
}

interface ApiResponse<T> {
  success: boolean;
  data: T;
  message?: string;
}

export const useCartStore = defineStore('cart', {
  state: (): CartState => ({
    items: [],
    isLoading: false,
    loadingProductIds: [],
    hasError: false,
    errorMessage: '',
  }),
  
  getters: {
    totalItems: (state): number => {
      return state.items.reduce((total, item) => total + item.quantity, 0);
    },
    
    subtotal: (state): number => {
      return state.items.reduce((total, item) => {
        // Use promotional price if available, otherwise use regular price
        const hasPromotion = item.product && item.product.promotion_price && 
                            parseFloat(item.product.promotion_price) < parseFloat(item.product.price);
        const price = hasPromotion ? 
                     parseFloat(item.product.promotion_price) : 
                     (item.product && item.product.price ? parseFloat(item.product.price) : 0);
        return total + (price * item.quantity);
      }, 0);
    },
    
    isEmpty: (state): boolean => {
      return state.items.length === 0;
    },
    
    // Add discount calculation (0 for now, can be expanded later)
    discount: (): number => {
      return 0;
    },
    
    // Calculate total after discount
    total: (state): number => {
      const subtotal = state.items.reduce((total, item) => {
        // Use promotional price if available, otherwise use regular price
        const hasPromotion = item.product && item.product.promotion_price && 
                            parseFloat(item.product.promotion_price) < parseFloat(item.product.price);
        const price = hasPromotion ? 
                     parseFloat(item.product.promotion_price) : 
                     (item.product && item.product.price ? parseFloat(item.product.price) : 0);
        return total + (price * item.quantity);
      }, 0);
      return subtotal - 0; // Discount is 0 for now
    },
    
    // Add isLoading as a getter that takes a productId parameter
    isLoadingProduct: (state) => (productId: number): boolean => {
      return state.loadingProductIds.includes(productId);
    },
    
    // Add formatted price getters
    formattedSubtotal: (state): string => {
      const subtotal = state.items.reduce((total, item) => {
        // Use promotional price if available, otherwise use regular price
        const hasPromotion = item.product && item.product.promotion_price && 
                            parseFloat(item.product.promotion_price) < parseFloat(item.product.price);
        const price = hasPromotion ? 
                     parseFloat(item.product.promotion_price) : 
                     (item.product && item.product.price ? parseFloat(item.product.price) : 0);
        return total + (price * item.quantity);
      }, 0);
      return subtotal.toFixed(2) + ' zł';
    },
    
    formattedDiscount: (): string => {
      // For now, discount is always 0
      return '0.00 zł';
    },
    
    formattedTotal: (state): string => {
      const total = state.items.reduce((total, item) => {
        // Use promotional price if available, otherwise use regular price
        const hasPromotion = item.product && item.product.promotion_price && 
                            parseFloat(item.product.promotion_price) < parseFloat(item.product.price);
        const price = hasPromotion ? 
                     parseFloat(item.product.promotion_price) : 
                     (item.product && item.product.price ? parseFloat(item.product.price) : 0);
        return total + (price * item.quantity);
      }, 0);
      // Discount is 0 for now
      return total.toFixed(2) + ' zł';
    }
  },
  
  actions: {
    // Load local cart data from localStorage on initialization
    initCart(): void {
      this.isLoading = true;
      
      try {
        // Check if user is logged in
        const authStore = useAuthStore();
        
        if (authStore.isLoggedIn) {
          // If logged in, fetch cart from API
          this.fetchCart();
        } else {
          // For guest get from localStorage
          const savedCart = localStorage.getItem('cart');
          if (savedCart) {
            try {
              const parsedCart = JSON.parse(savedCart);
              // Validate cart data structure
              if (Array.isArray(parsedCart)) {
                this.items = parsedCart;
              } else {
                console.error('Invalid cart data structure in localStorage');
                this.items = [];
                localStorage.removeItem('cart'); // Remove invalid data
              }
            } catch (e) {
              console.error('Error parsing cart from localStorage:', e);
              this.items = [];
              localStorage.removeItem('cart'); // Remove invalid data
            }
          } else {
            // Initialize with empty cart
            this.items = [];
          }
          this.isLoading = false;
          this.hasError = false;
          this.errorMessage = '';
        }
      } catch (error) {
        console.error('Failed to initialize cart:', error);
        this.hasError = true;
        this.errorMessage = 'Failed to initialize cart';
        this.isLoading = false;
        
        // Fallback to empty cart
        this.items = [];
      }
    },
    
    // Save cart to localStorage (for guests only)
    saveToLocalStorage(): void {
      const authStore = useAuthStore();
      
      if (!authStore.isLoggedIn) {
        localStorage.setItem('cart', JSON.stringify(this.items));
      }
    },
    
    // Fetch cart from API for logged in user
    async fetchCart(): Promise<void> {
      const authStore = useAuthStore();
      
      if (!authStore.isLoggedIn) {
        // For guest users, load from localStorage instead
        const savedCart = localStorage.getItem('cart');
        if (savedCart) {
          try {
            this.items = JSON.parse(savedCart);
          } catch (e) {
            console.error('Error parsing cart from localStorage:', e);
            this.items = [];
          }
        }
        return;
      }
      
      this.isLoading = true;
      
      try {
        const response = await apiService.get<CartResponse>('/cart');
        
        // apiService already handles the response format
        this.items = response.items || [];
      } catch (error: any) {
        console.error('Failed to fetch cart:', error);
        
        // If unauthorized or forbidden, fall back to localStorage
        if (error.response && (error.response.status === 401 || error.response.status === 403)) {
          // Reset auth state by clearing user data
          authStore.user = null;
          authStore.permissions = [];
          const savedCart = localStorage.getItem('cart');
          if (savedCart) {
            try {
              this.items = JSON.parse(savedCart);
            } catch (e) {
              console.error('Error parsing cart from localStorage:', e);
              this.items = [];
            }
          }
        } else {
          this.hasError = true;
          this.errorMessage = 'Failed to fetch cart contents';
        }
      } finally {
        this.isLoading = false;
      }
    },
    
    // Add product to cart
    async addToCart(productId: number, quantity: number = 1): Promise<any> {
      const authStore = useAuthStore();
      
      // Ensure quantity is a number and valid
      quantity = parseInt(quantity.toString());
      if (isNaN(quantity) || quantity < 1) {
        quantity = 1;
      }
      
      // Check if product is already being added to prevent duplicates
      if (this.loadingProductIds.includes(productId)) {
        return;
      }
      
      // Add to loading products
      this.loadingProductIds.push(productId);
      
      try {
        if (authStore.isLoggedIn) {
          // Dla zalogowanego użytkownika: użyj API
          const response = await apiService.post('/cart', {
            product_id: productId,
            quantity: quantity
          } as AddToCartRequest);
          
          await this.fetchCart(); // Refresh cart after adding
          return response;
        } else {
          // Dla gościa: obsługa w localStorage
          const existingItemIndex = this.items.findIndex(item => item.product_id === productId);
          if (existingItemIndex !== -1) {
            // Product already exists in cart, increase quantity
            this.items[existingItemIndex].quantity += quantity;
            // Uzupełnij dane produktu jeśli brakuje
            if (!this.items[existingItemIndex].product) {
              try {
                const response = await apiService.get<Product>(`/products/${productId}`);
                const product = response;
                this.items[existingItemIndex].product = product;
              } catch (error) {
                console.error('Failed to fetch product details:', error);
              }
            }
          } else {
            // Pobierz dane produktu z API
            try {
              const response = await apiService.get<Product>(`/products/${productId}`);
              const product = response;
              this.items.push({
                id: Date.now(),
                product_id: productId,
                quantity: quantity,
                product: product
              });
            } catch (error) {
              console.error('Failed to fetch product details:', error);
            }
          }
          this.saveToLocalStorage();
          
          // Return true to indicate success for guest users
          return true;
        }
      } catch (error: any) {
        console.error('Failed to add item to cart:', error);
        this.hasError = true;
        this.errorMessage = 'Failed to add product to cart';
        throw error;
      } finally {
        // Always remove from loading products, regardless of success or error
        this.loadingProductIds = this.loadingProductIds.filter(id => id !== productId);
      }
    },
    
    // Update cart item quantity
    async updateCartItem(productId: number, quantity: number): Promise<boolean> {
      const authStore = useAuthStore();
      
      try {
        if (authStore.isLoggedIn) {
          // For logged in users: find CartItem by product_id and use its ID for API call
          const item = this.items.find(item => item.product_id === productId);
          if (!item) {
            throw new Error('Cart item not found');
          }
          
          // Use CartItem ID for API call
          await apiService.put(`/cart/${item.id}`, {
            quantity: quantity
          } as UpdateCartItemRequest);
          
          // Refresh cart after updating
          await this.fetchCart();
        } else {
          // For guests: handle in localStorage
          const existingItemIndex = this.items.findIndex(item => item.product_id === productId);
          
          if (existingItemIndex !== -1) {
            this.items[existingItemIndex].quantity = quantity;
            this.saveToLocalStorage();
          }
        }
        return true;
      } catch (error: any) {
        console.error('Failed to update cart item:', error);
        this.hasError = true;
        this.errorMessage = 'Failed to update cart item';
        throw error;
      }
    },

    // Remove item from cart
    async removeFromCart(productId: number): Promise<boolean> {
      const authStore = useAuthStore();
      
      // Check if product is already being removed to prevent duplicates
      if (this.loadingProductIds.includes(productId)) {
        return false;
      }
      
      // Add to loading products
      this.loadingProductIds.push(productId);
      
      try {
        if (authStore.isLoggedIn) {
          // For logged in users: find CartItem by product_id and use its ID for API call
          const item = this.items.find(item => item.product_id === productId);
          if (!item) {
            throw new Error('Cart item not found');
          }
          
          // Use CartItem ID for API call
          await apiService.delete(`/cart/${item.id}`);
          
          // Refresh cart after removing
          await this.fetchCart();
        } else {
          // For guests: handle in localStorage
          this.items = this.items.filter(item => item.product_id !== productId);
          this.saveToLocalStorage();
        }
        
        return true;
      } catch (error: any) {
        console.error('Failed to remove item from cart:', error);
        this.hasError = true;
        this.errorMessage = 'Failed to remove item from cart';
        throw error;
      } finally {
        // Always remove from loading products, regardless of success or error
        this.loadingProductIds = this.loadingProductIds.filter(id => id !== productId);
      }
    },
    
    // Wyczyść cały koszyk
    async clearCart(): Promise<boolean> {
      const authStore = useAuthStore();
      
      try {
        this.isLoading = true;
        
        if (authStore.isLoggedIn) {
          // Dla zalogowanego użytkownika: użyj dedykowanego endpointu clear
          try {
            // Użyj dedykowanego endpointu do czyszczenia koszyka
            await apiService.delete('/cart');
          } catch (error) {
            console.error('Failed to clear cart via API:', error);
            // Kontynuuj mimo błędu API, aby wyczyścić lokalny stan
          }
        }
        
        // Zawsze wyczyść lokalny stan
        this.items = [];
        
        // Zawsze wyczyść localStorage
        localStorage.removeItem('cart');
        
        // Reset error state
        this.hasError = false;
        this.errorMessage = '';
        
        return true;
      } catch (error: any) {
        console.error('Failed to clear cart:', error);
        this.hasError = true;
        this.errorMessage = 'Nie udało się wyczyścić koszyka';
        return false;
      } finally {
        this.isLoading = false;
      }
    },
    
    // Synchronizuj koszyk z localStorage do API po zalogowaniu
    async syncCartAfterLogin(): Promise<void> {
      const savedCart = localStorage.getItem('cart');
      
      if (!savedCart) return;
      
      const cartItems: CartItem[] = JSON.parse(savedCart);
      
      if (cartItems.length === 0) return;
      
      this.isLoading = true;
      
      try {
        // Przygotuj dane do synchronizacji
        const itemsToSync = cartItems.map(item => ({
          product_id: item.product_id,
          quantity: item.quantity
        }));
        
        // Wyślij dane do API do synchronizacji
        await apiService.post('/cart/sync', { items: itemsToSync } as SyncCartRequest);
        
        // Po synchronizacji usuń dane z localStorage
        localStorage.removeItem('cart');
        
        // Pobierz zaktualizowany koszyk z API
        await this.fetchCart();
      } catch (error: any) {
        console.error('Failed to sync cart after login:', error);
        this.hasError = true;
        this.errorMessage = 'Nie udało się zsynchronizować koszyka';
      } finally {
        this.isLoading = false;
      }
    }
  }
});
