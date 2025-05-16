<template>
  <div>
    <div class="sm:flex sm:items-center">
      <div class="sm:flex-auto">
        <h1 class="text-2xl font-semibold text-gray-900">Zarządzanie zamówieniami</h1>
        <p class="mt-2 text-sm text-gray-700">Lista wszystkich zamówień z możliwością wyświetlania szczegółów, zmiany statusu i generowania faktur.</p>
      </div>
    </div>

    <!-- Search and filters -->
    <div class="mt-6 bg-white shadow px-4 py-5 sm:rounded-lg sm:px-6">
      <div class="flex flex-wrap gap-4">
        <div class="flex-1 min-w-[200px]">
          <label for="search" class="block text-sm font-medium text-gray-700">Wyszukaj</label>
          <div class="mt-1">
            <input
              type="text"
              name="search"
              id="search"
              v-model="filters.search"
              @input="debouncedFetchOrders"
              class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
              placeholder="Numer zamówienia, email klienta..."
            />
          </div>
        </div>
        
        <div class="w-full sm:w-auto">
          <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
          <select
            id="status"
            name="status"
            v-model="filters.status"
            @change="fetchOrders"
            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
          >
            <option value="">Wszystkie statusy</option>
            <option value="pending">Oczekujące</option>
            <option value="processing">W realizacji</option>
            <option value="completed">Zrealizowane</option>
            <option value="cancelled">Anulowane</option>
          </select>
        </div>
        
        <div class="w-full sm:w-auto">
          <label for="date_from" class="block text-sm font-medium text-gray-700">Od daty</label>
          <input
            type="date"
            id="date_from"
            name="date_from"
            v-model="filters.date_from"
            @change="fetchOrders"
            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
          />
        </div>
        
        <div class="w-full sm:w-auto">
          <label for="date_to" class="block text-sm font-medium text-gray-700">Do daty</label>
          <input
            type="date"
            id="date_to"
            name="date_to"
            v-model="filters.date_to"
            @change="fetchOrders"
            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
          />
        </div>
        
        <div class="w-full sm:w-auto">
          <label for="sort" class="block text-sm font-medium text-gray-700">Sortuj</label>
          <select
            id="sort"
            name="sort"
            v-model="filters.sort_field"
            @change="fetchOrders"
            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
          >
            <option value="created_at">Data zamówienia</option>
            <option value="total">Kwota</option>
            <option value="id">Numer zamówienia</option>
          </select>
        </div>
        
        <div class="w-full sm:w-auto">
          <label for="direction" class="block text-sm font-medium text-gray-700">Kierunek</label>
          <select
            id="direction"
            name="direction"
            v-model="filters.sort_direction"
            @change="fetchOrders"
            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
          >
            <option value="desc">Malejąco</option>
            <option value="asc">Rosnąco</option>
          </select>
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

    <!-- Orders list -->
    <div v-else class="mt-8 flex flex-col">
      <div class="-my-2 -mx-4 overflow-x-auto sm:-mx-6 lg:-mx-8">
        <div class="inline-block min-w-full py-2 align-middle md:px-6 lg:px-8">
          <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
            <table class="min-w-full divide-y divide-gray-300">
              <thead class="bg-gray-50">
                <tr>
                  <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">Nr zamówienia</th>
                  <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Klient</th>
                  <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Data</th>
                  <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Kwota</th>
                  <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Status</th>
                  <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                    <span class="sr-only">Akcje</span>
                  </th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-200 bg-white">
                <tr v-for="order in orders.data" :key="order.id">
                  <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">
                    #{{ order.id }}
                  </td>
                  <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                    {{ order.user ? order.user.name : 'Gość' }}
                    <div class="text-xs text-gray-400">{{ order.user ? order.user.email : order.email }}</div>
                  </td>
                  <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ formatDate(order.created_at) }}</td>
                  <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 font-medium">{{ order.total }} PLN</td>
                  <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                          :class="{
                            'bg-yellow-100 text-yellow-800': order.status === 'pending',
                            'bg-blue-100 text-blue-800': order.status === 'processing',
                            'bg-green-100 text-green-800': order.status === 'completed',
                            'bg-red-100 text-red-800': order.status === 'cancelled'
                          }">
                      {{ translateStatus(order.status) }}
                    </span>
                  </td>
                  <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                    <button @click="openOrderDetails(order)" class="text-indigo-600 hover:text-indigo-900 mr-3">Szczegóły</button>
                    <button @click="openStatusModal(order)" class="text-blue-600 hover:text-blue-900 mr-3">Status</button>
                    <a :href="`/api/admin/orders/${order.id}/invoice`" target="_blank" class="text-green-600 hover:text-green-900">Faktura</a>
                  </td>
                </tr>
                <tr v-if="orders.data.length === 0">
                  <td colspan="6" class="px-3 py-4 text-sm text-gray-500 text-center">Brak zamówień</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
      
      <!-- Pagination -->
      <div v-if="orders.data && orders.data.length > 0" class="mt-5 flex justify-between items-center">
        <div class="text-sm text-gray-700">
          Pokazuje {{ orders.from }} do {{ orders.to }} z {{ orders.total }} zamówień
        </div>
        <div>
          <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
            <button
              @click="goToPage(orders.current_page - 1)"
              :disabled="orders.current_page === 1"
              :class="[
                orders.current_page === 1 ? 'cursor-not-allowed opacity-50' : 'hover:bg-gray-50',
                'relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500'
              ]"
            >
              <span class="sr-only">Poprzednia</span>
              <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
              </svg>
            </button>
            <button
              v-for="page in paginationPages"
              :key="page"
              @click="goToPage(page)"
              :class="[
                page === orders.current_page
                  ? 'z-10 bg-indigo-50 border-indigo-500 text-indigo-600'
                  : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50',
                'relative inline-flex items-center px-4 py-2 border text-sm font-medium'
              ]"
            >
              {{ page }}
            </button>
            <button
              @click="goToPage(orders.current_page + 1)"
              :disabled="orders.current_page === orders.last_page"
              :class="[
                orders.current_page === orders.last_page ? 'cursor-not-allowed opacity-50' : 'hover:bg-gray-50',
                'relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500'
              ]"
            >
              <span class="sr-only">Następna</span>
              <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
              </svg>
            </button>
          </nav>
        </div>
      </div>
    </div>

    <!-- Order details modal -->
    <div v-if="showDetailsModal" class="fixed z-10 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
      <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" @click="showDetailsModal = false"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
          <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
            <div v-if="selectedOrder" class="sm:flex sm:items-start">
              <div class="w-full">
                <div class="flex items-center justify-between mb-4">
                  <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                    Zamówienie #{{ selectedOrder.id }}
                  </h3>
                  <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                        :class="{
                          'bg-yellow-100 text-yellow-800': selectedOrder.status === 'pending',
                          'bg-blue-100 text-blue-800': selectedOrder.status === 'processing',
                          'bg-green-100 text-green-800': selectedOrder.status === 'completed',
                          'bg-red-100 text-red-800': selectedOrder.status === 'cancelled'
                        }">
                    {{ translateStatus(selectedOrder.status) }}
                  </span>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                  <div>
                    <h4 class="text-sm font-medium text-gray-500">Dane klienta</h4>
                    <div class="mt-2 text-sm text-gray-900">
                      <p class="font-medium">{{ selectedOrder.user ? selectedOrder.user.name : selectedOrder.name }}</p>
                      <p>{{ selectedOrder.user ? selectedOrder.user.email : selectedOrder.email }}</p>
                      <p>{{ selectedOrder.phone }}</p>
                    </div>
                  </div>
                  
                  <div>
                    <h4 class="text-sm font-medium text-gray-500">Adres dostawy</h4>
                    <div class="mt-2 text-sm text-gray-900">
                      <p>{{ selectedOrder.address }}</p>
                      <p>{{ selectedOrder.postal_code }} {{ selectedOrder.city }}</p>
                      <p>{{ selectedOrder.country }}</p>
                    </div>
                  </div>
                </div>
                
                <div>
                  <h4 class="text-sm font-medium text-gray-500 mb-3">Produkty</h4>
                  <div class="border rounded-md overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                      <thead class="bg-gray-50">
                        <tr>
                          <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produkt</th>
                          <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cena</th>
                          <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ilość</th>
                          <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Suma</th>
                        </tr>
                      </thead>
                      <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="(item, index) in selectedOrder.items" :key="index">
                          <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ item.product ? item.product.name : item.product_name }}
                          </td>
                          <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">{{ item.price }} PLN</td>
                          <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">{{ item.quantity }}</td>
                          <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">{{ (item.price * item.quantity).toFixed(2) }} PLN</td>
                        </tr>
                      </tbody>
                      <tfoot class="bg-gray-50">
                        <tr>
                          <td colspan="3" class="px-4 py-3 text-sm font-medium text-gray-900 text-right">Suma:</td>
                          <td class="px-4 py-3 whitespace-nowrap text-sm font-bold text-gray-900">{{ selectedOrder.total }} PLN</td>
                        </tr>
                      </tfoot>
                    </table>
                  </div>
                </div>
                
                <div class="mt-6">
                  <h4 class="text-sm font-medium text-gray-500 mb-2">Uwagi</h4>
                  <div class="mt-1 text-sm text-gray-900 p-3 bg-gray-50 rounded-md">
                    {{ selectedOrder.notes || 'Brak uwag' }}
                  </div>
                </div>
                
                <div class="mt-6">
                  <h4 class="text-sm font-medium text-gray-500 mb-2">Metoda płatności</h4>
                  <div class="mt-1 text-sm text-gray-900">
                    {{ selectedOrder.payment_method || 'Nieznana' }}
                    <span v-if="selectedOrder.payment_status" class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                          :class="{
                            'bg-yellow-100 text-yellow-800': selectedOrder.payment_status === 'pending',
                            'bg-green-100 text-green-800': selectedOrder.payment_status === 'completed',
                            'bg-red-100 text-red-800': selectedOrder.payment_status === 'failed'
                          }">
                      {{ translatePaymentStatus(selectedOrder.payment_status) }}
                    </span>
                  </div>
                </div>
                
                <div class="mt-6">
                  <h4 class="text-sm font-medium text-gray-500 mb-2">Metoda dostawy</h4>
                  <div class="mt-1 text-sm text-gray-900">
                    {{ selectedOrder.shipping_method || 'Nieznana' }}
                    <span v-if="selectedOrder.shipping_cost" class="ml-2 text-sm text-gray-500">({{ selectedOrder.shipping_cost }} PLN)</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
            <button
              type="button"
              @click="showDetailsModal = false"
              class="w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm"
            >
              Zamknij
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Status change modal -->
    <div v-if="showStatusModal" class="fixed z-10 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
      <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" @click="showStatusModal = false"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
          <form @submit.prevent="updateOrderStatus">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
              <div class="sm:flex sm:items-start">
                <div class="w-full">
                  <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4" id="modal-title">
                    Zmień status zamówienia #{{ selectedOrder ? selectedOrder.id : '' }}
                  </h3>
                  
                  <div class="mt-2">
                    <label for="new_status" class="block text-sm font-medium text-gray-700">Status</label>
                    <select
                      id="new_status"
                      v-model="newStatus"
                      required
                      class="mt-1 block w-full bg-white border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                    >
                      <option value="pending">Oczekujące</option>
                      <option value="processing">W realizacji</option>
                      <option value="completed">Zrealizowane</option>
                      <option value="cancelled">Anulowane</option>
                    </select>
                  </div>
                  
                  <div class="mt-4">
                    <label for="status_note" class="block text-sm font-medium text-gray-700">Notatka (opcjonalnie)</label>
                    <textarea
                      id="status_note"
                      v-model="statusNote"
                      rows="3"
                      class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                      placeholder="Dodatkowe informacje o zmianie statusu..."
                    ></textarea>
                  </div>
                  
                  <div class="mt-4">
                    <div class="flex items-start">
                      <div class="flex items-center h-5">
                        <input
                          id="notify_customer"
                          name="notify_customer"
                          type="checkbox"
                          v-model="notifyCustomer"
                          class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded"
                        />
                      </div>
                      <div class="ml-3 text-sm">
                        <label for="notify_customer" class="font-medium text-gray-700">Powiadom klienta</label>
                        <p class="text-gray-500">Wyślij email z informacją o zmianie statusu zamówienia</p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
              <button
                type="submit"
                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm"
              >
                Zapisz zmiany
              </button>
              <button
                type="button"
                @click="showStatusModal = false"
                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
              >
                Anuluj
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, reactive, computed, onMounted } from 'vue'
import { useAlertStore } from '../../stores/alertStore'
import axios from 'axios'
import debounce from 'lodash/debounce'

