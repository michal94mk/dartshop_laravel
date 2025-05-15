import './bootstrap';
import { createApp } from 'vue';
import { createPinia } from 'pinia';
import App from './App.vue';
import router from './router';

// Import FontAwesome
import '@fortawesome/fontawesome-free/css/all.css';

// Initialize Alpine.js for any legacy code that might still use it
import Alpine from 'alpinejs';
window.Alpine = Alpine;
Alpine.start();

// Create Pinia (State Management)
const pinia = createPinia();

// Create Vue App
const app = createApp(App);

// Use Plugins
app.use(router);
app.use(pinia);

// Mount the app when the DOM is ready
app.mount('#app');
