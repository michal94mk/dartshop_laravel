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
      @update:filters="filters = $event"
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
    <div v-else-if="users.data && users.data.length > 0" class="mt-8 flex flex-col">
      <div class="-my-2 -mx-4 overflow-x-auto sm:-mx-6 lg:-mx-8">
        <div class="inline-block min-w-full py-2 align-middle md:px-6 lg:px-8">
          <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
            <table class="min-w-full divide-y divide-gray-300">
              <thead class="bg-gray-50">
                <tr>
                  <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">Użytkownik</th>
                  <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Email</th>
                  <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Rola</th>
                  <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Status</th>
                  <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Data rejestracji</th>
                  <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                    <span class="sr-only">Akcje</span>
                  </th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-200 bg-white">
                <tr v-for="user in users.data" :key="user.id">
                  <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm sm:pl-6">
                    <div class="flex items-center">
                      <div class="h-10 w-10 flex-shrink-0">
                        <div class="h-10 w-10 rounded-full bg-indigo-600 flex items-center justify-center text-white font-medium">
                          {{ getUserInitials(user.name) }}
                        </div>
                      </div>
                      <div class="ml-4">
                        <div class="font-medium text-gray-900">{{ user.name }}</div>
                      </div>
                    </div>
                  </td>
                  <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ user.email }}</td>
                  <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                    <span v-if="isUserAdmin(user)" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                      Administrator
                    </span>
                    <span v-else class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                      Użytkownik
                    </span>
                  </td>
                  <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                    <span v-if="user.email_verified_at" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                      Zweryfikowany
                    </span>
                    <span v-else class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                      Niezweryfikowany
                    </span>
                  </td>
                  <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ formatDate(user.created_at) }}</td>
                  <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                    <button @click="openModal(user)" class="px-3 py-1.5 bg-indigo-600 text-white text-sm font-medium rounded hover:bg-indigo-700 transition-colors mr-2">Edytuj</button>
                    <button 
                      @click="deleteUser(user.id)" 
                      class="px-3 py-1.5 bg-red-600 text-white text-sm font-medium rounded hover:bg-red-700 transition-colors"
                      :disabled="user.id === currentUserId"
                      :class="{ 'opacity-50 cursor-not-allowed': user.id === currentUserId }"
                    >
                      Usuń
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
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

    <!-- User Modal -->
    <div v-if="showModal" class="fixed z-10 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
      <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" @click="showModal = false"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
          <form @submit.prevent="saveUser">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
              <div class="sm:flex sm:items-start">
                <div class="w-full">
                  <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4" id="modal-title">
                    Edytuj użytkownika
                  </h3>
                  
                  <div class="grid grid-cols-1 gap-4">
                    <div>
                      <label for="name" class="block text-sm font-medium text-gray-700">Imię i nazwisko</label>
                      <input
                        type="text"
                        id="name"
                        v-model="currentUser.name"
                        required
                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                      />
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
                    
                    <div>
                      <label class="block text-sm font-medium text-gray-700">Rola</label>
                      <div class="mt-2">
                        <label class="inline-flex items-center mr-4">
                          <input 
                            type="radio" 
                            name="role" 
                            value="user" 
                            v-model="currentUser.role" 
                            class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300"
                          />
                          <span class="ml-2 text-sm text-gray-700">Użytkownik</span>
                        </label>
                        <label class="inline-flex items-center">
                          <input 
                            type="radio" 
                            name="role" 
                            value="admin" 
                            v-model="currentUser.role" 
                            class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300"
                          />
                          <span class="ml-2 text-sm text-gray-700">Administrator</span>
                        </label>
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

export default {
  name: 'AdminUsers',
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
      { value: 'created_at', label: 'Data rejestracji' },
      { value: 'name', label: 'Imię i nazwisko' },
      { value: 'email', label: 'Email' }
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
    const currentUserId = ref(authStore.user?.id || null)
    
    // Modals
    const showModal = ref(false)
    const showDeleteModal = ref(false)
    const userToDelete = ref(null)
    const currentUser = ref({
      id: null,
      name: '',
      email: '',
      role: 'user',
      verified: false,
      password: ''
    })
    
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
        const params = { ...filters }
        
        const response = await axios.get('/api/admin/users', { params })
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
      currentUser.value = {
        id: user.id,
        name: user.name,
        email: user.email,
        role: isUserAdmin(user) ? 'admin' : 'user',
        verified: !!user.email_verified_at,
        password: ''
      }
      showModal.value = true
    }
    
    const saveUser = async () => {
      try {
        const userData = {
          name: currentUser.value.name,
          email: currentUser.value.email,
          role: currentUser.value.role,
          email_verified: currentUser.value.verified
        }
        
        // Only include password if it was changed
        if (currentUser.value.password) {
          userData.password = currentUser.value.password
        }
        
        await axios.put(`/api/admin/users/${currentUser.value.id}`, userData)
        alertStore.setSuccessMessage('Użytkownik został zaktualizowany.')
        showModal.value = false
        fetchUsers()
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
    
    const getUserInitials = (name) => {
      if (!name) return '?'
      
      const names = name.split(' ')
      if (names.length === 1) return names[0].charAt(0).toUpperCase()
      
      return (names[0].charAt(0) + names[names.length - 1].charAt(0)).toUpperCase()
    }
    
    const isUserAdmin = (user) => {
      // Check for roles property first (if API returns roles)
      if (user.roles && Array.isArray(user.roles)) {
        return user.roles.some(role => role.name === 'admin')
      }
      
      // Fallback to is_admin property
      return !!user.is_admin
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
      isUserAdmin
    }
  }
}
</script> 