export default {
  name: 'AdminOrders',
  setup() {
    const alertStore = useAlertStore()
    
    // Data
    const loading = ref(true)
    const orders = ref({
      data: [],
      current_page: 1,
      from: 1,
      to: 0,
      total: 0,
      last_page: 1,
      per_page: 10
    })
    const filters = reactive({
      search: '',
      status: '',
      date_from: '',
      date_to: '',
      sort_field: 'created_at',
      sort_direction: 'desc',
      page: 1
    })
    
    // Modals
    const showDetailsModal = ref(false)
    const showStatusModal = ref(false)
    const selectedOrder = ref(null)
    const newStatus = ref('')
    const statusNote = ref('')
    const notifyCustomer = ref(true)
    
    // Computed
    const paginationPages = computed(() => {
      const total = orders.value.last_page
      const current = orders.value.current_page
      const pages = []
      
      if (total <= 7) {
        for (let i = 1; i <= total; i++) {
          pages.push(i)
        }
      } else {
        if (current <= 3) {
          pages.push(1, 2, 3, 4, '...', total)
        } else if (current >= total - 2) {
          pages.push(1, '...', total - 3, total - 2, total - 1, total)
        } else {
          pages.push(1, '...', current - 1, current, current + 1, '...', total)
        }
      }
      
      return pages
    })
    
    // Methods
    const fetchOrders = async () => {
      try {
        loading.value = true
        const params = { ...filters }
        
        const response = await axios.get('/api/admin/orders', { params })
        orders.value = response.data
      } catch (error) {
        console.error('Error fetching orders:', error)
        alertStore.setErrorMessage('Wystąpił błąd podczas pobierania zamówień.')
      } finally {
        loading.value = false
      }
    }
    
    const debouncedFetchOrders = debounce(fetchOrders, 300)
    
    const goToPage = (page) => {
      if (page === '...') return
      filters.page = page
      fetchOrders()
    }
    
    const openOrderDetails = async (order) => {
      try {
        // If we already have detailed order items, show the modal
        if (order.items) {
          selectedOrder.value = order
          showDetailsModal.value = true
          return
        }
        
        // Otherwise fetch the full order details first
        const response = await axios.get(`/api/admin/orders/${order.id}`)
        selectedOrder.value = response.data
        showDetailsModal.value = true
      } catch (error) {
        console.error('Error fetching order details:', error)
        alertStore.setErrorMessage('Wystąpił błąd podczas pobierania szczegółów zamówienia.')
      }
    }
    
    const openStatusModal = (order) => {
      selectedOrder.value = order
      newStatus.value = order.status
      statusNote.value = ''
      notifyCustomer.value = true
      showStatusModal.value = true
    }
    
    const updateOrderStatus = async () => {
      try {
        await axios.put(`/api/admin/orders/${selectedOrder.value.id}/status`, {
          status: newStatus.value,
          note: statusNote.value,
          notify_customer: notifyCustomer.value
        })
        
        alertStore.setSuccessMessage('Status zamówienia został zaktualizowany.')
        showStatusModal.value = false
        fetchOrders()
      } catch (error) {
        console.error('Error updating order status:', error)
        alertStore.setErrorMessage('Wystąpił błąd podczas aktualizacji statusu zamówienia.')
      }
    }
    
    const formatDate = (dateString) => {
      if (!dateString) return '-'
      const options = { year: 'numeric', month: 'numeric', day: 'numeric', hour: 'numeric', minute: 'numeric' }
      return new Date(dateString).toLocaleDateString('pl-PL', options)
    }
    
    const translateStatus = (status) => {
      const statusMap = {
        pending: 'Oczekujące',
        processing: 'W realizacji',
        completed: 'Zrealizowane',
        cancelled: 'Anulowane'
      }
      
      return statusMap[status] || status
    }
    
    const translatePaymentStatus = (status) => {
      const statusMap = {
        pending: 'Oczekująca',
        completed: 'Opłacone',
        failed: 'Nieudana'
      }
      
      return statusMap[status] || status
    }
    
    // Lifecycle
    onMounted(() => {
      fetchOrders()
    })
    
    return {
      loading,
      orders,
      filters,
      paginationPages,
      showDetailsModal,
      showStatusModal,
      selectedOrder,
      newStatus,
      statusNote,
      notifyCustomer,
      fetchOrders,
      debouncedFetchOrders,
      goToPage,
      openOrderDetails,
      openStatusModal,
      updateOrderStatus,
      formatDate,
      translateStatus,
      translatePaymentStatus
    }
  }
}
</script> 