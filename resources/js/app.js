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
axios.defaults.baseURL = window.location.protocol + '//' + window.location.host;  // Używaj pełnego URL
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axios.defaults.headers.common['Accept'] = 'application/json';
axios.defaults.withCredentials = true; // Bardzo ważne dla cookies

// Upewnij się, że axios używa pełnych adresów URL, aby uniknąć problemów z cookies
const baseApiUrl = window.location.protocol + '//' + window.location.host;
console.log('Base API URL:', baseApiUrl);

// Add debugging
console.log('Axios defaults set:', {
  baseURL: axios.defaults.baseURL,
  headers: axios.defaults.headers.common,
  withCredentials: axios.defaults.withCredentials
});

// Globalny interceptor dla Axios
axios.interceptors.request.use(config => {
  console.log('Axios Request:', config);
  
  // Add a timestamp to prevent caching
  if (config.method === 'get') {
    config.params = config.params || {};
    config.params._nocache = new Date().getTime();
  }
  
  // Dodaj nagłówek X-XSRF-TOKEN dla wszystkich żądań
  const token = document.cookie.match('(^|;)\\s*XSRF-TOKEN\\s*=\\s*([^;]+)');
  if (token) {
    config.headers['X-XSRF-TOKEN'] = decodeURIComponent(token[2]);
    console.log('XSRF Token found and added to headers');
  } else {
    console.warn('No XSRF Token found in cookies');
  }
  
  return config;
});

// Dodaj interceptor do odświeżania CSRF tokenu przy błędach 401/419
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
    // Obsługa wygaśnięcia sesji/CSRF tokenu (419) lub błędów autoryzacji (401)
    if (error.response && (error.response.status === 419 || error.response.status === 401) && !error.config._retry) {
      if (!isRefreshing) {
        console.log('Session expired or CSRF token mismatch. Refreshing...');
        isRefreshing = true;
        
        try {
          // Odśwież CSRF token
          await axios.get('/sanctum/csrf-cookie', { _retry: true });
          console.log('CSRF cookie refreshed');
          
          // Ponów oryginalne żądanie
          error.config._retry = true;
          isRefreshing = false;
          return axios(error.config);
        } catch (refreshError) {
          console.error('Failed to refresh CSRF token:', refreshError);
          // Jeśli nadal nie możemy odświeżyć tokenu, wyloguj użytkownika
          const authStore = useAuthStore();
          authStore.user = null;
          authStore.authInitialized = true;
          window.location.href = '/login';
          isRefreshing = false;
        }
      }
    }
    
    // Standardowe logowanie błędów
    console.error('Axios Error:', error);
    
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
  maxToasts: 5,
  newestOnTop: true,
  position: "top-center",
  timeout: 5000,
  closeOnClick: true,
  pauseOnFocusLoss: true,
  pauseOnHover: true,
  draggable: true,
  draggablePercent: 0.6,
  showCloseButtonOnHover: false,
  hideProgressBar: false,
  closeButton: "button",
  icon: true,
  rtl: false,
  toastDefaults: {
    // Custom styling to match the app theme
    success: {
      timeout: 6000,
      icon: "🎯"
    },
    error: {
      timeout: 5000,
      icon: "❌"
    },
    warning: {
      timeout: 5000,
      icon: "⚠️"
    },
    info: {
      timeout: 4000,
      icon: "ℹ️"
    }
  }
});

// Initialize stores
const productStore = useProductStore();
const cartStore = useCartStore();
const wishlistStore = useWishlistStore();
const authStore = useAuthStore();

// Mount the app when the DOM is ready
document.addEventListener('DOMContentLoaded', async () => {
  // Inicjalizuj stan autoryzacji przed montowaniem aplikacji z mechanizmem ponownych prób
  try {
    await authStore.initAuthWithRetry(3, 1000);
    console.log('Auth initialized before app mount:', authStore.isLoggedIn ? 'User is logged in' : 'User is not logged in');
  } catch (error) {
    console.error('Failed to initialize auth before app mount:', error);
  }
  
  // Montuj aplikację po inicjalizacji stanu autoryzacji
  app.mount('#app');
  
  // Ustaw okresowe odświeżanie sesji (co 15 minut)
  if (authStore.isLoggedIn) {
    console.log('Setting up session refresh interval');
    // Odświeżaj co 15 minut (900000 ms)
    const sessionRefreshInterval = setInterval(async () => {
      if (authStore.isLoggedIn) {
        console.log('Refreshing session automatically');
        await authStore.refreshSession();
      } else {
        // Jeśli użytkownik wylogował się, zatrzymaj interwał
        clearInterval(sessionRefreshInterval);
      }
    }, 900000);
  }
});
