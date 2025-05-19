<template>
  <div class="p-6">
    <div class="flex justify-between items-center mb-6">
      <div>
        <h1 class="text-2xl font-semibold text-gray-900">Zarządzanie markami</h1>
        <p class="mt-2 text-sm text-gray-700">Lista wszystkich marek produktów z możliwością dodawania, edycji i usuwania.</p>
      </div>
      <button 
        @click="showAddForm = true" 
        class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
      >
        Dodaj markę
      </button>
    </div>
    
    <!-- Search and filters -->
    <div class="mt-6 bg-white shadow px-4 py-5 sm:rounded-lg sm:px-6">
      <div class="flex flex-wrap gap-4">
        <div class="flex-1 min-w-[200px]">
          <label for="search" class="block text-sm font-medium text-gray-700">Wyszukaj</label>
          <div class="mt-1">
            <input
              type="text"
              name="search"
              id="search"
              v-model="filters.search"
              @input="onSearchChange"
              class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
              placeholder="Nazwa marki..."
            />
          </div>
        </div>
        
        <div class="w-full sm:w-auto">
          <label for="sort" class="block text-sm font-medium text-gray-700">Sortuj</label>
          <select
            id="sort"
            name="sort"
            v-model="filters.sort_field"
            @change="fetchBrands"
            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
          >
            <option value="id">ID</option>
            <option value="name">Nazwa</option>
            <option value="created_at">Data utworzenia</option>
            <option value="products_count">Liczba produktów</option>
          </select>
        </div>
        
        <div class="w-full sm:w-auto">
          <label for="direction" class="block text-sm font-medium text-gray-700">Kierunek</label>
          <select
            id="direction"
            name="direction"
            v-model="filters.sort_direction"
            @change="fetchBrands"
            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
          >
            <option value="desc">Malejąco</option>
            <option value="asc">Rosnąco</option>
          </select>
        </div>
      </div>
    </div>
    
    <!-- Loading indicator -->
    <div v-if="loading" class="flex justify-center my-12">
      <svg class="animate-spin h-10 w-10 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
      </svg>
    </div>
    
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
              <button 
                @click="editBrand(brand)" 
                class="text-indigo-600 hover:text-indigo-900 mr-4"
              >
                Edytuj
              </button>
              <button 
                @click="confirmDelete(brand)" 
                class="text-red-600 hover:text-red-900"
              >
                Usuń
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <div v-else class="bg-white shadow overflow-hidden sm:rounded-md p-6 text-center text-gray-500 mt-4">
      Brak marek do wyświetlenia
    </div>
    
    <!-- Pagination -->
    <div v-if="brands.last_page > 1" class="mt-5 flex justify-between items-center">
      <div class="text-sm text-gray-700">
        Pokazuje {{ brands.from }} do {{ brands.to }} z {{ brands.total }} marek
      </div>
      <div>
        <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
          <button
            @click="goToPage(brands.current_page - 1)"
            :disabled="brands.current_page === 1"
            :class="[
              brands.current_page === 1 ? 'cursor-not-allowed opacity-50' : 'hover:bg-gray-50',
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
              page === brands.current_page
                ? 'z-10 bg-indigo-50 border-indigo-500 text-indigo-600'
                : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50',
              'relative inline-flex items-center px-4 py-2 border text-sm font-medium'
            ]"
          >
            {{ page }}
          </button>
          <button
            @click="goToPage(brands.current_page + 1)"
            :disabled="brands.current_page === brands.last_page"
            :class="[
              brands.current_page === brands.last_page ? 'cursor-not-allowed opacity-50' : 'hover:bg-gray-50',
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
  </div>
</template>

<script>
import { ref, onMounted, computed, reactive } from 'vue'
import axios from 'axios'
import { useAlertStore } from '../../stores/alertStore'

export default {
  name: 'AdminBrands',
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
    let searchTimeout = null
    
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
    
    // Computed
    const paginationPages = computed(() => {
      const total = brands.value.last_page
      const current = brands.value.current_page
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
    
    // Debounced search
    const onSearchChange = () => {
      if (searchTimeout) {
        clearTimeout(searchTimeout)
      }
      
      searchTimeout = setTimeout(() => {
        filters.page = 1 // Reset to first page on new search
        fetchBrands()
      }, 300)
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
      brandToDelete.value = brand
      showDeleteModal.value = true
    }
    
    // Delete brand
    const deleteBrand = async () => {
      try {
        await axios.delete(`/api/admin/brands/${brandToDelete.value.id}`)
        alertStore.success('Marka została usunięta.')
        showDeleteModal.value = false
        fetchBrands() // Refresh the list
      } catch (error) {
        console.error('Error deleting brand:', error)
        alertStore.error('Wystąpił błąd podczas usuwania marki.')
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
      paginationPages,
      onSearchChange,
      fetchBrands,
      addBrand,
      editBrand,
      updateBrand,
      confirmDelete,
      deleteBrand,
      closeForm,
      goToPage,
      formatDate
    }
  }
}
</script> 