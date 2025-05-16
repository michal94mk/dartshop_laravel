import './bootstrap';
import { createApp } from 'vue';
import { createPinia } from 'pinia';
import App from './App.vue';
import router from './router';
import axios from 'axios';

// Set Axios defaults
axios.defaults.baseURL = '';  // Remove base URL to use relative paths correctly
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axios.defaults.headers.common['Accept'] = 'application/json';
axios.defaults.withCredentials = true;

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
  
  return config;
});

axios.interceptors.response.use(
  response => {
    console.log('Axios Response:', response);
    
    // Debug the raw response for API calls
    if (response.config.url.includes('/api/')) {
      console.log('Raw API response for', response.config.url.split('/').pop(), ':', response);
    }
    
    return response;
  },
  error => {
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

// Use Plugins
app.use(router);
app.use(pinia);

// Initialize stores
const productStore = useProductStore();
const cartStore = useCartStore();
const wishlistStore = useWishlistStore();
const authStore = useAuthStore();

// Mount the app when the DOM is ready
document.addEventListener('DOMContentLoaded', () => {
  app.mount('#app');
});
