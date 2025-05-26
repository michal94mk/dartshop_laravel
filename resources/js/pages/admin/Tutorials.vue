<template>
  <div class="p-6">
    <!-- Page Header -->
    <page-header 
      title="Zarządzanie poradnikami"
      subtitle="Lista wszystkich poradników z możliwością dodawania, edycji i usuwania."
      add-button-label="Dodaj poradnik"
      @add="showAddForm = true"
    />
    
    <!-- Search and filters -->
    <search-filters
      v-if="!loading"
      :filters.sync="filters"
      :sort-options="sortOptions"
      search-label="Wyszukaj"
      search-placeholder="Szukaj poradników..."
      @filter-change="filterTutorials"
    >
      <template v-slot:filters>
        <div class="w-full sm:w-auto">
          <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
          <select
            id="status"
            name="status"
            v-model="filters.status"
            @change="filterTutorials"
            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
          >
            <option value="">Wszystkie</option>
            <option value="draft">Szkice</option>
            <option value="published">Opublikowane</option>
            <option value="scheduled">Zaplanowane</option>
          </select>
        </div>
        
        <div class="w-full sm:w-auto">
          <label for="featured" class="block text-sm font-medium text-gray-700">Wyróżniony</label>
          <select
            id="featured"
            name="featured"
            v-model="filters.featured"
            @change="filterTutorials"
            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
          >
            <option value="">Wszystkie</option>
            <option value="true">Wyróżnione</option>
            <option value="false">Zwykłe</option>
          </select>
        </div>
      </template>
    </search-filters>
    
    <!-- Loading indicator -->
    <loading-spinner v-if="loading" />
    
    <!-- Tutorials Table -->
    <admin-table
      v-if="filteredTutorials.length"
      :columns="tableColumns"
      :items="filteredTutorials"
      :sort-by="filters.sort_field"
      :sort-order="filters.sort_direction"
      @sort="handleSort"
      :force-horizontal-scroll="true"
      class="mt-6"
    >
      <template #cell-title="{ item }">
        <div class="max-w-[340px]">
          <span class="block truncate text-sm" :title="item.title">{{ item.title }}</span>
        </div>
      </template>
      
      <template #cell-slug="{ item }">
        <div class="max-w-[210px]">
          <span class="block truncate text-sm" :title="item.slug">{{ item.slug }}</span>
        </div>
      </template>
      
      <template #cell-author="{ item }">
        <div class="max-w-[160px]">
          <span class="block truncate text-sm" :title="item.author ? item.author.name : 'Brak autora'">
            {{ item.author ? item.author.name : 'Brak autora' }}
          </span>
        </div>
      </template>
      
      <template #cell-status="{ item }">
        <admin-badge 
          :variant="getStatusVariant(item)"
          size="xs"
        >
          {{ getStatusLabel(item) }}
        </admin-badge>
      </template>
      
      <template #cell-actions="{ item }">
        <admin-button-group spacing="xs">
          <admin-button
            @click="editTutorial(item)"
            variant="primary"
            size="sm"
          >
            Edytuj
          </admin-button>
          <admin-button
            @click="confirmDelete(item)"
            variant="danger"
            size="sm"
          >
            Usuń
          </admin-button>
        </admin-button-group>
      </template>
    </admin-table>
    
    <!-- No data message -->
    <no-data-message v-else message="Brak poradników do wyświetlenia" />
    
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
          <label for="excerpt" class="block text-sm font-medium text-gray-700">Krótki opis</label>
          <textarea 
            id="excerpt" 
            v-model="form.excerpt" 
            rows="3"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
            required
          ></textarea>
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
            <label for="image_url" class="block text-sm font-medium text-gray-700">URL obrazka</label>
            <input 
              type="url" 
              id="image_url" 
              v-model="form.image_url" 
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
              required
            >
          </div>
          
          <div>
            <label for="published_at" class="block text-sm font-medium text-gray-700">Data publikacji</label>
            <input 
              type="datetime-local" 
              id="published_at" 
              v-model="form.published_at" 
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
              <option value="scheduled">Zaplanowany</option>
            </select>
          </div>
        </div>
        
        <div class="flex items-center">
          <input 
            type="checkbox" 
            id="is_featured" 
            v-model="form.is_featured" 
            class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
          >
          <label for="is_featured" class="ml-2 block text-sm text-gray-900">
            Wyróżniony
          </label>
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
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'
import { useAlertStore } from '../../stores/alertStore'
import { useAuthStore } from '../../stores/authStore'
import AdminTable from '../../components/admin/ui/AdminTable.vue'
import AdminButtonGroup from '../../components/admin/ui/AdminButtonGroup.vue'
import AdminButton from '../../components/admin/ui/AdminButton.vue'
import AdminBadge from '../../components/admin/ui/AdminBadge.vue'
import AdminModal from '../../components/admin/ui/AdminModal.vue'

