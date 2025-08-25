import './bootstrap';
import { createApp, type App as VueApp } from 'vue';
import { createPinia, type Pinia } from 'pinia';
import App from './App.vue';
import router from './router';
import axios, { type AxiosInstance, type InternalAxiosRequestConfig, type AxiosResponse, type AxiosError } from 'axios';

// Import Vue Toastification
import Toast, { type PluginOptions } from "vue-toastification";
import "vue-toastification/dist/index.css";

// Import FontAwesome
import '@fortawesome/fontawesome-free/css/all.css';

// Import admin components
import SearchFilters from './components/admin/SearchFilters.vue';
import LoadingSpinner from './components/admin/LoadingSpinner.vue';
import Pagination from './components/admin/Pagination.vue';
import ActionButtons from './components/admin/ActionButtons.vue';
import PageHeader from './components/admin/PageHeader.vue';
import NoDataMessage from './components/admin/NoDataMessage.vue';

// Import admin UI components
import AdminUIComponents from './components/admin/ui/index.js';

// Initialize Alpine.js for any legacy code that might still use it
import Alpine from 'alpinejs';

// Import all stores explicitly
import { useProductStore } from './stores/productStore';
import { useCartStore } from './stores/cartStore';
import { useWishlistStore } from './stores/wishlistStore';
import { useAuthStore } from './stores/authStore';
import { useCategoryStore } from './stores/categoryStore';

// Type definitions
interface GlobalAuthStore {
  isLoggedIn: boolean;
  isAdmin: boolean;
  isLoading: boolean;
  user: any;
  authInitialized: boolean;
}

declare global {
  interface Window {
    debug: (...args: any[]) => void;
    Alpine: typeof Alpine;
    axios: AxiosInstance;
    _: any;
  }
}

// Set Axios defaults
axios.defaults.baseURL = window.location.protocol + '//' + window.location.host;
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axios.defaults.headers.common['Accept'] = 'application/json';
axios.defaults.withCredentials = true;

// Debug axios configuration in development
if (import.meta.env.DEV) console.log('Base API URL:', window.location.protocol + '//' + window.location.host);

// Add debugging
if (import.meta.env.DEV) console.log('Axios defaults set:', {
  baseURL: axios.defaults.baseURL,
  headers: axios.defaults.headers.common,
  withCredentials: axios.defaults.withCredentials
});

// Global debug function
window.debug = function(...args: any[]): void {
    if (process.env.NODE_ENV === 'development') {
        console.log(...args);
    }
};

// Override console logs for production (suppress non-critical)
if (process.env.NODE_ENV === 'production') {
    const noop = function(): void {};
    console.log = noop;
    console.debug = noop;
    console.info = noop;
    // Keep console.warn/error for real issues
}

// Store authStore globally for axios interceptor
let globalAuthStore: GlobalAuthStore | null = null;

// Global interceptor for Axios
axios.interceptors.request.use((config: InternalAxiosRequestConfig): InternalAxiosRequestConfig | Promise<InternalAxiosRequestConfig> => {
  if (import.meta.env.DEV) console.log('Axios Request:', config);
  
  // Check admin API requests
  if (config.url && config.url.includes('/api/admin/')) {
    // Use global authStore if available
    if (globalAuthStore) {
      // Block admin requests if user is not logged in or not admin
      if (!globalAuthStore.isLoggedIn || !globalAuthStore.isAdmin) {
        console.log('Blocking admin API request - user not authorized:', {
          url: config.url,
          isLoggedIn: globalAuthStore.isLoggedIn,
          isAdmin: globalAuthStore.isAdmin
        });
        
        // Check if we're in the process of logging out
        if (globalAuthStore.isLoading) {
          console.log('User is logging out, allowing request to complete');
          return config;
        }
        
        // Check if we're on a non-admin page (user was redirected)
        const currentPath: string = window.location.pathname;
        if (!currentPath.startsWith('/admin')) {
          console.log('User is not on admin page, allowing request to complete');
          return config;
        }
        
        // Check if we're in the process of navigation (router is changing routes)
        if (document.visibilityState === 'hidden' || document.hidden) {
          console.log('Page is hidden, allowing request to complete');
          return config;
        }
        
        return Promise.reject(new Error('Unauthorized admin request blocked'));
      }
    }
  }
  
  // Cache busting is handled by apiService, but keep for direct axios calls
  if (config.method === 'get' && !config.url?.includes('/api/')) {
    config.params = config.params || {};
    config.params._nocache = new Date().getTime();
  }
  
  // Add X-XSRF-TOKEN header for all requests
  const token: RegExpMatchArray | null = document.cookie.match('(^|;)\\s*XSRF-TOKEN\\s*=\\s*([^;]+)');
  if (token) {
    if (!config.headers) {
      config.headers = {} as any;
    }
    config.headers['X-XSRF-TOKEN'] = decodeURIComponent(token[2]);
    if (import.meta.env.DEV) console.log('XSRF Token found and added to headers');
  } else {
    if (import.meta.env.DEV) console.warn('No XSRF Token found in cookies');
  }
  
  return config;
});

