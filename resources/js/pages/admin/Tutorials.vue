<template>
  <div class="p-6">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-semibold text-gray-900">Zarządzanie poradnikami</h1>
      <button 
        @click="showAddForm = true" 
        class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
      >
        Dodaj poradnik
      </button>
    </div>
    
    <!-- Search and filters -->
    <div v-if="!loading" class="mt-6 bg-white shadow px-4 py-5 sm:rounded-lg sm:px-6">
      <div class="flex flex-wrap gap-4">
        <div class="flex-1 min-w-[200px]">
          <label for="search" class="block text-sm font-medium text-gray-700">Wyszukaj</label>
          <div class="mt-1">
            <input
              type="text"
              name="search"
              id="search"
              v-model="filters.search"
              @input="debouncedFilterTutorials"
              class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
              placeholder="Szukaj poradników..."
            />
          </div>
        </div>
        
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
        
        <div class="w-full sm:w-auto">
          <label for="sort" class="block text-sm font-medium text-gray-700">Sortuj</label>
          <select
            id="sort"
            name="sort"
            v-model="filters.sort_field"
            @change="filterTutorials"
            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
          >
            <option value="created_at">Data dodania</option>
            <option value="published_at">Data publikacji</option>
            <option value="title">Tytuł</option>
          </select>
        </div>
        
        <div class="w-full sm:w-auto">
          <label for="direction" class="block text-sm font-medium text-gray-700">Kierunek</label>
          <select
            id="direction"
            name="direction"
            v-model="filters.sort_direction"
            @change="filterTutorials"
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
    
    <!-- Tutorials Table -->
    <div v-else-if="filteredTutorials.length" class="bg-white shadow overflow-hidden sm:rounded-md mt-6">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tytuł</th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Slug</th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Autor</th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data publikacji</th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Akcje</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="tutorial in filteredTutorials" :key="tutorial.id">
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ tutorial.id }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ tutorial.title }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
              {{ tutorial.slug }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ tutorial.author ? tutorial.author.name : 'Nieznany' }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ formatDate(tutorial.published_at) }}</td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                   :class="getStatusClass(tutorial)">
                {{ getStatusLabel(tutorial) }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
              <button 
                @click="editTutorial(tutorial)" 
                class="text-indigo-600 hover:text-indigo-900 mr-4"
              >
                Edytuj
              </button>
              <button 
                @click="confirmDelete(tutorial)" 
                class="text-red-600 hover:text-red-900"
              >
                Usuń
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <div v-else class="bg-white shadow overflow-hidden sm:rounded-md p-6 text-center text-gray-500 mt-6">
      Brak poradników do wyświetlenia
    </div>
    
    <!-- Add/Edit Modal -->
    <div v-if="showAddForm || showEditForm" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full flex items-center justify-center">
      <div class="relative mx-auto p-5 border w-full max-w-3xl shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
          <h3 class="text-lg font-medium text-gray-900">{{ showEditForm ? 'Edytuj poradnik' : 'Dodaj nowy poradnik' }}</h3>
          <button @click="closeForm" class="text-gray-400 hover:text-gray-500">
            <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
        
        <form @submit.prevent="showEditForm ? updateTutorial() : addTutorial()" class="overflow-y-auto max-h-screen">
          <div class="grid grid-cols-1 gap-4 mb-4">
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
            
            <div>
              <label for="excerpt" class="block text-sm font-medium text-gray-700">Krótki opis (fragment)</label>
              <textarea 
                id="excerpt" 
                v-model="form.excerpt" 
                rows="2"
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
            
            <div class="grid grid-cols-2 gap-4">
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
            
            <div>
              <label for="meta_title" class="block text-sm font-medium text-gray-700">Meta tytuł (SEO)</label>
              <input 
                type="text" 
                id="meta_title" 
                v-model="form.meta_title" 
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
              >
            </div>
            
            <div>
              <label for="meta_description" class="block text-sm font-medium text-gray-700">Meta opis (SEO)</label>
              <textarea 
                id="meta_description" 
                v-model="form.meta_description" 
                rows="2"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
              ></textarea>
            </div>
            
            <div class="flex items-center">
              <input
                id="featured" 
                type="checkbox" 
                v-model="form.featured"
                class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
              >
              <label for="featured" class="ml-2 block text-sm text-gray-900">Wyróżniony poradnik</label>
            </div>
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
              {{ showEditForm ? 'Zapisz zmiany' : 'Dodaj poradnik' }}
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
              Czy na pewno chcesz usunąć poradnik "{{ tutorialToDelete.title }}"?
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
              @click="deleteTutorial" 
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
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'
import { useAlertStore } from '../../stores/alertStore'
import { useAuthStore } from '../../stores/authStore'

export default {
  name: 'AdminTutorials',
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
        const tutorialData = { ...form.value, author_id: authStore.user.id }
        const response = await axios.post('/api/admin/tutorials', tutorialData)
        tutorials.value.push(response.data)
        alertStore.success('Poradnik został dodany.')
        closeForm()
      } catch (error) {
        console.error('Error adding tutorial:', error)
        alertStore.error('Wystąpił błąd podczas dodawania poradnika.')
      }
    }
    
    // Edit tutorial
    const editTutorial = (tutorial) => {
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
      showEditForm.value = true
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
    const confirmDelete = (tutorial) => {
      tutorialToDelete.value = tutorial
      showDeleteModal.value = true
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
      filterTutorials,
      debouncedFilterTutorials
    }
  }
}
</script> 