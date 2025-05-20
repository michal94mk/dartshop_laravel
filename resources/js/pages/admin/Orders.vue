<template>
  <div class="p-6">
    <!-- Page Header -->
    <page-header 
      title="Zarządzanie zamówieniami"
      subtitle="Lista wszystkich zamówień z możliwością wyświetlania szczegółów, zmiany statusu i generowania faktur."
      add-button-label="Utwórz zamówienie"
      @add="openCreateOrderModal()"
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
                    <button @click="openOrderDetails(order)" class="px-3 py-1.5 bg-indigo-600 text-white text-sm font-medium rounded hover:bg-indigo-700 transition-colors mr-2">Szczegóły</button>
                    <button @click="openStatusModal(order)" class="px-3 py-1.5 bg-blue-600 text-white text-sm font-medium rounded hover:bg-blue-700 transition-colors mr-2">Status</button>
                    <a :href="`/api/admin/orders/${order.id}/invoice`" target="_blank" class="px-3 py-1.5 bg-green-600 text-white text-sm font-medium rounded hover:bg-green-700 transition-colors">Faktura</a>
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

    <!-- Create Order Modal -->
    <div v-if="showCreateOrderModal" class="fixed z-10 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
      <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" @click="showCreateOrderModal = false"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
          <form @submit.prevent="createManualOrder">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
              <div class="sm:flex sm:items-start">
                <div class="w-full">
                  <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4" id="modal-title">
                    Utwórz nowe zamówienie
                  </h3>
                  
                  <!-- Customer Information -->
                  <div class="mb-6">
                    <h4 class="text-md font-medium text-gray-700 mb-3">Dane klienta</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                      <div>
                        <label for="customer_name" class="block text-sm font-medium text-gray-700">Imię i nazwisko</label>
                        <input
                          type="text"
                          id="customer_name"
                          v-model="newOrder.name"
                          required
                          class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                        />
                      </div>
                      
                      <div>
                        <label for="customer_email" class="block text-sm font-medium text-gray-700">Email</label>
                        <input
                          type="email"
                          id="customer_email"
                          v-model="newOrder.email"
                          required
                          class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                        />
                      </div>
                      
                      <div>
                        <label for="customer_phone" class="block text-sm font-medium text-gray-700">Telefon</label>
                        <input
                          type="text"
                          id="customer_phone"
                          v-model="newOrder.phone"
                          required
                          class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                        />
                      </div>
                      
                      <div>
                        <label for="customer_user_id" class="block text-sm font-medium text-gray-700">Przypisz do istniejącego użytkownika (opcjonalnie)</label>
                        <select
                          id="customer_user_id"
                          v-model="newOrder.user_id"
                          class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
                        >
                          <option value="">Brak (zamówienie gościa)</option>
                          <option v-for="user in users" :key="user.id" :value="user.id">
                            {{ user.name }} ({{ user.email }})
                          </option>
                        </select>
                      </div>
                    </div>
                  </div>
                  
                  <!-- Shipping Address -->
                  <div class="mb-6">
                    <h4 class="text-md font-medium text-gray-700 mb-3">Adres dostawy</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                      <div class="md:col-span-2">
                        <label for="address" class="block text-sm font-medium text-gray-700">Adres</label>
                        <input
                          type="text"
                          id="address"
                          v-model="newOrder.address"
                          required
                          class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                        />
                      </div>
                      
                      <div>
                        <label for="postal_code" class="block text-sm font-medium text-gray-700">Kod pocztowy</label>
                        <input
                          type="text"
                          id="postal_code"
                          v-model="newOrder.postal_code"
                          required
                          class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                        />
                      </div>
                      
                      <div>
                        <label for="city" class="block text-sm font-medium text-gray-700">Miasto</label>
                        <input
                          type="text"
                          id="city"
                          v-model="newOrder.city"
                          required
                          class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                        />
                      </div>
                      
                      <div>
                        <label for="country" class="block text-sm font-medium text-gray-700">Kraj</label>
                        <input
                          type="text"
                          id="country"
                          v-model="newOrder.country"
                          required
                          class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                        />
                      </div>
                    </div>
                  </div>
                  
                  <!-- Order Items -->
                  <div class="mb-6">
                    <div class="flex justify-between items-center mb-3">
                      <h4 class="text-md font-medium text-gray-700">Produkty</h4>
                      <button
                        type="button"
                        @click="addOrderItem"
                        class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                      >
                        Dodaj produkt
                      </button>
                    </div>
                    
                    <div v-for="(item, index) in newOrder.items" :key="index" class="border rounded-md p-4 mb-3">
                      <div class="flex justify-between items-start mb-3">
                        <h5 class="text-sm font-medium text-gray-700">Produkt {{ index + 1 }}</h5>
                        <button
                          type="button"
                          @click="removeOrderItem(index)"
                          class="text-red-600 hover:text-red-900"
                        >
                          Usuń
                        </button>
                      </div>
                      
                      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="md:col-span-2">
                          <label :for="`product_id_${index}`" class="block text-sm font-medium text-gray-700">Produkt</label>
                          <select
                            :id="`product_id_${index}`"
                            v-model="item.product_id"
                            @change="updateProductDetails(index)"
                            required
                            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
                          >
                            <option value="">Wybierz produkt</option>
                            <option v-for="product in products" :key="product.id" :value="product.id">
                              {{ product.name }} - {{ product.price }} PLN
                            </option>
                          </select>
                        </div>
                        
                        <div>
                          <label :for="`price_${index}`" class="block text-sm font-medium text-gray-700">Cena (PLN)</label>
                          <input
                            type="number"
                            :id="`price_${index}`"
                            v-model="item.price"
                            min="0"
                            step="0.01"
                            required
                            class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                          />
                        </div>
                        
                        <div>
                          <label :for="`quantity_${index}`" class="block text-sm font-medium text-gray-700">Ilość</label>
                          <input
                            type="number"
                            :id="`quantity_${index}`"
                            v-model="item.quantity"
                            min="1"
                            required
                            class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                          />
                        </div>
                      </div>
                    </div>
                    
                    <div v-if="newOrder.items.length === 0" class="text-center p-4 border border-dashed rounded-md">
                      <p class="text-gray-500">Dodaj produkty do zamówienia</p>
                    </div>
                    
                    <div v-if="newOrder.items.length > 0" class="mt-3 text-right">
                      <p class="text-sm font-medium text-gray-700">Łączna wartość: <span class="font-bold">{{ calculateOrderTotal() }} PLN</span></p>
                    </div>
                  </div>
                  
                  <!-- Shipping Method & Payment -->
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div>
                      <label for="shipping_method" class="block text-sm font-medium text-gray-700">Metoda dostawy</label>
                      <select
                        id="shipping_method"
                        v-model="newOrder.shipping_method"
                        required
                        class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
                      >
                        <option value="courier">Kurier</option>
                        <option value="inpost">InPost Paczkomat</option>
                        <option value="personal">Odbiór osobisty</option>
                      </select>
                    </div>
                    
                    <div>
                      <label for="payment_method" class="block text-sm font-medium text-gray-700">Metoda płatności</label>
                      <select
                        id="payment_method"
                        v-model="newOrder.payment_method"
                        required
                        class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
                      >
                        <option value="bank_transfer">Przelew bankowy</option>
                        <option value="cash_on_delivery">Płatność przy odbiorze</option>
                        <option value="credit_card">Karta płatnicza</option>
                        <option value="blik">BLIK</option>
                      </select>
                    </div>
                  </div>
                  
                  <!-- Notes -->
                  <div class="mb-4">
                    <label for="notes" class="block text-sm font-medium text-gray-700">Uwagi do zamówienia</label>
                    <textarea
                      id="notes"
                      v-model="newOrder.notes"
                      rows="3"
                      class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                    ></textarea>
                  </div>
                </div>
              </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
              <button
                type="submit"
                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm"
              >
                Utwórz zamówienie
              </button>
              <button
                type="button"
                @click="showCreateOrderModal = false"
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
import { ref, reactive, computed, onMounted, watch } from 'vue'
import { useAlertStore } from '../../stores/alertStore'
import axios from 'axios'
import { useToast } from 'vue-toastification'

