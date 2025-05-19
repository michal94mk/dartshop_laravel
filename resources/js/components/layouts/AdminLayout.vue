<template>
  <div class="flex h-screen bg-gray-100">
    <!-- Sidebar (statyczny na desktop, wysuwalny na mobile) -->
    <aside 
      class="fixed inset-y-0 left-0 z-50 w-64 bg-indigo-800 shadow-lg transform transition-transform duration-300 lg:relative lg:translate-x-0"
      :class="{'translate-x-0': sidebarOpen, '-translate-x-full': !sidebarOpen}"
    >
      <!-- Sidebar header -->
      <div class="flex h-16 items-center justify-between border-b border-indigo-700 px-6">
        <router-link to="/" class="text-xl font-bold text-white">
          <span>Dart</span><span class="text-indigo-200">Shop</span>
        </router-link>
        <button 
          @click="toggleSidebar" 
          class="rounded p-1 text-indigo-200 hover:bg-indigo-700 hover:text-white lg:hidden"
        >
          <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

            <!-- Sidebar navigation -->      <div class="py-4">        <div class="space-y-1 px-4">          <router-link             to="/admin/dashboard"             class="flex items-center rounded-md px-4 py-3 text-sm font-medium transition-colors duration-150"            :class="[$route.path === '/admin/dashboard' ? 'bg-indigo-700 text-white' : 'text-indigo-200 hover:bg-indigo-700 hover:text-white']"          >            <span>Dashboard</span>          </router-link>          <router-link             to="/admin/products"             class="flex items-center rounded-md px-4 py-3 text-sm font-medium transition-colors duration-150"            :class="[$route.path.includes('/admin/products') ? 'bg-indigo-700 text-white' : 'text-indigo-200 hover:bg-indigo-700 hover:text-white']"          >            <span>Produkty</span>          </router-link>          <router-link             to="/admin/categories"             class="flex items-center rounded-md px-4 py-3 text-sm font-medium transition-colors duration-150"            :class="[$route.path.includes('/admin/categories') ? 'bg-indigo-700 text-white' : 'text-indigo-200 hover:bg-indigo-700 hover:text-white']"          >            <span>Kategorie</span>          </router-link>          <router-link             to="/admin/brands"             class="flex items-center rounded-md px-4 py-3 text-sm font-medium transition-colors duration-150"            :class="[$route.path.includes('/admin/brands') ? 'bg-indigo-700 text-white' : 'text-indigo-200 hover:bg-indigo-700 hover:text-white']"          >            <span>Marki</span>          </router-link>          <router-link             to="/admin/orders"             class="flex items-center rounded-md px-4 py-3 text-sm font-medium transition-colors duration-150"            :class="[$route.path.includes('/admin/orders') ? 'bg-indigo-700 text-white' : 'text-indigo-200 hover:bg-indigo-700 hover:text-white']"          >            <span>Zamówienia</span>          </router-link>          <router-link             to="/admin/reviews"             class="flex items-center rounded-md px-4 py-3 text-sm font-medium transition-colors duration-150"            :class="[$route.path.includes('/admin/reviews') ? 'bg-indigo-700 text-white' : 'text-indigo-200 hover:bg-indigo-700 hover:text-white']"          >            <span>Recenzje</span>          </router-link>          <router-link             to="/admin/promotions"             class="flex items-center rounded-md px-4 py-3 text-sm font-medium transition-colors duration-150"            :class="[$route.path.includes('/admin/promotions') ? 'bg-indigo-700 text-white' : 'text-indigo-200 hover:bg-indigo-700 hover:text-white']"          >            <span>Promocje</span>          </router-link>          <router-link             to="/admin/tutorials"             class="flex items-center rounded-md px-4 py-3 text-sm font-medium transition-colors duration-150"            :class="[$route.path.includes('/admin/tutorials') ? 'bg-indigo-700 text-white' : 'text-indigo-200 hover:bg-indigo-700 hover:text-white']"          >            <span>Poradniki</span>          </router-link>          <router-link             to="/admin/contact-messages"             class="flex items-center rounded-md px-4 py-3 text-sm font-medium transition-colors duration-150"            :class="[$route.path.includes('/admin/contact-messages') ? 'bg-indigo-700 text-white' : 'text-indigo-200 hover:bg-indigo-700 hover:text-white']"          >            <span>Wiadomości</span>          </router-link>          <router-link             to="/admin/about"             class="flex items-center rounded-md px-4 py-3 text-sm font-medium transition-colors duration-150"            :class="[$route.path.includes('/admin/about') ? 'bg-indigo-700 text-white' : 'text-indigo-200 hover:bg-indigo-700 hover:text-white']"          >            <span>O nas</span>          </router-link>          <router-link             to="/admin/users"             class="flex items-center rounded-md px-4 py-3 text-sm font-medium transition-colors duration-150"            :class="[$route.path.includes('/admin/users') ? 'bg-indigo-700 text-white' : 'text-indigo-200 hover:bg-indigo-700 hover:text-white']"          >            <span>Użytkownicy</span>          </router-link>        </div>      </div>

      <!-- User profile section -->
      <div class="absolute bottom-0 w-full border-t border-indigo-700 p-4">
        <div class="flex items-center space-x-3">
          <div class="h-10 w-10 rounded-full bg-indigo-600 flex items-center justify-center">
            <span class="text-white font-medium">{{ userInitial }}</span>
          </div>
          <div>
            <p class="text-white">{{ userName }}</p>
            <p class="text-xs text-indigo-200">{{ userEmail }}</p>
          </div>
        </div>
        
        <div class="mt-4 space-y-2">
          <router-link to="/" class="block rounded-md px-4 py-2 text-sm text-indigo-200 hover:bg-indigo-700 hover:text-white">
            Strona główna
          </router-link>
          <router-link to="/profile" class="block rounded-md px-4 py-2 text-sm text-indigo-200 hover:bg-indigo-700 hover:text-white">
            Profil
          </router-link>
          <button @click="logout" class="block w-full rounded-md px-4 py-2 text-left text-sm text-indigo-200 hover:bg-indigo-700 hover:text-white">
            Wyloguj
          </button>
        </div>
      </div>
    </aside>

    <!-- Sidebar overlay (tylko na mobile) -->
    <div 
      @click="toggleSidebar" 
      class="fixed inset-0 z-40 bg-black bg-opacity-50 transition-opacity lg:hidden"
      :class="{'opacity-100 pointer-events-auto': sidebarOpen, 'opacity-0 pointer-events-none': !sidebarOpen}"
    ></div>

    <!-- Główna treść -->
    <div class="flex-1 flex flex-col h-screen overflow-hidden">
      <!-- Header -->
      <header class="bg-white shadow z-10">
        <div class="flex h-16 items-center justify-between px-6">
          <button 
            @click="toggleSidebar" 
            class="rounded p-1 text-gray-500 hover:bg-gray-100 hover:text-gray-700 lg:hidden"
          >
            <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
          </button>
          
          <div class="text-lg font-medium text-gray-800">
            {{ pageTitle }}
          </div>
        </div>
      </header>

      <!-- Alerts Container -->
      <alerts-container />

      <!-- Page Content - ze scrolla dla treści -->
      <main class="flex-1 overflow-y-auto p-6 bg-gray-100">
        <div class="mx-auto max-w-7xl flex flex-col">
          <div v-if="successMessage" class="mb-6 rounded relative border border-green-400 bg-green-100 px-4 py-3 text-green-700" role="alert">
            <strong class="font-bold">Sukces!</strong>
            <span class="block sm:inline">{{ successMessage }}</span>
            <button @click="alertStore.clearSuccess()" class="absolute right-0 top-0 px-4 py-3">
              <svg class="h-4 w-4 fill-current" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                <title>Close</title>
                <path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z" />
              </svg>
            </button>
          </div>
          
          <div v-if="errorMessage" class="mb-6 rounded relative border border-red-400 bg-red-100 px-4 py-3 text-red-700" role="alert">
            <strong class="font-bold">Błąd!</strong>
            <span class="block sm:inline">{{ errorMessage }}</span>
            <button @click="alertStore.clearError()" class="absolute right-0 top-0 px-4 py-3">
              <svg class="h-4 w-4 fill-current" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                <title>Close</title>
                <path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z" />
              </svg>
            </button>
          </div>

          <!-- Router-view with transitions -->
          <div class="overflow-hidden rounded-lg bg-white shadow-sm">
            <router-view v-slot="{ Component }">
              <transition 
                name="fade" 
                mode="out-in"
                enter-active-class="transition ease-out duration-200"
                enter-from-class="opacity-0 transform scale-95"
                enter-to-class="opacity-100 transform scale-100"
                leave-active-class="transition ease-in duration-150"
                leave-from-class="opacity-100 transform scale-100"
                leave-to-class="opacity-0 transform scale-95"
              >
                <component :is="Component" :key="$route.fullPath" />
              </transition>
            </router-view>
          </div>
        </div>
      </main>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue'
