<template>
  <div class="p-6">
    <!-- Page Header -->
    <page-header 
      title="Zarządzanie użytkownikami"
      subtitle="Lista wszystkich użytkowników z możliwością edycji, zmiany roli i usuwania kont."
      add-button-label="Dodaj użytkownika"
      @add="openModal({id: null, name: '', email: '', role: 'user', verified: false, password: ''})"
    />

    <!-- Search and filters -->
    <search-filters
      v-if="!loading"
      :filters="filters"
      :sort-options="sortOptions"
      search-label="Wyszukaj"
      search-placeholder="Imię, nazwisko lub email..."
      @update:filters="(newFilters) => { Object.assign(filters, newFilters); filters.page = 1; }"
      @filter-change="fetchUsers"
    >
      <template v-slot:filters>
        <div class="w-full sm:w-auto">
          <label for="role" class="block text-sm font-medium text-gray-700">Rola</label>
          <select
            id="role"
            name="role"
            v-model="filters.role"
            @change="fetchUsers"
            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
          >
            <option value="">Wszystkie role</option>
            <option value="admin">Administrator</option>
            <option value="user">Użytkownik</option>
          </select>
        </div>
        
        <div class="w-full sm:w-auto">
          <label for="verified" class="block text-sm font-medium text-gray-700">Status</label>
          <select
            id="verified"
            name="verified"
            v-model="filters.verified"
            @change="fetchUsers"
            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
          >
            <option value="">Wszyscy</option>
            <option value="1">Zweryfikowani</option>
            <option value="0">Niezweryfikowani</option>
          </select>
        </div>
      </template>
    </search-filters>

    <!-- Loading indicator -->
    <loading-spinner v-if="loading" />

    <!-- Users list -->
    <admin-table
      v-if="users.data && users.data.length > 0"
      :columns="tableColumns"
      :items="users.data"
      class="mt-8"
    >
      <template #cell-user="{ item }">
        <div class="flex items-center">
          <div class="h-10 w-10 flex-shrink-0">
            <div class="h-10 w-10 rounded-full bg-indigo-600 flex items-center justify-center text-white font-medium">
              {{ getUserInitials(item) }}
            </div>
          </div>
          <div class="ml-4">
            <div class="font-medium text-gray-900">{{ getDisplayName(item) }}</div>
            <div class="text-sm text-gray-500">@{{ item.name }}</div>
          </div>
        </div>
      </template>
      
      <template #cell-role="{ item }">
        <admin-badge 
          :variant="isUserAdmin(item) ? 'indigo' : 'gray'"
          size="xs"
        >
          {{ isUserAdmin(item) ? 'Administrator' : 'Użytkownik' }}
        </admin-badge>
      </template>
      
      <template #cell-status="{ item }">
        <admin-badge 
          :variant="item.email_verified_at ? 'green' : 'yellow'"
          size="xs"
        >
          {{ item.email_verified_at ? 'Zweryfikowany' : 'Niezweryfikowany' }}
        </admin-badge>
      </template>
      
      <template #cell-actions="{ item }">
        <admin-button-group spacing="xs">
          <admin-button
            @click="openModal(item)"
            variant="primary"
            size="sm"
          >
            Edytuj
          </admin-button>
          <admin-button
            @click="deleteUser(item.id)"
            variant="danger"
            size="sm"
            :disabled="item.id === currentUserId"
          >
            Usuń
          </admin-button>
        </admin-button-group>
      </template>
    </admin-table>
    
    <!-- No data message -->
    <no-data-message v-else-if="!loading" message="Brak użytkowników do wyświetlenia" />
    
    <!-- Pagination -->
    <pagination 
      v-if="!loading && users.data && users.data.length > 0" 
      :pagination="users" 
      items-label="użytkowników" 
      @page-change="goToPage" 
    />

    <!-- User Modal -->
    <div v-if="showModal" class="fixed z-10 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
      <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" @click="showModal = false"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
          <form @submit.prevent="saveUser">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
              <div class="sm:flex sm:items-start">
                <div class="w-full">
                  <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4" id="modal-title">
                    {{ currentUser.id ? 'Edytuj użytkownika' : 'Dodaj użytkownika' }}
                  </h3>
                  
                  <!-- Tabs -->
                  <div class="border-b border-gray-200 mb-6">
                    <nav class="-mb-px flex" aria-label="Tabs">
                      <button
                        type="button"
                        @click="activeTab = 'details'"
                        :class="[
                          activeTab === 'details'
                            ? 'border-indigo-500 text-indigo-600'
                            : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
                          'w-1/2 py-4 px-1 text-center border-b-2 font-medium text-sm'
                        ]"
                      >
                        Podstawowe dane
                      </button>
                      <button
                        type="button"
                        @click="activeTab = 'permissions'"
                        :class="[
                          activeTab === 'permissions'
                            ? 'border-indigo-500 text-indigo-600'
                            : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
                          'w-1/2 py-4 px-1 text-center border-b-2 font-medium text-sm'
                        ]"
                      >
                        Uprawnienia
                      </button>
                    </nav>
                  </div>
                  
                  <!-- Basic Details Tab -->
                  <div v-if="activeTab === 'details'" class="grid grid-cols-1 gap-4">
                    <div>
                      <label for="name" class="block text-sm font-medium text-gray-700">Nazwa użytkownika</label>
                      <input
                        type="text"
                        id="name"
                        v-model="currentUser.name"
                        required
                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                      />
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                      <div>
                        <label for="first_name" class="block text-sm font-medium text-gray-700">Imię</label>
                        <input
                          type="text"
                          id="first_name"
                          v-model="currentUser.first_name"
                          required
                          class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                        />
                      </div>
                      
                      <div>
                        <label for="last_name" class="block text-sm font-medium text-gray-700">Nazwisko</label>
                        <input
                          type="text"
                          id="last_name"
                          v-model="currentUser.last_name"
                          required
                          class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                        />
                      </div>
                    </div>
                    
                    <div>
                      <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                      <input
                        type="email"
                        id="email"
                        v-model="currentUser.email"
                        required
                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                      />
                    </div>
                    
                    <div v-if="!currentUser.id">
                      <label for="password" class="block text-sm font-medium text-gray-700">Hasło</label>
                      <input
                        type="password"
                        id="password"
                        v-model="currentUser.password"
                        :required="!currentUser.id"
                        minlength="8"
                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                      />
                    </div>
                    
                    <div v-if="currentUser.id">
                      <label for="new_password" class="block text-sm font-medium text-gray-700">Nowe hasło (pozostaw puste, aby nie zmieniać)</label>
                      <input
                        type="password"
                        id="new_password"
                        v-model="currentUser.password"
                        minlength="8"
                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                      />
                    </div>
                    
                    <div>
                      <label class="block text-sm font-medium text-gray-700">Status</label>
                      <div class="mt-2">
                        <label class="inline-flex items-center">
                          <input 
                            type="checkbox" 
                            v-model="currentUser.verified" 
                            class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded"
                          />
                          <span class="ml-2 text-sm text-gray-700">Email zweryfikowany</span>
                        </label>
                      </div>
                    </div>
                  </div>
                  
                  <!-- Permissions Tab -->
                  <div v-if="activeTab === 'permissions'" class="space-y-6">
                    <!-- Role Selection -->
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-2">Rola użytkownika</label>
                      <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div 
                          v-for="role in availableRoles" 
                          :key="role.value" 
                          @click="currentUser.role = role.value"
                          class="relative border rounded-lg p-4 cursor-pointer"
                          :class="currentUser.role === role.value ? 'border-indigo-500 bg-indigo-50' : 'border-gray-300 hover:border-indigo-300'"
                        >
                          <div class="flex items-center justify-between">
                            <div>
                              <span class="text-sm font-medium text-gray-900">{{ role.label }}</span>
                              <p class="text-xs text-gray-500 mt-1">{{ role.description }}</p>
                            </div>
                            <div class="h-5 w-5 text-indigo-600">
                              <svg v-if="currentUser.role === role.value" class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                              </svg>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    
                    <div>
                      <label for="password" class="block text-sm font-medium text-gray-700">Hasło (opcjonalnie)</label>
                      <input
                        type="password"
                        id="password"
                        v-model="currentUser.password"
                        placeholder="Pozostaw puste, aby nie zmieniać"
                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                      />
                      <p class="mt-1 text-xs text-gray-500">
                        Wprowadź nowe hasło tylko wtedy, gdy chcesz je zmienić.
                      </p>
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
                @click="showModal = false"
                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
              >
                Anuluj
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Delete confirmation modal -->
    <div v-if="showDeleteModal" class="fixed z-10 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
      <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" @click="showDeleteModal = false"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
          <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
            <div class="sm:flex sm:items-start">
              <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
              </div>
              <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                  Usuń użytkownika
                </h3>
                <div class="mt-2">
                  <p class="text-sm text-gray-500">
                    Czy na pewno chcesz usunąć tego użytkownika? Ta operacja jest nieodwracalna i spowoduje usunięcie wszystkich danych użytkownika.
                  </p>
                </div>
              </div>
            </div>
          </div>
          <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
            <button
              type="button"
              @click="confirmDelete"
              class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm"
            >
              Usuń
            </button>
            <button
              type="button"
              @click="showDeleteModal = false"
              class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
            >
              Anuluj
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, reactive, computed, onMounted } from 'vue'
import { useAlertStore } from '../../stores/alertStore'
import { useAuthStore } from '../../stores/authStore'
import axios from 'axios'
import debounce from 'lodash/debounce'
import AdminTable from '../../components/admin/ui/AdminTable.vue'
import AdminButtonGroup from '../../components/admin/ui/AdminButtonGroup.vue'
import AdminButton from '../../components/admin/ui/AdminButton.vue'
import AdminBadge from '../../components/admin/ui/AdminBadge.vue'
import SearchFilters from '../../components/admin/SearchFilters.vue'
import LoadingSpinner from '../../components/admin/LoadingSpinner.vue'
import NoDataMessage from '../../components/admin/NoDataMessage.vue'
import Pagination from '../../components/admin/Pagination.vue'
import PageHeader from '../../components/admin/PageHeader.vue'

