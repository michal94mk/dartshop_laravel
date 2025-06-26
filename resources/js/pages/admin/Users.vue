<template>
  <div class="space-y-6 p-4 bg-white rounded-lg shadow-sm min-h-full">
    <!-- Page Header -->
    <div class="px-6 py-4">
      <page-header
        title="Zarządzanie użytkownikami"
      >
        <template #actions>
          <admin-button
            variant="primary"
            @click="openModal({id: null, name: '', email: '', role: 'user', verified: false, password: ''})"
          >
            Dodaj
          </admin-button>
        </template>
      </page-header>
    </div>

    <!-- Filters -->
    <search-filters
      v-if="!loading"
      :filters="filters"
      :sort-options="sortOptions"
      :default-filters="defaultFilters"
      search-label="Wyszukaj"
      search-placeholder="Imię, nazwisko lub email..."
      @update:filters="(newFilters) => { Object.assign(filters, newFilters); filters.page = 1; }"
      @filter-change="fetchUsers"
      @reset-filters="resetFilters"
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

    <!-- Content -->
    <div class="bg-white shadow rounded-lg">
      <!-- Loading indicator -->
      <loading-spinner v-if="loading" />

      <!-- Users Custom Table -->
      <div v-if="!loading && users.data && users.data.length > 0" class="mt-6 bg-white shadow-sm rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-64">
                  Użytkownik
                </th>
                <th scope="col" class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-24">
                  Rola
                </th>
                <th scope="col" class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-28">
                  Status
                </th>
                <th scope="col" class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-32">
                  Ostatnie logowanie
                </th>
                <th scope="col" class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-32">
                  Data utworzenia
                </th>
                <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider w-40">
                  Akcje
                </th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="item in users.data" :key="item.id" class="hover:bg-gray-50">
                <!-- User Column -->
                <td class="px-4 py-4">
                  <div class="flex items-center">
                    <div class="h-8 w-8 flex-shrink-0">
                      <div class="h-8 w-8 rounded-full bg-indigo-600 flex items-center justify-center text-white text-xs font-medium">
                        {{ getUserInitials(item) }}
                      </div>
                    </div>
                    <div class="ml-3">
                      <div class="text-sm font-medium text-gray-900">{{ getDisplayName(item) }}</div>
                      <div class="text-xs text-gray-500 truncate max-w-[180px]" :title="item.email">{{ item.email }}</div>
                    </div>
                  </div>
                </td>
                
                <!-- Role Column -->
                <td class="px-3 py-4 text-center">
                  <admin-badge 
                    :variant="isUserAdmin(item) ? 'indigo' : 'gray'"
                    size="xs"
                  >
                    {{ isUserAdmin(item) ? 'Admin' : 'User' }}
                  </admin-badge>
                </td>
                
                <!-- Status Column -->
                <td class="px-3 py-4 text-center">
                  <admin-badge 
                    :variant="item.email_verified_at ? 'green' : 'yellow'"
                    size="xs"
                  >
                    {{ item.email_verified_at ? '✓' : '?' }}
                  </admin-badge>
                </td>
                
                <!-- Last Login Column -->
                <td class="px-3 py-4 text-center">
                  <span v-if="item.last_login_at" class="text-xs text-gray-900">
                    {{ formatDate(item.last_login_at) }}
                  </span>
                  <span v-else class="text-xs text-gray-400">Nigdy</span>
                </td>
                
                <!-- Created At Column -->
                <td class="px-3 py-4 text-center">
                  <span class="text-xs text-gray-500">{{ formatDate(item.created_at) }}</span>
                </td>
                
                <!-- Actions Column -->
                <td class="px-4 py-4 text-right">
                  <div class="flex items-center justify-end space-x-2">
                    <admin-button
                      v-if="!item.email_verified_at"
                      variant="info"
                      size="xs"
                      @click="verifyUser(item)"
                      title="Zweryfikuj użytkownika"
                    >
                      Weryfikuj
                    </admin-button>
                    <action-buttons 
                      :item="item" 
                      @edit="openModal" 
                      @delete="deleteUser"
                      :disable-delete="item.id === currentUserId"
                      justify="end"
                    />
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      
      <!-- No data message -->
      <no-data-message v-else-if="!loading" message="Brak użytkowników do wyświetlenia" />
      
      <!-- Pagination -->
      <pagination 
        v-if="!loading && users.data && users.data.length > 0" 
        :pagination="users" 
        items-label="użytkowników" 
        @page-change="goToPage" 
      />
    </div>
  </div>

  <!-- User Modal -->
  <admin-modal
    :show="showModal"
    :title="currentUser.id ? 'Edytuj użytkownika' : 'Dodaj nowego użytkownika'"
    size="lg"
    @close="showModal = false"
  >
    <form @submit.prevent="saveUser" class="space-y-6">
      <!-- Tabs -->
      <div class="border-b border-gray-200">
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
      <div v-if="activeTab === 'details'" class="space-y-4">
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
              class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
            />
          </div>
          
          <div>
            <label for="last_name" class="block text-sm font-medium text-gray-700">Nazwisko</label>
            <input
              type="text"
              id="last_name"
              v-model="currentUser.last_name"
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
            required
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
                  <div class="font-medium text-gray-900">{{ role.label }}</div>
                  <div class="text-sm text-gray-500">{{ role.description }}</div>
                </div>
                <div class="flex-shrink-0">
                  <div 
                    class="w-4 h-4 rounded-full border-2" 
                    :class="currentUser.role === role.value ? 'border-indigo-500 bg-indigo-500' : 'border-gray-300'"
                  >
                    <div v-if="currentUser.role === role.value" class="w-2 h-2 bg-white rounded-full m-0.5"></div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </form>
    
    <template #footer>
      <admin-button-group justify="end" spacing="sm">
        <admin-button
          @click="showModal = false"
          variant="secondary"
          outline
        >
          Anuluj
        </admin-button>
        <admin-button
          @click="saveUser"
          variant="primary"
          :loading="submitting"
        >
          {{ currentUser.id ? 'Zapisz zmiany' : 'Dodaj użytkownika' }}
        </admin-button>
      </admin-button-group>
    </template>
  </admin-modal>

  <!-- Delete confirmation modal -->
  <admin-modal
    :show="showDeleteModal"
    title="Usuń użytkownika"
    @close="showDeleteModal = false"
  >
    <div class="sm:flex sm:items-start">
      <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
        <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
        </svg>
      </div>
      <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
        <div class="mt-2">
          <p class="text-sm text-gray-500">
            Czy na pewno chcesz usunąć tego użytkownika? Ta operacja jest nieodwracalna.
          </p>
        </div>
      </div>
    </div>
    
    <template #footer>
      <admin-button-group justify="end" spacing="sm">
        <admin-button @click="showDeleteModal = false" variant="secondary" outline>
          Anuluj
        </admin-button>
        <admin-button @click="confirmDelete" variant="danger">
          Usuń użytkownika
        </admin-button>
      </admin-button-group>
    </template>
  </admin-modal>
