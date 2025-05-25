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
        // Ensure product.price exists and is a number
        const price = item.product && item.product.price ? parseFloat(item.product.price) : 0;
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
        const price = item.product && item.product.price ? parseFloat(item.product.price) : 0;
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
        const price = item.product && item.product.price ? parseFloat(item.product.price) : 0;
        return total + (price * item.quantity);
      }, 0);
      // Discount is 0 for now
      return total.toFixed(2) + ' zł';
    }
  },
  
  actions: {
    // Check if a specific product is in loading state
    isLoading(productId) {
      return this.loadingProductIds.includes(productId);
    },
    
    // Wczytaj lokalne dane koszyka z localStorage przy inicjalizacji
    initCart() {
      this.isLoading = true;
      
      try {
        // Sprawdź, czy użytkownik jest zalogowany
        const authStore = useAuthStore();
        
        if (authStore.isLoggedIn) {
          // Jeśli zalogowany, pobierz koszyk z API
          this.fetchCart();
        } else {
          // Dla gościa pobierz z localStorage
          const savedCart = localStorage.getItem('cart');
          if (savedCart) {
            try {
              this.items = JSON.parse(savedCart);
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
        }
      } catch (error) {
        console.error('Failed to initialize cart:', error);
        this.hasError = true;
        this.errorMessage = 'Nie udało się zainicjalizować koszyka';
        this.isLoading = false;
        
        // Fallback to empty cart
        this.items = [];
      }
    },
    
    // Zapisz koszyk w localStorage (tylko dla gości)
    saveToLocalStorage() {
      const authStore = useAuthStore();
      
      if (!authStore.isLoggedIn) {
        localStorage.setItem('cart', JSON.stringify(this.items));
      }
    },
    
    // Pobierz koszyk z API dla zalogowanego użytkownika
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
          this.errorMessage = 'Nie udało się pobrać zawartości koszyka';
        }
      } finally {
        this.isLoading = false;
      }
    },
    
    // Dodaj produkt do koszyka
    async addToCart(productId, quantity = 1) {
      const authStore = useAuthStore();
      
      // Add to loading products
      this.loadingProductIds.push(productId);
      
      if (authStore.isLoggedIn) {
        // Dla zalogowanego użytkownika: użyj API
        this.isLoading = true;
        
        try {
          const response = await axios.post('/api/cart', {
            product_id: productId,
            quantity: quantity
          });
          
          await this.fetchCart(); // Odśwież koszyk po dodaniu
          return response.data;
        } catch (error) {
          console.error('Failed to add item to cart:', error);
          this.hasError = true;
          this.errorMessage = 'Nie udało się dodać produktu do koszyka';
          throw error;
        } finally {
          this.isLoading = false;
          // Remove from loading products
          this.loadingProductIds = this.loadingProductIds.filter(id => id !== productId);
        }
      } else {
        // Dla gościa: obsługa w localStorage
        const existingItemIndex = this.items.findIndex(item => item.product.id === productId);
        
        if (existingItemIndex !== -1) {
          // Produkt już istnieje w koszyku, zwiększ ilość
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
        
        // Remove from loading products
        this.loadingProductIds = this.loadingProductIds.filter(id => id !== productId);
        
        // Zapisz zmiany w localStorage
        this.saveToLocalStorage();
        
        // Return true to indicate success for guest users
        return true;
      }
    },
    
    // Zmień ilość produktu w koszyku
    async updateCartItem(itemId, quantity) {
      const authStore = useAuthStore();
      
      if (authStore.isLoggedIn) {
        // Dla zalogowanego użytkownika: użyj API
        this.isLoading = true;
        
        try {
          const response = await axios.put(`/api/cart/${itemId}`, {
            quantity: quantity
          });
          
          await this.fetchCart(); // Odśwież koszyk po aktualizacji
          return response.data;
        } catch (error) {
          console.error('Failed to update cart item:', error);
          
          // Jeśli element nie istnieje (404), odśwież koszyk
          if (error.response && error.response.status === 404) {
            console.log(`Cart item ${itemId} not found, refreshing cart`);
            await this.fetchCart();
            this.hasError = true;
            this.errorMessage = 'Element koszyka już nie istnieje. Koszyk został odświeżony.';
          } else {
            this.hasError = true;
            this.errorMessage = 'Nie udało się zaktualizować produktu w koszyku';
            throw error;
          }
        } finally {
          this.isLoading = false;
        }
      } else {
        // Dla gościa: obsługa w localStorage
        // Sprawdź czy itemId jest id produktu czy id koszyka
        const itemIndex = this.items.findIndex(item => 
          item.id === itemId || item.product_id === itemId || item.product.id === itemId
        );
        
        if (itemIndex !== -1) {
          this.items[itemIndex].quantity = quantity;
          this.saveToLocalStorage();
        }
      }
    },
    
    // Usuń produkt z koszyka
    async removeFromCart(itemId) {
      const authStore = useAuthStore();
      
      if (authStore.isLoggedIn) {
        // Dla zalogowanego użytkownika: użyj API
        this.isLoading = true;
        
        try {
          const response = await axios.delete(`/api/cart/${itemId}`);
          
          await this.fetchCart(); // Odśwież koszyk po usunięciu
          return response.data;
        } catch (error) {
          console.error('Failed to remove item from cart:', error);
          
          // Jeśli element nie istnieje (404), odśwież koszyk
          if (error.response && error.response.status === 404) {
            console.log(`Cart item ${itemId} not found, refreshing cart`);
            await this.fetchCart();
            this.hasError = true;
            this.errorMessage = 'Element koszyka już nie istnieje. Koszyk został odświeżony.';
          } else {
            this.hasError = true;
            this.errorMessage = 'Nie udało się usunąć produktu z koszyka';
            throw error;
          }
        } finally {
          this.isLoading = false;
        }
      } else {
        // Dla gościa: obsługa w localStorage
        // Need to check all possible ID matches 
        this.items = this.items.filter(item => {
          // If none of these match, keep the item
          return !(
            item.id === itemId || 
            item.product_id === itemId || 
            (item.product && item.product.id === itemId)
          );
        });
        
        this.saveToLocalStorage();
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