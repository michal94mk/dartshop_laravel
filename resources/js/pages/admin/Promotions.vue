<template>
  <div class="p-6">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-semibold text-gray-900">Zarządzanie promocjami</h1>
      <button 
        @click="showAddForm = true" 
        class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
      >
        Dodaj promocję
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
              @input="debouncedFilterPromotions"
              class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
              placeholder="Nazwa lub kod promocji..."
            />
          </div>
        </div>
        
        <div class="w-full sm:w-auto">
          <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
          <select
            id="status"
            name="status"
            v-model="filters.status"
            @change="filterPromotions"
            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
          >
            <option value="">Wszystkie</option>
            <option value="active">Aktywne</option>
            <option value="inactive">Nieaktywne</option>
          </select>
        </div>
        
        <div class="w-full sm:w-auto">
          <label for="type" class="block text-sm font-medium text-gray-700">Typ</label>
          <select
            id="type"
            name="type"
            v-model="filters.type"
            @change="filterPromotions"
            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
          >
            <option value="">Wszystkie</option>
            <option value="percentage">Procentowe</option>
            <option value="fixed">Kwotowe</option>
          </select>
        </div>
        
        <div class="w-full sm:w-auto">
          <label for="sort" class="block text-sm font-medium text-gray-700">Sortuj</label>
          <select
            id="sort"
            name="sort"
            v-model="filters.sort_field"
            @change="filterPromotions"
            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
          >
            <option value="created_at">Data dodania</option>
            <option value="expires_at">Data ważności</option>
            <option value="name">Nazwa</option>
            <option value="value">Wartość</option>
          </select>
        </div>
        
        <div class="w-full sm:w-auto">
          <label for="direction" class="block text-sm font-medium text-gray-700">Kierunek</label>
          <select
            id="direction"
            name="direction"
            v-model="filters.sort_direction"
            @change="filterPromotions"
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
    
    <!-- Promotions Table -->
    <div v-else-if="filteredPromotions.length" class="bg-white shadow overflow-hidden sm:rounded-md mt-6">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nazwa</th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kod</th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Typ</th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Wartość</th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data ważności</th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Akcje</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="promotion in filteredPromotions" :key="promotion.id">
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ promotion.id }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ promotion.name }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
              <span class="px-2 py-1 bg-gray-100 rounded font-mono">{{ promotion.code }}</span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ getPromotionTypeName(promotion.type) }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
              {{ promotion.type === 'percentage' ? `${promotion.value}%` : `${promotion.value} zł` }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ formatDate(promotion.expires_at) }}</td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                   :class="promotion.active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                {{ promotion.active ? 'Aktywna' : 'Nieaktywna' }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
              <button 
                @click="editPromotion(promotion)" 
                class="text-indigo-600 hover:text-indigo-900 mr-4"
              >
                Edytuj
              </button>
              <button 
                @click="confirmDelete(promotion)" 
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
      Brak promocji do wyświetlenia
    </div>
    
    <!-- Add/Edit Modal -->
    <div v-if="showAddForm || showEditForm" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full flex items-center justify-center">
      <div class="relative mx-auto p-5 border w-full max-w-xl shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
          <h3 class="text-lg font-medium text-gray-900">{{ showEditForm ? 'Edytuj promocję' : 'Dodaj nową promocję' }}</h3>
          <button @click="closeForm" class="text-gray-400 hover:text-gray-500">
            <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
        
        <form @submit.prevent="showEditForm ? updatePromotion() : addPromotion()">
          <div class="grid grid-cols-1 gap-4 mb-4">
            <div>
              <label for="name" class="block text-sm font-medium text-gray-700">Nazwa</label>
              <input 
                type="text" 
                id="name" 
                v-model="form.name" 
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                required
              >
            </div>
            
            <div>
              <label for="code" class="block text-sm font-medium text-gray-700">Kod promocyjny</label>
              <div class="flex mt-1">
                <input 
                  type="text" 
                  id="code" 
                  v-model="form.code" 
                  class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                  required
                >
                <button 
                  type="button"
                  @click="generateCode"
                  class="ml-2 inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                >
                  Generuj
                </button>
              </div>
            </div>
            
            <div>
              <label for="description" class="block text-sm font-medium text-gray-700">Opis</label>
              <textarea 
                id="description" 
                v-model="form.description" 
                rows="3"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
              ></textarea>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label for="type" class="block text-sm font-medium text-gray-700">Typ promocji</label>
                <select 
                  id="type" 
                  v-model="form.type" 
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                  required
                >
                  <option value="percentage">Procent</option>
                  <option value="fixed">Wartość</option>
                </select>
              </div>
              
              <div>
                <label for="value" class="block text-sm font-medium text-gray-700">
                  {{ form.type === 'percentage' ? 'Wartość procentowa' : 'Kwota rabatu (zł)' }}
                </label>
                <input 
                  type="number" 
                  id="value" 
                  v-model.number="form.value" 
                  min="0"
                  :max="form.type === 'percentage' ? 100 : null"
                  step="0.01"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                  required
                >
              </div>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label for="min_order_value" class="block text-sm font-medium text-gray-700">Min. wartość zamówienia</label>
                <input 
                  type="number" 
                  id="min_order_value" 
                  v-model.number="form.min_order_value" 
                  min="0"
                  step="0.01"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                >
              </div>
              
              <div>
                <label for="max_uses" class="block text-sm font-medium text-gray-700">Maks. liczba użyć</label>
                <input 
                  type="number" 
                  id="max_uses" 
                  v-model.number="form.max_uses" 
                  min="0"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                >
              </div>
            </div>
            
            <div>
              <label for="expires_at" class="block text-sm font-medium text-gray-700">Data ważności</label>
              <input 
                type="datetime-local" 
                id="expires_at" 
                v-model="form.expires_at" 
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                required
              >
            </div>
            
            <div class="flex items-center">
              <input
                id="active" 
                type="checkbox" 
                v-model="form.active"
                class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
              >
              <label for="active" class="ml-2 block text-sm text-gray-900">Promocja aktywna</label>
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
              {{ showEditForm ? 'Zapisz zmiany' : 'Dodaj promocję' }}
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
              Czy na pewno chcesz usunąć promocję "{{ promotionToDelete.name }}"?
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
              @click="deletePromotion" 
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
import { ref, computed, onMounted, reactive } from 'vue'
import axios from 'axios'
import { useAlertStore } from '../../stores/alertStore'