</template>

<script>
import { ref, reactive, computed, onMounted } from 'vue'
import { useAlertStore } from '../../stores/alertStore'
import { useAuthStore } from '../../stores/authStore'
import axios from 'axios'
import AdminTable from '../../components/admin/ui/AdminTable.vue'
import AdminModal from '../../components/admin/ui/AdminModal.vue'
import AdminButtonGroup from '../../components/admin/ui/AdminButtonGroup.vue'
import AdminButton from '../../components/admin/ui/AdminButton.vue'
import SearchFilters from '../../components/admin/SearchFilters.vue'
import LoadingSpinner from '../../components/admin/LoadingSpinner.vue'
import NoDataMessage from '../../components/admin/NoDataMessage.vue'
import Pagination from '../../components/admin/Pagination.vue'
import ActionButtons from '../../components/admin/ActionButtons.vue'
import PageHeader from '../../components/admin/PageHeader.vue'
import AdminBadge from '../../components/admin/ui/AdminBadge.vue'

export default {
  name: 'AdminUsers',
  components: {
    AdminTable,
    AdminModal,
    AdminButtonGroup,
    AdminButton,
    SearchFilters,
    LoadingSpinner,
    NoDataMessage,
    Pagination,
    ActionButtons,
    PageHeader,
    AdminBadge
  },
  setup() {
    const alertStore = useAlertStore()
    const authStore = useAuthStore()
    


    // Other reactive data
    const submitting = ref(false)
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

    // Available roles
    const availableRoles = ref([
      {
        value: 'admin',
        label: 'Administrator',
        description: 'Pełny dostęp do wszystkich funkcji systemu'
      },
      {
        value: 'user',
        label: 'Użytkownik',
        description: 'Standardowy dostęp do funkcji użytkownika'
      }
    ])

    // Current user ID (for preventing self-deletion)
    const currentUserId = ref(null)
    
    // Sort options for the filter component
    const sortOptions = [
      { value: 'created_at', label: 'Data utworzenia' },
      { value: 'name', label: 'Nazwa użytkownika' },
      { value: 'email', label: 'Email' },
      { value: 'last_login_at', label: 'Ostatnie logowanie' }
    ]
    
    // Table columns definition
    const tableColumns = [
      { key: 'user', label: 'Użytkownik', width: '285px' },
      { key: 'role', label: 'Rola', width: '120px' },
      { key: 'status', label: 'Status', width: '120px' },
      { key: 'last_login', label: 'Ostatnie logowanie', width: '145px' },
      { key: 'created_at', label: 'Data utworzenia', type: 'date', width: '145px' },
      { key: 'actions', label: 'Akcje', align: 'right', width: '190px' }
    ]
    
    // Default filters
    const defaultFilters = {
      search: '',
      role: '',
      verified: '',
      sort_field: 'created_at',
      sort_direction: 'desc',
      page: 1
    }
    
    const filters = reactive({ ...defaultFilters })
    
    // Modals
    const showModal = ref(false)
    const showDeleteModal = ref(false)
    const userToDelete = ref(null)
    const activeTab = ref('details')
    const currentUser = ref({
      id: null,
      name: '',
      first_name: '',
      last_name: '',
      email: '',
      role: 'user',
      verified: false,
      password: ''
    })
    
    // Methods
    const fetchUsers = async () => {
      try {
        loading.value = true
        
        const params = {
          page: filters.page,
          search: filters.search,
          role: filters.role,
          verified: filters.verified,
          sort_field: filters.sort_field,
          sort_direction: filters.sort_direction
        }
        
        console.log('Fetching users with params:', params)
        console.log('Auth state:', {
          isLoggedIn: authStore.isLoggedIn,
          isAdmin: authStore.isAdmin,
          user: authStore.user
        })
        
        const response = await axios.get('/api/admin/users', { params })
        console.log('Users API response:', response.data)
        users.value = response.data
      } catch (error) {
        console.error('Error fetching users:', error)
        console.error('Error details:', error.response?.data)
        console.error('Status:', error.response?.status)
        console.error('Status text:', error.response?.statusText)
        alertStore.error('Wystąpił błąd podczas pobierania użytkowników: ' + (error.response?.data?.message || error.message))
      } finally {
        loading.value = false
      }
    }
    
    const goToPage = (page) => {
      if (page === '...') return
      filters.page = page
      fetchUsers()
    }
    
    const openModal = (user = null) => {
      if (user) {
        currentUser.value = {
          id: user.id,
          name: user.name,
          first_name: user.first_name || '',
          last_name: user.last_name || '',
          email: user.email,
          role: user.role || 'user',
          verified: !!user.email_verified_at,
          password: ''
        }
      } else {
        currentUser.value = {
          id: null,
          name: '',
          first_name: '',
          last_name: '',
          email: '',
          role: 'user',
          verified: false,
          password: ''
        }
      }
      
      activeTab.value = 'details'
      showModal.value = true
    }
    
    const saveUser = async () => {
      try {
        submitting.value = true
        
        const userData = {
          name: currentUser.value.name,
          first_name: currentUser.value.first_name,
          last_name: currentUser.value.last_name,
          email: currentUser.value.email,
          role: currentUser.value.role,
          verified: currentUser.value.verified
        }
        
        if (currentUser.value.password) {
          userData.password = currentUser.value.password
        }
        
        if (currentUser.value.id) {
          await axios.put(`/api/admin/users/${currentUser.value.id}`, userData)
          alertStore.success('Użytkownik został zaktualizowany.')
        } else {
          await axios.post('/api/admin/users', userData)
          alertStore.success('Użytkownik został dodany.')
        }
        
        showModal.value = false
        fetchUsers()
      } catch (error) {
        console.error('Error saving user:', error)
        if (error.response && error.response.data && error.response.data.errors) {
          const errors = error.response.data.errors
          const errorMessages = Object.values(errors).flat().join('\n')
          alertStore.error('Błędy walidacji:\n' + errorMessages)
        } else {
          alertStore.error('Wystąpił błąd podczas zapisywania użytkownika.')
        }
      } finally {
        submitting.value = false
      }
    }
    
    const deleteUser = (user) => {
      userToDelete.value = user
      showDeleteModal.value = true
    }
    
    const confirmDelete = async () => {
      try {
        loading.value = true
        
        const userId = typeof userToDelete.value === 'object' 
                      ? userToDelete.value.id 
                      : userToDelete.value
        
        await axios.delete(`/api/admin/users/${userId}`)
        alertStore.success('Użytkownik został usunięty.')
        showDeleteModal.value = false
        fetchUsers()
      } catch (error) {
        console.error('Error deleting user:', error)
        if (error.response?.status === 422) {
          if (error.response.data.message) {
            alertStore.error(error.response.data.message)
          } else {
            alertStore.error('Nie można usunąć tego użytkownika.')
          }
        } else {
          alertStore.error('Wystąpił błąd podczas usuwania użytkownika.')
        }
        showDeleteModal.value = false
      } finally {
        loading.value = false
      }
    }
    
    const verifyUser = async (user) => {
      try {
        await axios.post(`/api/admin/users/${user.id}/verify`)
        alertStore.success('Użytkownik został zweryfikowany.')
        fetchUsers()
      } catch (error) {
        console.error('Error verifying user:', error)
        alertStore.error('Wystąpił błąd podczas weryfikacji użytkownika.')
      }
    }
    
    const formatDate = (dateString) => {
      const options = { year: 'numeric', month: 'short', day: 'numeric' }
      return new Date(dateString).toLocaleDateString('pl-PL', options)
    }
    
    const resetFilters = () => {
      Object.assign(filters, defaultFilters)
      fetchUsers()
    }
    
    const getUserInitials = (user) => {
      if (user.first_name && user.last_name) {
        return (user.first_name.charAt(0) + user.last_name.charAt(0)).toUpperCase()
      }
      return user.name.charAt(0).toUpperCase()
    }
    
    const getDisplayName = (user) => {
      if (user.first_name && user.last_name) {
        return `${user.first_name} ${user.last_name}`
      }
      return user.name
    }
    
    const isUserAdmin = (user) => {
      return user.role === 'admin'
    }

    // Lifecycle
    onMounted(() => {
      fetchUsers()
      // Get current user ID from authenticated user
      // currentUserId.value = ... (implement based on your auth system)
    })
    
    return {
      loading,
      users,
      filters,
      defaultFilters,
      sortOptions,
      tableColumns,
      showModal,
      showDeleteModal,
      currentUser,
      activeTab,
      submitting,
      availableRoles,
      currentUserId,
      fetchUsers,
      goToPage,
      openModal,
      saveUser,
      deleteUser,
      confirmDelete,
      verifyUser,
      formatDate,
      resetFilters,
      getUserInitials,
      getDisplayName,
      isUserAdmin
    }
  }
}
</script> 