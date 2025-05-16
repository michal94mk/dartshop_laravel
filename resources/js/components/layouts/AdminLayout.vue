<template>
  <div class="min-h-screen bg-gray-100">
    <!-- Alerts Container -->
    <alerts-container />
    
    <!-- Top navigation -->
    <nav class="bg-indigo-800 border-b border-indigo-700">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
          <div class="flex">
            <!-- Logo -->
            <div class="shrink-0 flex items-center">
              <router-link to="/" class="text-white font-bold text-xl">
                <span>Dart</span><span class="text-indigo-200">Shop</span>
              </router-link>
            </div>

            <!-- Navigation Links -->
            <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
              <router-link 
                to="/admin/dashboard" 
                class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5"
                :class="[$route.path === '/admin/dashboard' ? 'border-white text-white' : 'border-transparent text-indigo-200 hover:text-white hover:border-indigo-300']"
              >
                Dashboard
              </router-link>
              <router-link 
                to="/admin/products" 
                class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5"
                :class="[$route.path.includes('/admin/products') ? 'border-white text-white' : 'border-transparent text-indigo-200 hover:text-white hover:border-indigo-300']"
              >
                Produkty
              </router-link>
              <router-link 
                to="/admin/categories" 
                class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5"
                :class="[$route.path.includes('/admin/categories') ? 'border-white text-white' : 'border-transparent text-indigo-200 hover:text-white hover:border-indigo-300']"
              >
                Kategorie
              </router-link>
              <router-link 
                to="/admin/orders" 
                class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5"
                :class="[$route.path.includes('/admin/orders') ? 'border-white text-white' : 'border-transparent text-indigo-200 hover:text-white hover:border-indigo-300']"
              >
                Zamówienia
              </router-link>
              <router-link 
                to="/admin/users" 
                class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5"
                :class="[$route.path.includes('/admin/users') ? 'border-white text-white' : 'border-transparent text-indigo-200 hover:text-white hover:border-indigo-300']"
              >
                Użytkownicy
              </router-link>
            </div>
          </div>

          <div class="hidden sm:flex sm:items-center sm:ml-6">
            <!-- Front Page Link -->
            <router-link to="/" class="ml-3 text-indigo-200 hover:text-white text-sm">
              <i class="fas fa-home mr-1"></i> Strona główna
            </router-link>

            <!-- Profile dropdown -->
            <div class="ml-3 relative">
              <div>
                <button @click="toggleUserMenu" type="button" class="flex items-center text-sm font-medium text-white hover:text-indigo-100 focus:outline-none transition duration-150 ease-in-out">
                  <div>{{ userName }}</div>
                  <div class="ml-1">
                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                  </div>
                </button>
              </div>

              <div v-show="userMenuOpen" class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-50" role="menu" aria-orientation="vertical">
                <router-link to="/profile" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">
                  Profil
                </router-link>
                <button @click="logout" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">
                  Wyloguj
                </button>
              </div>
            </div>
          </div>

          <!-- Mobile menu button -->
          <div class="-mr-2 flex items-center sm:hidden">
            <button @click="toggleMobileMenu" type="button" class="inline-flex items-center justify-center p-2 rounded-md text-indigo-200 hover:text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500">
              <span class="sr-only">Open main menu</span>
              <svg :class="{ 'hidden': mobileMenuOpen, 'block': !mobileMenuOpen }" class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
              </svg>
              <svg :class="{ 'hidden': !mobileMenuOpen, 'block': mobileMenuOpen }" class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>
        </div>
      </div>

      <!-- Mobile menu -->
      <div v-show="mobileMenuOpen" class="sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
          <router-link to="/admin/dashboard" class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium" :class="[$route.path === '/admin/dashboard' ? 'bg-indigo-50 border-indigo-500 text-indigo-700' : 'border-transparent text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800']">
            Dashboard
          </router-link>
          <router-link to="/admin/products" class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium" :class="[$route.path.includes('/admin/products') ? 'bg-indigo-50 border-indigo-500 text-indigo-700' : 'border-transparent text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800']">
            Produkty
          </router-link>
          <router-link to="/admin/categories" class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium" :class="[$route.path.includes('/admin/categories') ? 'bg-indigo-50 border-indigo-500 text-indigo-700' : 'border-transparent text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800']">
            Kategorie
          </router-link>
          <router-link to="/admin/orders" class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium" :class="[$route.path.includes('/admin/orders') ? 'bg-indigo-50 border-indigo-500 text-indigo-700' : 'border-transparent text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800']">
            Zamówienia
          </router-link>
          <router-link to="/admin/users" class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium" :class="[$route.path.includes('/admin/users') ? 'bg-indigo-50 border-indigo-500 text-indigo-700' : 'border-transparent text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800']">
            Użytkownicy
          </router-link>
          <router-link to="/" class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800">
            Strona główna
          </router-link>
        </div>

        <!-- Mobile user menu -->
        <div class="pt-4 pb-3 border-t border-indigo-700">
          <div class="flex items-center px-4">
            <div class="flex-shrink-0">
              <div class="h-10 w-10 rounded-full bg-indigo-600 flex items-center justify-center">
                <span class="text-white font-medium">{{ userInitial }}</span>
              </div>
            </div>
            <div class="ml-3">
              <div class="text-base font-medium text-white">{{ userName }}</div>
              <div class="text-sm font-medium text-indigo-200">{{ userEmail }}</div>
            </div>
          </div>
          <div class="mt-3 space-y-1">
            <router-link to="/profile" class="block px-4 py-2 text-base font-medium text-indigo-200 hover:text-white hover:bg-indigo-700">
              Profil
            </router-link>
            <button @click="logout" class="block w-full text-left px-4 py-2 text-base font-medium text-indigo-200 hover:text-white hover:bg-indigo-700">
              Wyloguj
            </button>
          </div>
        </div>
      </div>
    </nav>

    <!-- Page Content -->
    <main>
      <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div v-if="successMessage" class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
            <strong class="font-bold">Sukces!</strong>
            <span class="block sm:inline">{{ successMessage }}</span>
            <button @click="alertStore.clearSuccess()" class="absolute top-0 right-0 px-4 py-3">
              <svg class="h-4 w-4 fill-current" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                <title>Close</title>
                <path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z" />
              </svg>
            </button>
          </div>
          
          <div v-if="errorMessage" class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
            <strong class="font-bold">Błąd!</strong>
            <span class="block sm:inline">{{ errorMessage }}</span>
            <button @click="alertStore.clearError()" class="absolute top-0 right-0 px-4 py-3">
              <svg class="h-4 w-4 fill-current" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                <title>Close</title>
                <path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z" />
              </svg>
            </button>
          </div>

          <router-view v-slot="{ Component }">
            <transition name="fade" mode="out-in">
              <component :is="Component" :key="$route.fullPath" />
            </transition>
          </router-view>
        </div>
      </div>
    </main>
  </div>