export default {
  name: 'AdminUsers',
  components: {
    AdminTable,
    AdminButtonGroup,
    AdminButton,
    AdminBadge,
    SearchFilters,
    LoadingSpinner,
    NoDataMessage,
    Pagination,
    PageHeader
  },
  setup() {
    const alertStore = useAlertStore()
    const authStore = useAuthStore()
    
    // Data
    const loading = ref(true)
    const users = ref({
      data: [],
      current_page: 1,
      from: 1,
      to: 0,
      total: 0,
      last_page: 1,
      per_page: 10
    })
    
    // Sort options for the filter component
    const sortOptions = [
      { value: 'name', label: 'Nazwa' },
      { value: 'email', label: 'Email' },
      { value: 'created_at', label: 'Data rejestracji' }
    ]
    
    // Table columns definition
    const tableColumns = [
      { key: 'user', label: 'Użytkownik', width: '200px' },
      { key: 'email', label: 'Email', width: '250px' },
      { key: 'role', label: 'Rola', width: '120px' },
      { key: 'status', label: 'Status', width: '120px' },
      { key: 'created_at', label: 'Data rejestracji', type: 'date', width: '140px' },
      { key: 'actions', label: 'Akcje', align: 'right', width: '160px' }
    ]
    

    
    const filters = reactive({
      search: '',
      role: '',
      verified: '',
      sort_field: 'created_at',
      sort_direction: 'desc',
      page: 1
    })
    
    // Current user ID to prevent self-deletion
    const currentUserId = computed(() => authStore.user ? authStore.user.id : null)
    
    // Modals
    const showModal = ref(false)
    const showDeleteModal = ref(false)
    const userToDelete = ref(null)
    const currentUser = ref({
      id: null,
      name: '',
      first_name: '',
      last_name: '',
      email: '',
      role: 'user',
      verified: false,
      password: '',
      permissions: []
    })
    const activeTab = ref('details')
    
    // Available roles for the role selector
    const availableRoles = [
      {
        value: 'user',
        label: 'Użytkownik',
        description: 'Podstawowe uprawnienia dostępu, bez panelu administracyjnego.'
      },
      {
        value: 'admin',
        label: 'Administrator',
        description: 'Pełne uprawnienia administracyjne, możliwość zarządzania sklepem.'
      },
      {
        value: 'manager',
        label: 'Menedżer',
        description: 'Uprawnienia do zarządzania zamówieniami i produktami, bez dostępu do ustawień systemowych.'
      },
      {
        value: 'editor',
        label: 'Redaktor',
        description: 'Uprawnienia do zarządzania treścią, opisami produktów i stronami statycznymi.'
      },
      {
        value: 'custom',
        label: 'Niestandardowa',
        description: 'Własny zestaw uprawnień, określany indywidualnie.'
      }
    ]
    
    // Permission groups for the permissions selector
    const permissionGroups = {
      'Produkty': [
        { value: 'products.view', label: 'Przeglądanie produktów' },
        { value: 'products.create', label: 'Dodawanie produktów' },
        { value: 'products.edit', label: 'Edycja produktów' },
        { value: 'products.delete', label: 'Usuwanie produktów' }
      ],
      'Kategorie': [
        { value: 'categories.view', label: 'Przeglądanie kategorii' },
        { value: 'categories.create', label: 'Dodawanie kategorii' },
        { value: 'categories.edit', label: 'Edycja kategorii' },
        { value: 'categories.delete', label: 'Usuwanie kategorii' }
      ],
      'Marki': [
        { value: 'brands.view', label: 'Przeglądanie marek' },
        { value: 'brands.create', label: 'Dodawanie marek' },
        { value: 'brands.edit', label: 'Edycja marek' },
        { value: 'brands.delete', label: 'Usuwanie marek' }
      ],
      'Użytkownicy': [
        { value: 'users.view', label: 'Przeglądanie użytkowników' },
        { value: 'users.create', label: 'Dodawanie użytkowników' },
        { value: 'users.edit', label: 'Edycja użytkowników' },
        { value: 'users.delete', label: 'Usuwanie użytkowników' }
      ],
      'Zamówienia': [
        { value: 'orders.view', label: 'Przeglądanie zamówień' },
        { value: 'orders.create', label: 'Tworzenie zamówień' },
        { value: 'orders.edit', label: 'Edycja zamówień' },
        { value: 'orders.delete', label: 'Usuwanie zamówień' }
      ],
      'Recenzje': [
        { value: 'reviews.view', label: 'Przeglądanie recenzji' },
        { value: 'reviews.approve', label: 'Zatwierdzanie recenzji' },
        { value: 'reviews.edit', label: 'Edycja recenzji' },
        { value: 'reviews.delete', label: 'Usuwanie recenzji' }
      ],
      'Promocje': [
        { value: 'promotions.view', label: 'Przeglądanie promocji' },
        { value: 'promotions.create', label: 'Tworzenie promocji' },
        { value: 'promotions.edit', label: 'Edycja promocji' },
        { value: 'promotions.delete', label: 'Usuwanie promocji' }
      ],
      'Ustawienia': [
        { value: 'settings.view', label: 'Przeglądanie ustawień' },
        { value: 'settings.edit', label: 'Edycja ustawień' }
      ]
    }
    
    // Computed
    const paginationPages = computed(() => {
      const total = users.value.last_page
      const current = users.value.current_page
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
    const fetchUsers = async () => {
      try {
        loading.value = true
        
        const response = await axios.get('/api/admin/users', { params: filters })
        users.value = response.data
      } catch (error) {
        console.error('Error fetching users:', error)
        alertStore.setErrorMessage('Wystąpił błąd podczas pobierania użytkowników.')
      } finally {
        loading.value = false
      }
    }
    
    const debouncedFetchUsers = debounce(fetchUsers, 300)
    
    const goToPage = (page) => {
      if (page === '...') return
      filters.page = page
      fetchUsers()
    }
    
    const openModal = (user) => {
      if (user.id) {
        // Edit mode - fetch detailed user data including permissions
        fetchUserDetails(user.id)
      } else {
        // Create mode - reset form
        currentUser.value = {
          id: null,
          name: '',
          first_name: '',
          last_name: '',
          email: '',
          role: 'user',
          verified: false,
          password: '',
          permissions: []
        }
        activeTab.value = 'details'
      }
      
      showModal.value = true
    }
    
    const fetchUserDetails = async (userId) => {
      try {
        const response = await axios.get(`/api/admin/users/${userId}`)
        const userData = response.data
        
        currentUser.value = {
          id: userData.id,
          name: userData.name,
          first_name: userData.first_name || '',
          last_name: userData.last_name || '',
          email: userData.email,
          role: userData.role || 'user',
          verified: !!userData.email_verified_at,
          password: '',
          permissions: userData.permissions || []
        }
        
        activeTab.value = 'details'
      } catch (error) {
        console.error('Error fetching user details:', error)
        alertStore.setErrorMessage('Wystąpił błąd podczas pobierania danych użytkownika.')
      }
    }
    
    const saveUser = async () => {
      try {
        // Prepare data for API
        const userData = {
          name: currentUser.value.name,
          first_name: currentUser.value.first_name,
          last_name: currentUser.value.last_name,
          email: currentUser.value.email,
          role: currentUser.value.role,
          email_verified_at: currentUser.value.verified ? new Date().toISOString() : null
        }
        
        // Add password only if provided
        if (currentUser.value.password) {
          userData.password = currentUser.value.password
        }
        
        // Add permissions if role is custom or admin
        if (currentUser.value.role === 'custom' || currentUser.value.role === 'admin') {
          userData.permissions = currentUser.value.permissions
        }
        
        let response
        
        if (currentUser.value.id) {
          // Update existing user
          response = await axios.put(`/api/admin/users/${currentUser.value.id}`, userData)
          alertStore.setSuccessMessage('Użytkownik został zaktualizowany.')
        } else {
          // Create new user
          response = await axios.post('/api/admin/users', userData)
          alertStore.setSuccessMessage('Użytkownik został utworzony.')
        }
        
        showModal.value = false
        fetchUsers() // Refresh the users list
      } catch (error) {
        console.error('Error saving user:', error)
        alertStore.setErrorMessage('Wystąpił błąd podczas zapisywania użytkownika.')
      }
    }
    
    const deleteUser = (id) => {
      // Prevent deleting yourself
      if (id === currentUserId.value) return
      
      userToDelete.value = id
      showDeleteModal.value = true
    }
    
    const confirmDelete = async () => {
      try {
        await axios.delete(`/api/admin/users/${userToDelete.value}`)
        alertStore.setSuccessMessage('Użytkownik został usunięty.')
        showDeleteModal.value = false
        fetchUsers()
      } catch (error) {
        console.error('Error deleting user:', error)
        alertStore.setErrorMessage('Wystąpił błąd podczas usuwania użytkownika.')
      }
    }
    
    const formatDate = (dateString) => {
      if (!dateString) return '-'
      const options = { year: 'numeric', month: 'short', day: 'numeric' }
      return new Date(dateString).toLocaleDateString('pl-PL', options)
    }
    
    const getUserInitials = (user) => {
      if (!user) return '?'
      
      // Try to use first_name and last_name first
      if (user.first_name && user.last_name) {
        return (user.first_name.charAt(0) + user.last_name.charAt(0)).toUpperCase()
      }
      
      // Fallback to name
      if (user.name) {
        const names = user.name.split(' ')
        if (names.length === 1) return names[0].charAt(0).toUpperCase()
        return (names[0].charAt(0) + names[names.length - 1].charAt(0)).toUpperCase()
      }
      
      return '?'
    }
    
    const getDisplayName = (user) => {
      if (!user) return ''
      
      // Try to use first_name and last_name first
      if (user.first_name && user.last_name) {
        return `${user.first_name} ${user.last_name}`
      }
      
      // Fallback to name
      return user.name || ''
    }
    
    const isUserAdmin = (user) => {
      return user.role === 'admin'
    }
    
    // Lifecycle
    onMounted(() => {
      fetchUsers()
    })
    
    return {
      loading,
      users,
      filters,
      sortOptions,
      tableColumns,
      currentUserId,
      showModal,
      showDeleteModal,
      currentUser,
      paginationPages,
      fetchUsers,
      debouncedFetchUsers,
      goToPage,
      openModal,
      saveUser,
      deleteUser,
      confirmDelete,
      formatDate,
      getUserInitials,
      getDisplayName,
      isUserAdmin,
      activeTab,
      availableRoles,
      permissionGroups
    }
  }
}
</script> 