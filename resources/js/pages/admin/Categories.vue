<template>
  <div class="space-y-6 p-4 bg-white rounded-lg shadow-sm min-h-full">
    <!-- Page Header -->
    <div class="px-6 py-4">
      <page-header
        title="Zarządzanie kategoriami"
      >
        <template #actions>
          <admin-button
            variant="primary"
            @click="openModal()"
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
      search-placeholder="Nazwa kategorii..."
      @update:filters="(newFilters) => { Object.assign(filters, newFilters); filters.page = 1; }"
      @filter-change="fetchCategories"
      @reset-filters="resetFilters"
    >
      <template v-slot:filters>
        <!-- No additional filters needed -->
      </template>
    </search-filters>

    <!-- Content -->
    <div class="bg-white shadow rounded-lg">
      <!-- Loading indicator -->
      <loading-spinner v-if="loading" />
      
      <!-- Categories Custom Table -->
      <div v-if="!loading && categories.data && categories.data.length > 0" class="mt-6 bg-white shadow-sm rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-80">
                  Nazwa
                </th>
                <th scope="col" class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-32">
                  Liczba produktów
                </th>
                <th scope="col" class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-36">
                  Data utworzenia
                </th>
                <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider w-36">
                  Akcje
                </th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="item in categories.data" :key="item.id" class="hover:bg-gray-50">
                <!-- Name Column -->
                <td class="px-4 py-4">
                  <div class="text-sm font-medium text-gray-900">{{ item.name }}</div>
                </td>
                
                <!-- Products Count Column -->
                <td class="px-3 py-4 text-center">
                  <admin-badge variant="secondary" size="xs">
                    {{ item.products_count || 0 }}
                  </admin-badge>
                </td>
                
                <!-- Created At Column -->
                <td class="px-3 py-4 text-center">
                  <span class="text-xs text-gray-500">{{ formatDate(item.created_at) }}</span>
                </td>
                
                <!-- Actions Column -->
                <td class="px-4 py-4 text-right">
                  <action-buttons 
                    :item="item" 
                    @edit="openModal" 
                    @delete="deleteCategory"
                    justify="end"
                  />
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      
      <!-- Pagination -->
      <pagination 
        v-if="categories.data && categories.data.length > 0 && categories.last_page > 1"
        :pagination="categories" 
        items-label="kategorii" 
        @page-change="goToPage" 
      />
      
      <!-- No data message -->
      <no-data-message 
        v-if="!loading && (!categories.data || categories.data.length === 0)" 
        message="Brak kategorii do wyświetlenia" 
      />
    </div>
  </div>

  <!-- Category Modal -->
  <admin-modal
    :show="showModal"
    :title="currentCategory.id ? 'Edytuj kategorię' : 'Dodaj nową kategorię'"
    size="lg"
    @close="showModal = false"
  >
    <form @submit.prevent="saveCategory" class="space-y-6">
      <div>
        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nazwa kategorii</label>
        <input
          type="text"
          id="name"
          v-model="currentCategory.name"
          required
          class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
          placeholder="Np. Lotki, Tarcze, Akcesoria"
        />
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
          @click="saveCategory"
          variant="primary"
          :loading="submitting"
        >
          {{ currentCategory.id ? 'Zapisz zmiany' : 'Dodaj kategorię' }}
        </admin-button>
      </admin-button-group>
    </template>
  </admin-modal>

  <!-- Delete Confirmation Modal -->
  <admin-modal
    :show="showDeleteModal"
    title="Usuń kategorię"
    size="md"
    icon-variant="danger"
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
            Czy na pewno chcesz usunąć tę kategorię? Ta operacja jest nieodwracalna.
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
          Usuń kategorię
        </admin-button>
      </admin-button-group>
    </template>
  </admin-modal>

  <!-- Detailed Error Modal -->
  <detailed-error-modal
    :show="showErrorModal"
    :error-message="errorMessage"
    @close="showErrorModal = false"
  />
</template>

