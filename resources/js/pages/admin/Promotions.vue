<template>
  <div class="p-6">
    <!-- Page Header -->
    <page-header 
      title="Zarządzanie promocjami"
      subtitle="Lista wszystkich promocji w sklepie z możliwością dodawania, edycji i usuwania."
      add-button-label="Dodaj promocję"
      @add="showAddForm = true"
    />
    
    <!-- Search and filters -->
    <search-filters
      v-if="!loading"
      :filters="filters.value"
      :sort-options="sortOptions"
      search-label="Wyszukaj"
      search-placeholder="Nazwa lub kod promocji..."
      @update:filters="(newFilters) => { Object.assign(filters.value, newFilters); filters.value.page = 1; }"
      @filter-change="fetchPromotions"
    >
      <template v-slot:filters>
        <div class="w-full sm:w-auto">
          <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
          <select
            id="status"
            name="status"
            v-model="filters.value.status"
            @change="fetchPromotions"
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
            v-model="filters.value.type"
            @change="fetchPromotions"
            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
          >
            <option value="">Wszystkie</option>
            <option value="percentage">Procentowe</option>
            <option value="fixed">Kwotowe</option>
          </select>
        </div>
      </template>
    </search-filters>
    
    <!-- Loading indicator -->
    <loading-spinner v-if="loading" />
    
    <!-- Promotions Table -->
    <admin-table
      v-if="!loading && promotions.length > 0"
      :columns="tableColumns"
      :items="promotions"
      class="mt-6"
    >
      <template #cell-code="{ item }">
        <span class="px-2 py-1 bg-gray-100 rounded font-mono">{{ item.code }}</span>
      </template>
      
      <template #cell-type="{ item }">
        {{ getPromotionTypeName(item.discount_type) }}
      </template>
      
      <template #cell-value="{ item }">
        {{ item.discount_type === 'percentage' ? `${item.discount_value}%` : `${item.discount_value} zł` }}
      </template>
      
      <template #cell-status="{ item }">
        <admin-badge 
          :variant="item.is_active ? 'green' : 'red'"
          size="xs"
        >
          {{ item.is_active ? 'Aktywna' : 'Nieaktywna' }}
        </admin-badge>
      </template>
      
      <template #cell-actions="{ item }">
        <admin-button-group spacing="xs">
          <admin-button
            @click="editPromotion(item)"
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
    <no-data-message v-if="!loading && promotions.length === 0" message="Brak promocji do wyświetlenia" />
    
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
                  v-model="form.discount_type" 
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                  required
                >
                  <option value="percentage">Procent</option>
                  <option value="fixed">Wartość</option>
                </select>
              </div>
              
              <div>
                <label for="value" class="block text-sm font-medium text-gray-700">
                  {{ form.discount_type === 'percentage' ? 'Wartość procentowa' : 'Kwota rabatu (zł)' }}
                </label>
                <input 
                  type="number" 
                  id="value" 
                  v-model.number="form.discount_value" 
                  min="0"
                  :max="form.discount_type === 'percentage' ? 100 : null"
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
                  v-model.number="form.minimum_order_value" 
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
                  v-model.number="form.usage_limit" 
                  min="0"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                >
              </div>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label for="starts_at" class="block text-sm font-medium text-gray-700">Data rozpoczęcia</label>
                <input 
                  type="datetime-local" 
                  id="starts_at" 
                  v-model="form.starts_at" 
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                  required
                >
              </div>
              
              <div>
                <label for="ends_at" class="block text-sm font-medium text-gray-700">Data zakończenia</label>
                <input 
                  type="datetime-local" 
                  id="ends_at" 
                  v-model="form.ends_at" 
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                >
              </div>
            </div>
            
            <div class="flex items-center">
              <input
                id="active" 
                type="checkbox" 
                v-model="form.is_active"
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
import AdminTable from '../../components/admin/ui/AdminTable.vue'
import AdminButtonGroup from '../../components/admin/ui/AdminButtonGroup.vue'
import AdminButton from '../../components/admin/ui/AdminButton.vue'
import AdminBadge from '../../components/admin/ui/AdminBadge.vue'
import SearchFilters from '../../components/admin/SearchFilters.vue'
import LoadingSpinner from '../../components/admin/LoadingSpinner.vue'
import NoDataMessage from '../../components/admin/NoDataMessage.vue'
import PageHeader from '../../components/admin/PageHeader.vue'