</template>

<script>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../../stores/authStore'
import { useAlertStore } from '../../stores/alertStore'
import AlertsContainer from '../ui/AlertsContainer.vue'

export default {
  name: 'AdminLayout',
  components: {
    AlertsContainer
  },
  setup() {
    const authStore = useAuthStore()
    const alertStore = useAlertStore()
    const router = useRouter()
    
    // Menu state
    const userMenuOpen = ref(false)
    const mobileMenuOpen = ref(false)
    
    // Computed properties
    const userName = computed(() => authStore.user?.name || '')
    const userEmail = computed(() => authStore.user?.email || '')
    const userInitial = computed(() => {
      return userName.value ? userName.value.charAt(0).toUpperCase() : '';
    })
    
    // Alert messages
    const successMessage = computed(() => alertStore.successMessage)
    const errorMessage = computed(() => alertStore.errorMessage)
    
    // Toggle menu functions
    const toggleUserMenu = () => {
      userMenuOpen.value = !userMenuOpen.value
    }
    
    const toggleMobileMenu = () => {
      mobileMenuOpen.value = !mobileMenuOpen.value
    }
    
    // Click outside handler to close dropdown
    const closeDropdowns = (event) => {
      if (userMenuOpen.value && !event.target.closest('button')) {
        userMenuOpen.value = false
      }
    }
    
    // Logout function
    const logout = async () => {
      await authStore.logout()
      router.push('/')
    }
    
    // Add event listener on mount
    onMounted(() => {
      document.addEventListener('click', closeDropdowns)
      
      // Check if user is admin
      if (!authStore.isAdmin) {
        router.push('/')
      }
    })
    
    // Remove event listener before unmount
    onBeforeUnmount(() => {
      document.removeEventListener('click', closeDropdowns)
    })
    
    return {
      userMenuOpen,
      mobileMenuOpen,
      userName,
      userEmail,
      userInitial,
      successMessage,
      errorMessage,
      toggleUserMenu,
      toggleMobileMenu,
      logout,
      alertStore
    }
  }
}
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.15s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style> 