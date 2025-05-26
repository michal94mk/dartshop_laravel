<template>
  <div class="p-6">
    <!-- Page Header -->
    <page-header 
      title="Zamówienia"
      add-button-label="Dodaj zamówienie"
      @add="showCreateOrderModal = true"
    />

    <!-- Search and filters -->
    <search-filters
      v-if="!loading"
      :filters="filters"
      :sort-options="sortOptions"
      search-label="Wyszukaj"
      search-placeholder="Numer zamówienia, email klienta..."
      @update:filters="filters = $event"
      @filter-change="fetchOrders"
    >
      <template v-slot:filters>
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
      </template>
    </search-filters>

    <!-- Loading indicator -->
    <loading-spinner v-if="loading" />

    <!-- Orders list -->
    <admin-table
      v-if="orders.data && orders.data.length > 0"
      :columns="tableColumns"
      :items="orders.data"
      :sort-by="filters.sort_field"
      :sort-order="filters.sort_direction"
      @sort="handleSort"
      :force-horizontal-scroll="true"
      class="mt-8"
    >
      <template #cell-order_number="{ item }">
        #{{ item.id }}
      </template>
      
      <template #cell-customer="{ item }">
        <div>
          <div class="font-medium text-gray-900">{{ item.user ? item.user.name : 'Gość' }}</div>
          <div class="text-sm text-gray-400">{{ item.user ? item.user.email : item.email }}</div>
        </div>
      </template>
      
      <template #cell-total="{ item }">
        <span class="font-medium">{{ item.total }} PLN</span>
      </template>
      
      <template #cell-status="{ item }">
        <admin-badge 
          :variant="getStatusVariant(item.status)"
          size="sm"
        >
          {{ translateStatus(item.status) }}
        </admin-badge>
      </template>
      
      <template #cell-actions="{ item }">
        <div class="flex gap-1 justify-end min-w-[320px]">
          <admin-button 
            @click="openOrderDetails(item)" 
            variant="primary" 
            size="sm"
          >
            Szczegóły
          </admin-button>
          <admin-button 
            @click="openStatusModal(item)" 
            variant="info" 
            size="sm"
          >
            Status
          </admin-button>
          <admin-button 
            @click="openEditModal(item)" 
            variant="warning" 
            size="sm"
          >
            Edytuj
          </admin-button>
          <admin-button 
            @click="confirmDelete(item)" 
            variant="danger" 
            size="sm"
          >
            Usuń
          </admin-button>
          <admin-button 
            tag="a"
            :href="`/api/admin/orders/${item.id}/invoice`" 
            target="_blank" 
            variant="success" 
            size="sm"
          >
            Faktura
          </admin-button>
        </div>
      </template>
    </admin-table>
    
    <!-- No data message -->
    <no-data-message v-else-if="!loading" message="Brak zamówień do wyświetlenia" />
    
    <!-- Pagination -->
    <pagination 
      v-if="!loading && orders.data && orders.data.length > 0" 
      :pagination="orders" 
      items-label="zamówień" 
      @page-change="goToPage" 
    />

    <!-- Order details modal -->
    <admin-modal
      :show="showDetailsModal"
      title="Szczegóły zamówienia"
      size="4xl"
      @close="showDetailsModal = false"
    >
      <div v-if="selectedOrder">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg leading-6 font-medium text-gray-900">
            Zamówienie #{{ selectedOrder.id }}
          </h3>
          <admin-badge 
            :variant="getStatusVariant(selectedOrder.status)"
            size="md"
          >
            {{ translateStatus(selectedOrder.status) }}
          </admin-badge>
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
          <admin-table
            :columns="orderItemsColumns"
            :items="selectedOrder.items"
            :key-field="'id'"
          >
            <template #cell-product_name="{ item }">
              {{ item.product ? item.product.name : item.product_name }}
            </template>
            
            <template #cell-price="{ item }">
              {{ item.price }} PLN
            </template>
            
            <template #cell-total="{ item }">
              {{ (item.price * item.quantity).toFixed(2) }} PLN
            </template>
          </admin-table>
          
          <div class="mt-4 flex justify-end">
            <div class="text-lg font-bold">
              Suma: {{ selectedOrder.total }} PLN
            </div>
          </div>
        </div>
        
        <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <h4 class="text-sm font-medium text-gray-500 mb-2">Uwagi</h4>
            <div class="text-sm text-gray-900 p-3 bg-gray-50 rounded-md">
              {{ selectedOrder.notes || 'Brak uwag' }}
            </div>
          </div>
          
          <div>
            <h4 class="text-sm font-medium text-gray-500 mb-2">Metoda płatności</h4>
            <div class="text-sm text-gray-900">
              {{ selectedOrder.payment_method || 'Nieznana' }}
            </div>
            
            <h4 class="text-sm font-medium text-gray-500 mb-2 mt-4">Metoda dostawy</h4>
            <div class="text-sm text-gray-900">
              {{ selectedOrder.shipping_method || 'Nieznana' }}
              <span v-if="selectedOrder.shipping_cost" class="ml-2 text-sm text-gray-500">({{ selectedOrder.shipping_cost }} PLN)</span>
            </div>
          </div>
        </div>
      </div>
      
      <template #footer>
        <admin-button
          @click="showDetailsModal = false"
          variant="secondary"
          outline
        >
          Zamknij
        </admin-button>
      </template>
    </admin-modal>

    <!-- Status change modal -->
    <admin-modal
      :show="showStatusModal"
      title="Zmień status zamówienia"
      size="lg"
      @close="showStatusModal = false"
    >
      <form @submit.prevent="updateOrderStatus">
        <div class="space-y-4">
          <div>
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
              Zamówienie #{{ selectedOrder ? selectedOrder.id : '' }}
            </h3>
            
            <div>
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
      </form>
      
      <template #footer>
        <admin-button-group justify="end" spacing="sm">
          <admin-button
            @click="showStatusModal = false"
            variant="secondary"
            outline
          >
            Anuluj
          </admin-button>
          <admin-button
            @click="updateOrderStatus"
            variant="primary"
          >
            Zapisz zmiany
          </admin-button>
        </admin-button-group>
      </template>
    </admin-modal>

    <!-- Create/Edit Order Modal -->
    <modal v-if="showCreateOrderModal || showEditModal" @close="closeEditModal">
      <template #title>
        {{ showEditModal ? 'Edytuj zamówienie' : 'Dodaj nowe zamówienie' }}
      </template>
      <template #content>
        <form @submit.prevent="handleSubmit" class="space-y-4">
          <!-- Add validation errors display -->
          <div v-if="validationErrors.length > 0" class="bg-red-50 border-l-4 border-red-400 p-4 mb-4">
            <div class="flex">
              <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                </svg>
              </div>
              <div class="ml-3">
                <h3 class="text-sm font-medium text-red-800">Wystąpiły błędy walidacji:</h3>
                <div class="mt-2 text-sm text-red-700">
                  <ul class="list-disc pl-5 space-y-1">
                    <li v-for="error in validationErrors" :key="error">{{ error }}</li>
                  </ul>
                </div>
              </div>
            </div>
          </div>
          
          <!-- User selection -->
          <div>
            <label class="block text-sm font-medium text-gray-700">Użytkownik</label>
            <select
              v-model="editedOrder.user_id"
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
            >
              <option value="">Wybierz użytkownika</option>
              <option v-for="user in users" :key="user.id" :value="user.id">
                {{ user.name }} ({{ user.email }})
              </option>
            </select>
          </div>

          <!-- Customer details always needed -->
          <div>
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700">Imię <span class="text-red-500">*</span></label>
                <input
                  type="text"
                  v-model="editedOrder.first_name"
                  required
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">Nazwisko <span class="text-red-500">*</span></label>
                <input
                  type="text"
                  v-model="editedOrder.last_name"
                  required
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">Email <span class="text-red-500">*</span></label>
                <input
                  type="email"
                  v-model="editedOrder.email"
                  required
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                />
                <p class="text-xs text-gray-500">Format: nazwa@domena.pl</p>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">Telefon</label>
                <input
                  type="text"
                  v-model="editedOrder.phone"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                />
              </div>
            </div>
          </div>

          <!-- Address -->
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700">Adres</label>
              <input
                type="text"
                v-model="editedOrder.address"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
              />
            </div>
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700">Kod pocztowy</label>
                <input
                  type="text"
                  v-model="editedOrder.postal_code"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">Miasto</label>
                <input
                  type="text"
                  v-model="editedOrder.city"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                />
              </div>
            </div>
          </div>

          <!-- Products -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Produkty</label>
            <div v-for="(item, index) in editedOrder.items" :key="index" class="flex gap-4 mb-2">
              <select
                v-model="item.product_id"
                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
              >
                <option value="">Wybierz produkt</option>
                <option v-for="product in products" :key="product.id" :value="product.id">
                  {{ product.name }} ({{ formatPrice(product.price) }})
                </option>
              </select>
              <input
                type="number"
                v-model.number="item.quantity"
                min="1"
                class="block w-24 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                placeholder="Ilość"
              />
              <button
                type="button"
                @click="removeProduct(index)"
                class="px-2 py-1 bg-red-600 text-white rounded hover:bg-red-700"
              >
                Usuń
              </button>
            </div>
            <button
              type="button"
              @click="addProduct"
              class="mt-2 px-3 py-1.5 bg-indigo-600 text-white text-sm font-medium rounded hover:bg-indigo-700"
            >
              Dodaj produkt
            </button>
          </div>

          <!-- Status and shipping -->
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700">Status</label>
              <select
                v-model="editedOrder.status"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
              >
                <option value="pending">Oczekujące</option>
                <option value="processing">W realizacji</option>
                <option value="completed">Zakończone</option>
                <option value="cancelled">Anulowane</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Metoda wysyłki</label>
              <select
                v-model="editedOrder.shipping_method"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
              >
                <option value="courier">Kurier</option>
                <option value="inpost">InPost</option>
                <option value="pickup">Odbiór osobisty</option>
              </select>
            </div>
          </div>

          <!-- Payment Method and Status -->
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700">Metoda płatności</label>
              <select
                v-model="editedOrder.payment_method"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
              >
                <option value="bank_transfer">Przelew bankowy</option>
                <option value="cash_on_delivery">Płatność przy odbiorze</option>
                <option value="online">Płatność online</option>
                <option value="blik">BLIK</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Status płatności</label>
              <select
                v-model="editedOrder.payment_status"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
              >
                <option value="pending">Oczekująca</option>
                <option value="completed">Opłacone</option>
                <option value="failed">Nieudana</option>
              </select>
            </div>
          </div>

          <!-- Notes -->
          <div>
            <label class="block text-sm font-medium text-gray-700">Uwagi</label>
            <textarea
              v-model="editedOrder.notes"
              rows="3"
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
            ></textarea>
          </div>
        </form>
      </template>
      <template #footer>
        <button
          type="button"
          @click="closeEditModal"
          class="mr-3 px-4 py-2 text-sm font-medium text-gray-700 hover:text-gray-500"
        >
          Anuluj
        </button>
        <button
          type="button"
          @click="handleSubmit"
          class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded hover:bg-indigo-700"
        >
          {{ showEditModal ? 'Zapisz zmiany' : 'Dodaj zamówienie' }}
        </button>
      </template>
    </modal>

    <!-- Delete Confirmation Modal -->
    <modal v-if="showDeleteModal" @close="showDeleteModal = false">
      <template #title>
        Potwierdź usunięcie
      </template>
      <template #content>
        <p class="text-sm text-gray-500">
          Czy na pewno chcesz usunąć to zamówienie? Tej operacji nie można cofnąć.
        </p>
      </template>
      <template #footer>
        <button
          type="button"
          @click="showDeleteModal = false"
          class="mr-3 px-4 py-2 text-sm font-medium text-gray-700 hover:text-gray-500"
        >
          Anuluj
        </button>
        <button
          type="button"
          @click="deleteOrder"
          class="px-4 py-2 bg-red-600 text-white text-sm font-medium rounded hover:bg-red-700"
        >
          Usuń
        </button>
      </template>
    </modal>
  </div>
