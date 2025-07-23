<template>
  <div class="space-y-6 bg-white min-h-full">
    <!-- Page Header -->
    <div class="px-6 py-4">
      <div class="sm:flex sm:items-center">
        <div class="sm:flex-auto">
          <h1 class="text-2xl font-semibold text-gray-900">Zamówienia</h1>
        </div>
        <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none">
          <button 
            @click="openCreateOrderModal" 
            type="button" 
            class="px-3 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700 transition-colors"
          >
            Dodaj
          </button>
        </div>
      </div>
    </div>

    <!-- Search and filters -->
    <div v-if="!loading" class="mt-6 bg-white shadow px-4 py-5 sm:rounded-lg sm:px-6">
      <!-- Search Input -->
      <div class="mb-4">
        <label for="search" class="block text-sm font-medium text-gray-700">Wyszukaj</label>
        <div class="mt-1 flex rounded-md shadow-sm">
          <input
            type="text"
            name="search"
            id="search"
            v-model="filters.search"
            @keyup.enter="fetchOrders"
            class="flex-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full min-w-0 rounded-none rounded-l-md sm:text-sm border-gray-300"
            placeholder="Numer zamówienia, email klienta..."
          />
          <button
            type="button"
            @click="fetchOrders"
            class="inline-flex items-center px-3 py-2 border border-l-0 border-gray-300 rounded-r-md bg-gray-50 text-gray-500 text-sm hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
          >
            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            <span class="ml-1">Szukaj</span>
          </button>
        </div>
      </div>
      
      <!-- Other Filters and Sort Options -->
      <div class="flex flex-wrap gap-4 items-end">
        <!-- Custom Filters -->
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
            <option value="processing">W trakcie realizacji</option>
            <option value="completed">Zrealizowane</option>
            <option value="shipped">Wysłane</option>
            <option value="delivered">Dostarczone</option>
            <option value="cancelled">Anulowane</option>
            <option value="refunded">Zwrócone</option>
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
        
        <!-- Sort Field -->
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
            <option value="status">Status</option>
            <option value="total">Wartość</option>
            <option value="payment_status">Status płatności</option>
          </select>
        </div>
        
        <!-- Sort Direction -->
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
        
        <!-- Reset Filters Button -->
        <div class="w-full sm:w-auto">
          <button
            type="button"
            @click="resetFilters"
            class="mt-1 inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
          >
            <svg class="h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
            Resetuj filtry
          </button>
        </div>
      </div>
    </div>

    <!-- Loading indicator -->
    <div v-if="loading" class="flex justify-center items-center py-12">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div>
    </div>

    <!-- Orders Custom Table -->
    <div v-if="!loading && orders.data && orders.data.length > 0" class="mt-6 bg-white shadow-sm rounded-lg overflow-hidden">
                      <div class="overflow-x-auto -mx-6 px-6" style="scrollbar-width: thin; scrollbar-color: #d1d5db #f3f4f6;">
          <table class="min-w-full divide-y divide-gray-200 whitespace-nowrap">
          <thead class="bg-gray-50">
            <tr>
              <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-20">
                Nr zamówienia
              </th>
              <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">
                Klient
              </th>
              <th scope="col" class="px-2 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-16">
                Data
              </th>
              <th scope="col" class="px-2 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-16">
                Kwota
              </th>
              <th scope="col" class="px-2 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-20">
                Status
              </th>
              <th scope="col" class="px-3 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider w-32">
                Akcje
              </th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="item in orders.data" :key="item.id" class="hover:bg-gray-50">
              <!-- Order Number Column -->
              <td class="px-3 py-4">
                <div class="text-sm font-medium text-gray-900">
                  {{ item.order_number || '#' + item.id }}
                </div>
              </td>
              
              <!-- Customer Column -->
              <td class="px-3 py-4">
                <div class="flex items-center">
                  <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-800 flex items-center justify-center text-xs font-semibold mr-3">
                    {{ getCustomerInitials(item) }}
                  </div>
                  <div>
                    <div class="text-sm font-medium text-gray-900">{{ item.user ? item.user.name : 'Gość' }}</div>
                    <div class="text-xs text-gray-500 truncate max-w-[100px]" :title="item.user ? item.user.email : item.email">
                      {{ item.user ? item.user.email : item.email }}
                    </div>
                  </div>
                </div>
              </td>
              
              <!-- Date Column -->
              <td class="px-2 py-4 text-center">
                <div class="text-xs text-gray-900">
                  {{ formatShortDate(item.created_at) }}
                </div>
              </td>
              
              <!-- Total Column -->
              <td class="px-2 py-4 text-center">
                <span class="text-sm font-medium text-gray-900">{{ item.total }} PLN</span>
              </td>
              
              <!-- Status Column -->
              <td class="px-2 py-4 text-center">
                <div 
                  class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium cursor-pointer"
                  :class="getStatusClasses(item.status)"
                  @click="changeStatus(item)"
                  :title="'Kliknij aby zmienić status: ' + translateStatus(item.status)"
                >
                  {{ getStatusShort(item.status) }}
                </div>
              </td>
              
              <!-- Actions Column -->
              <td class="px-3 py-4 text-right">
                <action-buttons 
                  :item="item" 
                  @edit="editOrder" 
                  @delete="confirmDelete"
                  @details="openOrderDetails"
                  :show-details="true"
                  :show-edit="true"
                  :show-delete="true"
                  justify="end"
                />
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    
    <!-- No data message -->
    <div v-else-if="!loading && (!orders.data || orders.data.length === 0)" class="bg-white shadow overflow-hidden sm:rounded-md p-6 text-center text-gray-500 mt-6">
      Brak zamówień do wyświetlenia
    </div>
    
    <!-- Pagination -->
    <div v-if="!loading && orders.data && orders.data.length > 0 && orders.last_page > 1" class="mt-5 flex justify-between items-center p-6">
      <div class="text-sm text-gray-700">
        Pokazuje {{ orders.from || 0 }} do {{ orders.to || 0 }} z {{ orders.total || 0 }} zamówień
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
            Zamówienie {{ selectedOrder.order_number || '#' + selectedOrder.id }}
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
              <p class="font-medium">{{ selectedOrder.user ? selectedOrder.user.name : (selectedOrder.first_name + ' ' + selectedOrder.last_name) }}</p>
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
              {{ formatPrice(item.price || 0) }}
            </template>
            
            <template #cell-total="{ item }">
              {{ formatPrice((item.price || 0) * (item.quantity || 0)) }}
            </template>
          </admin-table>
          
          <div class="mt-4 flex justify-end">
            <div class="text-lg font-bold">
              Suma: {{ formatPrice(selectedOrder.total || 0) }}
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
              {{ translatePaymentMethod(selectedOrder.payment_method) || 'Nieznana' }}
            </div>
            
            <h4 class="text-sm font-medium text-gray-500 mb-2 mt-4">Metoda dostawy</h4>
            <div class="text-sm text-gray-900">
              {{ translateShippingMethod(selectedOrder.shipping_method) || 'Nieznana' }}
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
              Zamówienie {{ selectedOrder ? (selectedOrder.order_number || '#' + selectedOrder.id) : '' }}
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
                <option value="processing">W trakcie realizacji</option>
                <option value="completed">Zrealizowane</option>
                <option value="shipped">Wysłane</option>
                <option value="delivered">Dostarczone</option>
                <option value="cancelled">Anulowane</option>
                <option value="refunded">Zwrócone</option>
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
    <admin-modal
      :show="showCreateOrderModal || showEditModal"
      :title="showEditModal ? 'Edytuj zamówienie' : 'Dodaj nowe zamówienie'"
      size="4xl"
      @close="closeEditModal"
    >
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
                {{ user.name }} ({{ user.email }}){{ user.is_admin ? ' [ADMIN]' : '' }}
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
                  :class="[
                    'mt-1 block w-full rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm',
                    formErrors.first_name 
                      ? 'border-red-300 focus:border-red-500 focus:ring-red-500' 
                      : 'border-gray-300 focus:border-indigo-500 focus:ring-indigo-500'
                  ]"
                />
                <p v-if="formErrors.first_name" class="mt-1 text-sm text-red-600">{{ Array.isArray(formErrors.first_name) ? formErrors.first_name[0] : formErrors.first_name }}</p>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">Nazwisko <span class="text-red-500">*</span></label>
                <input
                  type="text"
                  v-model="editedOrder.last_name"
                  required
                  :class="[
                    'mt-1 block w-full rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm',
                    formErrors.last_name 
                      ? 'border-red-300 focus:border-red-500 focus:ring-red-500' 
                      : 'border-gray-300 focus:border-indigo-500 focus:ring-indigo-500'
                  ]"
                />
                <p v-if="formErrors.last_name" class="mt-1 text-sm text-red-600">{{ Array.isArray(formErrors.last_name) ? formErrors.last_name[0] : formErrors.last_name }}</p>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">Email <span class="text-red-500">*</span></label>
                <input
                  type="email"
                  v-model="editedOrder.email"
                  required
                  :class="[
                    'mt-1 block w-full rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm',
                    formErrors.email 
                      ? 'border-red-300 focus:border-red-500 focus:ring-red-500' 
                      : 'border-gray-300 focus:border-indigo-500 focus:ring-indigo-500'
                  ]"
                />
                <p v-if="formErrors.email" class="mt-1 text-sm text-red-600">{{ Array.isArray(formErrors.email) ? formErrors.email[0] : formErrors.email }}</p>
                <p v-else class="text-xs text-gray-500">Format: nazwa@domena.pl</p>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">Telefon</label>
                <input
                  type="text"
                  v-model="editedOrder.phone"
                  :class="[
                    'mt-1 block w-full rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm',
                    formErrors.phone 
                      ? 'border-red-300 focus:border-red-500 focus:ring-red-500' 
                      : 'border-gray-300 focus:border-indigo-500 focus:ring-indigo-500'
                  ]"
                />
                <p v-if="formErrors.phone" class="mt-1 text-sm text-red-600">{{ Array.isArray(formErrors.phone) ? formErrors.phone[0] : formErrors.phone }}</p>
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
                :class="[
                  'mt-1 block w-full rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm',
                  formErrors.address 
                    ? 'border-red-300 focus:border-red-500 focus:ring-red-500' 
                    : 'border-gray-300 focus:border-indigo-500 focus:ring-indigo-500'
                ]"
              />
              <p v-if="formErrors.address" class="mt-1 text-sm text-red-600">{{ Array.isArray(formErrors.address) ? formErrors.address[0] : formErrors.address }}</p>
            </div>
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700">Kod pocztowy</label>
                <input
                  type="text"
                  v-model="editedOrder.postal_code"
                  :class="[
                    'mt-1 block w-full rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm',
                    formErrors.postal_code 
                      ? 'border-red-300 focus:border-red-500 focus:ring-red-500' 
                      : 'border-gray-300 focus:border-indigo-500 focus:ring-indigo-500'
                  ]"
                />
                <p v-if="formErrors.postal_code" class="mt-1 text-sm text-red-600">{{ Array.isArray(formErrors.postal_code) ? formErrors.postal_code[0] : formErrors.postal_code }}</p>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">Miasto</label>
                <input
                  type="text"
                  v-model="editedOrder.city"
                  :class="[
                    'mt-1 block w-full rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm',
                    formErrors.city 
                      ? 'border-red-300 focus:border-red-500 focus:ring-red-500' 
                      : 'border-gray-300 focus:border-indigo-500 focus:ring-indigo-500'
                  ]"
                />
                <p v-if="formErrors.city" class="mt-1 text-sm text-red-600">{{ Array.isArray(formErrors.city) ? formErrors.city[0] : formErrors.city }}</p>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">Kraj</label>
                <input
                  type="text"
                  v-model="editedOrder.country"
                  :class="[
                    'mt-1 block w-full rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm',
                    formErrors.country 
                      ? 'border-red-300 focus:border-red-500 focus:ring-red-500' 
                      : 'border-gray-300 focus:border-indigo-500 focus:ring-indigo-500'
                  ]"
                />
                <p v-if="formErrors.country" class="mt-1 text-sm text-red-600">{{ Array.isArray(formErrors.country) ? formErrors.country[0] : formErrors.country }}</p>
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
              class="mt-2 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded hover:bg-indigo-700"
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
                :class="[
                  'mt-1 block w-full rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm',
                  formErrors.status 
                    ? 'border-red-300 focus:border-red-500 focus:ring-red-500' 
                    : 'border-gray-300 focus:border-indigo-500 focus:ring-indigo-500'
                ]"
              >
                <option value="pending">Oczekujące</option>
                <option value="processing">W trakcie realizacji</option>
                <option value="completed">Zrealizowane</option>
                <option value="shipped">Wysłane</option>
                <option value="delivered">Dostarczone</option>
                <option value="cancelled">Anulowane</option>
                <option value="refunded">Zwrócone</option>
              </select>
              <p v-if="formErrors.status" class="mt-1 text-sm text-red-600">{{ Array.isArray(formErrors.status) ? formErrors.status[0] : formErrors.status }}</p>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Metoda wysyłki</label>
              <select
                v-model="editedOrder.shipping_method"
                :class="[
                  'mt-1 block w-full rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm',
                  formErrors.shipping_method 
                    ? 'border-red-300 focus:border-red-500 focus:ring-red-500' 
                    : 'border-gray-300 focus:border-indigo-500 focus:ring-indigo-500'
                ]"
              >
                <option value="courier">Kurier</option>
                <option value="inpost">InPost</option>
                <option value="pickup">Odbiór osobisty</option>
              </select>
              <p v-if="formErrors.shipping_method" class="mt-1 text-sm text-red-600">{{ Array.isArray(formErrors.shipping_method) ? formErrors.shipping_method[0] : formErrors.shipping_method }}</p>
            </div>
          </div>

          <!-- Payment Method and Status -->
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700">Metoda płatności</label>
              <select
                v-model="editedOrder.payment_method"
                :class="[
                  'mt-1 block w-full rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm',
                  formErrors.payment_method 
                    ? 'border-red-300 focus:border-red-500 focus:ring-red-500' 
                    : 'border-gray-300 focus:border-indigo-500 focus:ring-indigo-500'
                ]"
              >
                <option value="bank_transfer">Przelew bankowy</option>
                <option value="cash_on_delivery">Płatność przy odbiorze</option>
                <option value="online">Płatność online</option>
                <option value="blik">BLIK</option>
              </select>
              <p v-if="formErrors.payment_method" class="mt-1 text-sm text-red-600">{{ Array.isArray(formErrors.payment_method) ? formErrors.payment_method[0] : formErrors.payment_method }}</p>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Status płatności</label>
              <select
                v-model="editedOrder.payment_status"
                :class="[
                  'mt-1 block w-full rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm',
                  formErrors.payment_status 
                    ? 'border-red-300 focus:border-red-500 focus:ring-red-500' 
                    : 'border-gray-300 focus:border-indigo-500 focus:ring-indigo-500'
                ]"
              >
                <option value="pending">Oczekująca</option>
                <option value="completed">Opłacone</option>
                <option value="failed">Nieudana</option>
              </select>
              <p v-if="formErrors.payment_status" class="mt-1 text-sm text-red-600">{{ Array.isArray(formErrors.payment_status) ? formErrors.payment_status[0] : formErrors.payment_status }}</p>
            </div>
          </div>

          <!-- Discount -->
          <div>
            <label class="block text-sm font-medium text-gray-700">Rabat (PLN)</label>
            <input
              type="number"
              step="0.01"
              min="0"
              v-model="editedOrder.discount"
              :class="[
                'mt-1 block w-full rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm',
                formErrors.discount 
                  ? 'border-red-300 focus:border-red-500 focus:ring-red-500' 
                  : 'border-gray-300 focus:border-indigo-500 focus:ring-indigo-500'
              ]"
              placeholder="0.00"
            />
            <p v-if="formErrors.discount" class="mt-1 text-sm text-red-600">{{ Array.isArray(formErrors.discount) ? formErrors.discount[0] : formErrors.discount }}</p>
          </div>

          <!-- Notes -->
          <div>
            <label class="block text-sm font-medium text-gray-700">Uwagi</label>
            <textarea
              v-model="editedOrder.notes"
              rows="3"
              :class="[
                'mt-1 block w-full rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm',
                formErrors.notes 
                  ? 'border-red-300 focus:border-red-500 focus:ring-red-500' 
                  : 'border-gray-300 focus:border-indigo-500 focus:ring-indigo-500'
              ]"
            ></textarea>
            <p v-if="formErrors.notes" class="mt-1 text-sm text-red-600">{{ Array.isArray(formErrors.notes) ? formErrors.notes[0] : formErrors.notes }}</p>
          </div>
        </form>
      
      <template #footer>
        <admin-button-group justify="end" spacing="sm">
          <admin-button
            @click="closeEditModal"
            variant="secondary"
            outline
          >
            Anuluj
          </admin-button>
          <admin-button
            @click="handleSubmit"
            variant="primary"
          >
            {{ showEditModal ? 'Zapisz zmiany' : 'Dodaj zamówienie' }}
          </admin-button>
        </admin-button-group>
      </template>
    </admin-modal>

    <!-- Delete Confirmation Modal -->
    <admin-modal
      :show="showDeleteModal"
      title="Potwierdź usunięcie"
      size="md"
      icon-variant="danger"
      @close="showDeleteModal = false"
    >
      <p class="text-sm text-gray-500">
        Czy na pewno chcesz usunąć to zamówienie? Tej operacji nie można cofnąć.
      </p>
      
      <template #footer>
        <admin-button-group justify="end" spacing="sm">
          <admin-button
            @click="showDeleteModal = false"
            variant="secondary"
            outline
          >
            Anuluj
          </admin-button>
          <admin-button
            @click="deleteOrder"
            variant="danger"
          >
            Usuń
          </admin-button>
        </admin-button-group>
      </template>
    </admin-modal>
  </div>
