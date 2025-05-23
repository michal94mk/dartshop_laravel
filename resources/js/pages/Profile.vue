<template>
  <div class="container mx-auto py-8">
    <div class="max-w-4xl mx-auto bg-white rounded-lg overflow-hidden shadow-lg">
      <div class="p-6">
        <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">Twój Profil</h2>
        
        <div v-if="authStore.isLoading" class="text-center py-4">
          <p class="text-gray-600">Ładowanie danych...</p>
        </div>
        
        <div v-else-if="!authStore.isLoggedIn" class="text-center py-4">
          <p class="text-gray-600 mb-4">Nie jesteś zalogowany.</p>
          <router-link 
            to="/login" 
            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
          >
            Zaloguj się
          </router-link>
        </div>
        
        <div v-else>
          <!-- Tabs navigation -->
          <div class="border-b border-gray-200 mb-6">
            <ul class="flex flex-wrap -mb-px">
              <li class="mr-2">
                <button 
                  @click="activeTab = 'profile'" 
                  class="inline-block p-4 rounded-t-lg"
                  :class="activeTab === 'profile' ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-500 hover:text-gray-800 hover:border-gray-300'"
                >
                  Profil
                </button>
              </li>
              <li class="mr-2">
                <button 
                  @click="activeTab = 'favorites'" 
                  class="inline-block p-4 rounded-t-lg"
                  :class="activeTab === 'favorites' ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-500 hover:text-gray-800 hover:border-gray-300'"
                >
                  Ulubione produkty
                </button>
              </li>
              <li class="mr-2">
                <button 
                  @click="activeTab = 'orders'" 
                  class="inline-block p-4 rounded-t-lg"
                  :class="activeTab === 'orders' ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-500 hover:text-gray-800 hover:border-gray-300'"
                >
                  Zamówienia
                </button>
              </li>
              <li>
                <button 
                  @click="activeTab = 'reviews'" 
                  class="inline-block p-4 rounded-t-lg"
                  :class="activeTab === 'reviews' ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-500 hover:text-gray-800 hover:border-gray-300'"
                >
                  Moje opinie
                </button>
              </li>
            </ul>
          </div>
          
          <!-- Profile tab content -->
          <div v-if="activeTab === 'profile'">
            <!-- Dane profilu -->
            <div v-if="!showChangePassword">
              <div class="flex items-center justify-center mb-6">
                <div class="w-20 h-20 rounded-full bg-blue-500 flex items-center justify-center text-white text-2xl font-bold">
                  {{ authStore.userInitial }}
                </div>
              </div>
              
              <div class="mb-4">
                <p class="text-gray-600 text-sm">Imię i nazwisko</p>
                <p class="text-gray-900 font-medium">{{ authStore.userName }}</p>
              </div>
              
              <div class="mb-4">
                <p class="text-gray-600 text-sm">Email</p>
                <p class="text-gray-900 font-medium">{{ authStore.userEmail }}</p>
                
                <!-- Weryfikacja emaila -->
                <div v-if="authStore.isEmailVerified" class="mt-1 text-xs text-green-500 flex items-center">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                  </svg>
                  Zweryfikowany
                </div>
                <div v-else class="mt-1 text-xs text-red-500 flex items-center">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                  </svg>
                  Niezweryfikowany
                  <button 
                    @click="resendVerification" 
                    class="ml-2 text-blue-500 hover:text-blue-700"
                    :disabled="isVerificationLoading"
                  >
                    {{ isVerificationLoading ? 'Wysyłanie...' : 'Wyślij ponownie' }}
                  </button>
                </div>
              </div>
              
              <div v-if="status" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ status }}
              </div>
              
              <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-2 mt-6">
                <button 
                  @click="showChangePassword = true" 
                  class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
                >
                  Zmień hasło
                </button>
                
                <button 
                  @click="handleLogout" 
                  class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
                  :disabled="isLoggingOut"
                >
                  <span v-if="isLoggingOut">Wylogowywanie...</span>
                  <span v-else>Wyloguj się</span>
                </button>
              </div>
            </div>
            
            <!-- Formularz zmiany hasła -->
            <div v-else>
              <h3 class="text-xl font-medium text-gray-800 mb-4">Zmiana hasła</h3>
              
              <div v-if="authStore.hasError" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                {{ authStore.errorMessage || 'Wystąpił błąd podczas zmiany hasła.' }}
              </div>
              
              <div v-if="passwordChangeStatus" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ passwordChangeStatus }}
              </div>
              
              <form @submit.prevent="handlePasswordChange">
                <div class="mb-4">
                  <label for="current-password" class="block text-gray-700 text-sm font-bold mb-2">Aktualne hasło</label>
                  <input 
                    id="current-password" 
                    type="password" 
                    v-model="currentPassword" 
                    required
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                  />
                </div>
                
                <div class="mb-4">
                  <label for="new-password" class="block text-gray-700 text-sm font-bold mb-2">Nowe hasło</label>
                  <input 
                    id="new-password" 
                    type="password" 
                    v-model="newPassword" 
                    required
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                  />
                </div>
                
                <div class="mb-6">
                  <label for="new-password-confirmation" class="block text-gray-700 text-sm font-bold mb-2">Powtórz nowe hasło</label>
                  <input 
                    id="new-password-confirmation" 
                    type="password" 
                    v-model="newPasswordConfirmation" 
                    required
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                  />
                </div>
                
                <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-2">
                  <button 
                    type="submit" 
                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
                    :disabled="authStore.isLoading"
                  >
                    <span v-if="authStore.isLoading">Zapisywanie...</span>
                    <span v-else>Zapisz nowe hasło</span>
                  </button>
                  
                  <button 
                    type="button"
                    @click="showChangePassword = false" 
                    class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
                  >
                    Anuluj
                  </button>
                </div>
              </form>
            </div>
          </div>
          
          <!-- Favorites tab content -->
          <div v-else-if="activeTab === 'favorites'">
            <h3 class="text-xl font-medium text-gray-800 mb-4">Ulubione produkty</h3>
            
            <div v-if="loadingFavorites" class="text-center py-4">
              <p class="text-gray-600">Ładowanie ulubionych produktów...</p>
            </div>
            
            <div v-else-if="favorites.length === 0" class="text-center py-4">
              <p class="text-gray-600 mb-4">Nie masz jeszcze żadnych ulubionych produktów.</p>
              <router-link 
                to="/products" 
                class="text-blue-500 hover:text-blue-700 font-medium"
              >
                Przeglądaj produkty
              </router-link>
            </div>
            
            <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div v-for="product in favorites" :key="product.id" class="border rounded-lg p-4 flex">
                <div class="w-20 h-20 flex-shrink-0">
                  <img 
                    :src="product.image_url || '/images/placeholder.png'" 
                    :alt="product.name"
                    class="w-full h-full object-cover rounded"
                  />
                </div>
                <div class="ml-4 flex-grow">
                  <h4 class="font-medium">{{ product.name }}</h4>
                  <p class="text-gray-600 text-sm mt-1">{{ product.price_formatted }}</p>
                  <div class="flex mt-2">
                    <router-link 
                      :to="`/products/${product.id}`" 
                      class="text-blue-500 hover:text-blue-700 text-sm mr-3"
                    >
                      Zobacz
                    </router-link>
                    <button 
                      @click="removeFromFavorites(product.id)" 
                      class="text-red-500 hover:text-red-700 text-sm"
                    >
                      Usuń
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Orders tab content -->
          <div v-else-if="activeTab === 'orders'">
            <h3 class="text-xl font-medium text-gray-800 mb-4">Twoje zamówienia</h3>
            
            <div v-if="loadingOrders" class="text-center py-4">
              <p class="text-gray-600">Ładowanie zamówień...</p>
            </div>
            
            <div v-else-if="orders.length === 0" class="text-center py-4">
              <p class="text-gray-600 mb-4">Nie złożyłeś jeszcze żadnych zamówień.</p>
              <router-link 
                to="/products" 
                class="text-blue-500 hover:text-blue-700 font-medium"
              >
                Rozpocznij zakupy
              </router-link>
            </div>
            
            <div v-else>
              <div v-for="order in orders" :key="order.id" class="border rounded-lg p-4 mb-4">
                <div class="flex justify-between items-start">
                  <div>
                    <span class="text-gray-600 text-sm">Zamówienie #{{ order.id }}</span>
                    <p class="font-medium">{{ formatDate(order.created_at) }}</p>
                  </div>
                  <div class="flex flex-col items-end">
                    <span class="text-gray-600 text-sm">Status:</span>
                    <span :class="getOrderStatusClass(order.status)">{{ getOrderStatusText(order.status) }}</span>
                  </div>
                </div>
                
                <div class="mt-3">
                  <p class="text-gray-600 text-sm">Całkowita wartość:</p>
                  <p class="font-medium">{{ order.total_formatted }}</p>
                </div>
                
                <div v-if="order.shipping_method" class="mt-2">
                  <p class="text-gray-600 text-sm">Metoda dostawy:</p>
                  <p>{{ order.shipping_method }}</p>
                </div>
                
                <div v-if="order.payment_method" class="mt-2">
                  <p class="text-gray-600 text-sm">Metoda płatności:</p>
                  <p>{{ order.payment_method }}</p>
                </div>
                
                <div class="mt-3 border-t pt-3">
                  <button 
                    @click="toggleOrderDetails(order.id)" 
                    class="text-blue-500 hover:text-blue-700 text-sm font-medium flex items-center"
                  >
                    <span v-if="expandedOrderId === order.id">Ukryj szczegóły</span>
                    <span v-else>Zobacz szczegóły</span>
                    <svg 
                      xmlns="http://www.w3.org/2000/svg" 
                      class="h-4 w-4 ml-1" 
                      :class="expandedOrderId === order.id ? 'transform rotate-180' : ''"
                      fill="none" 
                      viewBox="0 0 24 24" 
                      stroke="currentColor"
                    >
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                  </button>
                  
                  <div v-if="expandedOrderId === order.id" class="mt-3">
                    <div v-for="item in order.items" :key="item.id" class="flex py-2 border-b last:border-b-0">
                      <div class="w-10 h-10 flex-shrink-0">
                        <img 
                          :src="item.product.image_url || '/images/placeholder.png'" 
                          :alt="item.product.name"
                          class="w-full h-full object-cover rounded"
                        />
                      </div>
                      <div class="ml-3 flex-grow">
                        <div class="flex justify-between">
                          <p class="font-medium">{{ item.product.name }}</p>
                          <p>{{ item.price_formatted }}</p>
                        </div>
                        <p class="text-gray-600 text-sm">Ilość: {{ item.quantity }}</p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Reviews tab content -->
          <div v-else-if="activeTab === 'reviews'">
            <h3 class="text-xl font-medium text-gray-800 mb-4">Twoje opinie</h3>
            
            <div v-if="loadingReviews" class="text-center py-4">
              <p class="text-gray-600">Ładowanie opinii...</p>
            </div>
            
            <div v-else-if="reviews.length === 0" class="text-center py-4">
              <p class="text-gray-600 mb-4">Nie dodałeś jeszcze żadnych opinii.</p>
              <router-link 
                to="/products" 
                class="text-blue-500 hover:text-blue-700 font-medium"
              >
                Przeglądaj produkty
              </router-link>
            </div>
            
            <div v-else>
              <div v-for="review in reviews" :key="review.id" class="border rounded-lg p-4 mb-4">
                <div class="flex justify-between items-start">
                  <div>
                    <h4 class="font-medium">{{ review.product.name }}</h4>
                    <div class="flex items-center mt-1">
                      <!-- Stars rating -->
                      <div class="flex">
                        <template v-for="i in 5" :key="i">
                          <svg 
                            class="w-4 h-4" 
                            :class="i <= review.rating ? 'text-yellow-400' : 'text-gray-300'"
                            fill="currentColor" 
                            viewBox="0 0 20 20" 
                            xmlns="http://www.w3.org/2000/svg"
                          >
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                          </svg>
                        </template>
                      </div>
                      <span class="ml-2 text-gray-600 text-sm">{{ formatDate(review.created_at) }}</span>
                    </div>
                  </div>
                  <div class="px-2 py-1 rounded text-xs font-medium" :class="review.is_approved ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'">
                    {{ review.is_approved ? 'Zatwierdzona' : 'Oczekuje na zatwierdzenie' }}
                  </div>
                </div>
                
                <div v-if="review.title" class="mt-3">
                  <p class="font-medium">{{ review.title }}</p>
                </div>
                
                <div class="mt-2">
                  <p class="text-gray-700">{{ review.content }}</p>
                </div>
                
                <div class="mt-3 flex">
                  <router-link 
                    :to="`/products/${review.product.id}`" 
                    class="text-blue-500 hover:text-blue-700 text-sm mr-4"
                  >
                    Zobacz produkt
                  </router-link>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted, watch } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../stores/authStore';