</template>

<script>
import { ref, reactive, computed, onMounted, watch } from 'vue'
import { useAlertStore } from '../../stores/alertStore'
import axios from 'axios'
import { useToast } from 'vue-toastification'
import Modal from '../../components/Modal.vue'
import SearchFilters from '../../components/admin/SearchFilters.vue'
import LoadingSpinner from '../../components/admin/LoadingSpinner.vue'
import NoDataMessage from '../../components/admin/NoDataMessage.vue'
import Pagination from '../../components/admin/Pagination.vue'
import PageHeader from '../../components/admin/PageHeader.vue'
import AdminTable from '../../components/admin/ui/AdminTable.vue'
import AdminButtonGroup from '../../components/admin/ui/AdminButtonGroup.vue'
import AdminButton from '../../components/admin/ui/AdminButton.vue'
import AdminModal from '../../components/admin/ui/AdminModal.vue'
import AdminBadge from '../../components/admin/ui/AdminBadge.vue'

// Add axios interceptors for debugging
axios.interceptors.request.use(request => {
  if (request.url.includes('/api/admin/orders') && request.method === 'post') {
    console.log('Request:', request.method, request.url)
    console.log('Request data:', request.data)
  }
  return request
}, error => {
  return Promise.reject(error)
})

axios.interceptors.response.use(response => {
  if (response.config.url.includes('/api/admin/orders') && response.config.method === 'post') {
    console.log('Response:', response.status)
    console.log('Response data:', response.data)
  }
  return response
}, error => {
  if (error.response) {
    console.log('Error response:', error.response.status)
    console.log('Error data:', error.response.data)
  }
  return Promise.reject(error)
})

