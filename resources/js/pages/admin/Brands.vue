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
      search-label="Wyszukaj"
      search-placeholder="Nazwa marki..."
      @update:filters="filters = $event"
      @filter-change="fetchBrands"
    />
    
    <!-- Loading indicator -->
    <loading-spinner v-if="loading" />
    
    <!-- Brands Table -->
    <div v-else-if="brands.data && brands.data.length" class="bg-white shadow overflow-hidden sm:rounded-md mt-4">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nazwa</th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Liczba produktów</th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data utworzenia</th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Akcje</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="brand in brands.data" :key="brand.id">
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ brand.id }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ brand.name }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ brand.products_count }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ formatDate(brand.created_at) }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
              <action-buttons 
                :item="brand" 
                @edit="editBrand" 
                @delete="confirmDelete"
              />
            </td>
          </tr>
        </tbody>
      </table>
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
    <div v-if="showAddForm || showEditForm" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full flex items-center justify-center">
      <div class="relative mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
          <h3 class="text-lg font-medium text-gray-900">{{ showEditForm ? 'Edytuj markę' : 'Dodaj nową markę' }}</h3>
          <button @click="closeForm" class="text-gray-400 hover:text-gray-500">
            <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
        
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
          
          <div class="flex justify-end">
            <button 
              type="button" 
              @click="closeForm" 
              class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 mr-2"
            >
              Anuluj
            </button>
            <button 
              type="submit" 
              class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            >
              {{ showEditForm ? 'Zapisz zmiany' : 'Dodaj markę' }}
            </button>
          </div>
        </form>
      </div>
    </div>
    
    <!-- Delete Confirmation Modal -->
    <div v-if="showDeleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full flex items-center justify-center">
      <div class="relative mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
          <h3 class="text-lg leading-6 font-medium text-gray-900">Potwierdź usunięcie</h3>
          <div class="mt-2 px-7 py-3">
            <p class="text-sm text-gray-500">
              Czy na pewno chcesz usunąć markę "{{ brandToDelete.name }}"?
              <span v-if="brandToDelete.products_count > 0" class="mt-2 block font-semibold text-red-600">
                Uwaga: Ta marka jest przypisana do {{ brandToDelete.products_count }} produktów.
              </span>
            </p>
          </div>
          <div class="flex justify-center mt-4 gap-4">
            <button 
              @click="showDeleteModal = false" 
              class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            >
              Anuluj
            </button>
            <button 
              @click="deleteBrand" 
              class="bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
            >
              Usuń
            </button>
          </div>
        </div>
      </div>
    </div>
    
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

export default {
  name: 'AdminBrands',
  components: {
    DetailedErrorModal
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
    
    // Filters and pagination
    const filters = reactive({
      search: '',
      sort_field: 'id',
      sort_direction: 'asc',
      page: 1
    })
    
    const form = ref({
      name: ''
    })
    
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
      sortOptions,
      fetchBrands,
      addBrand,
      editBrand,
      updateBrand,
      confirmDelete,
      deleteBrand,
      closeForm,
      goToPage,
      formatDate,
      showErrorModal,
      errorMessage
    }
  }
}
</script> 