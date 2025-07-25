import { defineStore } from 'pinia';
import axios from 'axios';
import { useCartStore } from './cartStore';

// Note: Global axios interceptor is handled in app.js to avoid duplication

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    isLoading: false,
    isRegularLoading: false, // Loading for regular login
    isGoogleLoading: false,  // Loading for Google login
    hasError: false,
    errorMessage: '',
    permissions: [], // List of user permissions
    authInitialized: false // Flag indicating whether auth state has been initialized
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
    
    // Check if user has specific permission
    hasPermission: (state) => (permission) => {
      return state.permissions.includes(permission);
    },
    
    // Check if user has specific role
    hasRole: (state) => (role) => {
      return state.user?.roles?.includes(role) || false;
    },
    
    // Check if user is logged in via Google OAuth
    isGoogleUser: (state) => {
      return state.user?.is_google_user || false;
    }
  },
  
  actions: {
    // Save user data to localStorage
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
    
    // Load user data from localStorage
    loadUserFromLocalStorage() {
      try {
        const user = JSON.parse(localStorage.getItem('user'));
        const permissions = JSON.parse(localStorage.getItem('permissions')) || [];
        const authTime = parseInt(localStorage.getItem('auth_time') || '0');
        
        // Check if data is not too old (max 24 hours)
        const now = Date.now();
        const isExpired = (now - authTime) > 86400000; // 24 hours in milliseconds
        
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
    
    // Initialize auth state based on server data
    async initAuth() {
      if (this.authInitialized) {
        console.log('initAuth: Already initialized, returning existing user:', this.user?.email || 'no user');
        return this.user;
      }
      
      // First try to load from localStorage
      const loadedFromStorage = this.loadUserFromLocalStorage();
      
      this.isLoading = true;
      
      try {
        // Always refresh CSRF token before auth attempt
        await axios.get('/sanctum/csrf-cookie');
        console.log('CSRF token refreshed before auth check');
        
        // Check user status on server regardless of localStorage data
        const response = await axios.get('/api/user');
        console.log('User API response:', response);
        
        // Handle new API response format
        const userData = response.data.success ? response.data.data : response.data;
        
        if (userData) {
          this.user = userData;
          
          // Get user permissions if they exist
          if (userData.permissions) {
            this.permissions = userData.permissions;
          }
          
          // Save data to localStorage
          this.saveUserToLocalStorage();
          
          console.log('User authenticated:', this.user);
        }
        
        this.authInitialized = true;
        this.hasError = false;
        this.errorMessage = '';
        return this.user;
      } catch (error) {
        console.error('Failed to initialize auth state:', error);
        
        // Set error only if it's not 401 Unauthorized (user not logged in)
        if (error.response && error.response.status !== 401) {
          this.hasError = true;
          this.errorMessage = 'Failed to fetch user data';
        } else {
          // If user is not logged in (401), don't set error
          this.hasError = false;
          this.errorMessage = '';
          
          // If we get 401, clear all data locally
          this.user = null;
          this.permissions = [];
          localStorage.removeItem('user');
          localStorage.removeItem('permissions');
          localStorage.removeItem('auth_time');
        }
        
        // Even in case of error, mark that initialization was attempted
        this.authInitialized = true;
        return null;
      } finally {
        this.isLoading = false;
      }
    },
    
    // User login
    async login(email, password) {
      this.isRegularLoading = true;
      // Reset error state before attempting login
      this.hasError = false;
      this.errorMessage = '';
      
      try {
        // Get CSRF token
        await axios.get('/sanctum/csrf-cookie');
        console.log('CSRF cookie refreshed before login');
        
        // Make sure headers are correctly set
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        const xsrfToken = document.cookie
            .split('; ')
            .find(cookie => cookie.startsWith('XSRF-TOKEN='))
            ?.split('=')[1];
            
        const headers = {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        };
        
        if (csrfToken) {
            headers['X-CSRF-TOKEN'] = csrfToken;
        }
        
        if (xsrfToken) {
            headers['X-XSRF-TOKEN'] = decodeURIComponent(xsrfToken);
        }
        
        // Perform login
        const response = await axios.post('/api/login', {
            email,
            password
        }, { headers, withCredentials: true });
        
        // Handle new API response format
        if (response.data.success && response.data.data) {
            // New format: { success: true, data: { user, token, token_type } }
            this.user = response.data.data.user;
            this.token = response.data.data.token;
            
            // Save permissions if they exist
            if (response.data.data.user.permissions) {
                this.permissions = response.data.data.user.permissions;
            }
            
            // Save data to localStorage
            this.saveUserToLocalStorage();
            
            // Sync cart after login
            const cartStore = useCartStore();
            await cartStore.syncCartAfterLogin();
            
            // Set auth init to true and clear error status
            this.authInitialized = true;
            this.hasError = false;
            this.errorMessage = '';
            
            return true;
        }
        
        throw new Error('Invalid response format');
      } catch (error) {
        console.error('Login failed:', error);
        this.hasError = true;
        
        // Handle validation errors properly
        if (error.response?.status === 422 && error.response?.data?.errors) {
          // Extract specific validation errors
          const errors = error.response.data.errors;
          
          if (errors.email) {
            this.errorMessage = 'Nieprawidłowy adres email lub hasło.';
          } else if (errors.password) {
            this.errorMessage = 'Nieprawidłowy adres email lub hasło.';
          } else {
            // Fallback for other validation errors
            const firstError = Object.values(errors)[0];
            this.errorMessage = Array.isArray(firstError) ? firstError[0] : firstError;
          }
        } else {
          // Handle other types of errors
          this.errorMessage = error.response?.data?.message || 'Wystąpił błąd podczas logowania. Spróbuj ponownie.';
        }
        
        return false;
      } finally {
        this.isRegularLoading = false;
      }
    },
    
    // User registration
    async register(name, firstName, lastName, email, password, passwordConfirmation, privacyPolicyAccepted = false, newsletterConsent = false) {
      this.isRegularLoading = true;
      // Reset error state before attempting registration
      this.hasError = false;
      this.errorMessage = '';
      
      try {
        // Get CSRF token
        await axios.get('/sanctum/csrf-cookie');
        
        // Perform registration
        const response = await axios.post('/api/register', {
          name,
          first_name: firstName,
          last_name: lastName,
          email,
          password,
          password_confirmation: passwordConfirmation,
          privacy_policy_accepted: privacyPolicyAccepted,
          newsletter_consent: newsletterConsent
        });
        
        // Handle new API response format (BaseApiController)
        if (response.data.success && response.data.data) {
            // New format: { success: true, data: { user, token, token_type } }
            this.user = response.data.data.user;
            this.token = response.data.data.token;
            
            // Save permissions if they exist
            if (response.data.data.user.permissions) {
                this.permissions = response.data.data.user.permissions;
            }
        } else if (response.data.user) {
            // Fallback for old format: { user, token, token_type }
            this.user = response.data.user;
            this.token = response.data.token;
            
            // Save permissions if they exist
            if (response.data.user.permissions) {
                this.permissions = response.data.user.permissions;
            }
        } else {
            throw new Error('Invalid response format');
        }
        
        // Save data to localStorage
        this.saveUserToLocalStorage();
        
        // Sync cart after registration and automatic login
        const cartStore = useCartStore();
        await cartStore.syncCartAfterLogin();
        
        // Set auth init to true and clear error status
        this.authInitialized = true;
        this.hasError = false;
        this.errorMessage = '';
        
        return true;
      } catch (error) {
        console.error('Registration failed:', error);
        this.hasError = true;
        
        // Handle validation errors properly
        if (error.response?.status === 422 && error.response?.data?.errors) {
          // Extract specific validation errors
          const errors = error.response.data.errors;
          
          if (errors.email) {
            // Check if it's a custom message about unverified email
            const emailError = Array.isArray(errors.email) ? errors.email[0] : errors.email;
            if (emailError.includes('nie zostało zweryfikowane')) {
              this.errorMessage = emailError;
            } else {
              this.errorMessage = 'Podany adres email już istnieje.';
            }
          } else if (errors.password) {
            this.errorMessage = 'Hasło nie spełnia wymagań. Upewnij się, że ma co najmniej 8 znaków i jest poprawnie potwierdzone.';
          } else if (errors.terms) {
            this.errorMessage = 'Musisz zaakceptować regulamin.';
          } else if (errors.name || errors.first_name || errors.last_name) {
            this.errorMessage = 'Sprawdź poprawność wprowadzonych danych osobowych.';
          } else {
            // Fallback for other validation errors
            const firstError = Object.values(errors)[0];
            this.errorMessage = Array.isArray(firstError) ? firstError[0] : firstError;
          }
        } else {
          // Handle other types of errors
          this.errorMessage = error.response?.data?.message || 'Wystąpił błąd podczas rejestracji. Spróbuj ponownie.';
        }
        
        return false;
      } finally {
        this.isRegularLoading = false;
      }
    },
    
    // User logout
    async logout() {
      this.isLoading = true;
      
      try {
        console.log('Starting logout process...');
        
        // Get CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        
                  // Call logout API and wait for response
        try {
          const response = await fetch('/api/logout', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': csrfToken,
              'Accept': 'application/json',
              'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
          });
          
          if (!response.ok) {
            throw new Error(`Logout failed with status: ${response.status}`);
          }
          
          console.log('Logout API success:', await response.json());
        } catch (apiError) {
          console.error('Logout API error:', apiError);
          // Don't stop logout process if API fails
        }
        
        // Always clear local data after server logout attempt
        await this.forceLogout();
        
        return true;
      } catch (error) {
        console.error('Logout failed:', error);
        
        // Despite error, clear local data
        await this.forceLogout();
        
        this.hasError = true;
        this.errorMessage = 'Logout failed in API, but data was cleared locally';
        return true; // Return true to redirect user despite API error
      } finally {
        this.isLoading = false;
      }
    },
    
    // Forced logout (client-side only)
    async forceLogout() {
      // Clear user data in store
      this.user = null;
      this.permissions = [];
      this.authInitialized = true;
      
      // Remove data from localStorage
      localStorage.removeItem('user');
      localStorage.removeItem('permissions');
      localStorage.removeItem('auth_time');
      console.log('Local storage cleared');
      
      // Reset and reinitialize cart store
      const cartStore = useCartStore();
      cartStore.$reset();
      await cartStore.initCart(); // Reinitialize cart for guest mode
      console.log('Cart store reset and reinitialized');
      
      // Reset favorites store
      const { useFavoriteStore } = await import('./favoriteStore');
      const favoriteStore = useFavoriteStore();
      favoriteStore.resetStore();
      console.log('Favorites store reset');

      // Reset product store
      const { useProductStore } = await import('./productStore');
      const productStore = useProductStore();
      productStore.$reset();
      console.log('Product store reset');

      // Reset category store
      const { useCategoryStore } = await import('./categoryStore');
      const categoryStore = useCategoryStore();
      categoryStore.$reset();
      await categoryStore.fetchCategories(); // Categories are needed for guest users too
      console.log('Category store reset and reinitialized');

      // Reset wishlist store
      const { useWishlistStore } = await import('./wishlistStore');
      const wishlistStore = useWishlistStore();
      wishlistStore.$reset();
      console.log('Wishlist store reset');

      // Reset review store
      const { useReviewStore } = await import('./reviewStore');
      const reviewStore = useReviewStore();
      reviewStore.$reset();
      await reviewStore.fetchLatestReviews(); // Reviews are needed for guest users too
      console.log('Review store reset and reinitialized');
      
      // Clear all session-related cookies
      document.cookie.split(';').forEach(cookie => {
        const [name] = cookie.trim().split('=');
        document.cookie = `${name}=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;`;
      });
      console.log('Cookies cleared');
      
      // Refresh CSRF token
      try {
        await axios.get('/sanctum/csrf-cookie');
        console.log('CSRF token refreshed after logout');
      } catch (error) {
        console.error('Failed to refresh CSRF token:', error);
      }
    },
    
    // Method to refresh user session state
    async refreshSession() {
      // Don't refresh if we're not logged in
      if (!this.user) return null;
      
      console.log('Refreshing user session...');
      this.isLoading = true;
      
      try {
        // First refresh CSRF token
        await axios.get('/sanctum/csrf-cookie');
        
        // Then get current user data
        const response = await axios.get('/api/user');
        
        if (response.data) {
          this.user = response.data;
          
          // Get user permissions if they exist
          if (response.data.permissions) {
            this.permissions = response.data.permissions;
          }
          
          console.log('User session refreshed successfully');
          return this.user;
        }
        
        return null;
      } catch (error) {
        console.error('Failed to refresh user session:', error);
        
        // If we get 401 or 419 error, user is no longer logged in
        if (error.response && (error.response.status === 401 || error.response.status === 419)) {
          this.user = null;
          this.permissions = [];
        }
        
        return null;
      } finally {
        this.isLoading = false;
      }
    },
    
    // Method to initialize auth state with retry
    async initAuthWithRetry(maxRetries = 3, retryDelay = 1000) {
      let retries = 0;
      
      while (retries < maxRetries) {
        try {
          const result = await this.initAuth();
          if (result) return result;
          
          // If we didn't get user data but there was no error, just return null
          if (!this.hasError) return null;
        } catch (error) {
          console.error(`Auth initialization failed (attempt ${retries + 1}/${maxRetries}):`, error);
        }
        
        // If we're here, it means there was an error - try again after delay
        retries++;
        
        if (retries < maxRetries) {
          console.log(`Retrying auth initialization in ${retryDelay}ms...`);
          await new Promise(resolve => setTimeout(resolve, retryDelay));
          // Increase delay for each subsequent attempt
          retryDelay *= 1.5;
        }
      }
      
      console.error(`Failed to initialize auth after ${maxRetries} attempts`);
      return null;
    },
    
    // Google login (redirect flow)
    async loginWithGoogle() {
      this.isGoogleLoading = true;
      this.hasError = false;
      this.errorMessage = '';
      
      try {
        // Save current path to return after login
        const currentPath = window.location.pathname + window.location.search;
        
        // If login starts from login page, redirect to profile
        if (currentPath.includes('/login')) {
          localStorage.setItem('google_auth_redirect', '/profile');
        } else {
          localStorage.setItem('google_auth_redirect', currentPath);
        }
        
        // First get CSRF token
        await axios.get('/sanctum/csrf-cookie');
        
        // Get Google redirect URL
        const redirectResponse = await axios.get('/api/auth/google/redirect');
        
        if (!redirectResponse.data.success || !redirectResponse.data.data?.url) {
          throw new Error('Failed to get Google redirect URL');
        }
        
        console.log('Redirecting to Google auth:', redirectResponse.data.data.url);
        
        // Redirect to Google OAuth (without popup)
        window.location.href = redirectResponse.data.data.url;
        
        // This function won't return a value because the page will be redirected
        return true;
        
      } catch (error) {
        console.error('Google login error:', error);
        this.hasError = true;
        this.errorMessage = error.response?.data?.message || 'Error during Google login';
        return false;
      } finally {
        this.isGoogleLoading = false;
      }
    },
    
    // Resend email verification link
    async resendVerificationEmail() {
      this.isLoading = true;
      this.hasError = false;
      this.errorMessage = '';
      
      try {
        // Get CSRF token
        await axios.get('/sanctum/csrf-cookie');
        
        // Send resend verification request
        const response = await axios.post('/api/email/verification-notification');
        
        return response.data.message || 'Verification link has been sent again.';
      } catch (error) {
        console.error('Resend verification failed:', error);
        this.hasError = true;
        this.errorMessage = error.response?.data?.message || 'Failed to send verification link.';
        return false;
      } finally {
        this.isLoading = false;
      }
    },

    // Refresh user data (to update email_verified_at after verification)
    async refreshUser() {
      try {
        console.log('Starting user refresh...');
        
        // First refresh CSRF token
        await axios.get('/sanctum/csrf-cookie');
        console.log('CSRF token refreshed before user check');
        
        const response = await axios.get('/api/user');
        console.log('User API response:', response.data);
        
        // Handle new API response format
        const userData = response.data.success ? response.data.data : response.data;
        
        if (userData) {
          // Create a new object to ensure reactivity
          this.user = { ...userData };
          
          // Get user permissions if they exist
          if (userData.permissions) {
            this.permissions = [...userData.permissions];
          }
          
          // Save updated data to localStorage
          this.saveUserToLocalStorage();
          
          // Mark as initialized
          this.authInitialized = true;
          
          console.log('User refreshed successfully:', {
            email: this.user.email,
            isLoggedIn: this.isLoggedIn,
            emailVerified: !!this.user.email_verified_at,
            isAdmin: this.isAdmin
          });
          
          return this.user;
        }
        
        // If no user data, clear the state
        this.user = null;
        this.permissions = [];
        localStorage.removeItem('user');
        localStorage.removeItem('permissions');
        localStorage.removeItem('auth_time');
        
        return null;
      } catch (error) {
        console.error('Failed to refresh user data:', error);
        
        // If we get 401, clear user data
        if (error.response && error.response.status === 401) {
          this.user = null;
          this.permissions = [];
          localStorage.removeItem('user');
          localStorage.removeItem('permissions');
          localStorage.removeItem('auth_time');
          
          // Try to refresh CSRF token and retry once
          try {
            await axios.get('/sanctum/csrf-cookie');
            const retryResponse = await axios.get('/api/user');
            
            // Handle new API response format for retry
            const retryUserData = retryResponse.data.success ? retryResponse.data.data : retryResponse.data;
            
            if (retryUserData) {
              this.user = { ...retryUserData };
              if (retryUserData.permissions) {
                this.permissions = [...retryUserData.permissions];
              }
              this.saveUserToLocalStorage();
              return this.user;
            }
          } catch (retryError) {
            console.error('Retry failed:', retryError);
          }
        }
        
        return null;
      }
    },
    
    // Update user profile
    async updateProfile(userData) {
      this.isLoading = true;
      this.hasError = false;
      this.errorMessage = '';
      
      try {
        // Get CSRF token
        await axios.get('/sanctum/csrf-cookie');
        
        // Send update profile request
        const response = await axios.put('/api/user/profile', userData);
        
        // Update user data in store
        this.user = response.data.user;
        
        return true;
      } catch (error) {
        console.error('Profile update failed:', error);
        this.hasError = true;
        this.errorMessage = error.response?.data?.message || 'Profile update failed.';
        return false;
      } finally {
        this.isLoading = false;
      }
    },
    
    // Change password
    async updatePassword(currentPassword, newPassword, newPasswordConfirmation) {
      this.isLoading = true;
      this.hasError = false;
      this.errorMessage = '';
      
      try {
        // Get CSRF token
        await axios.get('/sanctum/csrf-cookie');
        
        // Send password change request
        const response = await axios.put('/api/user/password', {
          current_password: currentPassword,
          password: newPassword,
          password_confirmation: newPasswordConfirmation
        });
        
        return true;
      } catch (error) {
        console.error('Password change failed:', error);
        this.hasError = true;
        this.errorMessage = error.response?.data?.message || 'Password change failed.';
        return false;
      } finally {
        this.isLoading = false;
      }
    }
  }
}); 