import './bootstrap';
import { createApp } from 'vue';
import { createPinia } from 'pinia';
import App from './App.vue';
import router from './router';
import axios from 'axios';

// Import Vue Toastification
import Toast from "vue-toastification";
import "vue-toastification/dist/index.css";

// Set Axios defaults
axios.defaults.baseURL = window.location.protocol + '//' + window.location.host;  // Use full URL
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axios.defaults.headers.common['Accept'] = 'application/json';
axios.defaults.withCredentials = true; // Very important for cookies

// Make sure axios uses full URLs to avoid cookie issues
const baseApiUrl = window.location.protocol + '//' + window.location.host;
console.log('Base API URL:', baseApiUrl);

// Add debugging
console.log('Axios defaults set:', {
  baseURL: axios.defaults.baseURL,
  headers: axios.defaults.headers.common,
  withCredentials: axios.defaults.withCredentials
});

// Global debug function
window.debug = function(...args) {
    if (process.env.NODE_ENV === 'development') {
        console.log(...args);
    }
};

// Override console.log for production
if (process.env.NODE_ENV === 'production') {
    console.log = function() {};
    console.debug = function() {};
    console.info = function() {};
}

// Global interceptor for Axios
axios.interceptors.request.use(config => {
  console.log('Axios Request:', config);
  
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
        const currentPath = window.location.pathname;
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
  
  // Add a timestamp to prevent caching
  if (config.method === 'get') {
    config.params = config.params || {};
    config.params._nocache = new Date().getTime();
  }
  
  // Add X-XSRF-TOKEN header for all requests
  const token = document.cookie.match('(^|;)\\s*XSRF-TOKEN\\s*=\\s*([^;]+)');
  if (token) {
    config.headers['X-XSRF-TOKEN'] = decodeURIComponent(token[2]);
    console.log('XSRF Token found and added to headers');
  } else {
    console.warn('No XSRF Token found in cookies');
  }
  
  return config;
});

// Add interceptor to refresh CSRF token on 401/419 errors
let isRefreshing = false;

axios.interceptors.response.use(
  response => {
    console.log('Axios Response:', response);
    
    // Debug the raw response for API calls
    if (response.config.url.includes('/api/')) {
      console.log('Raw API response for', response.config.url.split('/').pop(), ':', response);
    }
    
    return response;
  },
  async error => {
    // Handle session/CSRF token expiration (419) or authorization errors (401)
    if (error.response && (error.response.status === 419 || error.response.status === 401) && !error.config._retry) {
      if (!isRefreshing) {
        console.log('Session expired or CSRF token mismatch. Refreshing...');
        isRefreshing = true;
        
        try {
          // Refresh CSRF token
          await axios.get('/sanctum/csrf-cookie');
          console.log('CSRF cookie refreshed');
          
          // Retry original request
          error.config._retry = true;
          isRefreshing = false;
          return axios(error.config);
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
    
    // Standard error logging
    console.error('Global axios error:', error);
    
    // More detailed error logging
    if (error.response) {
      // The request was made and the server responded with a status code
      // that falls out of the range of 2xx
      console.error('Error Data:', error.response.data);
      console.error('Error Status:', error.response.status);
      console.error('Error Headers:', error.response.headers);
    } else if (error.request) {
      // The request was made but no response was received
      console.error('Error Request:', error.request);
    } else {
      // Something happened in setting up the request that triggered an Error
      console.error('Error Message:', error.message);
    }
    
    return Promise.reject(error);
  }
);

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
window.Alpine = Alpine;
Alpine.start();

// Import all stores explicitly
import { useProductStore } from './stores/productStore';
import { useCartStore } from './stores/cartStore';
import { useWishlistStore } from './stores/wishlistStore';
import { useAuthStore } from './stores/authStore';
import { useCategoryStore } from './stores/categoryStore';

// Store authStore globally for axios interceptor
let globalAuthStore = null;

// Create Pinia (State Management)
const pinia = createPinia();

// Create Vue App
const app = createApp(App);

// Register global components
app.component('SearchFilters', SearchFilters);
app.component('LoadingSpinner', LoadingSpinner);
app.component('Pagination', Pagination);
app.component('ActionButtons', ActionButtons);
app.component('PageHeader', PageHeader);
app.component('NoDataMessage', NoDataMessage);

// Use Plugins
app.use(router);
app.use(pinia);
app.use(AdminUIComponents);
app.use(Toast, {
  transition: "Vue-Toastification__bounce",
  maxToasts: 3,
  newestOnTop: true,
  position: "top-center",
  timeout: 4000,
  closeOnClick: true,
  pauseOnFocusLoss: true,
  pauseOnHover: true,
  draggable: true,
  draggablePercent: 0.6,
  showCloseButtonOnHover: false,
  hideProgressBar: false,
  closeButton: "button",
  icon: false,
  rtl: false
});

// Initialize stores
const productStore = useProductStore();
const cartStore = useCartStore();
const wishlistStore = useWishlistStore();
const authStore = useAuthStore();
const categoryStore = useCategoryStore();

// Set global authStore for axios interceptor
globalAuthStore = authStore;

// Mount the app when the DOM is ready
document.addEventListener('DOMContentLoaded', () => {
  // Mount Vue app immediately
  app.mount('#app');
  console.log('Vue app mounted');
  
  // Hide blade loader after short delay to allow smooth transition
  setTimeout(() => {
    const fallbackLoader = document.getElementById('vue-fallback-loader') || document.getElementById('admin-fallback-loader');
    if (fallbackLoader) {
      fallbackLoader.style.transition = 'opacity 0.3s ease-out';
      fallbackLoader.style.opacity = '0';
      setTimeout(() => {
        fallbackLoader.style.display = 'none';
      }, 300);
    }
  }, 800); // Hide after Vue has time to load and auth to complete
});
