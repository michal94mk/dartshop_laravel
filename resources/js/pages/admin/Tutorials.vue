<template>
  <div class="space-y-6 p-4 bg-white rounded-lg shadow-sm min-h-full">
    <!-- Page Header -->
    <div class="px-6 py-4">
      <page-header 
        title="Zarządzanie poradnikami"
        add-button-label="Dodaj"
        @add="showAddForm = true"
      />
    </div>
    
    <!-- Search and filters -->
    <search-filters
      v-if="!loading"
      :filters="filters"
      @update:filters="(newFilters) => { Object.assign(filters, newFilters); filters.page = 1; }"
      :sort-options="sortOptions"
      search-label="Wyszukaj"
      search-placeholder="Szukaj poradników..."
      @filter-change="fetchTutorials"
    >
      <template v-slot:filters>
        <div class="w-full sm:w-auto">
          <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
          <select
            id="status"
            name="status"
            v-model="filters.status"
            @change="fetchTutorials"
            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
          >
            <option value="">Wszystkie</option>
            <option value="draft">Szkice</option>
            <option value="published">Opublikowane</option>
          </select>
        </div>
      </template>
    </search-filters>
    
    <!-- Loading indicator -->
    <loading-spinner v-if="loading" />
    
    <!-- Tutorials Custom Table -->
    <div v-if="!loading && tutorials.data && tutorials.data.length > 0" class="mt-6 bg-white shadow-sm rounded-lg overflow-hidden">
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-64">
                Tytuł
              </th>
              <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-40">
                Slug
              </th>
              <th scope="col" class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-32">
                Autor
              </th>
              <th scope="col" class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-28">
                Data
              </th>
              <th scope="col" class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-24">
                Status
              </th>
              <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider w-32">
                Akcje
              </th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="item in tutorials.data" :key="item.id" class="hover:bg-gray-50">
              <!-- Title Column -->
              <td class="px-4 py-4">
                <div class="text-sm font-medium text-gray-900 max-w-[220px] truncate" :title="item.title">
                  {{ item.title }}
                </div>
              </td>
              
              <!-- Slug Column -->
              <td class="px-3 py-4">
                <div class="text-xs text-gray-500 max-w-[140px] truncate" :title="item.slug">
                  {{ item.slug }}
                </div>
              </td>
              
              <!-- Author Column -->
              <td class="px-3 py-4 text-center">
                <div class="text-xs text-gray-900 max-w-[110px] truncate mx-auto" :title="item.author ? item.author.name : 'Brak autora'">
                  {{ item.author ? item.author.name : 'Brak autora' }}
                </div>
              </td>
              
              <!-- Published At Column -->
              <td class="px-3 py-4 text-center">
                <span class="text-xs text-gray-500">{{ formatDate(item.published_at) }}</span>
              </td>
              
              <!-- Status Column -->
              <td class="px-3 py-4 text-center">
                <admin-badge 
                  :variant="getStatusVariant(item)"
                  size="xs"
                >
                  {{ getStatusLabel(item) }}
                </admin-badge>
              </td>
              
              <!-- Actions Column -->
              <td class="px-4 py-4 text-right">
                <action-buttons 
                  :item="item" 
                  @edit="editTutorial" 
                  @delete="confirmDelete"
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
      v-if="tutorials.data && tutorials.data.length > 0 && tutorials.last_page > 1"
      :pagination="tutorials" 
      items-label="poradników" 
      @page-change="goToPage" 
      class="mt-6"
    />
    
    <!-- No data message -->
    <no-data-message v-if="!loading && (!tutorials.data || tutorials.data.length === 0)" message="Brak poradników do wyświetlenia" />
    
    <!-- Add/Edit Modal -->
    <admin-modal
      :show="showAddForm || showEditForm"
      :title="showEditForm ? 'Edytuj poradnik' : 'Dodaj nowy poradnik'"
      size="3xl"
      @close="closeForm"
    >
      <form @submit.prevent="showEditForm ? updateTutorial() : addTutorial()" class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label for="title" class="block text-sm font-medium text-gray-700">Tytuł</label>
            <input 
              type="text" 
              id="title" 
              v-model="form.title" 
              @input="generateSlug"
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
              required
            >
          </div>
          
          <div>
            <label for="slug" class="block text-sm font-medium text-gray-700">Slug</label>
            <input 
              type="text" 
              id="slug" 
              v-model="form.slug" 
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
              required
            >
          </div>
        </div>
        
        <div>
          <label for="content" class="block text-sm font-medium text-gray-700">Treść</label>
          <textarea 
            id="content" 
            v-model="form.content" 
            rows="10"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
            required
          ></textarea>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div>
            <label for="video_url" class="block text-sm font-medium text-gray-700">URL wideo (opcjonalnie)</label>
            <input 
              type="url" 
              id="video_url" 
              v-model="form.video_url" 
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
              placeholder="https://www.youtube.com/watch?v=..."
            >
          </div>
          
          <div>
            <label for="order" class="block text-sm font-medium text-gray-700">Kolejność wyświetlania</label>
            <input 
              type="number" 
              id="order" 
              v-model="form.order" 
              min="0"
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
            >
          </div>
          
          <div>
            <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
            <select 
              id="status" 
              v-model="form.status" 
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
              required
            >
              <option value="draft">Szkic</option>
              <option value="published">Opublikowany</option>
            </select>
          </div>
        </div>
      </form>
      
      <template #footer>
        <admin-button-group justify="end" spacing="sm">
          <admin-button 
            @click="closeForm" 
            variant="secondary"
            outline
          >
            Anuluj
          </admin-button>
          <admin-button 
            @click="showEditForm ? updateTutorial() : addTutorial()" 
            variant="primary"
          >
            {{ showEditForm ? 'Zapisz zmiany' : 'Dodaj poradnik' }}
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
          Czy na pewno chcesz usunąć poradnik "{{ tutorialToDelete.title }}"?
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
            @click="deleteTutorial" 
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
import { ref, reactive, computed, onMounted, watch } from 'vue'
import axios from 'axios'
import { useAlertStore } from '../../stores/alertStore'
import { useAuthStore } from '../../stores/authStore'
import AdminButtonGroup from '../../components/admin/ui/AdminButtonGroup.vue'
import AdminButton from '../../components/admin/ui/AdminButton.vue'
import AdminBadge from '../../components/admin/ui/AdminBadge.vue'
import AdminModal from '../../components/admin/ui/AdminModal.vue'
import SearchFilters from '../../components/admin/SearchFilters.vue'
import LoadingSpinner from '../../components/admin/LoadingSpinner.vue'
import NoDataMessage from '../../components/admin/NoDataMessage.vue'
import PageHeader from '../../components/admin/PageHeader.vue'
import Pagination from '../../components/admin/Pagination.vue'
import ActionButtons from '../../components/admin/ActionButtons.vue'