export default {
  name: 'AdminPromotions',
  setup() {
    const alertStore = useAlertStore()
    const loading = ref(true)
    const promotions = ref([])
    const showAddForm = ref(false)
    const showEditForm = ref(false)
    const showDeleteModal = ref(false)
    const promotionToDelete = ref({})
    const searchTimeout = ref(null)
    
    // Filters
    const filters = ref({
      search: '',
      status: '',
      type: '',
      sort_field: 'expires_at',
      sort_direction: 'asc'
    })
    
    // Fetch all promotions
    const fetchPromotions = async () => {
      try {
        loading.value = true
        const response = await axios.get('/api/admin/promotions')
        promotions.value = response.data
      } catch (error) {
        console.error('Error fetching promotions:', error)
        alertStore.error('Wystąpił błąd podczas pobierania promocji.')
      } finally {
        loading.value = false
      }
    }
    
    // Filter promotions
    const filterPromotions = () => {
      // This is just a UI function to trigger the filteredPromotions computed property
      console.log('Filtering with:', filters.value)
    }
    
    // Debounced filter promotions
    const debouncedFilterPromotions = () => {
      if (searchTimeout.value) {
        clearTimeout(searchTimeout.value)
      }
      
      searchTimeout.value = setTimeout(() => {
        filterPromotions()
      }, 300)
    }
    
    // Sort function
    const sortPromotions = (a, b) => {
      const direction = filters.value.sort_direction === 'asc' ? 1 : -1
      
      switch (filters.value.sort_field) {
        case 'name':
          return a.name.localeCompare(b.name) * direction
        case 'value':
          return (a.value - b.value) * direction
        case 'expires_at':
          return (new Date(a.expires_at) - new Date(b.expires_at)) * direction
        case 'created_at':
        default:
          return (new Date(a.created_at) - new Date(b.created_at)) * direction
      }
    }
    
    // Filtered promotions
    const filteredPromotions = computed(() => {
      return promotions.value
        .filter(promotion => {
          // Filter by status
          if (filters.value.status === 'active' && !promotion.active) {
            return false
          }
          if (filters.value.status === 'inactive' && promotion.active) {
            return false
          }
          
          // Filter by type
          if (filters.value.type && promotion.type !== filters.value.type) {
            return false
          }
          
          // Filter by search text (name or code)
          if (filters.value.search) {
            const searchText = filters.value.search.toLowerCase()
            const name = promotion.name.toLowerCase()
            const code = promotion.code.toLowerCase()
            
            if (!name.includes(searchText) && !code.includes(searchText)) {
              return false
            }
          }
          
          return true
        })
        .sort(sortPromotions)
    })
    
    // Add new promotion
    const addPromotion = async () => {
      try {
        const response = await axios.post('/api/admin/promotions', form.value)
        promotions.value.push(response.data)
        alertStore.success('Promocja została dodana.')
        closeForm()
      } catch (error) {
        console.error('Error adding promotion:', error)
        alertStore.error('Wystąpił błąd podczas dodawania promocji.')
      }
    }
    
    // Edit promotion
    const editPromotion = (promotion) => {
      form.value = {
        id: promotion.id,
        name: promotion.name,
        code: promotion.code,
        description: promotion.description || '',
        type: promotion.type,
        value: parseFloat(promotion.value),
        min_order_value: promotion.min_order_value ? parseFloat(promotion.min_order_value) : 0,
        max_uses: promotion.max_uses,
        expires_at: formatDateForInput(promotion.expires_at),
        active: promotion.active
      }
      showEditForm.value = true
    }
    
    // Update promotion
    const updatePromotion = async () => {
      try {
        const response = await axios.put(`/api/admin/promotions/${form.value.id}`, form.value)
        const index = promotions.value.findIndex(promotion => promotion.id === form.value.id)
        if (index !== -1) {
          promotions.value[index] = response.data
        }
        alertStore.success('Promocja została zaktualizowana.')
        closeForm()
      } catch (error) {
        console.error('Error updating promotion:', error)
        alertStore.error('Wystąpił błąd podczas aktualizacji promocji.')
      }
    }
    
    // Confirm delete
    const confirmDelete = (promotion) => {
      promotionToDelete.value = promotion
      showDeleteModal.value = true
    }
    
    // Delete promotion
    const deletePromotion = async () => {
      try {
        await axios.delete(`/api/admin/promotions/${promotionToDelete.value.id}`)
        promotions.value = promotions.value.filter(promotion => promotion.id !== promotionToDelete.value.id)
        alertStore.success('Promocja została usunięta.')
        showDeleteModal.value = false
      } catch (error) {
        console.error('Error deleting promotion:', error)
        alertStore.error('Wystąpił błąd podczas usuwania promocji.')
      }
    }
    
    // Close form
    const closeForm = () => {
      form.value = {
        name: '',
        code: '',
        description: '',
        type: 'percentage',
        value: 0,
        min_order_value: 0,
        max_uses: null,
        expires_at: '',
        active: true
      }
      showAddForm.value = false
      showEditForm.value = false
    }
    
    // Format date
    const formatDate = (dateString) => {
      if (!dateString) return 'Brak'
      const options = { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' }
      return new Date(dateString).toLocaleDateString('pl-PL', options)
    }
    
    // Format date for input
    const formatDateForInput = (dateString) => {
      if (!dateString) return ''
      const date = new Date(dateString)
      return date.toISOString().slice(0, 16)
    }
    
    // Get promotion type display name
    const getPromotionTypeName = (type) => {
      switch (type) {
        case 'percentage': return 'Procentowa'
        case 'fixed': return 'Kwotowa'
        default: return type
      }
    }
    
    // Generate random code
    const generateCode = () => {
      const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'
      let result = ''
      const length = 8
      for (let i = 0; i < length; i++) {
        result += characters.charAt(Math.floor(Math.random() * characters.length))
      }
      form.value.code = result
    }
    
    onMounted(() => {
      fetchPromotions()
    })
    
    return {
      loading,
      promotions,
      filteredPromotions,
      showAddForm,
      showEditForm,
      showDeleteModal,
      promotionToDelete,
      filters,
      form: ref({}),
      fetchPromotions,
      addPromotion,
      editPromotion,
      updatePromotion,
      confirmDelete,
      deletePromotion,
      closeForm,
      getPromotionTypeName,
      formatDate,
      generateCode,
      filterPromotions,
      debouncedFilterPromotions
    }
  }
}
</script> 