import { defineStore } from 'pinia';
import axios from 'axios';
import { useCartStore } from './cartStore';

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    isLoading: false,
    hasError: false,
    errorMessage: '',
    permissions: [], // Lista uprawnień użytkownika
    authInitialized: false // Flaga wskazująca, czy stan autoryzacji został zainicjalizowany
  }),
  
  getters: {
    isLoggedIn: (state) => !!state.user,
    
    userName: (state) => state.user?.name || '',
    
    userEmail: (state) => state.user?.email || '',
    
    userInitial: (state) => {
      return state.user?.name ? state.user.name.charAt(0).toUpperCase() : '';
    },
    
    isAdmin: (state) => {
      return state.user?.is_admin || (state.user?.roles?.includes('admin')) || false;
    },
    
    isEmailVerified: (state) => {
      return !!state.user?.email_verified_at;
    },
    
    // Sprawdzanie czy użytkownik ma dane uprawnienie
    hasPermission: (state) => (permission) => {
      return state.permissions.includes(permission);
    },
    
    // Sprawdzanie czy użytkownik ma daną rolę
    hasRole: (state) => (role) => {
      return state.user?.roles?.includes(role) || false;
    }
  },
  
  actions: {
    // Zapisz dane użytkownika do localStorage
    saveUserToLocalStorage() {
      if (this.user) {
        localStorage.setItem('user', JSON.stringify(this.user));
        localStorage.setItem('permissions', JSON.stringify(this.permissions));
        localStorage.setItem('auth_time', Date.now().toString());
      } else {
        localStorage.removeItem('user');
        localStorage.removeItem('permissions');
        localStorage.removeItem('auth_time');
      }
    },
    
    // Załaduj dane użytkownika z localStorage
    loadUserFromLocalStorage() {
      try {
        const user = JSON.parse(localStorage.getItem('user'));
        const permissions = JSON.parse(localStorage.getItem('permissions')) || [];
        const authTime = parseInt(localStorage.getItem('auth_time') || '0');
        
        // Sprawdź, czy dane nie są zbyt stare (max 1 godzina)
        const now = Date.now();
        const isExpired = (now - authTime) > 3600000; // 1 godzina w milisekundach
        
        if (user && !isExpired) {
          this.user = user;
          this.permissions = permissions;
          return true;
        }
        
        return false;
      } catch (error) {
        console.error('Error loading user data from localStorage:', error);
        return false;
      }
    },
    
    // Inicjalizacja stanu autoryzacji na podstawie danych z serwera
    async initAuth() {
      if (this.authInitialized && this.user) return this.user;
      
      // Najpierw spróbuj załadować z localStorage
      const loadedFromStorage = this.loadUserFromLocalStorage();
      if (loadedFromStorage) {
        console.log('User data loaded from localStorage');
        this.authInitialized = true;
        return this.user;
      }
      
      this.isLoading = true;
      
      try {
        const response = await axios.get('/api/user');
        if (response.data) {
          this.user = response.data;
          
          // Pobierz uprawnienia użytkownika, jeśli istnieją
          if (response.data.permissions) {
            this.permissions = response.data.permissions;
          }
          
          // Zapisz dane do localStorage
          this.saveUserToLocalStorage();
          
          console.log('User authenticated:', this.user);
        }
        
        this.authInitialized = true;
        this.hasError = false;
        this.errorMessage = '';
        return this.user;
      } catch (error) {
        console.error('Failed to initialize auth state:', error);
        
        // Ustaw błąd tylko jeśli nie jest to 401 Unauthorized (użytkownik nie zalogowany)
        if (error.response && error.response.status !== 401) {
          this.hasError = true;
          this.errorMessage = 'Nie udało się pobrać danych użytkownika';
        } else {
          // Jeśli użytkownik nie jest zalogowany (401), to nie ustawiaj błędu
          this.hasError = false;
          this.errorMessage = '';
        }
        
        // Nawet w przypadku błędu oznacz, że próbowano zainicjalizować stan
        this.authInitialized = true;
        return null;
      } finally {
        this.isLoading = false;
      }
    },
    
    // Logowanie użytkownika
    async login(email, password) {
      this.isLoading = true;
      // Reset error state before attempting login
      this.hasError = false;
      this.errorMessage = '';
      
      try {
        // Uzyskaj CSRF token
        await axios.get('/sanctum/csrf-cookie');
        
        // Wykonaj logowanie
        const response = await axios.post('/api/login', {
          email,
          password
        });
        
        this.user = response.data.user;
        
        // Zapisz uprawnienia, jeśli istnieją
        if (response.data.user.permissions) {
          this.permissions = response.data.user.permissions;
        }
        
        // Zapisz dane do localStorage
        this.saveUserToLocalStorage();
        
        // Synchronizacja koszyka po zalogowaniu
        const cartStore = useCartStore();
        await cartStore.syncCartAfterLogin();
        
        // Ustaw auth init na true i wyczyść status błędu
        this.authInitialized = true;
        this.hasError = false;
        this.errorMessage = '';
        
        return true;
      } catch (error) {
        console.error('Login failed:', error);
        this.hasError = true;
        this.errorMessage = error.response?.data?.message || 'Logowanie nie powiodło się';
        return false;
      } finally {
        this.isLoading = false;
      }
    },
    
    // Rejestracja użytkownika
    async register(name, email, password, passwordConfirmation) {
      this.isLoading = true;
      // Reset error state before attempting registration
      this.hasError = false;
      this.errorMessage = '';
      
      try {
        // Uzyskaj CSRF token
        await axios.get('/sanctum/csrf-cookie');
        
        // Wykonaj rejestrację
        const response = await axios.post('/api/register', {
          name,
          email,
          password,
          password_confirmation: passwordConfirmation
        });
        
        this.user = response.data.user;
        
        // Zapisz uprawnienia, jeśli istnieją
        if (response.data.user.permissions) {
          this.permissions = response.data.user.permissions;
        }
        
        // Zapisz dane do localStorage
        this.saveUserToLocalStorage();
        
        // Synchronizacja koszyka po rejestracji i automatycznym zalogowaniu
        const cartStore = useCartStore();
        await cartStore.syncCartAfterLogin();
        
        // Ustaw auth init na true i wyczyść status błędu
        this.authInitialized = true;
        this.hasError = false;
        this.errorMessage = '';
        
        return true;
      } catch (error) {
        console.error('Registration failed:', error);
        this.hasError = true;
        this.errorMessage = error.response?.data?.message || 'Rejestracja nie powiodła się';
        return false;
      } finally {
        this.isLoading = false;
      }
    },
    
    // Wylogowanie użytkownika
    async logout() {
      this.isLoading = true;
      
      try {
        console.log('Starting logout process...');

        // Niezależnie od wyniku API, wyczyść dane lokalnie
        this.user = null;
        this.permissions = [];
        this.authInitialized = true; 
        
        // Usuń dane z localStorage
        localStorage.removeItem('user');
        localStorage.removeItem('permissions');
        localStorage.removeItem('auth_time');
        console.log('Local storage cleared');
        
        // Zresetuj stan koszyka po wylogowaniu
        const cartStore = useCartStore();
        cartStore.$reset();
        console.log('Cart store reset');
        
        // Pobierz CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        
        // Wywołaj API wylogowania w tle, ale nie czekaj na odpowiedź
        fetch('/api/logout', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
          },
          credentials: 'same-origin'
        }).catch(error => {
          console.error('Logout API error (non-blocking):', error);
        });
        
        return true;
      } catch (error) {
        console.error('Logout failed:', error);
        
        // Mimo błędu, wyczyść dane lokalnie
        this.user = null;
        this.permissions = [];
        this.authInitialized = true;
        localStorage.removeItem('user');
        localStorage.removeItem('permissions');
        localStorage.removeItem('auth_time');
        
        this.hasError = true;
        this.errorMessage = 'Wylogowanie nie powiodło się w API, ale dane zostały wyczyszczone lokalnie';
        return true; // Zwróć true, aby przekierować użytkownika mimo błędu API
      } finally {
        this.isLoading = false;
      }
    },
    
    // Metoda do odświeżania stanu sesji użytkownika
    async refreshSession() {
      // Nie odświeżaj, jeśli nie jesteśmy zalogowani
      if (!this.user) return null;
      
      console.log('Refreshing user session...');
      this.isLoading = true;
      
      try {
        // Najpierw odśwież CSRF token
        await axios.get('/sanctum/csrf-cookie');
        
        // Następnie pobierz aktualne dane użytkownika
        const response = await axios.get('/api/user');
        
        if (response.data) {
          this.user = response.data;
          
          // Pobierz uprawnienia użytkownika, jeśli istnieją
          if (response.data.permissions) {
            this.permissions = response.data.permissions;
          }
          
          console.log('User session refreshed successfully');
          return this.user;
        }
        
        return null;
      } catch (error) {
        console.error('Failed to refresh user session:', error);
        
        // Jeśli otrzymamy błąd 401 lub 419, użytkownik nie jest już zalogowany
        if (error.response && (error.response.status === 401 || error.response.status === 419)) {
          this.user = null;
          this.permissions = [];
        }
        
        return null;
      } finally {
        this.isLoading = false;
      }
    },
    
    // Metoda do inicjalizacji stanu autoryzacji z ponawianiem próby
    async initAuthWithRetry(maxRetries = 3, retryDelay = 1000) {
      let retries = 0;
      
      while (retries < maxRetries) {
        try {
          const result = await this.initAuth();
          if (result) return result;
          
          // Jeśli nie uzyskaliśmy danych użytkownika, ale nie było błędu, po prostu zwróć null
          if (!this.hasError) return null;
        } catch (error) {
          console.error(`Auth initialization failed (attempt ${retries + 1}/${maxRetries}):`, error);
        }
        
        // Jeśli jesteśmy tu, znaczy to, że wystąpił błąd - spróbuj ponownie po opóźnieniu
        retries++;
        
        if (retries < maxRetries) {
          console.log(`Retrying auth initialization in ${retryDelay}ms...`);
          await new Promise(resolve => setTimeout(resolve, retryDelay));
          // Zwiększ opóźnienie dla każdej kolejnej próby
          retryDelay *= 1.5;
        }
      }
      
      console.error(`Failed to initialize auth after ${maxRetries} attempts`);
      return null;
    },
    
    // Wysłanie ponownie linku do weryfikacji e-mail
    async resendVerificationEmail() {
      this.isLoading = true;
      this.hasError = false;
      this.errorMessage = '';
      
      try {
        // Uzyskaj CSRF token
        await axios.get('/sanctum/csrf-cookie');
        
        // Wyślij żądanie ponownego wysłania weryfikacji
        const response = await axios.post('/api/email/verification-notification');
        
        return response.data.message || 'Link weryfikacyjny został wysłany ponownie.';
      } catch (error) {
        console.error('Resend verification failed:', error);
        this.hasError = true;
        this.errorMessage = error.response?.data?.message || 'Nie udało się wysłać linku weryfikacyjnego.';
        return false;
      } finally {
        this.isLoading = false;
      }
    },
    
    // Aktualizacja profilu użytkownika
    async updateProfile(userData) {
      this.isLoading = true;
      this.hasError = false;
      this.errorMessage = '';
      
      try {
        // Uzyskaj CSRF token
        await axios.get('/sanctum/csrf-cookie');
        
        // Wyślij żądanie aktualizacji profilu
        const response = await axios.put('/api/user/profile', userData);
        
        // Aktualizuj dane użytkownika w store
        this.user = response.data.user;
        
        return true;
      } catch (error) {
        console.error('Profile update failed:', error);
        this.hasError = true;
        this.errorMessage = error.response?.data?.message || 'Aktualizacja profilu nie powiodła się.';
        return false;
      } finally {
        this.isLoading = false;
      }
    },
    
    // Zmiana hasła
    async updatePassword(currentPassword, newPassword, newPasswordConfirmation) {
      this.isLoading = true;
      this.hasError = false;
      this.errorMessage = '';
      
      try {
        // Uzyskaj CSRF token
        await axios.get('/sanctum/csrf-cookie');
        
        // Wyślij żądanie zmiany hasła
        const response = await axios.put('/api/user/password', {
          current_password: currentPassword,
          password: newPassword,
          password_confirmation: newPasswordConfirmation
        });
        
        return true;
      } catch (error) {
        console.error('Password update failed:', error);
        this.hasError = true;
        this.errorMessage = error.response?.data?.message || 'Zmiana hasła nie powiodła się.';
        return false;
      } finally {
        this.isLoading = false;
      }
    }
  }
}); 