export default {
  name: 'AdminTutorials',
  components: {
    AdminButtonGroup,
    AdminButton,
    AdminBadge,
    AdminModal,
    SearchFilters,
    LoadingSpinner,
    NoDataMessage,
    PageHeader,
    Pagination,
    ActionButtons
  },
  setup() {
    const alertStore = useAlertStore()
    const authStore = useAuthStore()
    const loading = ref(true)
    const tutorials = ref([])
    const showAddForm = ref(false)
    const showEditForm = ref(false)
    const showDeleteModal = ref(false)
    const tutorialToDelete = ref({})
    const searchTimeout = ref(null)
    
    // Sort options for the filter component
    const sortOptions = [
      { value: 'created_at', label: 'Data dodania' },
      { value: 'published_at', label: 'Data publikacji' },
      { value: 'title', label: 'Tytuł' }
    ]
    
    // Filters
    const filters = reactive({
      search: '',
      status: '',
      sort_field: 'created_at',
      sort_direction: 'desc',
      page: 1
    })
    
    const form = ref({
      title: '',
      slug: '',
      content: '',
      video_url: '',
      order: 0,
      status: 'draft'
    })
    
    // Fetch all tutorials
    const fetchTutorials = async () => {
      try {
        loading.value = true
        
        const params = {
          page: filters.page,
          search: filters.search,
          status: filters.status,
          sort_field: filters.sort_field,
          sort_direction: filters.sort_direction
        }
        
        const response = await axios.get('/api/admin/tutorials', { params })
        tutorials.value = response.data
      } catch (error) {
        console.error('Error fetching tutorials:', error)
        alertStore.error('Wystąpił błąd podczas pobierania poradników.')
      } finally {
        loading.value = false
      }
    }
    
    // Debounced version for search
    const debounce = (func, wait) => {
      let timeout
      return function executedFunction(...args) {
        const later = () => {
          clearTimeout(timeout)
          func(...args)
        }
        clearTimeout(timeout)
        timeout = setTimeout(later, wait)
      }
    }
    
    const debouncedFetchTutorials = debounce(fetchTutorials, 300)
    
    // Pagination
    const goToPage = (page) => {
      if (page === '...') return
      filters.page = page
      fetchTutorials()
    }
    
    // Add new tutorial
    const addTutorial = async () => {
      try {
        const response = await axios.post('/api/admin/tutorials', form.value)
        // Refresh the list to get updated data
        fetchTutorials()
        alertStore.success('Poradnik został dodany.')
        closeForm()
      } catch (error) {
        console.error('Error adding tutorial:', error)
        alertStore.error('Wystąpił błąd podczas dodawania poradnika.')
      }
    }
    
    // Edit tutorial
    const editTutorial = (tutorialOrId) => {
      // Handle either a tutorial object or just an ID
      let tutorial;
      if (typeof tutorialOrId === 'object' && tutorialOrId !== null) {
        tutorial = tutorialOrId;
      } else {
        // Find the tutorial by ID
        tutorial = tutorials.value.data ? tutorials.value.data.find(t => t.id === tutorialOrId) : null;
        if (!tutorial) {
          console.error('Tutorial not found with ID:', tutorialOrId);
          return;
        }
      }
      
      form.value = {
        id: tutorial.id,
        title: tutorial.title,
        slug: tutorial.slug,
        content: tutorial.content || '',
        video_url: tutorial.video_url || '',
        order: tutorial.order || 0,
        status: tutorial.status || 'draft'
      }
      showEditForm.value = true;
    }
    
    // Update tutorial
    const updateTutorial = async () => {
      try {
        const response = await axios.put(`/api/admin/tutorials/${form.value.id}`, form.value)
        // Refresh the list to get updated data
        fetchTutorials()
        alertStore.success('Poradnik został zaktualizowany.')
        closeForm()
      } catch (error) {
        console.error('Error updating tutorial:', error)
        alertStore.error('Wystąpił błąd podczas aktualizacji poradnika.')
      }
    }
    
    // Confirm delete
    const confirmDelete = (tutorialOrId) => {
      // Handle either a tutorial object or just an ID
      let tutorial;
      if (typeof tutorialOrId === 'object' && tutorialOrId !== null) {
        tutorial = tutorialOrId;
      } else {
        // Find the tutorial by ID
        tutorial = tutorials.value.data ? tutorials.value.data.find(t => t.id === tutorialOrId) : null;
        if (!tutorial) {
          console.error('Tutorial not found with ID:', tutorialOrId);
          return;
        }
      }
      
      tutorialToDelete.value = tutorial;
      showDeleteModal.value = true;
    }
    
    // Delete tutorial
    const deleteTutorial = async () => {
      try {
        await axios.delete(`/api/admin/tutorials/${tutorialToDelete.value.id}`)
        // Refresh the list to get updated data
        fetchTutorials()
        alertStore.success('Poradnik został usunięty.')
        showDeleteModal.value = false
      } catch (error) {
        console.error('Error deleting tutorial:', error)
        alertStore.error('Wystąpił błąd podczas usuwania poradnika.')
      }
    }
    
    // Close form
    const closeForm = () => {
      form.value = {
        title: '',
        slug: '',
        content: '',
        video_url: '',
        order: 0,
        status: 'draft'
      }
      showAddForm.value = false
      showEditForm.value = false
    }
    
    // Format date
    const formatDate = (dateString) => {
      if (!dateString) return '-'
      const options = { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' }
      return new Date(dateString).toLocaleDateString('pl-PL', options)
    }
    
    // Generate slug from title
    const generateSlug = () => {
      if (!form.value.title) return
      
      const slug = form.value.title
        .toLowerCase()
        .replace(/[^\w\s-]/g, '') // Remove special characters
        .replace(/\s+/g, '-') // Replace spaces with hyphens
        .replace(/-+/g, '-') // Replace multiple consecutive hyphens with a single one
      
      form.value.slug = slug
    }
    
    // Get status label
    const getStatusLabel = (tutorial) => {
      switch (tutorial.status) {
        case 'published': return 'Opublikowany'
        case 'draft': return 'Szkic'
        default: return tutorial.status
      }
    }
    
    // Get status class
    const getStatusClass = (tutorial) => {
      switch (tutorial.status) {
        case 'published': return 'bg-green-100 text-green-800'
        case 'draft': return 'bg-gray-100 text-gray-800'
        default: return 'bg-gray-100 text-gray-800'
      }
    }
    
    // Get status variant for badge
    const getStatusVariant = (tutorial) => {
      switch (tutorial.status) {
        case 'published':
          return 'green'
        case 'draft':
          return 'gray'
        default:
          return 'gray'
      }
    }
    
    // Watch for search changes to trigger debounced fetch
    watch(() => filters.search, () => {
      filters.page = 1
      debouncedFetchTutorials()
    })
    
    onMounted(() => {
      fetchTutorials()
    })
    
    return {
      loading,
      tutorials,
      showAddForm,
      showEditForm,
      showDeleteModal,
      tutorialToDelete,
      filters,
      form,
      sortOptions,
      fetchTutorials,
      goToPage,
      addTutorial,
      editTutorial,
      updateTutorial,
      confirmDelete,
      deleteTutorial,
      closeForm,
      formatDate,
      generateSlug,
      getStatusLabel,
      getStatusClass,
      getStatusVariant
    }
  }
}
</script> 