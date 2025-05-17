<template>
  <header class="bg-white shadow">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between h-16">
        <!-- Logo -->
        <div class="flex-shrink-0 flex items-center">
          <router-link to="/" class="text-xl font-bold text-indigo-600">
            <span>Dart</span><span class="text-gray-800">Shop</span>
          </router-link>
        </div>
        
        <!-- Navigation Links -->
        <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                    <router-link             to="/"             class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium"            :class="[$route.path === '/' ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700']"            @click.prevent="navigateTo('/', $event)"          >            Home          </router-link>
          <router-link 
            to="/products" 
            class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium"
            :class="[$route.path.includes('/categories') || $route.path.includes('/products') ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700']"
            @click.prevent="navigateTo('/products', $event)"
          >
            Produkty
          </router-link>
                    <router-link             to="/promotions"             class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium"            :class="[$route.path === '/promotions' ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700']"            @click.prevent="navigateTo('/promotions', $event)"          >            Promocje          </router-link>
                    <router-link             to="/about"             class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium"            :class="[$route.path === '/about' ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700']"            @click.prevent="navigateTo('/about', $event)"          >            O nas          </router-link>
                    <router-link             to="/tutorials"             class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium"            :class="[$route.path.includes('/tutorials') ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700']"            @click.prevent="navigateTo('/tutorials', $event)"          >            Poradniki          </router-link>
                    <router-link             to="/contact"             class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium"            :class="[$route.path === '/contact' ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700']"            @click.prevent="navigateTo('/contact', $event)"          >            Kontakt          </router-link>
        </div>

        <!-- Right side buttons -->
        <div class="flex items-center">
                    <!-- Cart -->          <router-link to="/cart" class="ml-4 px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:text-gray-900 flex items-center" @click.prevent="navigateTo('/cart', $event)">            <i class="fas fa-shopping-cart mr-1"></i>            <span class="bg-indigo-600 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs">              {{ cartItemsCount }}            </span>          </router-link>
          
          <!-- User menu -->
          <div v-if="isLoggedIn" class="ml-3 relative">
            <div>
              <button @click="toggleUserMenu" type="button" class="max-w-xs bg-white flex items-center text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" id="user-menu-button" aria-expanded="false" aria-haspopup="true">
                <span class="sr-only">Open user menu</span>
                <span class="h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center">
                  {{ userInitial }}
                </span>
              </button>
            </div>
            <div v-show="userMenuOpen" class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-50" role="menu" aria-orientation="vertical" aria-labelledby="user-menu-button" tabindex="-1">
              <router-link v-if="isAdmin" to="/admin" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">Panel Administratora</router-link>
              <router-link to="/profile" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">Twój Profil</router-link>
              <button @click="logout" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">Wyloguj</button>
            </div>
          </div>
          <div v-else class="hidden sm:flex sm:items-center sm:ml-6">
            <router-link to="/login" class="text-sm text-gray-700 hover:text-indigo-600 mr-4">Logowanie</router-link>
            <router-link to="/register" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
              Rejestracja
            </router-link>
          </div>
          
          <!-- Mobile menu button -->
          <div class="flex items-center sm:hidden">
            <button @click="toggleMobileMenu" type="button" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500" aria-controls="mobile-menu" aria-expanded="false">
              <span class="sr-only">Open main menu</span>
              <svg class="block h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
              </svg>
            </button>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Mobile menu -->
    <div v-show="mobileMenuOpen" class="sm:hidden z-40" id="mobile-menu">
      <div class="pt-2 pb-3 space-y-1">
                <router-link           to="/"           class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium"          :class="[$route.path === '/' ? 'bg-indigo-50 border-indigo-500 text-indigo-700' : 'border-transparent text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800']"          @click.prevent="navigateTo('/', $event)"        >          Home        </router-link>
                <router-link           to="/products"           class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium"          :class="[$route.path.includes('/categories') || $route.path.includes('/products') ? 'bg-indigo-50 border-indigo-500 text-indigo-700' : 'border-transparent text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800']"          @click.prevent="navigateTo('/products', $event)"        >          Produkty        </router-link>
                <router-link           to="/promotions"           class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium"          :class="[$route.path === '/promotions' ? 'bg-indigo-50 border-indigo-500 text-indigo-700' : 'border-transparent text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800']"          @click.prevent="navigateTo('/promotions', $event)"        >          Promocje        </router-link>        <router-link           to="/about"           class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium"          :class="[$route.path === '/about' ? 'bg-indigo-50 border-indigo-500 text-indigo-700' : 'border-transparent text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800']"          @click.prevent="navigateTo('/about', $event)"        >          O nas        </router-link>        <router-link           to="/tutorials"           class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium"          :class="[$route.path.includes('/tutorials') ? 'bg-indigo-50 border-indigo-500 text-indigo-700' : 'border-transparent text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800']"          @click.prevent="navigateTo('/tutorials', $event)"        >          Poradniki        </router-link>        <router-link           to="/contact"           class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium"          :class="[$route.path === '/contact' ? 'bg-indigo-50 border-indigo-500 text-indigo-700' : 'border-transparent text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800']"          @click.prevent="navigateTo('/contact', $event)"        >          Kontakt        </router-link>
      </div>
      <div class="pt-4 pb-3 border-t border-gray-200">
        <div v-if="isLoggedIn">
          <div class="flex items-center px-4">
            <div class="flex-shrink-0">
              <span class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-semibold">
                {{ userInitial }}
              </span>
            </div>
            <div class="ml-3">
              <div class="text-base font-medium text-gray-800">{{ userName }}</div>
              <div class="text-sm font-medium text-gray-500">{{ userEmail }}</div>
            </div>
          </div>
          <div class="mt-3 space-y-1">
            <router-link v-if="isAdmin" to="/admin" class="block px-4 py-2 text-base font-medium text-gray-500 hover:text-gray-800 hover:bg-gray-100">Panel Administratora</router-link>
            <router-link to="/profile" class="block px-4 py-2 text-base font-medium text-gray-500 hover:text-gray-800 hover:bg-gray-100">Twój Profil</router-link>
            <button @click="logout" class="block w-full text-left px-4 py-2 text-base font-medium text-gray-500 hover:text-gray-800 hover:bg-gray-100">Wyloguj</button>
          </div>
        </div>
        <div v-else class="mt-3 space-y-1">
          <router-link to="/login" class="block px-4 py-2 text-base font-medium text-gray-500 hover:text-gray-800 hover:bg-gray-100">Logowanie</router-link>
          <router-link to="/register" class="block px-4 py-2 text-base font-medium text-gray-500 hover:text-gray-800 hover:bg-gray-100">Rejestracja</router-link>
        </div>
      </div>
    </div>
  </header>
</template>

<script>
import { useAuthStore } from '../../stores/authStore';
import { useCartStore } from '../../stores/cartStore';
import { storeToRefs } from 'pinia';
import { useRouter } from 'vue-router';

export default {
  name: 'SiteHeader',
  data() {
    return {
      userMenuOpen: false,
      mobileMenuOpen: false
    }
  },
  setup() {
    const authStore = useAuthStore();
    const cartStore = useCartStore();
    const router = useRouter();
    
    // Używamy storeToRefs, aby zachować reaktywność getterów i state w Pinia
    const { isLoggedIn, isAdmin, userName, userEmail, userInitial } = storeToRefs(authStore);
    const { totalItems: cartItemsCount } = storeToRefs(cartStore);
    
    return {
      authStore,
      cartStore,
      router,
      isLoggedIn,
      isAdmin,
      userName,
      userEmail,
      userInitial,
      cartItemsCount
    }
  },
  mounted() {
    // Inicjalizacja auth store i cart store
    this.authStore.initAuth();
    this.cartStore.initCart();
    
    // Zamykaj dropdown przy kliknięciu poza nim
    document.addEventListener('click', this.closeDropdowns);
  },
  beforeUnmount() {
    document.removeEventListener('click', this.closeDropdowns);
  },
  methods: {
    toggleUserMenu() {
      this.userMenuOpen = !this.userMenuOpen;
    },
    toggleMobileMenu() {
      this.mobileMenuOpen = !this.mobileMenuOpen;
    },
    closeDropdowns(event) {
      // Zamknij user menu jeśli kliknięto poza nim
      if (this.userMenuOpen && !event.target.closest('#user-menu-button') && !event.target.closest('[role="menuitem"]')) {
        this.userMenuOpen = false;
      }
    },
    logout() {
      try {
        console.log('Starting logout from SiteHeader...');
        this.authStore.logout().then(() => {
          this.userMenuOpen = false;
          
          // Użyj routera zamiast window.location
          this.$router.push('/');
        }).catch(error => {
          console.error('Logout error in SiteHeader:', error);
          alert('Wystąpił błąd podczas wylogowywania.');
        });
      } catch (error) {
        console.error('Logout error in SiteHeader:', error);
        alert('Wystąpił błąd podczas wylogowywania.');
      }
    },
    navigateTo(path, event) {
      // Prevent default behavior
      if (event) {
        event.preventDefault();
      }
      
      // Close menus
      this.userMenuOpen = false;
      this.mobileMenuOpen = false;

      // Log navigation attempt
      console.log('Router navigating from', this.$route.path, 'to', path);
      
      // If navigating to same route as current, do nothing
      if (this.$route.path === path) {
        console.log('Already on path:', path);
        return;
      }
      
      // Navigate to new route
      this.$router.push(path).catch(err => {
        console.error('Navigation error:', err);
      });
    }
  }
}
</script> 