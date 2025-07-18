<template>
  <div class="space-y-6 bg-white min-h-full lg:pr-6">
    <!-- Page Header -->
    <div class="px-6 py-4">
      <page-header 
        title="Marki"
        add-button-label="Dodaj"
        @add="openAddForm"
      />
    </div>
    
    <!-- Search and filters -->
    <search-filters
      v-if="!loading"
      :filters="filters"
      :sort-options="sortOptions"
      :default-filters="defaultFilters"
      search-label="Wyszukaj"
      search-placeholder="Nazwa marki..."
      @update:filters="(newFilters) => { Object.assign(filters, newFilters); filters.page = 1; }"
      @filter-change="fetchBrands"
      @reset-filters="resetFilters"
    />
    
    <!-- Loading indicator -->
    <loading-spinner v-if="loading" />
    
    <!-- Brands Custom Table -->
    <div v-if="!loading && brands.data && brands.data.length > 0" class="mt-6 bg-white shadow-sm rounded-lg overflow-hidden">
                      <div class="overflow-x-auto -mx-6 px-6" style="scrollbar-width: thin; scrollbar-color: #d1d5db #f3f4f6;">
          <table class="min-w-full divide-y divide-gray-200 whitespace-nowrap">
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
            <tr v-for="item in brands.data" :key="item.id" class="hover:bg-gray-50">
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
                  @edit="editBrand" 
                  @delete="confirmDelete"
                  justify="end"
                />
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    
    <!-- No data message -->
    <no-data-message v-else-if="!loading" message="Brak marek do wyświetlenia" />
    
    <!-- Pagination -->
    <pagination 
      v-if="!loading && brands.last_page > 1" 
      :pagination="brands" 
      items-label="marek"
      @page-change="goToPage" 
    />
    
    <!-- Add/Edit Modal -->
    <admin-modal
      :show="showAddForm || showEditForm"
      :title="showEditForm ? 'Edytuj markę' : 'Dodaj nową markę'"
      size="md"
      @close="closeForm"
    >
      <form @submit.prevent="showEditForm ? updateBrand() : addBrand()">
        <div class="mb-4">
          <label for="name" class="block text-sm font-medium text-gray-700">Nazwa</label>
          <input 
            type="text" 
            id="name" 
            v-model="form.name" 
            :class="[
              'mt-1 block w-full rounded-md shadow-sm sm:text-sm',
              formErrors.name 
                ? 'border-red-300 focus:border-red-500 focus:ring-red-500' 
                : 'border-gray-300 focus:border-indigo-500 focus:ring-indigo-500'
            ]"
            required
          >
          <p v-if="formErrors.name" class="mt-1 text-sm text-red-600">{{ Array.isArray(formErrors.name) ? formErrors.name[0] : formErrors.name }}</p>
        </div>
      </form>
      
      <template #footer>
        <admin-button-group justify="end" spacing="sm">
          <admin-button 
            type="button" 
            @click="closeForm" 
            variant="secondary"
            outline
          >
            Anuluj
          </admin-button>
          <admin-button 
            type="button"
            @click="showEditForm ? updateBrand() : addBrand()" 
            variant="primary"
          >
            {{ showEditForm ? 'Zapisz zmiany' : 'Dodaj markę' }}
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
      <div class="text-center">
        <p class="text-sm text-gray-500">
          Czy na pewno chcesz usunąć markę "{{ brandToDelete.name }}"?
          <span v-if="brandToDelete.products_count > 0" class="mt-2 block font-semibold text-red-600">
            Uwaga: Ta marka jest przypisana do {{ brandToDelete.products_count }} produktów.
          </span>
        </p>
      </div>
      
      <template #footer>
        <admin-button-group justify="center" spacing="sm">
          <admin-button 
            @click="showDeleteModal = false" 
            variant="secondary"
            outline
          >
            Anuluj
          </admin-button>
          <admin-button 
            @click="deleteBrand" 
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
import { ref, onMounted, computed, reactive, watch } from 'vue'
import axios from 'axios'
import { useAlertStore } from '../../stores/alertStore'

