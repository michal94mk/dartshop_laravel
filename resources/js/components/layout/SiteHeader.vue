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
        <div class="hidden lg:ml-6 lg:flex lg:space-x-8">
          <router-link 
            to="/" 
            class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium"
            :class="[$route.path === '/' ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700']"
            @click.prevent="navigateTo('/', $event)"
          >
            Home
          </router-link>
          
          <!-- Products with dropdown -->
          <div class="relative flex items-center" @mouseenter="showProductsDropdown = true" @mouseleave="showProductsDropdown = false">
            <router-link 
              to="/products" 
              class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium h-16"
              :class="[$route.path.includes('/categories') || $route.path.includes('/products') ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700']"
              @click.prevent="navigateTo('/products', $event)"
            >
              Produkty
              <svg class="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
              </svg>
            </router-link>
            
            <!-- Dropdown Menu -->
            <div 
              v-show="showProductsDropdown"
              class="absolute left-0 top-full mt-1 w-56 origin-top-left bg-white border border-gray-200 divide-y divide-gray-100 rounded-md shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none z-50"
            >
              <div class="py-1">
                <router-link 
                  to="/products" 
                  class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-600"
                  @click="showProductsDropdown = false"
                >
                  <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                  </svg>
                  Wszystkie produkty
                </router-link>
              </div>
              <div class="py-1">
                <div class="px-4 py-2">
                  <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Kategorie</p>
                </div>
                <router-link 
                  v-for="category in topCategories"
                  :key="category.id"
                  :to="`/products?category=${category.id}`" 
                  class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-600"
                  @click="showProductsDropdown = false"
                >
                  <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                  </svg>
                  {{ category.name }}
                  <span class="ml-auto text-xs text-gray-400">({{ category.products_count }})</span>
                </router-link>
              </div>
            </div>
          </div>
          
          <router-link 
            to="/promotions" 
            class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium"
            :class="[$route.path === '/promotions' ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700']"
            @click.prevent="navigateTo('/promotions', $event)"
          >
            Promocje
          </router-link>
          <router-link 
            to="/about" 
            class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium"
            :class="[$route.path === '/about' ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700']"
            @click.prevent="navigateTo('/about', $event)"
          >
            O nas
          </router-link>
          <router-link 
            to="/tutorials" 
            class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium"
            :class="[$route.path.includes('/tutorials') ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700']"
            @click.prevent="navigateTo('/tutorials', $event)"
          >
            Poradniki
          </router-link>
          <router-link 
            to="/contact" 
            class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium"
            :class="[$route.path === '/contact' ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700']"
            @click.prevent="navigateTo('/contact', $event)"
          >
            Kontakt
          </router-link>
        </div>

        <!-- Right side buttons -->
        <div class="flex items-center">
          <!-- Cart -->
          <router-link 
            to="/cart" 
            class="ml-4 px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:text-gray-900 flex items-center" 
            @click.prevent="navigateTo('/cart', $event)"
          >
            <i class="fas fa-shopping-cart mr-1"></i>
            <span class="bg-indigo-600 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs">
              {{ cartItemsCount }}
            </span>
          </router-link>
          
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
          <div v-else class="hidden lg:flex lg:items-center lg:ml-6">
            <router-link to="/login" class="text-sm text-gray-700 hover:text-indigo-600 mr-4">Logowanie</router-link>
            <router-link to="/register" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
              Rejestracja
            </router-link>
          </div>
          
          <!-- Mobile menu button -->
          <div class="flex items-center lg:hidden">
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

    <!-- Search Bar Section -->
    <div class="bg-gradient-to-r from-indigo-50 via-blue-50 to-purple-50 border-t border-gray-100">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <div class="flex justify-center">
          <div class="w-full max-w-2xl">
            <product-search />
          </div>
        </div>
      </div>
    </div>
    
    <!-- Mobile menu -->
    <div v-show="mobileMenuOpen" class="lg:hidden z-40" id="mobile-menu">
      <!-- Mobile Search -->
      <div class="px-4 py-3 border-b border-gray-200">
        <product-search />
      </div>
      
      <div class="pt-2 pb-3 space-y-1">
        <router-link
          to="/"
          class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium"
          :class="[$route.path === '/' ? 'bg-indigo-50 border-indigo-500 text-indigo-700' : 'border-transparent text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800']"
          @click.prevent="navigateTo('/', $event)"
        >
          Home
        </router-link>
        
        <!-- Products with mobile dropdown -->
        <div class="border-l-4 border-transparent">
          <router-link
            to="/products"
            class="block pl-3 pr-4 py-2 text-base font-medium"
            :class="[$route.path.includes('/categories') || $route.path.includes('/products') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-800']"
            @click.prevent="navigateTo('/products', $event)"
          >
            Produkty
          </router-link>
          
          <!-- Mobile categories submenu -->
          <div class="pl-6 space-y-1">
            <router-link
              to="/products"
              class="block pl-3 pr-4 py-2 text-sm text-gray-500 hover:text-gray-700 hover:bg-gray-50"
              @click="mobileMenuOpen = false"
            >
              Wszystkie produkty
            </router-link>
            <router-link
              v-for="category in topCategories"
              :key="`mobile-${category.id}`"
              :to="`/products?category=${category.id}`"
              class="block pl-3 pr-4 py-2 text-sm text-gray-500 hover:text-gray-700 hover:bg-gray-50"
              @click="mobileMenuOpen = false"
            >
              {{ category.name }}
              <span class="text-xs text-gray-400 ml-1">({{ category.products_count }})</span>
            </router-link>
          </div>
        </div>
        
        <router-link
          to="/promotions"
          class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium"
          :class="[$route.path === '/promotions' ? 'bg-indigo-50 border-indigo-500 text-indigo-700' : 'border-transparent text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800']"
          @click.prevent="navigateTo('/promotions', $event)"
        >
          Promocje
        </router-link>
        <router-link
          to="/about"
          class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium"
          :class="[$route.path === '/about' ? 'bg-indigo-50 border-indigo-500 text-indigo-700' : 'border-transparent text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800']"
          @click.prevent="navigateTo('/about', $event)"
        >
          O nas
        </router-link>
        <router-link
          to="/tutorials"
          class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium"
          :class="[$route.path.includes('/tutorials') ? 'bg-indigo-50 border-indigo-500 text-indigo-700' : 'border-transparent text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800']"
          @click.prevent="navigateTo('/tutorials', $event)"
        >
          Poradniki
        </router-link>
        <router-link
          to="/contact"
          class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium"
          :class="[$route.path === '/contact' ? 'bg-indigo-50 border-indigo-500 text-indigo-700' : 'border-transparent text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800']"
          @click.prevent="navigateTo('/contact', $event)"
        >
          Kontakt
        </router-link>
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
import { useCategoryStore } from '../../stores/categoryStore';
import { storeToRefs } from 'pinia';
import { useRouter } from 'vue-router';
import { computed } from 'vue';
import ProductSearch from '../ui/ProductSearch.vue';

