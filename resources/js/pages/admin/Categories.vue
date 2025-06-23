<template>
  <admin-tabs-layout
    title="Zarządzanie kategoriami"
    subtitle="Lista wszystkich kategorii produktów z możliwością dodawania, edycji i usuwania"
    :tabs="tabs"
    v-model="activeTab"
    @tab-change="handleTabChange"
  >
    <!-- Header slot -->
    <template #header>
      <admin-button
        variant="primary"
        @click="openModal()"
      >
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
        </svg>
        Dodaj kategorię
      </admin-button>
    </template>

    <!-- Toolbar slot -->
    <template #toolbar>
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
      >
        <template v-slot:filters>
          <!-- No additional filters needed -->
        </template>
      </search-filters>
    </template>

    <!-- Main tab content -->
    <template #default="{ activeTab }">
      <!-- Categories list -->
      <admin-tab-panel
        tab-id="list"
        :active-tab="activeTab"
        title="Lista kategorii"
        description="Zarządzaj wszystkimi kategoriami produktów"
      >
        <template #header>
          <admin-button variant="secondary" outline>
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            Eksportuj kategorie
          </admin-button>
        </template>

        <!-- Loading indicator -->
        <loading-spinner v-if="loading" />
        
        <!-- Categories table -->
        <admin-table
          v-if="categories.data && categories.data.length > 0"
          :columns="tableColumns"
          :items="categories.data"
        >
          <template #cell-image="{ item }">
            <div class="flex justify-center">
              <img 
                v-if="item.image_url"
                :src="item.image_url" 
                :alt="item.name" 
                class="h-12 w-12 object-cover rounded-lg shadow-sm"
              />
              <div v-else class="h-12 w-12 bg-gray-200 rounded-lg flex items-center justify-center">
                <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
              </div>
            </div>
          </template>
          
          <template #cell-products_count="{ item }">
            <admin-badge variant="secondary">
              {{ item.products_count || 0 }}
            </admin-badge>
          </template>

          <template #cell-is_active="{ item }">
            <admin-badge :variant="item.is_active ? 'success' : 'danger'">
              {{ item.is_active ? 'Aktywna' : 'Nieaktywna' }}
            </admin-badge>
          </template>
          
          <template #cell-actions="{ item }">
            <action-buttons 
              :item="item" 
              @edit="openModal" 
              @delete="confirmDelete"
            />
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
        <no-data-message 
          v-if="!loading && (!categories.data || categories.data.length === 0)" 
          message="Brak kategorii do wyświetlenia" 
        />
      </admin-tab-panel>

      <!-- Category settings -->
      <admin-tab-panel
        tab-id="settings"
        :active-tab="activeTab"
        title="Ustawienia kategorii"
        description="Konfiguracja wyświetlania i zarządzania kategoriami"
      >
        <div class="space-y-6">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-4">
              <h4 class="text-lg font-medium text-gray-900">Wyświetlanie</h4>
              
              <div>
                <label class="flex items-center">
                  <input type="checkbox" v-model="settings.showInactiveCategories" class="rounded border-gray-300">
                  <span class="ml-2 text-sm text-gray-700">Pokazuj nieaktywne kategorie</span>
                </label>
              </div>

              <div>
                <label class="flex items-center">
                  <input type="checkbox" v-model="settings.showProductCount" class="rounded border-gray-300">
                  <span class="ml-2 text-sm text-gray-700">Pokazuj liczbę produktów</span>
                </label>
              </div>

              <div>
                <label class="flex items-center">
                  <input type="checkbox" v-model="settings.showCategoryImages" class="rounded border-gray-300">
                  <span class="ml-2 text-sm text-gray-700">Wyświetlaj obrazki kategorii</span>
                </label>
              </div>
            </div>

            <div class="space-y-4">
              <h4 class="text-lg font-medium text-gray-900">Zarządzanie</h4>
              
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Domyślny status nowej kategorii
                </label>
                <select v-model="settings.defaultStatus" class="block w-full rounded-md border-gray-300">
                  <option value="active">Aktywna</option>
                  <option value="inactive">Nieaktywna</option>
                </select>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Sortowanie kategorii
                </label>
                <select v-model="settings.defaultSortOrder" class="block w-full rounded-md border-gray-300">
                  <option value="sort_order">Kolejność ręczna</option>
                  <option value="name">Alfabetycznie</option>
                  <option value="created_at">Data utworzenia</option>
                  <option value="products_count">Liczba produktów</option>
                </select>
              </div>
            </div>
          </div>

          <!-- Import/Export categories -->
          <div class="bg-gray-50 rounded-lg p-6">
            <h4 class="text-sm font-medium text-gray-900 mb-4">Import/Export</h4>
            <div class="flex space-x-3">
              <admin-button variant="secondary" size="sm" outline>
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                </svg>
                Import kategorii
              </admin-button>
              <admin-button variant="secondary" size="sm" outline>
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                </svg>
                Export kategorii
              </admin-button>
            </div>
          </div>
        </div>

        <template #footer>
          <admin-button variant="secondary" outline>
            Przywróć domyślne
          </admin-button>
          <admin-button variant="primary" @click="saveSettings">
            Zapisz ustawienia
          </admin-button>
        </template>
      </admin-tab-panel>
    </template>
  </admin-tabs-layout>

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
          class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
          placeholder="Np. Lotki, Tarcze, Akcesoria"
        />
      </div>

      <div>
        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Opis kategorii</label>
        <textarea
          id="description"
          v-model="currentCategory.description"
          rows="3"
          class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
          placeholder="Krótki opis kategorii, który będzie wyświetlany na stronie głównej"
        ></textarea>
      </div>

      <div>
        <label for="image" class="block text-sm font-medium text-gray-700 mb-2">Obrazek kategorii</label>
        <div class="mt-1 flex items-center">
          <!-- Show existing image from URL -->
          <span v-if="currentCategory.image_url && !isFileObject(currentCategory.image) && !imagePreview" class="inline-block h-12 w-12 rounded-md overflow-hidden bg-gray-100">
            <img
              :src="currentCategory.image_url"
              :alt="currentCategory.name || 'Category image'"
              class="h-full w-full object-cover"
              @error="$event.target.style.display = 'none'"
            />
          </span>
          <!-- Show uploaded file preview -->
          <span v-else-if="imagePreview" class="inline-block h-12 w-12 rounded-md overflow-hidden bg-gray-100">
            <img
              :src="imagePreview"
              :alt="currentCategory.name || 'Category image'"
              class="h-full w-full object-cover"
            />
          </span>
          <!-- Placeholder icon -->
          <span v-else class="inline-block h-12 w-12 rounded-md overflow-hidden bg-gray-100">
            <svg class="h-full w-full text-gray-300" fill="currentColor" viewBox="0 0 24 24">
              <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
          </span>
          <input
            type="file"
            id="category-file-upload"
            class="hidden"
            accept="image/*"
            @change="handleImageUpload"
          />
          <admin-button
            type="button"
            @click="triggerFileUpload"
            variant="secondary"
            outline
            size="sm"
          >
            {{ currentCategory.image_url || imagePreview ? 'Zmień zdjęcie' : 'Dodaj zdjęcie' }}
          </admin-button>
          <admin-button
            v-if="currentCategory.image_url || imagePreview"
            type="button"
            @click="removeImage"
            variant="danger"
            outline
            size="sm"
            class="ml-2"
          >
            Usuń
          </admin-button>
        </div>
        <p class="mt-1 text-sm text-gray-500">PNG, JPG, GIF do 2MB</p>
      </div>

      <div>
        <label for="sort_order" class="block text-sm font-medium text-gray-700 mb-2">Kolejność sortowania</label>
        <input
          type="number"
          id="sort_order"
          v-model.number="currentCategory.sort_order"
          min="0"
          class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
          placeholder="0"
        />
        <p class="mt-1 text-sm text-gray-500">Niższe wartości będą wyświetlane jako pierwsze</p>
      </div>

      <div class="flex items-center">
        <input
          id="is_active"
          type="checkbox"
          v-model="currentCategory.is_active"
          class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
        />
        <label for="is_active" class="ml-2 block text-sm text-gray-900">
          Kategoria aktywna
        </label>
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
import ActionButtons from '../../components/admin/ActionButtons.vue'
import AdminTabsLayout from '../../components/admin/AdminTabsLayout.vue'
import AdminTabPanel from '../../components/admin/AdminTabPanel.vue'
import AdminBadge from '../../components/admin/ui/AdminBadge.vue'

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
    PageHeader,
    ActionButtons,
    AdminTabsLayout,
    AdminTabPanel,
    AdminBadge
  },
  setup() {
    const alertStore = useAlertStore()
    
    // Tabs configuration
    const activeTab = ref('list')
    const tabs = [
      {
        id: 'list',
        label: 'Lista kategorii',
        iconPath: 'M4 6h16M4 10h16M4 14h16M4 18h16',
        badge: {
          value: computed(() => categories.value.total || 0),
          variant: 'primary'
        }
      },
      {
        id: 'settings',
        label: 'Ustawienia',
        iconPath: 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z'
      }
    ]

    // Settings configuration
    const settings = reactive({
      showInactiveCategories: true,
      showProductCount: true,
      showCategoryImages: true,
      defaultStatus: 'active',
      defaultSortOrder: 'sort_order'
    })

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
      { value: 'id', label: 'ID' },
      { value: 'name', label: 'Nazwa' },
      { value: 'created_at', label: 'Data utworzenia' },
      { value: 'products_count', label: 'Liczba produktów' }
    ]
    
    // Table columns definition
    const tableColumns = [
      { key: 'name', label: 'Nazwa', width: '300px' },
      { key: 'image', label: 'Obrazek', width: '100px' },
      { key: 'products_count', label: 'Liczba produktów', width: '150px' },
      { key: 'is_active', label: 'Status', width: '120px' },
      { key: 'created_at', label: 'Data utworzenia', type: 'date', width: '150px' },
      { key: 'actions', label: 'Akcje', align: 'right', width: '160px' }
    ]
    
    // Default filters
    const defaultFilters = {
      search: '',
      sort_field: 'created_at',
      sort_direction: 'desc',
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
      name: '',
      description: '',
      image_url: null,
      sort_order: 0,
      is_active: true
    })
    
    // Detailed Error Modal
    const showErrorModal = ref(false)
    const errorMessage = ref('')
    
    // Image upload handling
    const imagePreview = ref(null)
    const selectedImageFile = ref(null)
    
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
          name: category.name,
          description: category.description,
          image_url: category.image_url,
          sort_order: category.sort_order,
          is_active: category.is_active
        }
      } else {
        currentCategory.value = {
          id: null,
          name: '',
          description: '',
          image_url: null,
          sort_order: 0,
          is_active: true
        }
      }
      // Reset image upload state
      imagePreview.value = null
      selectedImageFile.value = null
      showModal.value = true
    }
    
    const saveCategory = async () => {
      try {
        submitting.value = true
        const formData = new FormData()
        formData.append('name', currentCategory.value.name)
        formData.append('description', currentCategory.value.description || '')
        formData.append('sort_order', currentCategory.value.sort_order || 0)
        formData.append('is_active', currentCategory.value.is_active ? 1 : 0)
        
        // Add image if selected
        if (selectedImageFile.value) {
          formData.append('image', selectedImageFile.value)
        }
        
        if (currentCategory.value.id) {
          // Update existing category - use POST with _method for file upload
          formData.append('_method', 'PUT')
          await axios.post(`/api/admin/categories/${currentCategory.value.id}`, formData, {
            headers: {
              'Content-Type': 'multipart/form-data'
            }
          })
          alertStore.success('Kategoria została zaktualizowana.')
        } else {
          // Create new category
          await axios.post('/api/admin/categories', formData, {
            headers: {
              'Content-Type': 'multipart/form-data'
            }
          })
          alertStore.success('Kategoria została dodana.')
        }
        
        showModal.value = false
        // Reset form and image upload
        imagePreview.value = null
        selectedImageFile.value = null
        fetchCategories()
      } catch (error) {
        console.error('Error saving category:', error)
        if (error.response && error.response.data && error.response.data.errors) {
          const errors = error.response.data.errors
          const errorMessages = Object.values(errors).flat().join('\n')
          alertStore.error('Błędy walidacji:\n' + errorMessages)
        } else {
          alertStore.error('Wystąpił błąd podczas zapisywania kategorii.')
        }
      } finally {
        submitting.value = false
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
    
    const resetFilters = () => {
      Object.assign(filters, defaultFilters)
      fetchCategories()
    }
    
    const handleImageUpload = (event) => {
      const file = event.target.files[0]
      if (file) {
        // Validate file size (2MB max)
        if (file.size > 2048 * 1024) {
          alertStore.error('Plik jest za duży. Maksymalny rozmiar to 2MB.')
          return
        }
        
        // Validate file type
        const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp']
        if (!allowedTypes.includes(file.type)) {
          alertStore.error('Nieprawidłowy typ pliku. Dozwolone formaty: JPEG, PNG, JPG, GIF, WEBP.')
          return
        }
        
        selectedImageFile.value = file
        
        // Create preview
        const reader = new FileReader()
        reader.onload = (e) => {
          imagePreview.value = e.target.result
        }
        reader.readAsDataURL(file)
      }
    }
    
    const triggerFileUpload = () => {
      document.getElementById('category-file-upload').click()
    }
    
    const removeImage = () => {
      imagePreview.value = null
      selectedImageFile.value = null
      
      // Reset the file input safely  
      const fileInputElement = document.getElementById('category-file-upload')
      if (fileInputElement) {
        try {
          fileInputElement.value = ''
        } catch (e) {
          console.error('Error resetting file input:', e)
        }
      }
    }
    
    const isFileObject = (obj) => {
      return obj && 
        typeof obj === 'object' && 
        typeof obj.name === 'string' && 
        typeof obj.size === 'number' && 
        typeof obj.type === 'string'
    }

    // Tab and settings methods
    const handleTabChange = (newTab, oldTab) => {
      console.log(`Zmiana zakładki z ${oldTab} na ${newTab}`)
      activeTab.value = newTab
    }

    const saveSettings = async () => {
      try {
        submitting.value = true
        
        // Tutaj można dodać logikę zapisywania ustawień do backendu
        // await axios.put('/api/admin/categories/settings', settings)
        
        // Na razie tylko symulujemy zapis
        await new Promise(resolve => setTimeout(resolve, 1000))
        
        alertStore.success('Ustawienia zostały zapisane.')
      } catch (error) {
        console.error('Error saving settings:', error)
        alertStore.error('Wystąpił błąd podczas zapisywania ustawień.')
      } finally {
        submitting.value = false
      }
    }
    
    // Lifecycle
    onMounted(() => {
      fetchCategories()
    })
    
    return {
      loading,
      categories,
      filters,
      defaultFilters,
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
      resetFilters,
      sortOptions,
      tableColumns,
      showErrorModal,
      errorMessage,
      imagePreview,
      selectedImageFile,
      handleImageUpload,
      triggerFileUpload,
      removeImage,
      isFileObject,
      activeTab,
      tabs,
      settings,
      handleTabChange,
      saveSettings
    }
  }
}
</script> 