import SearchFilters from '../../components/admin/SearchFilters.vue'
import LoadingSpinner from '../../components/admin/LoadingSpinner.vue'
import NoDataMessage from '../../components/admin/NoDataMessage.vue'
import Pagination from '../../components/admin/Pagination.vue'
import PageHeader from '../../components/admin/PageHeader.vue'
import AdminModal from '../../components/admin/ui/AdminModal.vue'
import AdminButtonGroup from '../../components/admin/ui/AdminButtonGroup.vue'
import AdminButton from '../../components/admin/ui/AdminButton.vue'
import AdminBadge from '../../components/admin/ui/AdminBadge.vue'
import ActionButtons from '../../components/admin/ActionButtons.vue'
import { useAuthStore } from '../../stores/authStore'
import { useRoute } from 'vue-router'

export default {
  name: 'AdminBrands',
  components: {
    SearchFilters,
    LoadingSpinner,
    NoDataMessage,
    Pagination,
    PageHeader,
    AdminModal,
    AdminButtonGroup,
    AdminButton,
    AdminBadge,
    ActionButtons
  },
  setup() {
    const alertStore = useAlertStore()
    const authStore = useAuthStore()
    const route = useRoute()
    const loading = ref(true)
    const brands = ref({
      data: [],
      current_page: 1,
      from: 1,
      to: 0,
      total: 0,
      last_page: 1,
      per_page: 10
    })
    const showAddForm = ref(false)
    const showEditForm = ref(false)
    const showDeleteModal = ref(false)
    const brandToDelete = ref({})
    

    
    // Sort options for the filter component
    const sortOptions = [
      { value: 'id', label: 'ID' },
      { value: 'name', label: 'Nazwa' },
      { value: 'created_at', label: 'Data utworzenia' },
      { value: 'products_count', label: 'Liczba produktów' }
    ]
    
    // Default filters
    const defaultFilters = {
      search: '',
      sort_field: 'id',
      sort_direction: 'asc',
      page: 1
    }
    
    // Filters and pagination
    const filters = reactive({ ...defaultFilters })
    
    const form = ref({
      name: ''
    })
    
    // Form validation errors
    const formErrors = ref({})
    
    // Fetch all brands with pagination and filters
    const fetchBrands = async () => {
      try {
        loading.value = true
        const response = await axios.get('/api/admin/brands', { params: filters })
        brands.value = response.data
      } catch (error) {
        console.error('Error fetching brands:', error)
        
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
        
        if (error.response && error.response.data && error.response.data.message) {
          alertStore.error(error.response.data.message)
        } else {
          alertStore.error('Wystąpił błąd podczas pobierania marek.')
        }
        // Reset search if there was an error with it
        if (filters.search) {
          console.log('Resetting search after error')
          filters.search = ''
        }
      } finally {
        loading.value = false
      }
    }
    
    // Pagination
    const goToPage = (page) => {
      if (page === '...') return
      
      filters.page = page
      fetchBrands()
    }
    
    // Open add form
    const openAddForm = () => {
      form.value = {
        name: ''
      }
      formErrors.value = {}
      showAddForm.value = true
    }
    
    // Add new brand
    const addBrand = async () => {
      try {
        // Clear previous errors
        formErrors.value = {}
        
        await axios.post('/api/admin/brands', form.value)
        alertStore.success('Marka została dodana.')
        closeForm()
        fetchBrands() // Refresh the list
      } catch (error) {
        console.error('Error adding brand:', error)
        if (error.response && error.response.status === 422) {
          // Validation errors
          if (error.response.data.errors) {
            formErrors.value = error.response.data.errors
          } else if (error.response.data.message) {
            alertStore.error(error.response.data.message)
          }
        } else if (error.response && error.response.data && error.response.data.message) {
          alertStore.error(error.response.data.message)
        } else {
          alertStore.error('Wystąpił błąd podczas dodawania marki.')
        }
      }
    }
    
    // Edit brand
    const editBrand = (brand) => {
      form.value = {
        id: brand.id,
        name: brand.name
      }
      formErrors.value = {}
      showEditForm.value = true
    }
    
    // Update brand
    const updateBrand = async () => {
      try {
        // Clear previous errors
        formErrors.value = {}
        
        await axios.put(`/api/admin/brands/${form.value.id}`, form.value)
        alertStore.success('Marka została zaktualizowana.')
        closeForm()
        fetchBrands() // Refresh the list
      } catch (error) {
        console.error('Error updating brand:', error)
        if (error.response && error.response.status === 422) {
          // Validation errors
          if (error.response.data.errors) {
            formErrors.value = error.response.data.errors
          } else if (error.response.data.message) {
            alertStore.error(error.response.data.message)
          }
        } else if (error.response && error.response.data && error.response.data.message) {
          alertStore.error(error.response.data.message)
        } else {
          alertStore.error('Wystąpił błąd podczas aktualizacji marki.')
        }
      }
    }
    
    // Confirm delete
    const confirmDelete = (brand) => {
      console.log('confirmDelete called with brand:', brand);
      console.log('Brand ID type:', typeof brand.id);
      console.log('Brand ID value:', brand.id);
      
      brandToDelete.value = brand
      showDeleteModal.value = true
    }
    
    // Delete brand
    const deleteBrand = async () => {
      try {
        // Ensure we have the correct ID format
        const brandId = typeof brandToDelete.value === 'object' 
                      ? brandToDelete.value.id 
                      : brandToDelete.value;
        
        await axios.delete(`/api/admin/brands/${brandId}`)
        alertStore.success('Marka została usunięta.')
        showDeleteModal.value = false
        fetchBrands() // Refresh the list
      } catch (error) {
        showDeleteModal.value = false
        console.error('Error deleting brand:', error)
        if (error.response && error.response.data && error.response.data.message) {
          alertStore.error(error.response.data.message)
        } else {
          alertStore.error('Wystąpił błąd podczas usuwania marki.')
        }
      }
    }
    
    // Close form
    const closeForm = () => {
      form.value = {
        name: ''
      }
      formErrors.value = {}
      showAddForm.value = false
      showEditForm.value = false
    }
    
    // Format date
    const formatDate = (dateString) => {
      if (!dateString) return '-';
      const options = { year: 'numeric', month: 'short', day: 'numeric' };
      return new Date(dateString).toLocaleDateString('pl-PL', options);
    }
    
    const resetFilters = () => {
      Object.assign(filters, defaultFilters)
      fetchBrands()
    }
    
    onMounted(async () => {
      // Check if user is logged in and is admin before fetching data
      if (!authStore.isLoggedIn || !authStore.isAdmin) {
        console.log('User not logged in or not admin, skipping data fetch');
        return;
      }
      
      await fetchBrands()
    })
    
    // Watch for auth state changes (logout)
    watch(() => authStore.isLoggedIn, (newValue) => {
      console.log('Brands: Auth state changed, isLoggedIn:', newValue)
      if (!newValue) {
        console.log('Brands: User logged out, clearing data')
        loading.value = false
        // Clear data when user logs out
        brands.value = {
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
      console.log('Brands: Route changed to:', newPath)
      if (!newPath.startsWith('/admin')) {
        console.log('Brands: Not on admin page, stopping data fetch')
        loading.value = false
      }
    })
    
    // Lifecycle
    
    return {
      loading,
      brands,
      showAddForm,
      showEditForm,
      showDeleteModal,
      brandToDelete,
      form,
      formErrors,
      filters,
      defaultFilters,
      sortOptions,
      fetchBrands,
      openAddForm,
      addBrand,
      editBrand,
      updateBrand,
      confirmDelete,
      deleteBrand,
      closeForm,
      goToPage,
      formatDate,
      resetFilters,

    }
  }
}
</script> 