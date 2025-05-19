<template>
  <div>
    <div class="sm:flex sm:items-center p-6">
      <div class="sm:flex-auto">
        <h1 class="text-2xl font-semibold text-gray-900">Zarządzanie kategoriami</h1>
        <p class="mt-2 text-sm text-gray-700">Lista wszystkich kategorii produktów z możliwością dodawania, edycji i usuwania.</p>
      </div>
      <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none">
        <button @click="openModal()" type="button" class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded hover:bg-indigo-700 transition-colors">
          Dodaj kategorię
        </button>
      </div>
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
              placeholder="Nazwa kategorii..."
            />
          </div>
        </div>
        
        <div class="w-full sm:w-auto">
          <label for="sort" class="block text-sm font-medium text-gray-700">Sortuj</label>
          <select
            id="sort"
            name="sort"
            v-model="filters.sort_field"
            @change="fetchCategories"
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
            @change="fetchCategories"
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
    
    <!-- Categories list -->
    <div v-else class="mt-8 flex flex-col">
      <div class="-my-2 -mx-4 overflow-x-auto sm:-mx-6 lg:-mx-8">
        <div class="inline-block min-w-full py-2 align-middle md:px-6 lg:px-8">
          <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
            <table class="min-w-full divide-y divide-gray-300">
              <thead class="bg-gray-50">
                <tr>
                  <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">ID</th>
                  <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">Nazwa</th>
                  <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Liczba produktów</th>
                  <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Data utworzenia</th>
                  <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                    <span class="sr-only">Akcje</span>
                  </th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-200 bg-white">
                <tr v-for="category in categories.data" :key="category.id">
                  <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">
                    {{ category.id }}
                  </td>
                  <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">
                    {{ category.name }}
                  </td>
                  <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                    {{ category.products_count || 0 }}
                  </td>
                  <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                    {{ formatDate(category.created_at) }}
                  </td>
                  <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                    <button @click="openModal(category)" class="px-3 py-1.5 bg-indigo-600 text-white text-sm font-medium rounded hover:bg-indigo-700 transition-colors mr-2">Edytuj</button>
                    <button @click="deleteCategory(category.id)" class="px-3 py-1.5 bg-red-600 text-white text-sm font-medium rounded hover:bg-red-700 transition-colors">Usuń</button>
                  </td>
                </tr>
                <tr v-if="categories.data && categories.data.length === 0">
                  <td colspan="5" class="px-3 py-4 text-sm text-gray-500 text-center">Brak kategorii</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Pagination -->
    <div v-if="categories.last_page > 1" class="mt-5 flex justify-between items-center p-6">
      <div class="text-sm text-gray-700">
        Pokazuje {{ categories.from }} do {{ categories.to }} z {{ categories.total }} kategorii
      </div>
      <div>
        <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
          <button
            @click="goToPage(categories.current_page - 1)"
            :disabled="categories.current_page === 1"
            :class="[
              categories.current_page === 1 ? 'cursor-not-allowed opacity-50' : 'hover:bg-gray-50',
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
              page === categories.current_page
                ? 'z-10 bg-indigo-50 border-indigo-500 text-indigo-600'
                : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50',
              'relative inline-flex items-center px-4 py-2 border text-sm font-medium'
            ]"
          >
            {{ page }}
          </button>
          <button
            @click="goToPage(categories.current_page + 1)"
            :disabled="categories.current_page === categories.last_page"
            :class="[
              categories.current_page === categories.last_page ? 'cursor-not-allowed opacity-50' : 'hover:bg-gray-50',
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
    
    <!-- Category Modal -->
    <div v-if="showModal" class="fixed z-10 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
      <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" @click="showModal = false"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
          <form @submit.prevent="saveCategory">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
              <div class="sm:flex sm:items-start">
                <div class="w-full">
                  <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4" id="modal-title">
                    {{ currentCategory.id ? 'Edytuj kategorię' : 'Dodaj nową kategorię' }}
                  </h3>
                  
                  <div class="grid grid-cols-1 gap-4">
                    <div>
                      <label for="name" class="block text-sm font-medium text-gray-700">Nazwa kategorii</label>
                      <input
                        type="text"
                        id="name"
                        v-model="currentCategory.name"
                        required
                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                      />
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
                {{ currentCategory.id ? 'Zapisz zmiany' : 'Dodaj kategorię' }}
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
                  Usuń kategorię
                </h3>
                <div class="mt-2">
                  <p class="text-sm text-gray-500">
                    Czy na pewno chcesz usunąć tę kategorię? Wszystkie produkty w tej kategorii zostaną pozbawione kategorii.
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
              Usuń kategorię
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
import { ref, computed, onMounted, reactive } from 'vue'
import { useAlertStore } from '../../stores/alertStore'
import axios from 'axios'

export default {
  name: 'AdminCategories',
  setup() {
    const alertStore = useAlertStore()
    
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
    
    // Filters and pagination
    const filters = reactive({
      search: '',
      sort_field: 'created_at',
      sort_direction: 'desc',
      page: 1
    })
    
    // Debounce timer
    let searchTimeout = null
    
    // Modals
    const showModal = ref(false)
    const showDeleteModal = ref(false)
    const categoryToDelete = ref(null)
    const currentCategory = ref({
      id: null,
      name: ''
    })
    
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
        const response = await axios.get('/api/admin/categories', { params: filters })
        categories.value = response.data
      } catch (error) {
        console.error('Error fetching categories:', error)
        alertStore.error('Wystąpił błąd podczas pobierania kategorii.')
      } finally {
        loading.value = false
      }
    }
    
    const onSearchChange = () => {
      // Debounce search input to prevent too many requests
      if (searchTimeout) {
        clearTimeout(searchTimeout)
      }
      
      searchTimeout = setTimeout(() => {
        filters.page = 1 // Reset to first page on new search
        fetchCategories()
      }, 300)
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
        if (currentCategory.value.id) {
          // Update existing category
          await axios.put(`/api/admin/categories/${currentCategory.value.id}`, { 
            name: currentCategory.value.name 
          })
          alertStore.success('Kategoria została zaktualizowana.')
        } else {
          // Create new category
          await axios.post('/api/admin/categories', { 
            name: currentCategory.value.name 
          })
          alertStore.success('Kategoria została dodana.')
        }
        
        showModal.value = false
        fetchCategories()
      } catch (error) {
        console.error('Error saving category:', error)
        alertStore.error('Wystąpił błąd podczas zapisywania kategorii.')
      }
    }
    
    const deleteCategory = (id) => {
      categoryToDelete.value = id
      showDeleteModal.value = true
    }
    
    const confirmDelete = async () => {
      try {
        await axios.delete(`/api/admin/categories/${categoryToDelete.value}`)
        alertStore.success('Kategoria została usunięta.')
        showDeleteModal.value = false
        fetchCategories()
      } catch (error) {
        console.error('Error deleting category:', error)
        alertStore.error('Wystąpił błąd podczas usuwania kategorii.')
      }
    }
    
    const formatDate = (dateString) => {
      const options = { year: 'numeric', month: 'short', day: 'numeric' }
      return new Date(dateString).toLocaleDateString('pl-PL', options)
    }
    
    // Lifecycle
    onMounted(() => {
      fetchCategories()
    })
    
    return {
      loading,
      categories,
      filters,
      showModal,
      showDeleteModal,
      currentCategory,
      paginationPages,
      fetchCategories,
      openModal,
      saveCategory,
      deleteCategory,
      confirmDelete,
      formatDate,
      onSearchChange,
      goToPage
    }
  }
}
</script> 