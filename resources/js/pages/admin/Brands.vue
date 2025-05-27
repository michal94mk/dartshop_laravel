<template>
  <div class="p-6">
    <!-- Page Header -->
    <page-header 
      title="Zarządzanie markami"
      subtitle="Lista wszystkich marek produktów z możliwością dodawania, edycji i usuwania."
      add-button-label="Dodaj markę"
      @add="showAddForm = true"
    />
    
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
    
    <!-- Brands Table -->
    <admin-table
      v-if="brands.data && brands.data.length"
      :columns="tableColumns"
      :items="brands.data"
      class="mt-4"
    >
      <template #cell-actions="{ item }">
        <action-buttons 
          :item="item" 
          @edit="editBrand" 
          @delete="confirmDelete"
        />
      </template>
    </admin-table>
    
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
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
            required
          >
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
    
    <!-- Detailed Error Modal -->
    <detailed-error-modal
      :show="showErrorModal"
      :message="errorMessage"
      title="Nie można usunąć marki"
      @close="showErrorModal = false"
    />
  </div>
</template>

<script>
import { ref, onMounted, computed, reactive } from 'vue'
import axios from 'axios'
import { useAlertStore } from '../../stores/alertStore'
import DetailedErrorModal from '../../components/ui/DetailedErrorModal.vue'
import SearchFilters from '../../components/admin/SearchFilters.vue'
import LoadingSpinner from '../../components/admin/LoadingSpinner.vue'
import NoDataMessage from '../../components/admin/NoDataMessage.vue'
import Pagination from '../../components/admin/Pagination.vue'
import PageHeader from '../../components/admin/PageHeader.vue'
import AdminTable from '../../components/admin/ui/AdminTable.vue'
import AdminModal from '../../components/admin/ui/AdminModal.vue'
import AdminButtonGroup from '../../components/admin/ui/AdminButtonGroup.vue'
import AdminButton from '../../components/admin/ui/AdminButton.vue'
import ActionButtons from '../../components/admin/ActionButtons.vue'

export default {
  name: 'AdminBrands',
  components: {
    DetailedErrorModal,
    SearchFilters,
    LoadingSpinner,
    NoDataMessage,
    Pagination,
    PageHeader,
    AdminTable,
    AdminModal,
    AdminButtonGroup,
    AdminButton,
    ActionButtons
  },
  setup() {
    const alertStore = useAlertStore()
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
    
    // Error modal
    const showErrorModal = ref(false)
    const errorMessage = ref('')
    
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
    
    // Table columns definition
    const tableColumns = [
      { key: 'name', label: 'Nazwa', width: '350px' },
      { key: 'products_count', label: 'Liczba produktów', width: '180px' },
      { key: 'created_at', label: 'Data utworzenia', type: 'date', width: '180px' },
      { key: 'actions', label: 'Akcje', align: 'right', width: '160px' }
    ]
    
    // Fetch all brands with pagination and filters
    const fetchBrands = async () => {
      try {
        loading.value = true
        const response = await axios.get('/api/admin/brands', { params: filters })
        brands.value = response.data
      } catch (error) {
        console.error('Error fetching brands:', error)
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
    
    // Add new brand
    const addBrand = async () => {
      try {
        await axios.post('/api/admin/brands', form.value)
        alertStore.success('Marka została dodana.')
        closeForm()
        fetchBrands() // Refresh the list
      } catch (error) {
        console.error('Error adding brand:', error)
        alertStore.error('Wystąpił błąd podczas dodawania marki.')
      }
    }
    
    // Edit brand
    const editBrand = (brand) => {
      form.value = {
        id: brand.id,
        name: brand.name
      }
      showEditForm.value = true
    }
    
    // Update brand
    const updateBrand = async () => {
      try {
        await axios.put(`/api/admin/brands/${form.value.id}`, form.value)
        alertStore.success('Marka została zaktualizowana.')
        closeForm()
        fetchBrands() // Refresh the list
      } catch (error) {
        console.error('Error updating brand:', error)
        alertStore.error('Wystąpił błąd podczas aktualizacji marki.')
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
        
        console.log('Attempting to delete brand with ID:', brandId);
        
        await axios.delete(`/api/admin/brands/${brandId}`)
        alertStore.success('Marka została usunięta.')
        showDeleteModal.value = false
        fetchBrands() // Refresh the list
      } catch (error) {
        // Close the delete confirmation modal
        showDeleteModal.value = false
        
        console.error('Error deleting brand:', error)
        console.dir(error, { depth: null }) // Full error object dump
        
        if (error.response) {
          console.group('Error Response Details:')
          console.log('Status:', error.response.status)
          console.log('Status Text:', error.response.statusText)
          console.log('Full Response Data:', error.response.data)
          console.log('Headers:', error.response.headers)
          console.groupEnd()
          
          // Display detailed error in modal if appropriate
          if (error.response.status === 422) {
            // For database constraint errors
            if (error.response.data.message) {
              // Dokładnie przekazuj komunikat bez modyfikacji, tak jak w CategoryController
              errorMessage.value = error.response.data.message
              console.log('Error message content:', errorMessage.value)
              console.log('Message contains newlines:', errorMessage.value.includes('\n'))
              console.log('Message contains PHP_EOL:', errorMessage.value.includes('PHP_EOL'))
            } else {
              // If there's no message, stringify the entire response
              errorMessage.value = JSON.stringify(error.response.data, null, 2)
            }
            showErrorModal.value = true
          } else if (error.response.status === 404) {
            alertStore.error('Nie znaleziono marki.')
          } else {
            // For any other error, also show detailed error
            if (error.response.data && error.response.data.message) {
              errorMessage.value = error.response.data.message
              showErrorModal.value = true
            } else {
              alertStore.error('Wystąpił błąd podczas usuwania marki: ' + error.message)
            }
          }
        } else {
          // Network error or something else
          alertStore.error('Wystąpił błąd podczas komunikacji z serwerem.')
        }
        
        // Always refresh the brands list
        fetchBrands()
      }
    }
    
    // Close form
    const closeForm = () => {
      form.value = {
        name: ''
      }
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
    
    onMounted(() => {
      fetchBrands()
    })
    
    return {
      loading,
      brands,
      showAddForm,
      showEditForm,
      showDeleteModal,
      brandToDelete,
      form,
      filters,
      defaultFilters,
      sortOptions,
      tableColumns,
      fetchBrands,
      addBrand,
      editBrand,
      updateBrand,
      confirmDelete,
      deleteBrand,
      closeForm,
      goToPage,
      formatDate,
      resetFilters,
      showErrorModal,
      errorMessage
    }
  }
}
</script> 