</template>

<script>
import { ref, reactive, computed, onMounted, watch } from 'vue'
import { useAlertStore } from '../../stores/alertStore'
import { useAuthStore } from '../../stores/authStore'
import axios from 'axios'
import { useRoute } from 'vue-router'

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
import ActionButtons from '../../components/admin/ActionButtons.vue'



export default {
  name: 'AdminOrders',
  components: {
    SearchFilters,
    LoadingSpinner,
    NoDataMessage,
    Pagination,
    PageHeader,
    AdminTable,
    AdminButtonGroup,
    AdminButton,
    AdminModal,
    AdminBadge,
    ActionButtons
  },
  setup() {
    const alertStore = useAlertStore()
    const authStore = useAuthStore()
    const route = useRoute()
    
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
      { value: 'created_at', label: 'Data zamówienia' },
      { value: 'status', label: 'Status' },
      { value: 'total', label: 'Wartość' },
      { value: 'payment_status', label: 'Status płatności' }
    ]
    
    // Order items table columns
    const orderItemsColumns = [
      { key: 'product_name', label: 'Produkt', width: '40%' },
      { key: 'price', label: 'Cena', align: 'right', width: '20%' },
      { key: 'quantity', label: 'Ilość', align: 'center', width: '15%' },
      { key: 'total', label: 'Suma', align: 'right', width: '25%' }
    ]
    
    // Status variant mapping for badges
    const getStatusVariant = (status) => {
      const variants = {
        'pending': 'yellow',
        'processing': 'blue', 
        'completed': 'green',
        'shipped': 'purple',
        'delivered': 'green',
        'cancelled': 'red',
        'refunded': 'gray'
      }
      return variants[status] || 'gray'
    }
    
    // Computed pagination pages
    const paginationPages = computed(() => {
      if (!orders.value || !orders.value.last_page) return []
      
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
    

    
    // Default filters
    const defaultFilters = {
      search: '',
      status: '',
      payment_status: '',
      sort_field: 'created_at',
      sort_direction: 'desc',
      page: 1
    }
    
    const filters = ref({ ...defaultFilters })
    
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
    const formErrors = ref({})
    
    // Methods
    const fetchOrders = async () => {
      try {
        loading.value = true
        
        const response = await axios.get('/api/admin/orders', { params: filters.value })
        orders.value = response.data.data
      } catch (error) {
        console.error('Error fetching orders:', error)
        
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
        
        alertStore.error('Nie udało się pobrać zamówień')
      } finally {
        loading.value = false
      }
    }
    
    const fetchProducts = async () => {
      try {
        const response = await axios.get('/api/admin/products', { 
          params: { 
            per_page: 100,
            sort_field: 'name',
            sort_direction: 'asc'
          } 
        })
        products.value = response.data.data
      } catch (error) {
        console.error('Error fetching products:', error)
        alertStore.error('Nie udało się pobrać listy produktów')
      }
    }
    
    const fetchUsers = async () => {
      try {
        const response = await axios.get('/api/admin/users', { 
          params: { 
            per_page: 100,
            sort_field: 'name',
            sort_direction: 'asc'
          } 
        })
        users.value = response.data.data
        console.log('Fetched users:', users.value.length, 'users (including', users.value.filter(u => u.is_admin).length, 'admins)')
      } catch (error) {
        console.error('Error fetching users:', error)
        alertStore.error('Nie udało się pobrać listy użytkowników')
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
        selectedOrder.value = response.data.data
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
        console.log('Updating order status:', {
          orderId: selectedOrder.value.id,
          newStatus: newStatus.value,
          note: statusNote.value,
          notifyCustomer: notifyCustomer.value
        })
        
        const response = await axios.put(`/api/admin/orders/${selectedOrder.value.id}/status`, {
          status: newStatus.value,
          note: statusNote.value,
          notify_customer: notifyCustomer.value
        })
        
        console.log('Status update response:', response.data)
        
        // Update the order in the list
        const index = orders.value.data.findIndex(o => o.id === selectedOrder.value.id)
        if (index !== -1) {
          orders.value.data[index].status = newStatus.value
        }
        
        showStatusModal.value = false
        alertStore.success(response.data.message || 'Status zamówienia został zaktualizowany')
      } catch (error) {
        console.error('Error updating order status:', error)
        console.error('Error response:', error.response?.data)
        
        let errorMessage = 'Nie udało się zaktualizować statusu zamówienia'
        if (error.response?.data?.message) {
          errorMessage += ': ' + error.response.data.message
        }
        
        alertStore.error(errorMessage)
      }
    }
    
    const formatDate = (dateString) => {
      const options = { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' }
      return new Date(dateString).toLocaleDateString('pl-PL', options)
    }
    
    const translateStatus = (status) => {
      const statusMap = {
        'pending': 'Oczekujące',
        'processing': 'W trakcie realizacji',
        'completed': 'Zrealizowane',
        'shipped': 'Wysłane',
        'delivered': 'Dostarczone',
        'cancelled': 'Anulowane',
        'refunded': 'Zwrócone'
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
        discount: 0,
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
      console.log('Opening edit modal for order:', order);
      console.log('Order ID:', order?.id, 'Type:', typeof order?.id);
      
      // Fetch products and users if not already loaded
      if (products.value.length === 0) {
        fetchProducts()
      }
      if (users.value.length === 0) {
        fetchUsers()
      }
      
      if (order) {
        // Clone the order to avoid modifying the original
        const orderData = { ...order };
        
        // Fetch product details if needed
        if (!orderData.items || orderData.items.length === 0) {
          axios.get(`/api/admin/orders/${orderData.id}`)
            .then(response => {
              const fullOrder = response.data.data;
              
              // Map items to the format expected by the form
              const items = fullOrder.items.map(item => ({
                product_id: item.product_id,
                quantity: item.quantity
              }));
              
              // Ensure all required fields are properly mapped for guest orders
              editedOrder.value = {
                id: parseInt(fullOrder.id) || null, // Ensure ID is a number
                user_id: fullOrder.user_id || '',
                first_name: fullOrder.first_name || '',
                last_name: fullOrder.last_name || '',
                email: fullOrder.email || '',
                phone: fullOrder.phone || '',
                address: fullOrder.address || '',
                postal_code: fullOrder.postal_code || '',
                city: fullOrder.city || '',
                country: fullOrder.country || 'Polska',
                status: fullOrder.status || 'pending',
                payment_status: fullOrder.payment_status || 'pending',
                payment_method: fullOrder.payment_method || 'bank_transfer',
                shipping_method: fullOrder.shipping_method || 'courier',
                shipping_cost: fullOrder.shipping_cost || 0,
                discount: fullOrder.discount || 0,
                total: fullOrder.total || 0,
                notes: fullOrder.notes || '',
                items: items
              };
              
              console.log('Mapped full order data for edit:', editedOrder.value);
              
              // Small delay to ensure data is properly set
              setTimeout(() => {
                showEditModal.value = true;
              }, 100);
            })
            .catch(error => {
              console.error('Error fetching order details:', error);
              alertStore.error('Nie udało się pobrać szczegółów zamówienia');
            });
        } else {
          // Map items to the format expected by the form
          const items = orderData.items.map(item => ({
            product_id: item.product_id,
            quantity: item.quantity
          }));
          
          // Ensure all required fields are properly mapped for guest orders
          editedOrder.value = {
            id: parseInt(orderData.id) || null, // Ensure ID is a number
            user_id: orderData.user_id || '',
            first_name: orderData.first_name || '',
            last_name: orderData.last_name || '',
            email: orderData.email || '',
            phone: orderData.phone || '',
            address: orderData.address || '',
            postal_code: orderData.postal_code || '',
            city: orderData.city || '',
            country: orderData.country || 'Polska',
            status: orderData.status || 'pending',
            payment_status: orderData.payment_status || 'pending',
            payment_method: orderData.payment_method || 'bank_transfer',
            shipping_method: orderData.shipping_method || 'courier',
            shipping_cost: orderData.shipping_cost || 0,
            discount: orderData.discount || 0,
            total: orderData.total || 0,
            notes: orderData.notes || '',
            items: items
          };
          
                      console.log('Mapped order data for edit:', editedOrder.value);
            
            // Small delay to ensure data is properly set
            setTimeout(() => {
              showEditModal.value = true;
            }, 100);
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
          discount: 0,
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
      formErrors.value = {}
      editedOrder.value = {
        id: null, // Reset the order ID
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
        discount: 0,
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
    watch(() => editedOrder.value.user_id, async (newUserId, oldUserId) => {
      // Skip if this is the initial setup (oldUserId is undefined)
      if (oldUserId === undefined) {
        return;
      }
      
      if (newUserId) {
        try {
          const response = await axios.get(`/api/admin/users/${newUserId}`)
          const userData = response.data
          
          // Only fill in data if we're not editing an existing order
          if (!showEditModal.value) {
            // Fill in email from user data - ensuring it's a valid string
            editedOrder.value.email = userData.email || ''
            
            // Use first_name and last_name if available, otherwise parse name
            if (userData.first_name && userData.last_name) {
              editedOrder.value.first_name = userData.first_name
              editedOrder.value.last_name = userData.last_name
            } else if (userData.first_name) {
              // If only first_name is available
              editedOrder.value.first_name = userData.first_name
              editedOrder.value.last_name = userData.last_name || ''
            } else if (userData.name) {
              // Parse from name field
              const nameParts = userData.name.split(' ')
              if (nameParts.length > 1) {
                editedOrder.value.first_name = nameParts[0]
                editedOrder.value.last_name = nameParts.slice(1).join(' ')
              } else {
                editedOrder.value.first_name = userData.name
                editedOrder.value.last_name = ''
              }
            } else {
              // Fallback - clear fields if no name data available
              editedOrder.value.first_name = ''
              editedOrder.value.last_name = ''
            }
          }
          
          // Also prefill address data if available (only when not editing)
          if (!showEditModal.value) {
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
          }
          
          console.log('Loaded user data:', userData)
        } catch (error) {
          console.error('Error fetching user data:', error)
          alertStore.error('Nie udało się pobrać danych użytkownika')
        }
      } else {
        // Only clear form fields when no user is selected AND we're not editing an existing order
        if (!showEditModal.value) {
          editedOrder.value.email = ''
          editedOrder.value.first_name = ''
          editedOrder.value.last_name = ''
          editedOrder.value.phone = ''
          editedOrder.value.address = ''
          editedOrder.value.city = ''
          editedOrder.value.postal_code = ''
        }
      }
    })

    const ensureString = (value) => {
      if (value === null || value === undefined || value === '') return ''
      // Convert to string and trim any whitespace
      const stringValue = String(value).trim()
      console.log(`Converting value: "${value}" (${typeof value}) to string: "${stringValue}"`)
      return stringValue
    }

    const debugFormValue = (key, value) => {
      console.log(`Form field ${key}:`, value, `(type: ${typeof value})`)
    }

    const handleSubmit = async () => {
      try {
        // Reset validation errors at the beginning
        validationErrors.value = []
        formErrors.value = {}
        
        // Special handling for edit case - fetch user details if needed
        if (showEditModal.value && editedOrder.value.user_id && (!editedOrder.value.email || !editedOrder.value.first_name)) {
          try {
            const userResponse = await axios.get(`/api/admin/users/${editedOrder.value.user_id}`)
            const userData = userResponse.data
            
            // If we have a user but missing fields, fill them from user data
            if (!editedOrder.value.email) {
              editedOrder.value.email = userData.email
            }
            
            // Extract first and last name from user data
            if (!editedOrder.value.first_name) {
              if (userData.first_name && userData.last_name) {
                editedOrder.value.first_name = userData.first_name
                editedOrder.value.last_name = userData.last_name
              } else if (userData.name) {
                const nameParts = userData.name.split(' ')
                if (nameParts.length > 1) {
                  editedOrder.value.first_name = nameParts[0]
                  editedOrder.value.last_name = nameParts.slice(1).join(' ')
                } else {
                  editedOrder.value.first_name = userData.name
                  editedOrder.value.last_name = ''
                }
              }
            }
          } catch (error) {
            console.error('Error fetching user details:', error)
          }
        }

        // Check if the order has at least one item
        if (editedOrder.value.items.length === 0) {
          alertStore.error('Dodaj co najmniej jeden produkt do zamówienia')
          return
        }

        // Check if email is empty and user_id is not set, add required field indicator
        if (!editedOrder.value.user_id && (!editedOrder.value.email || !editedOrder.value.email.trim())) {
          alertStore.error('Adres email jest wymagany')
          validationErrors.value.push('Adres email jest wymagany')
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
              alertStore.error('Wprowadź prawidłowy adres email')
              validationErrors.value.push('Nieprawidłowy format adresu email')
              return
            }
          
          // Additional validation for unregistered users
          if (!editedOrder.value.first_name || !editedOrder.value.last_name) {
            alertStore.error('Dla niezarejestrowanego użytkownika, imię i nazwisko są wymagane')
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
            alertStore.error('Wybierz produkt dla wszystkich pozycji zamówienia')
            return
          }

          // Find product to get the price
          const product = products.value.find(p => p.id == item.product_id)
          if (!product) {
            alertStore.error(`Nie znaleziono produktu o ID ${item.product_id}`)
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

        // Calculate total with discount
        const discount = editedOrder.value.discount || 0
        const total = Math.max(0, subtotal + shipping_cost - discount)

        // Ensure all required fields are present
        if (!editedOrder.value.user_id) {
          if (!editedOrder.value.first_name || !editedOrder.value.last_name || !editedOrder.value.email) {
            alertStore.error('Wypełnij wszystkie wymagane pola klienta')
            if (!editedOrder.value.first_name) validationErrors.value.push('Imię jest wymagane')
            if (!editedOrder.value.last_name) validationErrors.value.push('Nazwisko jest wymagane')
            if (!editedOrder.value.email) validationErrors.value.push('Email jest wymagany')
            return
          }
        }

        // Ensure address fields are filled
        if (!editedOrder.value.address || !editedOrder.value.city || !editedOrder.value.postal_code) {
          alertStore.error('Wypełnij wszystkie pola adresu')
          if (!editedOrder.value.address) validationErrors.value.push('Adres jest wymagany')
          if (!editedOrder.value.city) validationErrors.value.push('Miasto jest wymagane')
          if (!editedOrder.value.postal_code) validationErrors.value.push('Kod pocztowy jest wymagany')
          return
        }

        // Ensure all items have valid product_id and quantity
        for (let i = 0; i < editedOrder.value.items.length; i++) {
          const item = editedOrder.value.items[i]
          if (!item.product_id) {
            alertStore.error(`Wybierz produkt dla pozycji ${i + 1}`)
            validationErrors.value.push(`Produkt dla pozycji ${i + 1} jest wymagany`)
            return
          }
          if (!item.quantity || item.quantity < 1) {
            alertStore.error(`Ilość dla pozycji ${i + 1} musi być większa od 0`)
            validationErrors.value.push(`Ilość dla pozycji ${i + 1} musi być większa od 0`)
            return
          }
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
          alertStore.error('Proszę wprowadzić poprawny adres email')
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
          discount: editedOrder.value.discount || 0,
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
        
        // Additional debugging for items array
        if (formattedOrderData.items && formattedOrderData.items.length > 0) {
          console.log('Items array structure:')
          formattedOrderData.items.forEach((item, index) => {
            console.log(`Item ${index}:`, item)
          })
        }

        // Submit the order
        if (showEditModal.value && editedOrder.value.id) {
          console.log('Updating order with ID:', editedOrder.value.id, 'Type:', typeof editedOrder.value.id)
          
          // Ensure ID is a number
          const orderId = parseInt(editedOrder.value.id)
          if (isNaN(orderId)) {
            alertStore.error('Nieprawidłowe ID zamówienia')
            return
          }
          
          await axios.put(`/api/admin/orders/${orderId}`, formattedOrderData)
          alertStore.success(response.data.message || 'Zamówienie zostało zaktualizowane')
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
          alertStore.success(response.data.message || 'Zamówienie zostało utworzone')
        }
        
        // Close modal and refresh orders list
        closeEditModal()
        fetchOrders()
      } catch (error) {
        console.error('Error saving order:', error.response?.data || error)
        let errorMessage = 'Wystąpił błąd podczas zapisywania zamówienia'
        
        validationErrors.value = [] // Reset validation errors
        
        if (error.response?.status === 422 && error.response?.data?.errors) {
          // Store validation errors for individual field display
          formErrors.value = error.response.data.errors
          console.log('Validation errors:', error.response.data.errors)
        } else if (error.response?.status === 404) {
          errorMessage = 'Zamówienie nie zostało znalezione'
        } else if (error.response?.data?.message) {
          errorMessage += ': ' + error.response.data.message
        }
        
        alertStore.error(errorMessage)
      }
    }
    
    const confirmDelete = (order) => {
      orderToDelete.value = order
      showDeleteModal.value = true
    }
    
    const editOrder = (order) => {
      console.log('editOrder called with:', order);
      console.log('Order ID in editOrder:', order?.id, 'Type:', typeof order?.id);
      
      if (!order || !order.id) {
        console.error('No order or order ID provided to editOrder')
        alertStore.error('Nie można edytować zamówienia - brak ID')
        return
      }
      
      openEditModal(order)
    }
    
    const changeStatus = (order) => {
      openStatusModal(order)
    }
    
    const deleteOrder = async () => {
      try {
        await axios.delete(`/api/admin/orders/${orderToDelete.value.id}`)
        alertStore.success(response.data.message || 'Zamówienie zostało usunięte')
        showDeleteModal.value = false
        orderToDelete.value = null
        fetchOrders()
      } catch (error) {
        console.error('Error deleting order:', error)
        alertStore.error('Wystąpił błąd podczas usuwania zamówienia')
      }
    }
    
    const formatPrice = (price) => {
      return new Intl.NumberFormat('pl-PL', {
        style: 'currency',
        currency: 'PLN'
      }).format(price)
    }
    
    // Helper functions for custom table
    const getCustomerInitials = (order) => {
      if (order.user && order.user.name) {
        const nameParts = order.user.name.split(' ')
        if (nameParts.length > 1) {
          return nameParts[0].charAt(0).toUpperCase() + nameParts[nameParts.length - 1].charAt(0).toUpperCase()
        }
        return nameParts[0].charAt(0).toUpperCase()
      }
      return 'G' // For "Gość"
    }
    
    const formatShortDate = (dateString) => {
      const date = new Date(dateString)
      return date.toLocaleDateString('pl-PL', { 
        day: '2-digit', 
        month: '2-digit',
        year: '2-digit'
      })
    }
    
    const getStatusClasses = (status) => {
      const baseClasses = 'bg-opacity-10 border'
      const statusClasses = {
        'pending': 'text-yellow-800 bg-yellow-100 border-yellow-200',
        'processing': 'text-blue-800 bg-blue-100 border-blue-200',
        'completed': 'text-green-800 bg-green-100 border-green-200',
        'shipped': 'text-purple-800 bg-purple-100 border-purple-200',
        'delivered': 'text-green-800 bg-green-100 border-green-200',
        'cancelled': 'text-red-800 bg-red-100 border-red-200',
        'refunded': 'text-gray-800 bg-gray-100 border-gray-200'
      }
      return statusClasses[status] || 'text-gray-800 bg-gray-100 border-gray-200'
    }
    
    const getStatusShort = (status) => {
      const statusShortMap = {
        'pending': 'Oczek.',
        'processing': 'W real.',
        'completed': 'Gotowe',
        'shipped': 'Wysł.',
        'delivered': 'Dostar.',
        'cancelled': 'Anul.',
        'refunded': 'Zwroc.'
      }
      return statusShortMap[status] || status
    }
    
    const translatePaymentMethod = (method) => {
      const methodMap = {
        'bank_transfer': 'Przelew bankowy',
        'cash_on_delivery': 'Płatność przy odbiorze',
        'online': 'Płatność online',
        'blik': 'BLIK'
      }
      return methodMap[method] || method
    }
    
    const translateShippingMethod = (method) => {
      const methodMap = {
        'courier': 'Kurier',
        'inpost': 'InPost',
        'pickup': 'Odbiór osobisty'
      }
      return methodMap[method] || method
    }
    
    const resetFilters = () => {
      Object.assign(filters.value, defaultFilters)
      fetchOrders()
    }
    
    // Watch for auth state changes (logout)
    watch(() => authStore.isLoggedIn, (newValue) => {
      console.log('Orders: Auth state changed, isLoggedIn:', newValue)
      if (!newValue) {
        console.log('Orders: User logged out, clearing data')
        loading.value = false
        // Clear data when user logs out
        orders.value = {
          data: [],
          current_page: 1,
          last_page: 1,
          per_page: 10,
          total: 0
        }
      }
    })
    
    // Watch for route changes to prevent data fetching when not on admin page
    watch(() => route.path, (newPath) => {
      console.log('Orders: Route changed to:', newPath)
      if (!newPath.startsWith('/admin')) {
        console.log('Orders: Not on admin page, stopping data fetch')
        loading.value = false
      }
    })
    
    // Lifecycle
    onMounted(async () => {
      // Check if user is logged in and is admin before fetching data
      if (!authStore.isLoggedIn || !authStore.isAdmin) {
        console.log('User not logged in or not admin, skipping data fetch');
        return;
      }
      
      await fetchOrders()
      await fetchProducts()
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
      formErrors,
      paginationPages,
      fetchOrders,
      goToPage,
      openOrderDetails,
      openStatusModal,
      updateOrderStatus,
      formatDate,
      translateStatus,
      translatePaymentStatus,
      getStatusVariant,
      openCreateOrderModal,
      openEditModal,
      closeEditModal,
      addProduct,
      removeProduct,
      handleSubmit,
      confirmDelete,
      deleteOrder,
      formatPrice,
      editOrder,
      changeStatus,
      resetFilters,
      getCustomerInitials,
      formatShortDate,
      getStatusClasses,
      getStatusShort,
      translatePaymentMethod,
      translateShippingMethod
    }
  }
}
</script> 