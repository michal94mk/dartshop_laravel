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
  }),
  
  getters: {
    isLoggedIn: (state) => !!state.user,
    
    userName: (state) => state.user?.name || '',
    
    userEmail: (state) => state.user?.email || '',
    
    userInitial: (state) => {
      return state.user?.name ? state.user.name.charAt(0).toUpperCase() : '';
    },
    
    isAdmin: (state) => {
      return state.user?.roles?.includes('admin') || false;
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
    // Inicjalizacja stanu autoryzacji na podstawie danych z serwera
    async initAuth() {
      if (this.user) return;
      
      this.isLoading = true;
      
      try {
        const response = await axios.get('/api/user');
        if (response.data) {
          this.user = response.data;
          
          // Pobierz uprawnienia użytkownika, jeśli istnieją
          if (response.data.permissions) {
            this.permissions = response.data.permissions;
          }
        }
      } catch (error) {
        console.error('Failed to initialize auth state:', error);
        this.hasError = true;
        this.errorMessage = 'Nie udało się pobrać danych użytkownika';
      } finally {
        this.isLoading = false;
      }
    },
    
    // Logowanie użytkownika
    async login(email, password) {
      this.isLoading = true;
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
        
        // Synchronizacja koszyka po zalogowaniu
        const cartStore = useCartStore();
        await cartStore.syncCartAfterLogin();
        
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
        
        // Synchronizacja koszyka po rejestracji i automatycznym zalogowaniu
        const cartStore = useCartStore();
        await cartStore.syncCartAfterLogin();
        
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
        await axios.post('/api/logout');
        this.user = null;
        this.permissions = [];
        
        // Zresetuj stan koszyka po wylogowaniu
        const cartStore = useCartStore();
        cartStore.$reset();
        
        return true;
      } catch (error) {
        console.error('Logout failed:', error);
        this.hasError = true;
        this.errorMessage = 'Wylogowanie nie powiodło się';
        return false;
      } finally {
        this.isLoading = false;
      }
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