export default {
  name: 'AdminPromotions',
  components: {
    AdminTable,
    AdminButtonGroup,
    AdminButton,
    AdminBadge,
    SearchFilters,
    LoadingSpinner,
    NoDataMessage,
    PageHeader
  },
  setup() {
    const alertStore = useAlertStore()
    const loading = ref(true)
    const promotions = ref([])
    const showAddForm = ref(false)
    const showEditForm = ref(false)
    const showDeleteModal = ref(false)
    const promotionToDelete = ref({})
    const searchTimeout = ref(null)
    const form = ref({
      name: '',
      code: '',
      description: '',
      discount_type: 'percentage',
      discount_value: 0,
      minimum_order_value: 0,
      usage_limit: null,
      starts_at: new Date().toISOString().slice(0, 16),
      ends_at: '',
      is_active: true
    })
    
    // Filters
    const filters = ref({
      search: '',
      status: '',
      type: '',
      sort_field: 'ends_at',
      sort_direction: 'asc',
      page: 1
    })
    
    // Sort options for the filter component
    const sortOptions = [
      { value: 'name', label: 'Nazwa' },
      { value: 'code', label: 'Kod' },
      { value: 'ends_at', label: 'Data ważności' },
      { value: 'created_at', label: 'Data utworzenia' }
    ]
    
    // Table columns definition
    const tableColumns = [
      { key: 'name', label: 'Nazwa', width: '250px' },
      { key: 'code', label: 'Kod', width: '140px' },
      { key: 'type', label: 'Typ', width: '120px' },
      { key: 'value', label: 'Wartość', width: '120px' },
      { key: 'ends_at', label: 'Data ważności', type: 'date', width: '160px' },
      { key: 'status', label: 'Status', width: '120px' },
      { key: 'actions', label: 'Akcje', align: 'right', width: '160px' }
    ]
    

    
    // Fetch all promotions
    const fetchPromotions = async () => {
      try {
        loading.value = true
        const response = await axios.get('/api/admin/promotions', { params: filters.value })
        promotions.value = response.data
      } catch (error) {
        console.error('Error fetching promotions:', error)
        alertStore.error('Wystąpił błąd podczas pobierania promocji.')
      } finally {
        loading.value = false
      }
    }
    

    
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
        discount_type: promotion.discount_type,
        discount_value: parseFloat(promotion.discount_value),
        minimum_order_value: promotion.minimum_order_value ? parseFloat(promotion.minimum_order_value) : 0,
        usage_limit: promotion.usage_limit,
        starts_at: formatDateForInput(promotion.starts_at),
        ends_at: formatDateForInput(promotion.ends_at),
        is_active: promotion.is_active
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
        discount_type: 'percentage',
        discount_value: 0,
        minimum_order_value: 0,
        usage_limit: null,
        starts_at: new Date().toISOString().slice(0, 16),
        ends_at: '',
        is_active: true
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
    const generateCode = async () => {
      try {
        const response = await axios.get('/api/admin/promotions/generate-code')
        form.value.code = response.data.code
      } catch (error) {
        console.error('Error generating code:', error)
        alertStore.error('Wystąpił błąd podczas generowania kodu promocji.')
      }
    }
    
    onMounted(() => {
      fetchPromotions()
    })
    
    return {
      loading,
      promotions,
      showAddForm,
      showEditForm,
      showDeleteModal,
      promotionToDelete,
      filters,
      form,
      sortOptions,
      tableColumns,
      fetchPromotions,
      addPromotion,
      editPromotion,
      updatePromotion,
      confirmDelete,
      deletePromotion,
      closeForm,
      getPromotionTypeName,
      formatDate,
      generateCode
    }
  }
}
</script> 