// Add interceptor to refresh CSRF token on 401/419 errors
let isRefreshing: boolean = false;

axios.interceptors.response.use(
  (response: AxiosResponse): AxiosResponse => {
    if (import.meta.env.DEV) console.log('Axios Response:', response);
    
    // Debug the raw response for API calls
    if (import.meta.env.DEV && response.config.url?.includes('/api/')) {
      console.log('Raw API response for', response.config.url.split('/').pop(), ':', response);
    }
    
    return response;
  },
  async (error: AxiosError): Promise<AxiosResponse> => {
    const status: number | undefined = error?.response?.status;
    const url: string = error?.config?.url || '';

    // For GET /api/user 401/419, do not retry or spam logs. Let callers handle guest state gracefully.
    const isUserEndpoint: boolean = typeof url === 'string' && url.includes('/api/user');
    if ((status === 401 || status === 419) && isUserEndpoint) {
      return Promise.reject(error);
    }

    // Suppress global logging for validation errors; let forms handle 422 locally
    if (status === 422) {
      return Promise.reject(error);
    }

    // Suppress logs for blocked admin requests (expected during redirects or guest state)
    if (error?.message === 'Unauthorized admin request blocked') {
      return Promise.reject(error);
    }

    // Handle session/CSRF token expiration (419) or other 401 (excluding /api/user)
    if (error.response && (status === 419 || status === 401) && !(error.config as any)._retry) {
      if (!isRefreshing) {
        console.log('Session expired or CSRF token mismatch. Refreshing...');
        isRefreshing = true;
        
        try {
          // Refresh CSRF token using full URL to avoid routing issues
          await axios.get(window.location.protocol + '//' + window.location.host + '/sanctum/csrf-cookie');
          console.log('CSRF cookie refreshed');
          
          // Retry original request
          (error.config as any)._retry = true;
          isRefreshing = false;
          return axios(error.config!);
        } catch (refreshError) {
          console.error('Failed to refresh CSRF token, forcing logout');
          // If we still can't refresh token, log out user
          if (typeof useAuthStore !== 'undefined') {
            const authStore = useAuthStore();
            authStore.user = null;
            authStore.authInitialized = true;
          }
          window.location.href = '/login?expired=1';
          isRefreshing = false;
        }
      }
    }
    
    // Standard error logging (suppress for expected 401 guest on /api/user)
    if (!(status === 401 && isUserEndpoint)) {
      console.error('Global axios error:', error);
      
      // More detailed error logging
      if (error.response) {
        console.error('Error Data:', error.response.data);
        console.error('Error Status:', error.response.status);
        console.error('Error Headers:', error.response.headers);
      } else if (error.request) {
        console.error('Error Request:', error.request);
      } else {
        console.error('Error Message:', error.message);
      }
    }
    
    return Promise.reject(error);
  }
);

// Set Alpine.js globally
window.Alpine = Alpine;
Alpine.start();

// Create Pinia (State Management)
const pinia: Pinia = createPinia();

// Create Vue App
const app: VueApp = createApp(App);

// Register global components
app.component('SearchFilters', SearchFilters);
app.component('LoadingSpinner', LoadingSpinner);
app.component('Pagination', Pagination);
app.component('ActionButtons', ActionButtons);
app.component('PageHeader', PageHeader);
app.component('NoDataMessage', NoDataMessage);

// Toast configuration
const toastOptions: PluginOptions = {
  transition: "Vue-Toastification__fade",
  maxToasts: 3,
  newestOnTop: true,
  position: "top-center" as any,
  timeout: 4000,
  closeOnClick: false,
  pauseOnFocusLoss: true,
  pauseOnHover: true,
  draggable: true,
  draggablePercent: 0.6,
  showCloseButtonOnHover: false,
  hideProgressBar: true,
  closeButton: "button",
  icon: true,
  rtl: false,
  toastClassName: "custom-toast",
  bodyClassName: ["custom-toast-body"],
  containerClassName: "custom-toast-container"
};

// Use Plugins
app.use(router);
app.use(pinia);
app.use(AdminUIComponents);
app.use(Toast, toastOptions);

// Initialize stores
const productStore = useProductStore();
const cartStore = useCartStore();
const wishlistStore = useWishlistStore();
const authStore = useAuthStore();
const categoryStore = useCategoryStore();

// Set global authStore for axios interceptor
globalAuthStore = authStore;

// Mount the app when the DOM is ready
document.addEventListener('DOMContentLoaded', (): void => {
  // Mount Vue app immediately
  app.mount('#app');
  console.log('Vue app mounted');
  
  // Hide blade loader after short delay to allow smooth transition
  setTimeout((): void => {
    const fallbackLoader: HTMLElement | null = document.getElementById('vue-fallback-loader') || document.getElementById('admin-fallback-loader');
    if (fallbackLoader) {
      fallbackLoader.style.transition = 'opacity 0.3s ease-out';
      fallbackLoader.style.opacity = '0';
      setTimeout((): void => {
        if (fallbackLoader) {
          fallbackLoader.style.display = 'none';
        }
      }, 300);
    }
  }, 800); // Hide after Vue has time to load and auth to complete
});