import { useRouter, useRoute } from 'vue-router'
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
    const route = useRoute()
    
    // Sidebar state
    const sidebarOpen = ref(false)
    
    // Toggle sidebar
    const toggleSidebar = () => {
      sidebarOpen.value = !sidebarOpen.value
    }
    
    // Page title
    const pageTitle = computed(() => {
      if (route.path === '/admin/dashboard') return 'Dashboard'
      if (route.path.includes('/admin/products')) return 'Produkty'
      if (route.path.includes('/admin/categories')) return 'Kategorie'
      if (route.path.includes('/admin/brands')) return 'Marki'
      if (route.path.includes('/admin/orders')) return 'Zamówienia'
      if (route.path.includes('/admin/reviews')) return 'Recenzje'
      if (route.path.includes('/admin/promotions')) return 'Promocje'
      if (route.path.includes('/admin/tutorials')) return 'Poradniki'
      if (route.path.includes('/admin/contact-messages')) return 'Wiadomości'
      if (route.path.includes('/admin/about')) return 'O nas'
      if (route.path.includes('/admin/users')) return 'Użytkownicy'
      return 'Panel Administratora'
    })
    
    // Sprawdź, czy używamy admin view na podstawie window.Laravel
    const isAdminView = computed(() => {
      return window.Laravel && window.Laravel.isAdmin === true;
    });

    // Sprawdź, czy użytkownik jest zalogowany i czy jest administratorem
    const isAdmin = computed(() => {
      return authStore.isAdmin;
    });

    // Jeśli nie jesteśmy w widoku admina lub użytkownik nie jest adminem, przekieruj
    onMounted(() => {
      console.log('AdminLayout mounted, isAdminView:', isAdminView.value, 'isAdmin:', isAdmin.value);
      
      // Trochę czasu na zainicjalizowanie Auth Store
      setTimeout(() => {
        if (!isAdmin.value) {
          console.warn('Non-admin user tries to access admin layout, redirecting to home');
          alertStore.error('Nie masz dostępu do panelu administracyjnego.');
          router.push('/');
        }
      }, 100);
    });
    
    // Menu state
    const userMenuOpen = ref(false)
    
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
    
    // Click outside handler to close dropdown
    const closeDropdowns = (event) => {
      if (userMenuOpen.value && !event.target.closest('button') && !event.target.closest('[role="menuitem"]')) {
        userMenuOpen.value = false
      }
    }
    
    onMounted(() => {
      document.addEventListener('click', closeDropdowns)
    })
    
    onBeforeUnmount(() => {
      document.removeEventListener('click', closeDropdowns)
    })
    
    // Logout function
    const logout = async () => {
      try {
        await authStore.logout()
        router.push('/')
      } catch (error) {
        console.error('Logout error:', error)
        alertStore.error('Wystąpił błąd podczas wylogowywania.')
      }
    }
    
    return {
      isAdmin,
      userName,
      userEmail,
      userInitial,
      userMenuOpen,
      sidebarOpen,
      toggleSidebar,
      successMessage,
      errorMessage,
      toggleUserMenu,
      logout,
      alertStore,
      pageTitle
    }
  }
}
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style> 