export default {
  name: 'AdminOrders',
  components: {
    Modal,
    SearchFilters,
    LoadingSpinner,
    NoDataMessage,
    Pagination,
    PageHeader,
    AdminTable,
    AdminButtonGroup,
    AdminButton,
    AdminModal,
    AdminBadge
  },
  setup() {
    const alertStore = useAlertStore()
    const toast = useToast()
    
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
    const products = ref([])
    const users = ref([])
    
    // Sort options for the filter component
    const sortOptions = [
      { value: 'created_at|desc', label: 'Najnowsze' },
      { value: 'created_at|asc', label: 'Najstarsze' },
      { value: 'total|desc', label: 'Najwyższa kwota' },
      { value: 'total|asc', label: 'Najniższa kwota' }
    ]
    
    // Table columns definition
    const tableColumns = [
      { key: 'order_number', label: 'Nr zamówienia', sortable: true, width: '120px' },
      { key: 'customer', label: 'Klient', sortable: false, width: '200px' },
      { key: 'created_at', label: 'Data', sortable: true, type: 'date', width: '120px' },
      { key: 'total', label: 'Kwota', sortable: true, width: '100px' },
      { key: 'status', label: 'Status', sortable: true, align: 'center', width: '120px' },
      { key: 'actions', label: 'Akcje', align: 'right', width: '320px' }
    ]
    
    // Order items table columns
    const orderItemsColumns = [
      { key: 'product_name', label: 'Produkt' },
      { key: 'price', label: 'Cena', align: 'right' },
      { key: 'quantity', label: 'Ilość', align: 'center' },
      { key: 'total', label: 'Suma', align: 'right' }
    ]
    
    // Status variant mapping for badges
    const getStatusVariant = (status) => {
      const variants = {
        'pending': 'yellow',
        'processing': 'blue', 
        'completed': 'green',
        'cancelled': 'red'
      }
      return variants[status] || 'gray'
    }
    
    // Handle table sorting
    const handleSort = (sortData) => {
      filters.value.sort_field = sortData.key
      filters.value.sort_direction = sortData.order
      filters.value.page = 1 // Reset to first page when sorting
      fetchOrders()
    }
    
    const filters = ref({
      search: '',
      status: '',
      date_from: '',
      date_to: '',
      sort_by: 'created_at',
      sort_dir: 'desc',
      page: 1
    })
    
    // Modals
    const showDetailsModal = ref(false)
    const showStatusModal = ref(false)
    const selectedOrder = ref(null)
    const newStatus = ref('')
    const statusNote = ref('')
    const notifyCustomer = ref(true)
    const showCreateOrderModal = ref(false)
    const showEditModal = ref(false)
    const showDeleteModal = ref(false)
    const editedOrder = ref({
      user_id: '',
      first_name: '',
      last_name: '',
      email: '',
      phone: '',
      address: '',
      postal_code: '',
      city: '',
      country: 'Polska',
      items: [],
      shipping_method: 'courier',
      payment_method: 'bank_transfer',
      payment_status: 'pending',
      notes: '',
      status: 'pending'
    })
    const orderToDelete = ref(null)
    const validationErrors = ref([])
    
    // Methods
    const fetchOrders = async () => {
      try {
        loading.value = true
        
        // Convert sort option to sort_by and sort_dir
        if (filters.value.sort_option) {
          const [sortBy, sortDir] = filters.value.sort_option.split('|')
          filters.value.sort_by = sortBy
          filters.value.sort_dir = sortDir
        }
        
        const response = await axios.get('/api/admin/orders', { params: filters.value })
        orders.value = response.data
      } catch (error) {
        console.error('Error fetching orders:', error)
        toast.error('Nie udało się pobrać zamówień')
      } finally {
        loading.value = false
      }
    }
    
    const fetchProducts = async () => {
      try {
        const response = await axios.get('/api/admin/products', { 
          params: { 
            per_page: 100,
            sort_by: 'name',
            sort_dir: 'asc'
          } 
        })
        products.value = response.data.data
      } catch (error) {
        console.error('Error fetching products:', error)
        toast.error('Nie udało się pobrać listy produktów')
      }
    }
    
    const fetchUsers = async () => {
      try {
        const response = await axios.get('/api/admin/users', { 
          params: { 
            per_page: 100,
            sort_by: 'name',
            sort_dir: 'asc'
          } 
        })
        users.value = response.data.data
      } catch (error) {
        console.error('Error fetching users:', error)
        toast.error('Nie udało się pobrać listy użytkowników')
      }
    }
    
    const goToPage = (page) => {
      if (page === '...') return
      filters.value.page = page
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
        const response = await axios.put(`/api/admin/orders/${selectedOrder.value.id}/status`, {
          status: newStatus.value,
          note: statusNote.value,
          notify_customer: notifyCustomer.value
        })
        
        // Update the order in the list
        const index = orders.value.data.findIndex(o => o.id === selectedOrder.value.id)
        if (index !== -1) {
          orders.value.data[index].status = newStatus.value
        }
        
        showStatusModal.value = false
        toast.success('Status zamówienia został zaktualizowany')
      } catch (error) {
        console.error('Error updating order status:', error)
        toast.error('Nie udało się zaktualizować statusu zamówienia')
      }
    }
    
    const formatDate = (dateString) => {
      const options = { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' }
      return new Date(dateString).toLocaleDateString('pl-PL', options)
    }
    
    const translateStatus = (status) => {
      const statusMap = {
        'pending': 'Oczekujące',
        'processing': 'W realizacji',
        'completed': 'Zrealizowane',
        'cancelled': 'Anulowane'
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
    
    // Manual order creation functions
    const openCreateOrderModal = () => {
      // Reset the form
      editedOrder.value = {
        user_id: '',
        first_name: '',
        last_name: '',
        email: '',
        phone: '',
        address: '',
        postal_code: '',
        city: '',
        country: 'Polska',
        items: [],
        shipping_method: 'courier',
        payment_method: 'bank_transfer',
        payment_status: 'pending',
        notes: '',
        status: 'pending'
      }
      
      // Fetch products and users if not already loaded
      if (products.value.length === 0) {
        fetchProducts()
      }
      if (users.value.length === 0) {
        fetchUsers()
      }
      
      showCreateOrderModal.value = true
    }
    
    const openEditModal = (order = null) => {
      if (order) {
        // Clone the order to avoid modifying the original
        const orderData = { ...order };
        
        // Fetch product details if needed
        if (!orderData.items || orderData.items.length === 0) {
          axios.get(`/api/admin/orders/${orderData.id}`)
            .then(response => {
              const fullOrder = response.data;
              
              // Map items to the format expected by the form
              const items = fullOrder.items.map(item => ({
                product_id: item.product_id,
                quantity: item.quantity
              }));
              
              editedOrder.value = {
                ...fullOrder,
                items: items
              };
              
              showEditModal.value = true;
            })
            .catch(error => {
              console.error('Error fetching order details:', error);
              toast.error('Nie udało się pobrać szczegółów zamówienia');
            });
        } else {
          // Map items to the format expected by the form
          const items = orderData.items.map(item => ({
            product_id: item.product_id,
            quantity: item.quantity
          }));
          
          editedOrder.value = {
            ...orderData,
            items: items
          };
          
          showEditModal.value = true;
        }
      } else {
        // Reset form for creating a new order
        editedOrder.value = {
          user_id: '',
          first_name: '',
          last_name: '',
          email: '',
          phone: '',
          address: '',
          postal_code: '',
          city: '',
          country: 'Polska',
          items: [],
          shipping_method: 'courier',
          payment_method: 'bank_transfer',
          payment_status: 'pending',
          notes: '',
          status: 'pending'
        };
        
        showCreateOrderModal.value = true;
      }
    }
    
    const closeEditModal = () => {
      showEditModal.value = false
      showCreateOrderModal.value = false
      validationErrors.value = []
      editedOrder.value = {
        user_id: '',
        first_name: '',
        last_name: '',
        email: '',
        phone: '',
        address: '',
        postal_code: '',
        city: '',
        country: 'Polska',
        items: [],
        shipping_method: 'courier',
        payment_method: 'bank_transfer',
        payment_status: 'pending',
        notes: '',
        status: 'pending'
      }
    }
    
    const addProduct = () => {
      editedOrder.value.items.push({
        product_id: '',
        quantity: 1
      })
    }
    
    const removeProduct = (index) => {
      editedOrder.value.items.splice(index, 1)
    }
    
    // Add a watch on user_id to fetch user details when changed
    watch(() => editedOrder.value.user_id, async (newUserId) => {
      if (newUserId) {
        try {
          const response = await axios.get(`/api/admin/users/${newUserId}`)
          const userData = response.data
          
          // Fill in email from user data - ensuring it's a valid string
          editedOrder.value.email = userData.email || ''
          
          // Parse name into first and last name
          if (userData.name) {
            const nameParts = userData.name.split(' ')
            if (nameParts.length > 1) {
              editedOrder.value.first_name = nameParts[0]
              editedOrder.value.last_name = nameParts.slice(1).join(' ')
            } else {
              editedOrder.value.first_name = userData.name
              editedOrder.value.last_name = ''
            }
          }
          
          // Also prefill address data if available
          if (userData.address) {
            editedOrder.value.address = userData.address
          }
          if (userData.city) {
            editedOrder.value.city = userData.city
          }
          if (userData.postal_code) {
            editedOrder.value.postal_code = userData.postal_code
          }
          if (userData.phone) {
            editedOrder.value.phone = userData.phone
          }
          
          console.log('Loaded user data:', userData)
        } catch (error) {
          console.error('Error fetching user data:', error)
          toast.error('Nie udało się pobrać danych użytkownika')
        }
      } else {
        // Clear form fields when no user is selected
        editedOrder.value.email = ''
        editedOrder.value.first_name = ''
        editedOrder.value.last_name = ''
      }
    })

    // Add these helper functions outside handleSubmit
    const debugFormValue = (key, value) => {
      console.log(`Field ${key}:`, value, 'Type:', typeof value, 'Value is empty?', !value)
      return value
    }

    const ensureString = (value) => {
      if (value === null || value === undefined || value === '') return ''
      // Convert to string and trim any whitespace
      const stringValue = String(value).trim()
      console.log(`Converting value: "${value}" (${typeof value}) to string: "${stringValue}"`)
      return stringValue
    }

    const handleSubmit = async () => {
      try {
        // Reset validation errors at the beginning
        validationErrors.value = []
        
        // Special handling for edit case - fetch user details if needed
        if (showEditModal.value && editedOrder.value.user_id && (!editedOrder.value.email || !editedOrder.value.first_name)) {
          try {
            const userResponse = await axios.get(`/api/admin/users/${editedOrder.value.user_id}`)
            const userData = userResponse.data
            
            // If we have a user but missing fields, fill them from user data
            if (!editedOrder.value.email) {
              editedOrder.value.email = userData.email
            }
            
            // Extract first and last name from the user's name
            if (!editedOrder.value.first_name && userData.name) {
              const nameParts = userData.name.split(' ')
              if (nameParts.length > 1) {
                editedOrder.value.first_name = nameParts[0]
                editedOrder.value.last_name = nameParts.slice(1).join(' ')
              } else {
                editedOrder.value.first_name = userData.name
                editedOrder.value.last_name = ''
              }
            }
          } catch (error) {
            console.error('Error fetching user details:', error)
          }
        }

        // Check if the order has at least one item
        if (editedOrder.value.items.length === 0) {
          toast.error('Dodaj co najmniej jeden produkt do zamówienia')
          return
        }

        // Check if email is empty and user_id is not set, add required field indicator
        if (!editedOrder.value.user_id && (!editedOrder.value.email || !editedOrder.value.email.trim())) {
          toast.error('Email jest wymagany')
          validationErrors.value.push('Email jest wymagany')
          return
        }

        // Trim email field to remove any whitespace
        if (editedOrder.value.email) {
          editedOrder.value.email = editedOrder.value.email.trim()
        }

        // Validate email format if not a registered user
        if (!editedOrder.value.user_id) {
          const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
          if (!emailRegex.test(editedOrder.value.email)) {
            toast.error('Wprowadź poprawny adres email')
            validationErrors.value.push('Niepoprawny format adresu email')
            return
          }
          
          // Additional validation for unregistered users
          if (!editedOrder.value.first_name || !editedOrder.value.last_name) {
            toast.error('Dla niezarejestrowanego użytkownika, imię i nazwisko są wymagane')
            if (!editedOrder.value.first_name) validationErrors.value.push('Imię jest wymagane')
            if (!editedOrder.value.last_name) validationErrors.value.push('Nazwisko jest wymagane')
            return
          }
        }

        // Find prices for all products and calculate totals
        let subtotal = 0
        const shipping_cost = 15 // Default shipping cost

        // Prepare items with prices
        const orderItems = []
        for (const item of editedOrder.value.items) {
          if (!item.product_id) {
            toast.error('Wybierz produkt dla wszystkich pozycji zamówienia')
            return
          }

          // Find product to get the price
          const product = products.value.find(p => p.id == item.product_id)
          if (!product) {
            toast.error(`Nie znaleziono produktu o ID ${item.product_id}`)
            return
          }

          // Calculate item total
          const price = parseFloat(product.price)
          const quantity = parseInt(item.quantity)
          const itemTotal = price * quantity
          subtotal += itemTotal

          // Add to items array with all required fields
          orderItems.push({
            product_id: item.product_id,
            product_name: product.name,
            quantity: quantity,
            price: price,
            total: itemTotal
          })
        }

        // Calculate total
        const total = subtotal + shipping_cost

        // Ensure all required fields are present
        if (!editedOrder.value.user_id) {
          if (!editedOrder.value.first_name || !editedOrder.value.last_name || !editedOrder.value.email) {
            toast.error('Wypełnij wszystkie wymagane pola klienta')
            if (!editedOrder.value.first_name) validationErrors.value.push('Imię jest wymagane')
            if (!editedOrder.value.last_name) validationErrors.value.push('Nazwisko jest wymagane')
            if (!editedOrder.value.email) validationErrors.value.push('Email jest wymagany')
            return
          }
        }

        // Ensure address fields are filled
        if (!editedOrder.value.address || !editedOrder.value.city || !editedOrder.value.postal_code) {
          toast.error('Wypełnij wszystkie pola adresu')
          if (!editedOrder.value.address) validationErrors.value.push('Adres jest wymagany')
          if (!editedOrder.value.city) validationErrors.value.push('Miasto jest wymagane')
          if (!editedOrder.value.postal_code) validationErrors.value.push('Kod pocztowy jest wymagany')
          return
        }

        // Just before preparing orderData
        console.log('Form state before submission:', {
          user_id: editedOrder.value.user_id,
          first_name: editedOrder.value.first_name,
          last_name: editedOrder.value.last_name,
          email: editedOrder.value.email,
          items: editedOrder.value.items
        })

        // Ensure first_name and last_name are valid strings
        const firstName = editedOrder.value.first_name || ''
        const lastName = editedOrder.value.last_name || ''

        // Create name from first and last name, ensuring it's a valid string
        const fullName = firstName + ' ' + lastName
        console.log('Name value:', fullName, 'Type:', typeof fullName)

        // Ensure email is valid
        const emailValue = editedOrder.value.email ? String(editedOrder.value.email).trim() : ''
        console.log('Email value:', emailValue, 'Type:', typeof emailValue)

        // Front-end validation before api call
        if (!emailValue || !emailValue.includes('@') || !emailValue.includes('.')) {
          toast.error('Proszę wprowadzić poprawny adres email')
          validationErrors.value.push('Adres email musi być w poprawnym formacie')
          return
        }

        // Prepare complete order data according to Order model
        const formattedOrderData = {
          // Basic user info
          user_id: editedOrder.value.user_id || null,
          first_name: ensureString(firstName),
          last_name: ensureString(lastName),
          email: ensureString(emailValue),
          
          // Contact and address fields
          phone: ensureString(editedOrder.value.phone),
          address: ensureString(editedOrder.value.address),
          city: ensureString(editedOrder.value.city),
          postal_code: ensureString(editedOrder.value.postal_code),
          country: ensureString(editedOrder.value.country || 'Polska'),
          
          // Order details
          notes: ensureString(editedOrder.value.notes),
          status: ensureString(editedOrder.value.status || 'pending'),
          payment_status: ensureString(editedOrder.value.payment_status || 'pending'),
          payment_method: ensureString(editedOrder.value.payment_method || 'bank_transfer'),
          shipping_method: ensureString(editedOrder.value.shipping_method || 'courier'),
          shipping_cost: shipping_cost,
          subtotal: subtotal,
          total: total,
          
          // Order items
          items: orderItems
        }

        // Debug each field
        Object.entries(formattedOrderData).forEach(([key, value]) => {
          debugFormValue(key, value)
        })

        // Use formattedOrderData instead of orderData
        console.log('Sending order data:', JSON.stringify(formattedOrderData))

        // Submit the order
        if (showEditModal.value) {
          await axios.put(`/api/admin/orders/${editedOrder.value.id}`, formattedOrderData)
          toast.success('Zamówienie zostało zaktualizowane')
        } else {
          // Log the full request for debugging
          console.log('Submit URL: /api/admin/orders')
          console.log('Submit data:', {
            user_id: formattedOrderData.user_id,
            first_name: formattedOrderData.first_name,
            last_name: formattedOrderData.last_name,
            email: formattedOrderData.email
          })
          
          const response = await axios.post('/api/admin/orders', formattedOrderData)
          console.log('Server response:', response.data)
          toast.success('Zamówienie zostało utworzone')
        }
        
        // Close modal and refresh orders list
        closeEditModal()
        fetchOrders()
      } catch (error) {
        console.error('Error saving order:', error.response?.data || error)
        let errorMessage = 'Wystąpił błąd podczas zapisywania zamówienia'
        
        validationErrors.value = [] // Reset validation errors
        
        if (error.response?.data?.errors) {
          // Format validation errors
          const errors = error.response.data.errors
          console.log('Validation errors:', errors)
          Object.entries(errors).forEach(([field, errorArray]) => {
            validationErrors.value.push(...errorArray)
            console.log(`Field ${field} errors:`, errorArray)
          })
          errorMessage += ': Sprawdź formularz'
        } else if (error.response?.data?.message) {
          errorMessage += ': ' + error.response.data.message
        }
        
        toast.error(errorMessage)
      }
    }
    
    const confirmDelete = (order) => {
      orderToDelete.value = order
      showDeleteModal.value = true
    }
    
    const deleteOrder = async () => {
      try {
        await axios.delete(`/api/admin/orders/${orderToDelete.value.id}`)
        toast.success('Zamówienie zostało usunięte')
        showDeleteModal.value = false
        orderToDelete.value = null
        fetchOrders()
      } catch (error) {
        console.error('Error deleting order:', error)
        toast.error('Wystąpił błąd podczas usuwania zamówienia')
      }
    }
    
    const formatPrice = (price) => {
      return new Intl.NumberFormat('pl-PL', {
        style: 'currency',
        currency: 'PLN'
      }).format(price)
    }
    
    // Lifecycle
    onMounted(() => {
      fetchOrders()
      fetchProducts()
      fetchUsers()
    })
    
    watch(() => filters.value.search, () => {
      // Debounce search input
      clearTimeout(window.searchTimeout)
      window.searchTimeout = setTimeout(() => {
        fetchOrders()
      }, 500)
    })
    
    return {
      loading,
      orders,
      products,
      users,
      filters,
      sortOptions,
      tableColumns,
      orderItemsColumns,
      selectedOrder,
      showDetailsModal,
      showStatusModal,
      newStatus,
      statusNote,
      notifyCustomer,
      showCreateOrderModal,
      showEditModal,
      showDeleteModal,
      editedOrder,
      orderToDelete,
      validationErrors,
      fetchOrders,
      goToPage,
      openOrderDetails,
      openStatusModal,
      updateOrderStatus,
      formatDate,
      translateStatus,
      translatePaymentStatus,
      getStatusVariant,
      handleSort,
      openCreateOrderModal,
      openEditModal,
      closeEditModal,
      addProduct,
      removeProduct,
      handleSubmit,
      confirmDelete,
      deleteOrder,
      formatPrice
    }
  }
}
</script> 