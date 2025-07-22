import { defineStore } from 'pinia';
import axios from 'axios';
import { useAuthStore } from './authStore';

export const useCartStore = defineStore('cart', {
  state: () => ({
    items: [],
    isLoading: false,
    loadingProductIds: [],
    hasError: false,
    errorMessage: '',
  }),
  
  getters: {
    totalItems: (state) => {
      return state.items.reduce((total, item) => total + item.quantity, 0);
    },
    
    subtotal: (state) => {
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
    
    isEmpty: (state) => {
      return state.items.length === 0;
    },
    
    // Add discount calculation (0 for now, can be expanded later)
    discount: () => {
      return 0;
    },
    
    // Calculate total after discount
    total: (state, getters) => {
      return getters.subtotal - getters.discount;
    },
    
    // Add isLoading as a getter that takes a productId parameter
    isLoadingProduct: (state) => (productId) => {
      return state.loadingProductIds.includes(productId);
    },
    
    // Add formatted price getters
    formattedSubtotal: (state) => {
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
    
    formattedDiscount: (state) => {
      // For now, discount is always 0
      return '0.00 zł';
    },
    
    formattedTotal: (state) => {
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
    initCart() {
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
    saveToLocalStorage() {
      const authStore = useAuthStore();
      
      if (!authStore.isLoggedIn) {
        localStorage.setItem('cart', JSON.stringify(this.items));
      }
    },
    
    // Fetch cart from API for logged in user
    async fetchCart() {
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
        const response = await axios.get('/api/cart');
        this.items = response.data.items || [];
      } catch (error) {
        console.error('Failed to fetch cart:', error);
        
        // If unauthorized or forbidden, fall back to localStorage
        if (error.response && (error.response.status === 401 || error.response.status === 403)) {
          authStore.isLoggedIn = false; // Reset auth state
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
    async addToCart(productId, quantity = 1) {
      const authStore = useAuthStore();
      
      // Ensure quantity is a number and valid
      quantity = parseInt(quantity);
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
          const response = await axios.post('/api/cart', {
            product_id: productId,
            quantity: quantity
          });
          
          await this.fetchCart(); // Refresh cart after adding
          return response.data;
        } else {
          // Dla gościa: obsługa w localStorage
          const existingItemIndex = this.items.findIndex(item => item.product_id === productId);
          
          if (existingItemIndex !== -1) {
            // Product already exists in cart, increase quantity
            this.items[existingItemIndex].quantity += quantity;
          } else {
            // Najpierw pobierz dane produktu z API
            try {
              const response = await axios.get(`/api/products/${productId}`);
              const product = response.data;
              
              // Dodaj nowy produkt do koszyka z pełnymi danymi
              this.items.push({
                id: Date.now(), // Tymczasowe ID dla localStorage
                product_id: productId,
                quantity: quantity,
                product: product
              });
            } catch (error) {
              console.error('Failed to fetch product details:', error);
              // Jeśli nie udało się pobrać danych produktu, użyj podstawowych informacji
              const product = { id: productId, name: 'Produkt', price: 0 };
              
              this.items.push({
                id: Date.now(),
                product_id: productId,
                quantity: quantity,
                product: product
              });
            }
          }
          
          // Zapisz zmiany w localStorage
          this.saveToLocalStorage();
          
          // Return true to indicate success for guest users
          return true;
        }
      } catch (error) {
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
    async updateCartItem(productId, quantity) {
      const authStore = useAuthStore();
      
      try {
        if (authStore.isLoggedIn) {
          // For logged in users: find CartItem by product_id and use its ID for API call
          const item = this.items.find(item => item.product_id === productId);
          if (!item) {
            throw new Error('Cart item not found');
          }
          
          // Use CartItem ID for API call
          await axios.put(`/api/cart/${item.id}`, {
            quantity: quantity
          });
          
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
      } catch (error) {
        console.error('Failed to update cart item:', error);
        this.hasError = true;
        this.errorMessage = 'Failed to update cart item';
        throw error;
      }
    },

    // Remove item from cart
    async removeFromCart(productId) {
      const authStore = useAuthStore();
      
      // Check if product is already being removed to prevent duplicates
      if (this.loadingProductIds.includes(productId)) {
        return;
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
          await axios.delete(`/api/cart/${item.id}`);
          
          // Refresh cart after removing
          await this.fetchCart();
        } else {
          // For guests: handle in localStorage
          this.items = this.items.filter(item => item.product_id !== productId);
          this.saveToLocalStorage();
        }
        
        return true;
      } catch (error) {
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
    async clearCart() {
      const authStore = useAuthStore();
      
      if (authStore.isLoggedIn) {
        // Dla zalogowanego użytkownika: użyj dedykowanego endpointu clear
        this.isLoading = true;
        
        try {
          // Użyj dedykowanego endpointu do czyszczenia koszyka
          await axios.delete('/api/cart');
          
          // Wyczyść lokalny stan
          this.items = [];
          
        } catch (error) {
          console.error('Failed to clear cart:', error);
          
          // Jeśli nie udało się wyczyścić przez API, spróbuj pobrać aktualny stan
          try {
            await this.fetchCart();
          } catch (fetchError) {
            console.error('Failed to fetch cart after clear error:', fetchError);
            this.hasError = true;
            this.errorMessage = 'Nie udało się wyczyścić koszyka';
          }
        } finally {
          this.isLoading = false;
        }
      } else {
        // Dla gościa: czyścimy localStorage
        this.items = [];
        localStorage.removeItem('cart');
      }
    },
    
    // Synchronizuj koszyk z localStorage do API po zalogowaniu
    async syncCartAfterLogin() {
      const savedCart = localStorage.getItem('cart');
      
      if (!savedCart) return;
      
      const cartItems = JSON.parse(savedCart);
      
      if (cartItems.length === 0) return;
      
      this.isLoading = true;
      
      try {
        // Przygotuj dane do synchronizacji
        const itemsToSync = cartItems.map(item => ({
          product_id: item.product_id,
          quantity: item.quantity
        }));
        
        // Wyślij dane do API do synchronizacji
        await axios.post('/api/cart/sync', { items: itemsToSync });
        
        // Po synchronizacji usuń dane z localStorage
        localStorage.removeItem('cart');
        
        // Pobierz zaktualizowany koszyk z API
        await this.fetchCart();
      } catch (error) {
        console.error('Failed to sync cart after login:', error);
        this.hasError = true;
        this.errorMessage = 'Nie udało się zsynchronizować koszyka';
      } finally {
        this.isLoading = false;
      }
    }
  }
}); 