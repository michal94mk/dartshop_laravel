<template>
  <div class="space-y-6 bg-white min-h-full">
    <!-- Page Header -->
    <div class="px-6 py-4">
      <page-header
        title="Kategorie"
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
    />

    <!-- Loading indicator -->
    <loading-spinner v-if="loading" />
    
    <!-- Categories Custom Table -->
    <div v-if="!loading && categories.data && categories.data.length > 0" class="mt-6 bg-white shadow-sm rounded-lg overflow-hidden">
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
    
    <!-- No data message -->
    <no-data-message v-else-if="!loading" message="Brak kategorii do wyświetlenia" />
    
    <!-- Pagination -->
    <pagination 
      v-if="!loading && categories.data && categories.data.length > 0 && categories.last_page > 1"
      :pagination="categories" 
      items-label="kategorii" 
      @page-change="goToPage" 
    />
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
          :class="[
            'mt-1 block w-full shadow-sm sm:text-sm rounded-md',
            formErrors.name 
              ? 'border-red-300 focus:border-red-500 focus:ring-red-500' 
              : 'border-gray-300 focus:border-indigo-500 focus:ring-indigo-500'
          ]"
          placeholder="Np. Lotki, Tarcze, Akcesoria"
        />
        <p v-if="formErrors.name" class="mt-1 text-sm text-red-600">{{ Array.isArray(formErrors.name) ? formErrors.name[0] : formErrors.name }}</p>
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
    title="Potwierdź usunięcie"
    size="md"
    icon-variant="danger"
    @close="showDeleteModal = false"
  >
    <div class="text-center">
      <p class="text-sm text-gray-500">
        Czy na pewno chcesz usunąć kategorię "{{ categoryToDelete?.name }}"?
        <span v-if="categoryToDelete?.products_count > 0" class="mt-2 block font-semibold text-red-600">
          Uwaga: Ta kategoria jest przypisana do {{ categoryToDelete.products_count }} produktów.
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
          @click="confirmDelete" 
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
    :error-message="errorMessage"
    @close="showErrorModal = false"
  />
</template>

<script>
import { ref, computed, onMounted, reactive, watch } from 'vue'
import { useAlertStore } from '../../stores/alertStore'
import { useAuthStore } from '../../stores/authStore'
import { useCategoryStore } from '../../stores/categoryStore'
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
import { useRoute } from 'vue-router'

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
    const categoryStore = useCategoryStore()
    const route = useRoute()
    


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
      sort_field: 'name',
      sort_direction: 'asc',
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
    
    // Form validation errors
    const formErrors = ref({})
    
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
        
        const response = await axios.get('/api/admin/categories', { params })
        const pag = response.data.pagination || {};
        categories.value = {
          data: response.data.data || [],
          current_page: pag.current_page ?? 1,
          last_page: pag.last_page ?? 1,
          per_page: pag.per_page ?? 15,
          total: pag.total ?? 0,
          from: pag.from ?? 0,
          to: pag.to ?? 0
        };
      } catch (error) {
        console.error('Error fetching categories:', error);
        
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
      formErrors.value = {}
      showModal.value = true
    }
    
    const saveCategory = async () => {
      try {
        submitting.value = true
        // Clear previous errors
        formErrors.value = {}
        
        const url = currentCategory.value.id 
          ? `/api/admin/categories/${currentCategory.value.id}`
          : '/api/admin/categories'
        
        const method = currentCategory.value.id ? 'put' : 'post'
        
        const response = await axios[method](url, {
          name: currentCategory.value.name
        })
        
        showModal.value = false
        alertStore.success(response.data.message || (currentCategory.value.id ? 'Kategoria została zaktualizowana' : 'Kategoria została dodana'))
        await fetchCategories()
        // Refresh categories in the store
        await categoryStore.refreshCategories()
        
      } catch (error) {
        console.error('Error saving category:', error)
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
          alertStore.error('Wystąpił błąd podczas zapisywania kategorii.')
        }
      } finally {
        submitting.value = false
      }
    }
    
    const deleteCategory = (category) => {
      categoryToDelete.value = typeof category === 'object' ? category : categories.value.data.find(c => c.id === category);
      showDeleteModal.value = true;
    }
    
    const confirmDelete = async () => {
      try {
        const response = await axios.delete(`/api/admin/categories/${categoryToDelete.value.id}`)
        showDeleteModal.value = false
        alertStore.success(response.data.message || 'Kategoria została usunięta')
        await fetchCategories()
        // Refresh categories in the store
        await categoryStore.refreshCategories()
      } catch (error) {
        console.error('Error deleting category:', error)
        if (error.response && error.response.data && error.response.data.message) {
          alertStore.error(error.response.data.message)
        } else {
          alertStore.error('Wystąpił błąd podczas usuwania kategorii.')
        }
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
    
    // Watch for auth state changes (logout)
    watch(() => authStore.isLoggedIn, (newValue) => {
      console.log('Categories: Auth state changed, isLoggedIn:', newValue)
      if (!newValue) {
        console.log('Categories: User logged out, clearing data')
        loading.value = false
        // Clear data when user logs out
        categories.value = {
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
      console.log('Categories: Route changed to:', newPath)
      if (!newPath.startsWith('/admin')) {
        console.log('Categories: Not on admin page, stopping data fetch')
        loading.value = false
      }
    })
    
    // Lifecycle
    onMounted(async () => {
      // Check if user is logged in and is admin before fetching data
      if (!authStore.isLoggedIn || !authStore.isAdmin) {
        console.log('User not logged in or not admin, skipping data fetch');
        return;
      }
      
      await fetchCategories()
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
      formErrors,
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