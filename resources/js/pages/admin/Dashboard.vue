<template>
  <div class="px-6 py-4">
    <div class="sm:flex sm:items-center">
      <div class="sm:flex-auto">
        <h1 class="text-2xl font-semibold text-gray-900">Dashboard</h1>
        <p class="mt-2 text-sm text-gray-700">Przegląd statystyk sklepu</p>
      </div>
    </div>
    
    <!-- Dashboard Settings -->
    <div class="mt-4 bg-white p-4 rounded-lg shadow">
      <div class="flex flex-col md:flex-row md:items-center md:justify-between">
        <h2 class="text-md font-medium text-gray-700 mb-2 md:mb-0">Ustawienia widoku</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div>
            <label for="date-range" class="block text-sm font-medium text-gray-700">Zakres dat</label>
            <select 
              id="date-range" 
              v-model="dateRange"
              @change="fetchDashboardData"
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
            >
              <option value="today">Dzisiaj</option>
              <option value="yesterday">Wczoraj</option>
              <option value="week">Ostatni tydzień</option>
              <option value="month">Ostatni miesiąc</option>
              <option value="quarter">Ostatni kwartał</option>
              <option value="year">Ostatni rok</option>
              <option value="custom">Niestandardowy zakres</option>
            </select>
          </div>
          
          <div v-if="dateRange === 'custom'" class="md:col-span-2 grid grid-cols-2 gap-4">
            <div>
              <label for="date-from" class="block text-sm font-medium text-gray-700">Od</label>
              <input 
                type="date" 
                id="date-from" 
                v-model="customDateRange.from"
                @change="fetchDashboardData"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
              />
            </div>
            <div>
              <label for="date-to" class="block text-sm font-medium text-gray-700">Do</label>
              <input 
                type="date" 
                id="date-to" 
                v-model="customDateRange.to"
                @change="fetchDashboardData"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
              />
            </div>
          </div>
          
          <div>
            <label for="chart-type" class="block text-sm font-medium text-gray-700">Typ wykresu</label>
            <select 
              id="chart-type" 
              v-model="chartType"
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
            >
              <option value="line">Liniowy</option>
              <option value="bar">Słupkowy</option>
              <option value="area">Obszarowy</option>
            </select>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Loading indicator -->
    <div v-if="loading" class="flex justify-center my-12">
      <svg class="animate-spin h-10 w-10 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
      </svg>
    </div>
    
    <div v-else>
      <!-- Stats -->
      <div class="mt-6 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
        <div class="bg-white overflow-hidden shadow rounded-lg">
          <div class="px-4 py-5 sm:p-6">
            <div class="flex items-center">
              <div class="flex-shrink-0 bg-indigo-500 rounded-md p-3">
                <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10" />
                </svg>
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">Produkty</dt>
                  <dd class="flex items-baseline">
                    <div class="text-2xl font-semibold text-gray-900">{{ stats.counts.products }}</div>
                  </dd>
                </dl>
              </div>
            </div>
          </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
          <div class="px-4 py-5 sm:p-6">
            <div class="flex items-center">
              <div class="flex-shrink-0 bg-indigo-500 rounded-md p-3">
                <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">Użytkownicy</dt>
                  <dd class="flex items-baseline">
                    <div class="text-2xl font-semibold text-gray-900">{{ stats.counts.users }}</div>
                  </dd>
                </dl>
              </div>
            </div>
          </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
          <div class="px-4 py-5 sm:p-6">
            <div class="flex items-center">
              <div class="flex-shrink-0 bg-indigo-500 rounded-md p-3">
                <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                </svg>
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">Zamówienia</dt>
                  <dd class="flex items-baseline">
                    <div class="text-2xl font-semibold text-gray-900">{{ stats.counts.orders }}</div>
                    <span v-if="stats.trends?.orders" :class="[
                      stats.trends.orders > 0 ? 'text-green-600' : 'text-red-600',
                      'ml-2 text-sm font-semibold'
                    ]">
                      <template v-if="stats.trends.orders > 0">+</template>{{ stats.trends.orders }}%
                    </span>
                  </dd>
                </dl>
              </div>
            </div>
          </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
          <div class="px-4 py-5 sm:p-6">
            <div class="flex items-center">
              <div class="flex-shrink-0 bg-indigo-500 rounded-md p-3">
                <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                </svg>
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">Recenzje produktów</dt>
                  <dd class="flex items-baseline">
                    <div class="text-2xl font-semibold text-gray-900">{{ stats.counts.reviews }}</div>
                    <span v-if="stats.trends?.reviews" :class="[
                      stats.trends.reviews > 0 ? 'text-green-600' : 'text-red-600',
                      'ml-2 text-sm font-semibold'
                    ]">
                      <template v-if="stats.trends.reviews > 0">+</template>{{ stats.trends.reviews }}%
                    </span>
                  </dd>
                </dl>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Revenue & Orders Chart -->
      <div class="mt-8 bg-white shadow rounded-lg p-6">
        <div class="flex justify-between items-center mb-6">
          <h2 class="text-lg font-medium text-gray-900">Analiza sprzedaży</h2>
          <div>
            <select 
              v-model="salesMetric" 
              @change="updateChartData"
              class="mt-1 block rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
            >
              <option value="revenue">Przychód</option>
              <option value="orders">Liczba zamówień</option>
              <option value="average">Średnia wartość zamówienia</option>
            </select>
          </div>
        </div>
        <div class="h-80">
          <SalesChart 
            :data="stats.sales_data" 
            :chart-type="chartType" 
            :metric="salesMetric"
            :key="`chart-${stats.sales_data.length}-${chartType}-${salesMetric}`"
          />
        </div>
      </div>
      
      <!-- Categories Distribution Chart -->
      <div class="mt-8 bg-white shadow rounded-lg p-6">
        <div class="flex justify-between items-center mb-6">
          <h2 class="text-lg font-medium text-gray-900">Rozkład produktów według kategorii</h2>
        </div>
        <div class="h-80">
          <CategoryChart :data="stats.categories_data" />
        </div>
      </div>
      
      <!-- Recent Orders & Top Products Grid -->
      <div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Recent Orders -->
        <div class="bg-white shadow rounded-lg overflow-hidden">
          <div class="px-6 py-5 border-b border-gray-200">
            <h2 class="text-lg font-medium text-gray-900">Ostatnie zamówienia</h2>
          </div>
          <div class="divide-y divide-gray-200">
            <div v-if="stats.recent_orders && stats.recent_orders.length" class="flow-root">
              <ul class="divide-y divide-gray-200">
                <li v-for="order in stats.recent_orders" :key="order.id" class="px-6 py-4">
                  <div class="flex items-center justify-between">
                    <div>
                      <p class="text-sm font-medium text-gray-900">#{{ order.id }} - {{ order.user ? order.user.name : 'Gość' }}</p>
                      <p class="text-sm text-gray-500">{{ formatDate(order.created_at) }}</p>
                    </div>
                    <div class="flex flex-col items-end">
                      <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                            :class="{
                              'bg-green-100 text-green-800': order.status === 'completed',
                              'bg-yellow-100 text-yellow-800': order.status === 'processing',
                              'bg-red-100 text-red-800': order.status === 'cancelled',
                              'bg-gray-100 text-gray-800': order.status === 'pending'
                            }">
                        {{ order.status }}
                      </span>
                      <p class="mt-1 text-sm font-medium text-gray-900">{{ order.total }} PLN</p>
                    </div>
                  </div>
                </li>
              </ul>
            </div>
            <div v-else class="p-6 text-center text-sm text-gray-500">
              Brak zamówień
            </div>
          </div>
          <div class="bg-gray-50 px-6 py-3 border-t border-gray-200">
            <router-link to="/admin/orders" class="text-sm font-medium text-indigo-600 hover:text-indigo-900">
              Zobacz wszystkie zamówienia
            </router-link>
          </div>
        </div>
        
        <!-- Top Products -->
        <div class="bg-white shadow rounded-lg overflow-hidden">
          <div class="px-6 py-5 border-b border-gray-200">
            <h2 class="text-lg font-medium text-gray-900">Najlepiej sprzedające się produkty</h2>
          </div>
          <div class="divide-y divide-gray-200">
            <div v-if="stats.top_products && stats.top_products.length" class="flow-root">
              <ul class="divide-y divide-gray-200">
                <li v-for="product in stats.top_products" :key="product.id" class="px-6 py-4">
                  <div class="flex items-center justify-between">
                    <div class="flex items-center">
                      <div class="flex-shrink-0 h-10 w-10">
                        <img 
                          :src="getProductImageUrl(product.image_url, product.name, 40, 40)" 
                          class="h-10 w-10 rounded-full object-cover"
                          alt="Product image"
                          @error="(e) => handleImageError(e, product.name, 40, 40)"
                        />
                      </div>
                      <div class="ml-4">
                        <p class="text-sm font-medium text-gray-900">{{ product.name }}</p>
                        <p class="text-sm text-gray-500">{{ product.category ? product.category.name : '-' }}</p>
                      </div>
                    </div>
                    <div class="flex flex-col items-end">
                      <p class="text-sm font-medium text-gray-900">{{ product.total_sold }} szt.</p>
                      <p class="text-sm text-gray-500">{{ product.price }} PLN</p>
                    </div>
                  </div>
                </li>
              </ul>
            </div>
            <div v-else class="p-6 text-center text-sm text-gray-500">
              Brak danych o produktach
            </div>
          </div>
          <div class="bg-gray-50 px-6 py-3 border-t border-gray-200">
            <router-link to="/admin/products" class="text-sm font-medium text-indigo-600 hover:text-indigo-900">
              Zobacz wszystkie produkty
            </router-link>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted, watch } from 'vue'
