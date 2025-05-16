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
      return state.items.reduce((total, item) => total + (item.product.price * item.quantity), 0);
    },
    
    isEmpty: (state) => {
      return state.items.length === 0;
    },
    
    // Add isLoading as a getter that takes a productId parameter
    isLoadingProduct: (state) => (productId) => {
      return state.loadingProductIds.includes(productId);
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
            this.items = JSON.parse(savedCart);
          }
        }
      } catch (error) {
        console.error('Failed to initialize cart:', error);
        this.hasError = true;
        this.errorMessage = 'Nie udało się zainicjalizować koszyka';
      } finally {
        this.isLoading = false;
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
      
      if (!authStore.isLoggedIn) return;
      
      this.isLoading = true;
      
      try {
        const response = await axios.get('/api/cart');
        this.items = response.data.items;
      } catch (error) {
        console.error('Failed to fetch cart:', error);
        this.hasError = true;
        this.errorMessage = 'Nie udało się pobrać zawartości koszyka';
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
          // Znajdź produkt lub użyj zaślepki
          const product = { id: productId };
          
          // Dodaj nowy produkt do koszyka
          this.items.push({
            id: Date.now(), // Tymczasowe ID dla localStorage
            product_id: productId,
            quantity: quantity,
            product: product
          });
        }
        
        // Remove from loading products
        this.loadingProductIds = this.loadingProductIds.filter(id => id !== productId);
        
        // Zapisz zmiany w localStorage
        this.saveToLocalStorage();
      }
    },
    
    // Zmień ilość produktu w koszyku
    async updateCartItem(cartItemId, quantity) {
      const authStore = useAuthStore();
      
      if (authStore.isLoggedIn) {
        // Dla zalogowanego użytkownika: użyj API
        this.isLoading = true;
        
        try {
          const response = await axios.put(`/api/cart/${cartItemId}`, {
            quantity: quantity
          });
          
          await this.fetchCart(); // Odśwież koszyk po aktualizacji
          return response.data;
        } catch (error) {
          console.error('Failed to update cart item:', error);
          this.hasError = true;
          this.errorMessage = 'Nie udało się zaktualizować produktu w koszyku';
          throw error;
        } finally {
          this.isLoading = false;
        }
      } else {
        // Dla gościa: obsługa w localStorage
        const itemIndex = this.items.findIndex(item => item.id === cartItemId);
        
        if (itemIndex !== -1) {
          this.items[itemIndex].quantity = quantity;
          this.saveToLocalStorage();
        }
      }
    },
    
    // Usuń produkt z koszyka
    async removeFromCart(cartItemId) {
      const authStore = useAuthStore();
      
      if (authStore.isLoggedIn) {
        // Dla zalogowanego użytkownika: użyj API
        this.isLoading = true;
        
        try {
          const response = await axios.delete(`/api/cart/${cartItemId}`);
          
          await this.fetchCart(); // Odśwież koszyk po usunięciu
          return response.data;
        } catch (error) {
          console.error('Failed to remove item from cart:', error);
          this.hasError = true;
          this.errorMessage = 'Nie udało się usunąć produktu z koszyka';
          throw error;
        } finally {
          this.isLoading = false;
        }
      } else {
        // Dla gościa: obsługa w localStorage
        this.items = this.items.filter(item => item.id !== cartItemId);
        this.saveToLocalStorage();
      }
    },
    
    // Wyczyść cały koszyk
    async clearCart() {
      const authStore = useAuthStore();
      
      if (authStore.isLoggedIn) {
        // Dla zalogowanego użytkownika: usuwamy wszystkie elementy przez API
        this.isLoading = true;
        
        try {
          // Usuń każdy element pojedynczo - zakładamy, że API nie ma dedykowanego endpointu do czyszczenia
          const deletePromises = this.items.map(item => axios.delete(`/api/cart/${item.id}`));
          await Promise.all(deletePromises);
          
          this.items = [];
        } catch (error) {
          console.error('Failed to clear cart:', error);
          this.hasError = true;
          this.errorMessage = 'Nie udało się wyczyścić koszyka';
          throw error;
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