<script>
import { ref, computed, onMounted, reactive } from 'vue'
import { useAlertStore } from '../../stores/alertStore'
import { useAuthStore } from '../../stores/authStore'
import axios from 'axios'
import DetailedErrorModal from '../../components/ui/DetailedErrorModal.vue'
import AdminModal from '../../components/admin/ui/AdminModal.vue'
import AdminButtonGroup from '../../components/admin/ui/AdminButtonGroup.vue'
import AdminButton from '../../components/admin/ui/AdminButton.vue'
import SearchFilters from '../../components/admin/SearchFilters.vue'
import LoadingSpinner from '../../components/admin/LoadingSpinner.vue'
import NoDataMessage from '../../components/admin/NoDataMessage.vue'
import Pagination from '../../components/admin/Pagination.vue'
import PageHeader from '../../components/admin/PageHeader.vue'
import ActionButtons from '../../components/admin/ActionButtons.vue'
import AdminBadge from '../../components/admin/ui/AdminBadge.vue'

export default {
  name: 'AdminCategories',
  components: {
    DetailedErrorModal,
    AdminModal,
    AdminButtonGroup,
    AdminButton,
    SearchFilters,
    LoadingSpinner,
    NoDataMessage,
    Pagination,
    PageHeader,
    ActionButtons,
    AdminBadge
  },
  setup() {
    const alertStore = useAlertStore()
    const authStore = useAuthStore()
    


    // Other reactive data
    const submitting = ref(false)
    
    // Data
    const loading = ref(true)
    const categories = ref({
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
      { value: 'id', label: 'ID' },
      { value: 'name', label: 'Nazwa' },
      { value: 'created_at', label: 'Data utworzenia' },
      { value: 'products_count', label: 'Liczba produktów' }
    ]
    
    // Table columns definition
    const tableColumns = [
      { key: 'name', label: 'Nazwa', width: '415px' },
      { key: 'products_count', label: 'Liczba produktów', width: '153px' },
      { key: 'created_at', label: 'Data utworzenia', type: 'date', width: '153px' },
      { key: 'actions', label: 'Akcje', align: 'right', width: '165px' }
    ]
    
    // Default filters
    const defaultFilters = {
      search: '',
      sort_field: 'created_at',
      sort_direction: 'desc',
      page: 1
    }
    
    // Filters and pagination
    const filters = reactive({ ...defaultFilters })
    
    // Modals
    const showModal = ref(false)
    const showDeleteModal = ref(false)
    const categoryToDelete = ref(null)
    const currentCategory = ref({
      id: null,
      name: ''
    })
    
    // Detailed Error Modal
    const showErrorModal = ref(false)
    const errorMessage = ref('')
    
    // Computed
    const paginationPages = computed(() => {
      const total = categories.value.last_page
      const current = categories.value.current_page
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
    const fetchCategories = async () => {
      try {
        loading.value = true
        
        const params = {
          page: filters.page,
          search: filters.search,
          sort_field: filters.sort_field,
          sort_direction: filters.sort_direction
        }
        
        console.log('Fetching categories with params:', params)
        console.log('Auth state:', {
          isLoggedIn: authStore.isLoggedIn,
          isAdmin: authStore.isAdmin,
          user: authStore.user
        })
        
        const response = await axios.get('/api/admin/categories', { params })
        console.log('Categories API response:', response.data)
        categories.value = response.data
      } catch (error) {
        console.error('Error fetching categories:', error)
        console.error('Error details:', error.response?.data)
        console.error('Status:', error.response?.status)
        console.error('Status text:', error.response?.statusText)
        alertStore.error('Wystąpił błąd podczas pobierania kategorii: ' + (error.response?.data?.message || error.message))
      } finally {
        loading.value = false
      }
    }
    
    const goToPage = (page) => {
      if (page === '...') return
      
      filters.page = page
      fetchCategories()
    }
    
    const openModal = (category = null) => {
      if (category) {
        currentCategory.value = { 
          id: category.id,
          name: category.name
        }
      } else {
        currentCategory.value = {
          id: null,
          name: ''
        }
      }
      showModal.value = true
    }
    
    const saveCategory = async () => {
      try {
        submitting.value = true
        const formData = new FormData()
        formData.append('name', currentCategory.value.name)
        
        if (currentCategory.value.id) {
          // Update existing category - use POST with _method for file upload
          formData.append('_method', 'PUT')
          await axios.post(`/api/admin/categories/${currentCategory.value.id}`, formData, {
            headers: {
              'Content-Type': 'multipart/form-data'
            }
          })
          alertStore.success('Kategoria została zaktualizowana.')
        } else {
          // Create new category
          await axios.post('/api/admin/categories', formData, {
            headers: {
              'Content-Type': 'multipart/form-data'
            }
          })
          alertStore.success('Kategoria została dodana.')
        }
        
        showModal.value = false
        fetchCategories()
      } catch (error) {
        console.error('Error saving category:', error)
        if (error.response && error.response.data && error.response.data.errors) {
          const errors = error.response.data.errors
          const errorMessages = Object.values(errors).flat().join('\n')
          alertStore.error('Błędy walidacji:\n' + errorMessages)
        } else {
          alertStore.error('Wystąpił błąd podczas zapisywania kategorii.')
        }
      } finally {
        submitting.value = false
      }
    }
    
    const deleteCategory = (id) => {
      console.log('Delete category called with ID:', id); // Debug the ID being passed
      categoryToDelete.value = typeof id === 'object' && id.id ? id.id : id;
      showDeleteModal.value = true;
    }
    
    const confirmDelete = async () => {
      if (!categoryToDelete.value) {
        alertStore.error('Brak ID kategorii do usunięcia.')
        showDeleteModal.value = false
        return
      }
      
      // Ensure categoryToDelete is a number or string, not an object
      const categoryId = typeof categoryToDelete.value === 'object' 
                        ? categoryToDelete.value.id 
                        : categoryToDelete.value;
      
      console.log('Attempting to delete category with ID:', categoryId); // Debug the ID
      
      try {
        const response = await axios.delete(`/api/admin/categories/${categoryId}`)
        console.log('Delete response:', response)
        alertStore.success('Kategoria została usunięta.')
        showDeleteModal.value = false
        fetchCategories()
      } catch (error) {
        // Close the delete confirmation modal
        showDeleteModal.value = false
        
        // Complete debugging of the error
        console.error('Error deleting category:', error)
        console.dir(error, { depth: null }) // Full error object dump
        
        if (error.response) {
          console.group('Error Response Details:')
          console.log('Status:', error.response.status)
          console.log('Status Text:', error.response.statusText)
          console.log('Full Response Data:', error.response.data)
          console.log('Headers:', error.response.headers)
          console.groupEnd()
          
          // Always show the error in the detailed modal
          if (error.response.status === 422) {
            // For database constraint errors
            if (error.response.data.message) {
              errorMessage.value = error.response.data.message
            } else {
              // If there's no message, stringify the entire response
              errorMessage.value = JSON.stringify(error.response.data, null, 2)
            }
            showErrorModal.value = true
          } else if (error.response.status === 404) {
            alertStore.error('Nie znaleziono kategorii.')
          } else {
            // For any other error, also show detailed error
            if (error.response.data && error.response.data.message) {
              errorMessage.value = error.response.data.message
            } else {
              errorMessage.value = 'Wystąpił błąd: ' + error.message
            }
            showErrorModal.value = true
          }
        } else {
          // Network error or something else
          alertStore.error('Wystąpił błąd podczas komunikacji z serwerem.')
        }
        
        // Always refresh the categories list
        fetchCategories()
      }
    }
    
    const formatDate = (dateString) => {
      const options = { year: 'numeric', month: 'short', day: 'numeric' }
      return new Date(dateString).toLocaleDateString('pl-PL', options)
    }
    
    const resetFilters = () => {
      Object.assign(filters, defaultFilters)
      fetchCategories()
    }
    
    // Lifecycle
    onMounted(() => {
      fetchCategories()
    })
    
    return {
      loading,
      categories,
      filters,
      defaultFilters,
      showModal,
      showDeleteModal,
      currentCategory,
      categoryToDelete,
      submitting,
      paginationPages,
      fetchCategories,
      openModal,
      saveCategory,
      deleteCategory,
      confirmDelete,
      formatDate,
      goToPage,
      resetFilters,
      sortOptions,
      tableColumns,
      showErrorModal,
      errorMessage
    }
  }
}
</script> 