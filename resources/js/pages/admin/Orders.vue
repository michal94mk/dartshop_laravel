<template>
  <div class="p-6">
    <!-- Page Header -->
    <div class="sm:flex sm:items-center mb-6">
      <div class="sm:flex-auto">
        <h1 class="text-xl font-semibold text-gray-900">Zamówienia</h1>
      </div>
      <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none">
        <button
          @click="showCreateOrderModal = true"
          class="inline-flex items-center justify-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:w-auto"
        >
          Dodaj zamówienie
        </button>
      </div>
    </div>

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
    <div v-else-if="orders.data && orders.data.length > 0" class="mt-8 flex flex-col">
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
                    <button @click="openOrderDetails(order)" class="px-3 py-1.5 bg-indigo-600 text-white text-sm font-medium rounded hover:bg-indigo-700 transition-colors mr-2">
                      Szczegóły
                    </button>
                    <button @click="openStatusModal(order)" class="px-3 py-1.5 bg-blue-600 text-white text-sm font-medium rounded hover:bg-blue-700 transition-colors mr-2">
                      Status
                    </button>
                    <button @click="openEditModal(order)" class="px-3 py-1.5 bg-yellow-600 text-white text-sm font-medium rounded hover:bg-yellow-700 transition-colors mr-2">
                      Edytuj
                    </button>
                    <button @click="confirmDelete(order)" class="px-3 py-1.5 bg-red-600 text-white text-sm font-medium rounded hover:bg-red-700 transition-colors mr-2">
                      Usuń
                    </button>
                    <a :href="`/api/admin/orders/${order.id}/invoice`" target="_blank" class="px-3 py-1.5 bg-green-600 text-white text-sm font-medium rounded hover:bg-green-700 transition-colors">
                      Faktura
                    </a>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
    
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

          <!-- Payment Method -->
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
import LoadingSpinner from '../../components/ui/LoadingSpinner.vue'
import NoDataMessage from '../../components/admin/NoDataMessage.vue'
import Pagination from '../../components/admin/Pagination.vue'

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
    Pagination
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