export default {
  name: 'AdminOrders',
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
    const newOrder = ref({
      name: '',
      email: '',
      phone: '',
      user_id: '',
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
      newOrder.value = {
        name: '',
        email: '',
        phone: '',
        user_id: '',
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
    
    const addOrderItem = () => {
      newOrder.value.items.push({
        product_id: '',
        product_name: '',
        price: 0,
        quantity: 1
      })
    }
    
    const removeOrderItem = (index) => {
      newOrder.value.items.splice(index, 1)
    }
    
    const updateProductDetails = (index) => {
      const productId = newOrder.value.items[index].product_id
      if (productId) {
        const product = products.value.find(p => p.id == productId)
        if (product) {
          newOrder.value.items[index].product_name = product.name
          newOrder.value.items[index].price = parseFloat(product.price)
        }
      }
    }
    
    const calculateOrderTotal = () => {
      return newOrder.value.items.reduce((total, item) => {
        return total + (parseFloat(item.price) * parseInt(item.quantity))
      }, 0).toFixed(2)
    }
    
    const createManualOrder = async () => {
      try {
        if (newOrder.value.items.length === 0) {
          toast.error('Dodaj co najmniej jeden produkt do zamówienia')
          return
        }
        
        const orderData = {
          ...newOrder.value,
          total: calculateOrderTotal()
        }
        
        const response = await axios.post('/api/admin/orders', orderData)
        
        showCreateOrderModal.value = false
        toast.success('Zamówienie zostało utworzone')
        
        // Refresh the orders list
        fetchOrders()
      } catch (error) {
        console.error('Error creating order:', error)
        toast.error('Nie udało się utworzyć zamówienia: ' + (error.response?.data?.message || error.message))
      }
    }
    
    // Lifecycle
    onMounted(() => {
      fetchOrders()
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
      newOrder,
      fetchOrders,
      goToPage,
      openOrderDetails,
      openStatusModal,
      updateOrderStatus,
      formatDate,
      translateStatus,
      translatePaymentStatus,
      openCreateOrderModal,
      addOrderItem,
      removeOrderItem,
      updateProductDetails,
      calculateOrderTotal,
      createManualOrder
    }
  }
}
</script> 