import axios from 'axios'
import { useAlertStore } from '../../stores/alertStore'
import { useAuthStore } from '../../stores/authStore'
import { getProductImageUrl, handleImageError } from '../../utils/imageHelpers'
import SalesChart from '../../components/charts/SalesChart.vue'
import CategoryChart from '../../components/charts/CategoryChart.vue'

export default {
  name: 'AdminDashboard',
  components: {
    SalesChart,
    CategoryChart
  },
  setup() {
    const alertStore = useAlertStore()
    const authStore = useAuthStore()
    const loading = ref(true)
    const stats = ref({
      counts: {
        products: 0,
        users: 0,
        orders: 0,
        reviews: 0
      },
      trends: {
        orders: 0,
        reviews: 0,
        revenue: 0
      },
      recent_orders: [],
      sales_data: [],
      top_products: [],
      categories_data: []
    })
    
    // Dashboard customization options
    const dateRange = ref('week')
    const customDateRange = ref({
      from: new Date(new Date().setDate(new Date().getDate() - 7)).toISOString().substr(0, 10),
      to: new Date().toISOString().substr(0, 10)
    })
    const chartType = ref('line')
    const salesMetric = ref('revenue')
    
    const fetchDashboardData = async () => {
      try {
        loading.value = true
        
        // Sprawdź czy użytkownik jest zalogowany i czy jest adminem
        if (!authStore.isLoggedIn || !authStore.isAdmin) {
          console.log('Dashboard: User not logged in or not admin, skipping data fetch')
          console.log('Is logged in:', authStore.isLoggedIn, 'Is admin:', authStore.isAdmin)
          loading.value = false
          return
        }
        
        // Prepare date range parameters
        let params = { period: dateRange.value }
        
        if (dateRange.value === 'custom') {
          params = {
            period: 'custom',
            start_date: customDateRange.value.from,
            end_date: customDateRange.value.to
          }
        }
        
        console.log('Fetching dashboard data with params:', params)
        console.log('Date range:', dateRange.value)
        console.log('Current user:', authStore.user)
        console.log('Is admin:', authStore.isAdmin)
        console.log('Is logged in:', authStore.isLoggedIn)
        
        const response = await axios.get('/api/admin/dashboard', { params })
        console.log('Dashboard API response:', response.data)
        stats.value = response.data.data
        console.log('Stats after assignment:', stats.value)
        console.log('Sales data:', stats.value.sales_data)
        console.log('Sales data length:', stats.value.sales_data?.length)
        console.log('Sales data type:', typeof stats.value.sales_data)
        console.log('First sales data item:', stats.value.sales_data?.[0])
        
        // Add fallback data for testing if no real data exists
        if (!stats.value.sales_data || stats.value.sales_data.length === 0) {
          console.log('No sales data, adding fallback data')
          stats.value.sales_data = [
            { date: '2024-01-01', total_sales: 1200, order_count: 5 },
            { date: '2024-01-02', total_sales: 800, order_count: 3 },
            { date: '2024-01-03', total_sales: 1500, order_count: 7 },
            { date: '2024-01-04', total_sales: 900, order_count: 4 },
            { date: '2024-01-05', total_sales: 2100, order_count: 9 },
            { date: '2024-01-06', total_sales: 1800, order_count: 8 },
            { date: '2024-01-07', total_sales: 1300, order_count: 6 }
          ]
        }
        
        if (!stats.value.categories_data || stats.value.categories_data.length === 0) {
          console.log('No categories data, adding fallback data')
          stats.value.categories_data = [
            { id: 1, name: 'Elektronika', products_count: 15 },
            { id: 2, name: 'Odzież', products_count: 25 },
            { id: 3, name: 'Dom i ogród', products_count: 12 },
            { id: 4, name: 'Sport', products_count: 8 },
            { id: 5, name: 'Książki', products_count: 20 }
          ]
        }
        
        // After loading data, update chart data for the selected metric
        updateChartData()
        
        // Force reactivity update
        nextTick(() => {
          console.log('Dashboard: After nextTick - sales data:', stats.value.sales_data)
          console.log('Dashboard: After nextTick - sales data length:', stats.value.sales_data?.length)
        })
      } catch (error) {
        console.error('Error fetching dashboard data:', error)
        console.error('Error response:', error.response)
        console.error('Error status:', error.response?.status)
        console.error('Error data:', error.response?.data)
        
        // Don't show error if user is logging out or not on admin page
        if (error.message === 'Unauthorized admin request blocked') {
          console.log('Admin request blocked - user likely logging out or not authorized');
          return;
        }
        
        // Don't show error if we're not on admin page (user was redirected)
        const currentPath = window.location.pathname;
        if (!currentPath.startsWith('/admin')) {
          console.log('Not on admin page, skipping error display');
          return;
        }
        
        // Don't show error if it's a network timeout or connection issue
        if (error.code === 'ECONNABORTED' || error.message.includes('timeout')) {
          console.log('Dashboard: Network timeout, not showing error alert');
          return;
        }
        
        // Don't show error if it's a network error (no response)
        if (!error.response && error.request) {
          console.log('Dashboard: Network error, not showing error alert');
          return;
        }
        
        if (error.response?.status === 401) {
          console.log('Dashboard: Unauthorized - user needs to login again')
          // Don't show alert for 401 as it's handled by auth system
        } else if (error.response?.status === 403) {
          console.log('Dashboard: Forbidden - user is not admin')
          // Don't show alert for 403 as it's handled by auth system
        } else {
          console.log('Dashboard: Other error occurred:', error.message)
          // Only show alert for actual server errors, not auth issues
          if (error.response?.status >= 500) {
            alertStore.error('Nie udało się pobrać danych dla panelu administracyjnego')
          }
        }
      } finally {
        loading.value = false
      }
    }
    
    const updateChartData = () => {
      // This function would update the chart data based on the selected metric
      // It's a placeholder for the actual chart implementation
      console.log(`Updating chart data for metric: ${salesMetric.value}, chart type: ${chartType.value}`)
    }
    
    const formatDate = (dateString) => {
      const options = { year: 'numeric', month: 'short', day: 'numeric' }
      return new Date(dateString).toLocaleDateString('pl-PL', options)
    }
    
    // Watch for changes in chart settings
    watch(chartType, () => {
      updateChartData()
    })
    
    // Watch for auth state changes (logout)
    watch(() => authStore.isLoggedIn, (newValue) => {
      console.log('Dashboard: Auth state changed, isLoggedIn:', newValue)
      if (!newValue) {
        console.log('Dashboard: User logged out, clearing data')
        loading.value = false
        // Wyczyść dane gdy użytkownik się wyloguje
        stats.value = {
          counts: { products: 0, users: 0, orders: 0, reviews: 0 },
          trends: { orders: 0, reviews: 0, revenue: 0 },
          recent_orders: [],
          sales_data: [],
          top_products: [],
          categories_data: []
        }
      }
    })
    
    onMounted(async () => {
      // Sprawdź czy auth store jest zainicjalizowany
      if (!authStore.authInitialized) {
        console.log('Dashboard: Auth not initialized, waiting...')
        await authStore.initAuth()
      }
      
      // Dodaj małe opóźnienie, aby upewnić się, że auth state jest w pełni załadowany
      await new Promise(resolve => setTimeout(resolve, 100))
      
      // Sprawdź czy użytkownik może dostać się do dashboardu
      if (authStore.isLoggedIn && authStore.isAdmin) {
        console.log('Dashboard: User is logged in and admin, fetching data')
        fetchDashboardData()
      } else {
        console.log('Dashboard: User not authorized for dashboard')
      }
    })
    
    return {
      loading,
      stats,
      dateRange,
      customDateRange,
      chartType,
      salesMetric,
      formatDate,
      fetchDashboardData,
      updateChartData,
      getProductImageUrl,
      handleImageError
    }
  }
}
</script> 