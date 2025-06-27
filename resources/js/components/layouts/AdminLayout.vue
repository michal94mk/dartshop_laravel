<template>
  <div class="h-screen bg-gray-100 overflow-hidden">
    <!-- Top Header with Admin info -->
    <div class="fixed top-0 left-0 right-0 z-50 bg-indigo-800 shadow-lg">
      <div class="flex h-16 items-center justify-between px-6">
        <!-- Left side - Admin info -->
        <div class="flex items-center space-x-4">
          <div class="flex items-center space-x-3">
            <div class="h-8 w-8 rounded-full bg-indigo-600 flex items-center justify-center">
              <span class="text-white font-medium text-sm">{{ userInitial }}</span>
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-white text-sm font-medium">Admin</p>
              <p class="text-xs text-indigo-200 truncate">{{ userEmail }}</p>
            </div>
          </div>
        </div>
        
        <!-- Right side - Navigation options -->
        <div class="flex items-center space-x-1">
          <router-link to="/" class="rounded-md px-3 py-2 text-sm font-medium text-indigo-200 hover:bg-indigo-700 hover:text-white transition-colors">
            Strona główna
          </router-link>
          <router-link to="/profile" class="rounded-md px-3 py-2 text-sm font-medium text-indigo-200 hover:bg-indigo-700 hover:text-white transition-colors">
            Profil
          </router-link>
          <button @click="logout" class="rounded-md px-3 py-2 text-sm font-medium text-indigo-200 hover:bg-indigo-700 hover:text-white transition-colors">
            Wyloguj
          </button>
          
          <!-- Mobile menu button -->
          <button 
            @click="toggleSidebar" 
            class="ml-4 rounded p-1 text-indigo-200 hover:bg-indigo-700 hover:text-white lg:hidden"
          >
            <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
          </button>
        </div>
      </div>
    </div>

    <!-- Sidebar (przyklejony na desktop, wysuwalny na mobile) -->
    <aside 
      class="fixed top-0 bottom-0 left-0 z-40 bg-indigo-800 shadow-lg transform transition-transform duration-300 lg:translate-x-0 flex flex-col h-screen"
      :class="{'translate-x-0': sidebarOpen, '-translate-x-full': !sidebarOpen}"
      style="width: 256px;"
    >
      <!-- Sidebar header -->
      <div class="flex h-16 items-center justify-between border-b border-indigo-700 px-6 flex-shrink-0 mt-16">
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

      <!-- Sidebar navigation -->
      <div class="flex-1 py-4 overflow-y-auto">
        <div class="space-y-1 px-3">
          <!-- Dashboard -->
          <router-link 
            to="/admin/dashboard"
            class="flex items-center rounded-md px-3 py-2 text-sm font-medium transition-colors duration-150"
            :class="[$route.path === '/admin/dashboard' ? 'bg-indigo-700 text-white' : 'text-indigo-200 hover:bg-indigo-700 hover:text-white']"
          >
            <svg class="w-4 h-4 mr-3" fill="currentColor" viewBox="0 0 20 20">
              <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
            </svg>
            <span>Dashboard</span>
          </router-link>

          <!-- Zarządzanie sklepem -->
          <div class="pt-3">
            <div class="px-3 py-1">
              <p class="text-xs font-semibold text-indigo-300 uppercase tracking-wider">Sklep</p>
            </div>
            <router-link 
              to="/admin/products"
              class="flex items-center rounded-md px-3 py-2 text-sm font-medium transition-colors duration-150"
              :class="[$route.path.includes('/admin/products') ? 'bg-indigo-700 text-white' : 'text-indigo-200 hover:bg-indigo-700 hover:text-white']"
            >
              <svg class="w-4 h-4 mr-3" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 2L3 7v11a1 1 0 001 1h12a1 1 0 001-1V7l-7-5z" clip-rule="evenodd"/>
              </svg>
              <span>Produkty</span>
            </router-link>
            <router-link 
              to="/admin/categories"
              class="flex items-center rounded-md px-3 py-2 text-sm font-medium transition-colors duration-150"
              :class="[$route.path.includes('/admin/categories') ? 'bg-indigo-700 text-white' : 'text-indigo-200 hover:bg-indigo-700 hover:text-white']"
            >
              <svg class="w-4 h-4 mr-3" fill="currentColor" viewBox="0 0 20 20">
                <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
              </svg>
              <span>Kategorie</span>
            </router-link>
            <router-link 
              to="/admin/brands"
              class="flex items-center rounded-md px-3 py-2 text-sm font-medium transition-colors duration-150"
              :class="[$route.path.includes('/admin/brands') ? 'bg-indigo-700 text-white' : 'text-indigo-200 hover:bg-indigo-700 hover:text-white']"
            >
              <svg class="w-4 h-4 mr-3" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M17.707 9.293a1 1 0 010 1.414l-7 7a1 1 0 01-1.414 0l-7-7A.997.997 0 012 10V5a3 3 0 013-3h5c.256 0 .512.098.707.293l7 7zM5 6a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/>
              </svg>
              <span>Marki</span>
            </router-link>
            <router-link 
              to="/admin/promotions"
              class="flex items-center rounded-md px-3 py-2 text-sm font-medium transition-colors duration-150"
              :class="[$route.path.includes('/admin/promotions') ? 'bg-indigo-700 text-white' : 'text-indigo-200 hover:bg-indigo-700 hover:text-white']"
            >
              <svg class="w-4 h-4 mr-3" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M12.395 2.553a1 1 0 00-1.45-.385c-.345.23-.614.558-.822.88-.214.33-.403.713-.57 1.116-.334.804-.614 1.768-.84 2.734a31.365 31.365 0 00-.613 3.58 2.64 2.64 0 01-.945-1.067c-.328-.68-.398-1.534-.398-2.654A1 1 0 005.05 6.05 6.981 6.981 0 003 11a7 7 0 1011.95-4.95c-.592-.591-.98-.985-1.348-1.467-.363-.476-.724-1.063-1.207-2.03zM12.12 15.12A3 3 0 017 13s.879.5 2.5.5c0-1 .5-4 1.25-4.5.5 1 .786 1.293 1.371 1.879A2.99 2.99 0 0113 13a2.99 2.99 0 01-.879 2.121z" clip-rule="evenodd"/>
              </svg>
              <span>Promocje</span>
            </router-link>
          </div>

          <!-- Zamówienia i klienci -->
          <div class="pt-3">
            <div class="px-3 py-1">
              <p class="text-xs font-semibold text-indigo-300 uppercase tracking-wider">Klienci</p>
            </div>
            <router-link 
              to="/admin/orders"
              class="flex items-center rounded-md px-3 py-2 text-sm font-medium transition-colors duration-150"
              :class="[$route.path.includes('/admin/orders') ? 'bg-indigo-700 text-white' : 'text-indigo-200 hover:bg-indigo-700 hover:text-white']"
            >
              <svg class="w-4 h-4 mr-3" fill="currentColor" viewBox="0 0 20 20">
                <path d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 000-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 3H6.28l-.31-1.243A1 1 0 005 1H3zM16 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM6.5 18a1.5 1.5 0 100-3 1.5 1.5 0 000 3z"/>
              </svg>
              <span>Zamówienia</span>
            </router-link>
            <router-link 
              to="/admin/users"
              class="flex items-center rounded-md px-3 py-2 text-sm font-medium transition-colors duration-150"
              :class="[$route.path.includes('/admin/users') ? 'bg-indigo-700 text-white' : 'text-indigo-200 hover:bg-indigo-700 hover:text-white']"
            >
              <svg class="w-4 h-4 mr-3" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
              </svg>
              <span>Użytkownicy</span>
            </router-link>
            <router-link 
              to="/admin/reviews"
              class="flex items-center rounded-md px-3 py-2 text-sm font-medium transition-colors duration-150"
              :class="[$route.path.includes('/admin/reviews') ? 'bg-indigo-700 text-white' : 'text-indigo-200 hover:bg-indigo-700 hover:text-white']"
            >
              <svg class="w-4 h-4 mr-3" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
              </svg>
              <span>Recenzje produktów</span>
            </router-link>
          </div>

          <!-- Treści -->
          <div class="pt-3">
            <div class="px-3 py-1">
              <p class="text-xs font-semibold text-indigo-300 uppercase tracking-wider">Treści</p>
            </div>
            <router-link 
              to="/admin/tutorials"
              class="flex items-center rounded-md px-3 py-2 text-sm font-medium transition-colors duration-150"
              :class="[$route.path.includes('/admin/tutorials') ? 'bg-indigo-700 text-white' : 'text-indigo-200 hover:bg-indigo-700 hover:text-white']"
            >
              <svg class="w-4 h-4 mr-3" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M3 5a1 1 0 011-1h12a1 1 0 011 1v8a1 1 0 01-1 1H4a1 1 0 01-1-1V5zM6 7v2h2V7H6zm6 0v6h2V7h-2z" clip-rule="evenodd"/>
              </svg>
              <span>Poradniki</span>
            </router-link>
            <router-link 
              to="/admin/about"
              class="flex items-center rounded-md px-3 py-2 text-sm font-medium transition-colors duration-150"
              :class="[$route.path.includes('/admin/about') ? 'bg-indigo-700 text-white' : 'text-indigo-200 hover:bg-indigo-700 hover:text-white']"
            >
              <svg class="w-4 h-4 mr-3" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
              </svg>
              <span>O nas</span>
            </router-link>
          </div>

          <!-- Komunikacja -->
          <div class="pt-3">
            <div class="px-3 py-1">
              <p class="text-xs font-semibold text-indigo-300 uppercase tracking-wider">Komunikacja</p>
            </div>
            <router-link 
              to="/admin/contact-messages"
              class="flex items-center rounded-md px-3 py-2 text-sm font-medium transition-colors duration-150"
              :class="[$route.path.includes('/admin/contact-messages') ? 'bg-indigo-700 text-white' : 'text-indigo-200 hover:bg-indigo-700 hover:text-white']"
            >
              <svg class="w-4 h-4 mr-3" fill="currentColor" viewBox="0 0 20 20">
                <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
                <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
              </svg>
              <span>Wiadomości</span>
            </router-link>
            <router-link 
              to="/admin/newsletter"
              class="flex items-center rounded-md px-3 py-2 text-sm font-medium transition-colors duration-150"
              :class="[$route.path.includes('/admin/newsletter') ? 'bg-indigo-700 text-white' : 'text-indigo-200 hover:bg-indigo-700 hover:text-white']"
            >
              <svg class="w-4 h-4 mr-3" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M2.94 6.412A2 2 0 002 8.108V16a2 2 0 002 2h12a2 2 0 002-2V8.108a2 2 0 00-.94-1.696l-6-3.75a2 2 0 00-2.12 0l-6 3.75zm2.615 2.423a1 1 0 10-1.11 1.664l5 3.333a1 1 0 001.11 0l5-3.333a1 1 0 00-1.11-1.664L10 12.798 5.555 8.835z" clip-rule="evenodd"/>
              </svg>
              <span>Newsletter</span>
            </router-link>
          </div>

          <!-- Ustawienia -->
          <div class="pt-3 pb-4">
            <div class="px-3 py-1">
              <p class="text-xs font-semibold text-indigo-300 uppercase tracking-wider">Ustawienia</p>
            </div>
            <router-link 
              to="/admin/privacy-policies"
              class="flex items-center rounded-md px-3 py-2 text-sm font-medium transition-colors duration-150"
              :class="[$route.path.includes('/admin/privacy-policies') ? 'bg-indigo-700 text-white' : 'text-indigo-200 hover:bg-indigo-700 hover:text-white']"
            >
              <svg class="w-4 h-4 mr-3" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
              </svg>
              <span>Polityki Prywatności</span>
            </router-link>
            <router-link 
              to="/admin/terms-of-service"
              class="flex items-center rounded-md px-3 py-2 text-sm font-medium transition-colors duration-150"
              :class="[$route.path.includes('/admin/terms-of-service') ? 'bg-indigo-700 text-white' : 'text-indigo-200 hover:bg-indigo-700 hover:text-white']"
            >
              <svg class="w-4 h-4 mr-3" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/>
              </svg>
              <span>Regulaminy</span>
            </router-link>
          </div>
        </div>
      </div>
    </aside>

    <!-- Sidebar overlay (tylko na mobile) -->
    <div 
      @click="toggleSidebar" 
      class="fixed inset-0 z-30 bg-black bg-opacity-50 transition-opacity lg:hidden"
      :class="{'opacity-100 pointer-events-auto': sidebarOpen, 'opacity-0 pointer-events-none': !sidebarOpen}"
    ></div>

    <!-- Główna treść -->
    <div class="pt-16 lg:ml-64 h-screen overflow-hidden">
      <!-- Alerts Container -->
      <alerts-container />

      <!-- Page Content -->
      <main class="p-4 bg-gray-100 h-full overflow-y-auto">
        <router-view :key="$route.fullPath" />
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
      if (route.path.includes('/admin/reviews')) return 'Recenzje produktów'
      if (route.path.includes('/admin/promotions')) return 'Promocje'
      if (route.path.includes('/admin/tutorials')) return 'Poradniki'
      if (route.path.includes('/admin/contact-messages')) return 'Wiadomości'
      if (route.path.includes('/admin/about')) return 'O nas'
      if (route.path.includes('/admin/users')) return 'Użytkownicy'
      if (route.path.includes('/admin/newsletter')) return 'Newsletter'
      if (route.path.includes('/admin/privacy-policies')) return 'Polityki Prywatności'
      if (route.path.includes('/admin/terms-of-service')) return 'Regulaminy'
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
    
    // Alert messages (AlertsContainer handles toast display now)
    
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
        console.log('AdminLayout: Starting logout...')
        
        // Czekaj na zakończenie procesu wylogowywania
        const success = await authStore.logout()
        
        if (success) {
          console.log('AdminLayout: Logout successful, redirecting...')
          // Poczekaj dłużej na zaktualizowanie stanu przed przekierowaniem
          setTimeout(() => {
            router.push('/')
          }, 200)
        }
      } catch (error) {
        console.error('Logout error:', error)
        alertStore.error('Wystąpił błąd podczas wylogowywania.')
        // Mimo błędu, spróbuj przekierować (logout może się powieść lokalnie)
        setTimeout(() => {
          router.push('/')
        }, 200)
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