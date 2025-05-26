<template>
  <div>
    <!-- Page Header -->
    <page-header 
      title="Zarządzanie kategoriami"
      subtitle="Lista wszystkich kategorii produktów z możliwością dodawania, edycji i usuwania."
      add-button-label="Dodaj kategorię"
      @add="openModal()"
    />
    
    <!-- Search and filters -->
    <search-filters
      v-if="!loading"
      :filters="filters"
      :sort-options="sortOptions"
      search-label="Wyszukaj"
      search-placeholder="Nazwa kategorii..."

      @update:filters="(newFilters) => { Object.assign(filters, newFilters); filters.page = 1; }"
      @filter-change="fetchCategories"
    >
      <template v-slot:filters>
        <!-- No additional filters needed -->
      </template>
    </search-filters>
    
    <!-- Loading indicator -->
    <loading-spinner v-if="loading" />
    
    <!-- Categories list -->
    <admin-table
      v-if="categories.data && categories.data.length > 0"
      :columns="tableColumns"
      :items="categories.data"
      class="mt-8"
    >
      <template #cell-products_count="{ item }">
        {{ item.products_count || 0 }}
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
            @click="deleteCategory(item)"
            variant="danger"
            size="sm"
          >
            Usuń
          </admin-button>
        </admin-button-group>
      </template>
    </admin-table>
    
    <!-- Pagination -->
    <pagination 
      v-if="categories.data && categories.data.length > 0 && categories.last_page > 1"
      :pagination="categories" 
      items-label="kategorii" 
      @page-change="goToPage" 
    />
    
    <!-- No data message -->
    <no-data-message v-if="!loading && (!categories.data || categories.data.length === 0)" message="Brak kategorii do wyświetlenia" />
    
    <!-- Category Modal -->
    <admin-modal
      :show="showModal"
      :title="currentCategory.id ? 'Edytuj kategorię' : 'Dodaj nową kategorię'"
      size="lg"
      @close="showModal = false"
    >
      <form @submit.prevent="saveCategory" class="space-y-4">
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
          >
            {{ currentCategory.id ? 'Zapisz zmiany' : 'Dodaj kategorię' }}
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
          Czy na pewno chcesz usunąć tę kategorię? Tej operacji nie da się cofnąć.
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
            @click="confirmDelete"
            variant="danger"
          >
            Usuń kategorię
          </admin-button>
        </admin-button-group>
      </template>
    </admin-modal>
    
    <!-- Detailed Error Modal -->
    <detailed-error-modal
      :show="showErrorModal"
      :message="errorMessage"
      title="Nie można usunąć kategorii"
      @close="showErrorModal = false"
    />
  </div>
</template>

<script>
import { ref, computed, onMounted, reactive } from 'vue'
import { useAlertStore } from '../../stores/alertStore'
import axios from 'axios'
import DetailedErrorModal from '../../components/ui/DetailedErrorModal.vue'
import AdminTable from '../../components/admin/ui/AdminTable.vue'
import AdminModal from '../../components/admin/ui/AdminModal.vue'
import AdminButtonGroup from '../../components/admin/ui/AdminButtonGroup.vue'
import AdminButton from '../../components/admin/ui/AdminButton.vue'
import SearchFilters from '../../components/admin/SearchFilters.vue'
import LoadingSpinner from '../../components/admin/LoadingSpinner.vue'
import NoDataMessage from '../../components/admin/NoDataMessage.vue'
import Pagination from '../../components/admin/Pagination.vue'
import PageHeader from '../../components/admin/PageHeader.vue'

export default {
  name: 'AdminCategories',
  components: {
    DetailedErrorModal,
    AdminTable,
    AdminModal,
    AdminButtonGroup,
    AdminButton,
    SearchFilters,
    LoadingSpinner,
    NoDataMessage,
    Pagination,
    PageHeader
  },
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
    
    // Sort options for the filter component
    const sortOptions = [
      { value: 'id', label: 'ID' },
      { value: 'name', label: 'Nazwa' },
      { value: 'created_at', label: 'Data utworzenia' },
      { value: 'products_count', label: 'Liczba produktów' }
    ]
    
    // Table columns definition
    const tableColumns = [
      { key: 'name', label: 'Nazwa', width: '350px' },
      { key: 'products_count', label: 'Liczba produktów', width: '180px' },
      { key: 'created_at', label: 'Data utworzenia', type: 'date', width: '180px' },
      { key: 'actions', label: 'Akcje', align: 'right', width: '160px' }
    ]
    

    
    // Filters and pagination
    const filters = reactive({
      search: '',
      sort_field: 'created_at',
      sort_direction: 'desc',
      page: 1
    })
    

    
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
        
        const response = await axios.get('/api/admin/categories', { params })
        categories.value = response.data
      } catch (error) {
        console.error('Error fetching categories:', error)
        console.error('Error details:', error.response?.data)
        alertStore.error('Wystąpił błąd podczas pobierania kategorii.')
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
      goToPage,
      sortOptions,
      tableColumns,
      showErrorModal,
      errorMessage
    }
  }
}
</script> 