export default {
  name: 'AdminTutorials',
  components: {
    AdminTable,
    AdminButtonGroup,
    AdminButton,
    AdminBadge,
    AdminModal
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
    
    // Table columns definition
    const tableColumns = [
      { key: 'title', label: 'Tytuł', sortable: true, width: '350px' },
      { key: 'slug', label: 'Slug', sortable: false, width: '220px' },
      { key: 'author', label: 'Autor', sortable: false, width: '170px' },
      { key: 'published_at', label: 'Data', sortable: true, type: 'date', width: '140px' },
      { key: 'status', label: 'Status', sortable: true, align: 'center', width: '120px' },
      { key: 'actions', label: 'Akcje', align: 'center', width: '140px' }
    ]
    
    // Handle table sorting
    const handleSort = (sortData) => {
      filters.value.sort_field = sortData.key
      filters.value.sort_direction = sortData.order
    }
    
    // Get status variant for badge
    const getStatusVariant = (tutorial) => {
      switch (tutorial.status) {
        case 'published':
          return 'green'
        case 'draft':
          return 'gray'
        case 'scheduled':
          return 'blue'
        default:
          return 'gray'
      }
    }
    
    // Filters
    const filters = ref({
      search: '',
      status: '',
      featured: '',
      sort_field: 'published_at',
      sort_direction: 'desc'
    })
    
    const form = ref({
      title: '',
      slug: '',
      excerpt: '',
      content: '',
      image_url: '',
      published_at: null,
      status: 'draft',
      meta_title: '',
      meta_description: '',
      featured: false
    })
    
    // Fetch all tutorials
    const fetchTutorials = async () => {
      try {
        loading.value = true
        const response = await axios.get('/api/admin/tutorials')
        tutorials.value = response.data
      } catch (error) {
        console.error('Error fetching tutorials:', error)
        alertStore.error('Wystąpił błąd podczas pobierania poradników.')
      } finally {
        loading.value = false
      }
    }
    
    // Filter tutorials
    const filterTutorials = () => {
      // This is just a UI function to trigger the filteredTutorials computed property
      console.log('Filtering with:', filters.value)
    }
    
    // Debounced filter tutorials
    const debouncedFilterTutorials = () => {
      if (searchTimeout.value) {
        clearTimeout(searchTimeout.value)
      }
      
      searchTimeout.value = setTimeout(() => {
        filterTutorials()
      }, 300)
    }
    
    // Sort function
    const sortTutorials = (a, b) => {
      const direction = filters.value.sort_direction === 'asc' ? 1 : -1
      
      switch (filters.value.sort_field) {
        case 'title':
          return a.title.localeCompare(b.title) * direction
        case 'published_at':
          const aDate = a.published_at ? new Date(a.published_at) : new Date(0)
          const bDate = b.published_at ? new Date(b.published_at) : new Date(0)
          return (aDate - bDate) * direction
        case 'created_at':
        default:
          return (new Date(a.created_at) - new Date(b.created_at)) * direction
      }
    }
    
    // Filtered tutorials
    const filteredTutorials = computed(() => {
      return tutorials.value
        .filter(tutorial => {
          // Filter by status
          if (filters.value.status && tutorial.status !== filters.value.status) {
            return false
          }
          
          // Filter by featured
          if (filters.value.featured === 'true' && !tutorial.featured) {
            return false
          }
          if (filters.value.featured === 'false' && tutorial.featured) {
            return false
          }
          
          // Filter by search text (title or excerpt)
          if (filters.value.search) {
            const searchText = filters.value.search.toLowerCase()
            const title = tutorial.title.toLowerCase()
            const excerpt = tutorial.excerpt ? tutorial.excerpt.toLowerCase() : ''
            
            if (!title.includes(searchText) && !excerpt.includes(searchText)) {
              return false
            }
          }
          
          return true
        })
        .sort(sortTutorials)
    })
    
    // Add new tutorial
    const addTutorial = async () => {
      try {
        const response = await axios.post('/api/admin/tutorials', form.value)
        tutorials.value.push(response.data)
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
        tutorial = tutorials.value.find(t => t.id === tutorialOrId);
        if (!tutorial) {
          console.error('Tutorial not found with ID:', tutorialOrId);
          return;
        }
      }
      
      form.value = {
        id: tutorial.id,
        title: tutorial.title,
        slug: tutorial.slug,
        excerpt: tutorial.excerpt || '',
        content: tutorial.content || '',
        image_url: tutorial.image_url || '',
        published_at: tutorial.published_at ? formatDateForInput(tutorial.published_at) : null,
        status: tutorial.status || 'draft',
        meta_title: tutorial.meta_title || '',
        meta_description: tutorial.meta_description || '',
        featured: tutorial.featured || false
      }
      showEditForm.value = true;
    }
    
    // Update tutorial
    const updateTutorial = async () => {
      try {
        const response = await axios.put(`/api/admin/tutorials/${form.value.id}`, form.value)
        const index = tutorials.value.findIndex(tutorial => tutorial.id === form.value.id)
        if (index !== -1) {
          tutorials.value[index] = response.data
        }
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
        tutorial = tutorials.value.find(t => t.id === tutorialOrId);
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
        tutorials.value = tutorials.value.filter(tutorial => tutorial.id !== tutorialToDelete.value.id)
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
        excerpt: '',
        content: '',
        image_url: '',
        published_at: null,
        status: 'draft',
        meta_title: '',
        meta_description: '',
        featured: false
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
    
    // Format date for input
    const formatDateForInput = (dateString) => {
      if (!dateString) return ''
      const date = new Date(dateString)
      return date.toISOString().slice(0, 16)
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
        case 'scheduled': 
          if (tutorial.published_at) {
            const publishDate = new Date(tutorial.published_at)
            if (publishDate > new Date()) {
              return 'Zaplanowany'
            } else {
              return 'Opublikowany'
            }
          }
          return 'Zaplanowany'
        default: return tutorial.status
      }
    }
    
    // Get status class
    const getStatusClass = (tutorial) => {
      switch (tutorial.status) {
        case 'published': return 'bg-green-100 text-green-800'
        case 'draft': return 'bg-gray-100 text-gray-800'
        case 'scheduled': 
          if (tutorial.published_at) {
            const publishDate = new Date(tutorial.published_at)
            if (publishDate > new Date()) {
              return 'bg-yellow-100 text-yellow-800'
            } else {
              return 'bg-green-100 text-green-800'
            }
          }
          return 'bg-yellow-100 text-yellow-800'
        default: return 'bg-gray-100 text-gray-800'
      }
    }
    
    onMounted(() => {
      fetchTutorials()
    })
    
    return {
      loading,
      tutorials,
      filteredTutorials,
      showAddForm,
      showEditForm,
      showDeleteModal,
      tutorialToDelete,
      filters,
      form,
      sortOptions,
      tableColumns,
      fetchTutorials,
      addTutorial,
      editTutorial,
      updateTutorial,
      confirmDelete,
      deleteTutorial,
      closeForm,
      formatDate,
      formatDateForInput,
      generateSlug,
      getStatusLabel,
      getStatusClass,
      getStatusVariant,
      handleSort,
      filterTutorials,
      debouncedFilterTutorials
    }
  }
}
</script> 