import { useFavoriteStore } from '../stores/favoriteStore';
import axios from 'axios';

export default {
  name: 'ProfilePage',
  
  setup() {
    // Auth related refs
    const isLoggingOut = ref(false);
    const isVerificationLoading = ref(false);
    const showChangePassword = ref(false);
    const currentPassword = ref('');
    const newPassword = ref('');
    const newPasswordConfirmation = ref('');
    const status = ref('');
    const passwordChangeStatus = ref('');
    
    // Tab management
    const activeTab = ref('profile');
    
    // Favorites tab
    const loadingFavorites = ref(false);
    const favorites = ref([]);
    
    // Orders tab
    const loadingOrders = ref(false);
    const orders = ref([]);
    const expandedOrderId = ref(null);
    
    // Reviews tab
    const loadingReviews = ref(false);
    const reviews = ref([]);
    
    const router = useRouter();
    const authStore = useAuthStore();
    const favoriteStore = useFavoriteStore();
    
    // Fetch data when tab changes
    watch(activeTab, (newTab) => {
      if (newTab === 'favorites') {
        fetchFavorites();
      } else if (newTab === 'orders') {
        fetchOrders();
      } else if (newTab === 'reviews') {
        fetchReviews();
      }
    });
    
    onMounted(() => {
      // Inicjalizacja stanu autoryzacji, jeśli jeszcze nie został zainicjalizowany
      if (!authStore.user) {
        authStore.initAuth();
      }
      
      // Initialize the favorite store
      favoriteStore.initializeFavorites();
    });
    
    // Profile tab methods
    const handleLogout = async () => {
      isLoggingOut.value = true;
      
      try {
        const success = await authStore.logout();
        
        if (success) {
          router.push('/');
        }
      } finally {
        isLoggingOut.value = false;
      }
    };
    
    const resendVerification = async () => {
      isVerificationLoading.value = true;
      
      try {
        const message = await authStore.resendVerificationEmail();
        if (message) {
          status.value = message;
          setTimeout(() => {
            status.value = '';
          }, 5000);
        }
      } finally {
        isVerificationLoading.value = false;
      }
    };
    
    const handlePasswordChange = async () => {
      passwordChangeStatus.value = '';
      
      if (newPassword.value !== newPasswordConfirmation.value) {
        authStore.hasError = true;
        authStore.errorMessage = 'Nowe hasła nie są identyczne.';
        return;
      }
      
      const success = await authStore.updatePassword(
        currentPassword.value,
        newPassword.value,
        newPasswordConfirmation.value
      );
      
      if (success) {
        passwordChangeStatus.value = 'Hasło zostało pomyślnie zmienione.';
        currentPassword.value = '';
        newPassword.value = '';
        newPasswordConfirmation.value = '';
        
        setTimeout(() => {
          showChangePassword.value = false;
          passwordChangeStatus.value = '';
        }, 2000);
      }
    };
    
    // Favorites tab methods
    const fetchFavorites = async () => {
      if (!authStore.isLoggedIn) return;
      
      loadingFavorites.value = true;
      
      try {
        // Load favorites from API
        await favoriteStore.loadFavorites();
        favorites.value = favoriteStore.favorites;
      } catch (error) {
        console.error('Error fetching favorites:', error);
      } finally {
        loadingFavorites.value = false;
      }
    };
    
    const removeFromFavorites = async (productId) => {
      // Find the product in favorites
      const product = favorites.value.find(item => item.id === productId);
      if (product) {
        try {
          await favoriteStore.toggleFavorite(product);
          // Refresh the favorites list
          favorites.value = favoriteStore.favorites;
        } catch (error) {
          console.error('Error removing favorite:', error);
        }
      }
    };
    
    // Orders tab methods
    const fetchOrders = async () => {
      if (!authStore.isLoggedIn) return;
      
      loadingOrders.value = true;
      
      try {
        const response = await axios.get('/api/orders/my-orders');
        orders.value = response.data.data || response.data;
      } catch (error) {
        console.error('Error fetching orders:', error);
      } finally {
        loadingOrders.value = false;
      }
    };
    
    const toggleOrderDetails = (orderId) => {
      if (expandedOrderId.value === orderId) {
        expandedOrderId.value = null;
      } else {
        expandedOrderId.value = orderId;
      }
    };
    
    const getOrderStatusText = (status) => {
      const statusMap = {
        'pending': 'Oczekujące',
        'processing': 'W trakcie realizacji',
        'shipped': 'Wysłane',
        'delivered': 'Dostarczone',
        'cancelled': 'Anulowane'
      };
      
      return statusMap[status] || status;
    };
    
    const getOrderStatusClass = (status) => {
      const classMap = {
        'pending': 'text-yellow-600',
        'processing': 'text-blue-600',
        'shipped': 'text-purple-600',
        'delivered': 'text-green-600',
        'cancelled': 'text-red-600'
      };
      
      return classMap[status] || '';
    };
    
    // Reviews tab methods
    const fetchReviews = async () => {
      if (!authStore.isLoggedIn) return;
      
      loadingReviews.value = true;
      
      try {
        const response = await axios.get('/api/reviews/my-reviews');
        reviews.value = response.data.data || response.data;
      } catch (error) {
        console.error('Error fetching reviews:', error);
      } finally {
        loadingReviews.value = false;
      }
    };
    
    // Utility methods
    const formatDate = (dateString) => {
      if (!dateString) return '';
      
      const date = new Date(dateString);
      return date.toLocaleDateString('pl-PL', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
      });
    };
    
    return {
      // Auth and profile
      authStore,
      isLoggingOut,
      isVerificationLoading,
      showChangePassword,
      currentPassword,
      newPassword,
      newPasswordConfirmation,
      status,
      passwordChangeStatus,
      handleLogout,
      resendVerification,
      handlePasswordChange,
      
      // Tabs
      activeTab,
      
      // Favorites
      favorites,
      loadingFavorites,
      removeFromFavorites,
      
      // Orders
      orders,
      loadingOrders,
      expandedOrderId,
      toggleOrderDetails,
      getOrderStatusText,
      getOrderStatusClass,
      
      // Reviews
      reviews,
      loadingReviews,
      
      // Utilities
      formatDate
    };
  }
}
</script> 