export default {
  name: 'SiteHeader',
  components: {
    ProductSearch
  },
  data() {
    return {
      userMenuOpen: false,
      mobileMenuOpen: false,
      showProductsDropdown: false
    }
  },
  setup() {
    const authStore = useAuthStore();
    const cartStore = useCartStore();
    const categoryStore = useCategoryStore();
    const router = useRouter();
    
    // Używamy storeToRefs, aby zachować reaktywność getterów i state w Pinia
    const { isLoggedIn, isAdmin, userName, userEmail, userInitial } = storeToRefs(authStore);
    const { totalItems: cartItemsCount } = storeToRefs(cartStore);
    
    // Computed property for top categories (first 5 categories with products)
    const topCategories = computed(() => {
      return categoryStore.orderedCategories
        .filter(cat => cat.is_active && cat.products_count > 0)
        .slice(0, 5); // Show only first 5 categories in dropdown
    });
    
    return {
      authStore,
      cartStore,
      categoryStore,
      router,
      isLoggedIn,
      isAdmin,
      userName,
      userEmail,
      userInitial,
      cartItemsCount,
      topCategories
    }
  },
  mounted() {
    // Inicjalizacja auth store i cart store
    this.authStore.initAuth();
    this.cartStore.initCart();
    this.categoryStore.fetchCategories();
    
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
      
      // Zamknij products dropdown jeśli kliknięto poza nim
      if (this.showProductsDropdown && !event.target.closest('.relative')) {
        this.showProductsDropdown = false;
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