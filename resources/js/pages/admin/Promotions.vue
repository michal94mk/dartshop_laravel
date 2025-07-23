<template>
  <div class="space-y-6 bg-white min-h-full pr-6">
    <!-- Page Header -->
    <div class="px-6 py-4">
      <page-header 
        title="Promocje produktowe"
        add-button-label="Dodaj"
        @add="openCreateModal"
      />
    </div>

    <!-- Filters -->
    <search-filters
      v-if="!loading"
      :filters="filters"
      :sort-options="sortOptions"
      :default-filters="defaultFilters"
      search-label="Wyszukaj"
      search-placeholder="Nazwa, tytuł promocji..."
      @update:filters="(newFilters) => { Object.assign(filters, newFilters); filters.page = 1; }"
      @filter-change="fetchPromotions"
      @reset-filters="resetFilters"
    >
      <template v-slot:filters>
        <div class="w-full sm:w-auto">
          <label class="block text-sm font-medium text-gray-700">Status</label>
          <select
            v-model="filters.is_active"
            @change="fetchPromotions"
            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
          >
            <option value="">Wszystkie</option>
            <option value="true">Aktywne</option>
            <option value="false">Nieaktywne</option>
          </select>
        </div>
        <div class="w-full sm:w-auto">
          <label class="block text-sm font-medium text-gray-700">Typ rabatu</label>
          <select
            v-model="filters.discount_type"
            @change="fetchPromotions"
            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
          >
            <option value="">Wszystkie</option>
            <option value="percentage">Procentowy</option>
            <option value="fixed">Kwotowy</option>
          </select>
        </div>
        <div class="w-full sm:w-auto">
          <label class="block text-sm font-medium text-gray-700">Wyróżnione</label>
          <select
            v-model="filters.is_featured"
            @change="fetchPromotions"
            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
          >
            <option value="">Wszystkie</option>
            <option value="true">Wyróżnione</option>
            <option value="false">Zwykłe</option>
          </select>
        </div>
      </template>
    </search-filters>

    <!-- Content -->
    <div class="bg-white shadow rounded-lg">
      <loading-spinner v-if="loading" />
      <div v-if="!loading && promotions.length > 0" class="mt-6 bg-white shadow-sm rounded-lg overflow-hidden">
        <div class="overflow-x-auto -mx-6 px-6" style="scrollbar-width: thin; scrollbar-color: #d1d5db #f3f4f6;">
          <table class="min-w-full divide-y divide-gray-200 whitespace-nowrap">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-56">Tytuł</th>
                <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-24">Typ rabatu</th>
                <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-24">Wartość</th>
                <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-24">Status</th>
                <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-32">Produkty</th>
                <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-32">Daty</th>
                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider w-36">Akcje</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="promotion in promotions" :key="promotion.id" class="hover:bg-gray-50">
                <!-- Title -->
                <td class="px-4 py-4">
                  <div class="flex items-center">
                    <div>
                      <div class="text-sm font-medium text-gray-900">{{ promotion.title }}</div>
                      <div class="text-xs text-gray-500 truncate max-w-[180px]" :title="promotion.description">{{ promotion.description }}</div>
                      <span v-if="promotion.is_featured" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 ml-1">★ Wyróżniona</span>
                    </div>
                  </div>
                </td>
                <!-- Discount type -->
                <td class="px-3 py-4 text-center">
                  <span class="text-xs font-medium text-gray-700">{{ promotion.discount_type === 'percentage' ? 'Procentowy' : 'Kwotowy' }}</span>
                </td>
                <!-- Discount value -->
                <td class="px-3 py-4 text-center">
                  <span class="text-sm font-medium text-gray-900">{{ promotion.discount_value }}{{ promotion.discount_type === 'percentage' ? '%' : ' zł' }}</span>
                </td>
                <!-- Status -->
                <td class="px-3 py-4 text-center">
                  <span :class="promotion.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">{{ promotion.is_active ? 'Aktywna' : 'Nieaktywna' }}</span>
                </td>
                <!-- Products -->
                <td class="px-3 py-4 text-center">
                  <div class="flex flex-wrap gap-1 justify-center">
                    <span v-for="product in promotion.products.slice(0, 2)" :key="product.id" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">{{ product.name }}</span>
                    <span v-if="promotion.products.length > 2" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">+{{ promotion.products.length - 2 }} więcej</span>
                  </div>
                </td>
                <!-- Dates -->
                <td class="px-3 py-4 text-center">
                  <div class="flex flex-col items-center">
                    <span class="text-xs text-gray-500">Od: {{ formatDate(promotion.starts_at) }}</span>
                    <span v-if="promotion.ends_at" class="text-xs text-gray-500">Do: {{ formatDate(promotion.ends_at) }}</span>
                  </div>
                </td>
                <!-- Actions -->
                <td class="px-4 py-4 text-right">
                  <div class="flex justify-end space-x-1">
                    <admin-button @click="toggleActive(promotion)" :variant="promotion.is_active ? 'danger' : 'success'" size="sm">
                      {{ promotion.is_active ? 'Dezaktywuj' : 'Aktywuj' }}
                    </admin-button>
                    <admin-button @click="editPromotion(promotion)" variant="warning" size="sm">Edytuj</admin-button>
                    <admin-button @click="deletePromotion(promotion)" variant="danger" size="sm">Usuń</admin-button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      <no-data-message 
        v-if="!loading && (!promotions || promotions.length === 0)" 
        message="Brak promocji do wyświetlenia" 
      />
    </div>

    <!-- Paginacja -->
    <div v-if="pagination.total > pagination.per_page" class="mt-6">
      <nav class="flex items-center justify-between border-t border-gray-200 bg-white px-4 py-3 sm:px-6">
        <div class="flex flex-1 justify-between sm:hidden">
          <button
            @click="changePage(pagination.current_page - 1)"
            :disabled="pagination.current_page <= 1"
            class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:opacity-50"
          >
            Poprzednia
          </button>
          <button
            @click="changePage(pagination.current_page + 1)"
            :disabled="pagination.current_page >= pagination.last_page"
            class="relative ml-3 inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:opacity-50"
          >
            Następna
          </button>
        </div>
        <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
          <div>
            <p class="text-sm text-gray-700">
              Wyświetlanie
              <span class="font-medium">{{ pagination.from }}</span>
              do
              <span class="font-medium">{{ pagination.to }}</span>
              z
              <span class="font-medium">{{ pagination.total }}</span>
              wyników
            </p>
          </div>
          <div>
            <nav class="isolate inline-flex -space-x-px rounded-md shadow-sm">
              <button
                @click="changePage(pagination.current_page - 1)"
                :disabled="pagination.current_page <= 1"
                class="relative inline-flex items-center rounded-l-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-none disabled:opacity-50"
              >
                <ChevronLeftIcon class="h-5 w-5" />
              </button>
              
              <button
                v-for="page in visiblePages"
                :key="page"
                @click="changePage(page)"
                :class="[
                  'relative inline-flex items-center px-4 py-2 text-sm font-semibold focus:z-20 focus:outline-none',
                  page === pagination.current_page
                    ? 'z-10 bg-indigo-600 text-white focus:ring-2 focus:ring-indigo-600'
                    : 'text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50'
                ]"
              >
                {{ page }}
              </button>
              
              <button
                @click="changePage(pagination.current_page + 1)"
                :disabled="pagination.current_page >= pagination.last_page"
                class="relative inline-flex items-center rounded-r-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-none disabled:opacity-50"
              >
                <ChevronRightIcon class="h-5 w-5" />
              </button>
            </nav>
          </div>
        </div>
      </nav>
    </div>

    <!-- Modal tworzenia/edycji promocji -->
    <PromotionModal
      v-if="showModal"
      :promotion="selectedPromotion"
      @close="closeModal"
      @saved="handlePromotionSaved"
    />

    <!-- Delete confirmation modal -->
    <admin-modal
      :show="showDeleteModal"
      title="Potwierdź usunięcie"
      size="md"
      icon-variant="danger"
      @close="showDeleteModal = false"
    >
      <div class="text-center">
        <p class="text-sm text-gray-500">
          Czy na pewno chcesz usunąć promocję "{{ promotionToDelete?.title }}"?
          <span v-if="promotionToDelete?.products_count > 0" class="mt-2 block font-semibold text-red-600">
            Uwaga: Ta promocja jest przypisana do {{ promotionToDelete.products_count }} produktów.
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
  </div>
</template>

<script>
import { ref, reactive, computed, onMounted, watch } from 'vue'
import { PlusIcon, PencilIcon, TrashIcon, ChevronLeftIcon, ChevronRightIcon } from '@heroicons/vue/24/outline'
import PromotionModal from '../../components/admin/PromotionModal.vue'
import { useAlertStore } from '../../stores/alertStore'
import AdminButton from '../../components/admin/ui/AdminButton.vue'
import LoadingSpinner from '../../components/admin/LoadingSpinner.vue'
import PageHeader from '../../components/admin/PageHeader.vue'
import SearchFilters from '../../components/admin/SearchFilters.vue'
import ActionButtons from '../../components/admin/ActionButtons.vue'
import AdminModal from '../../components/admin/ui/AdminModal.vue'
import AdminButtonGroup from '../../components/admin/ui/AdminButtonGroup.vue'
import NoDataMessage from '../../components/admin/NoDataMessage.vue'

export default {
  name: 'AdminPromotions',
  components: {
    PlusIcon,
    PencilIcon,
    TrashIcon,
    ChevronLeftIcon,
    ChevronRightIcon,
    PromotionModal,
    AdminButton,
    LoadingSpinner,
    PageHeader,
    SearchFilters,
    ActionButtons,
    AdminModal,
    AdminButtonGroup,
    NoDataMessage
  },
  setup() {
    const alertStore = useAlertStore()
    
    const loading = ref(false)
    const promotions = ref([])
    const showModal = ref(false)
    const selectedPromotion = ref(null)
    const showDeleteModal = ref(false)
    const promotionToDelete = ref(null)
    
    const filters = reactive({
      search: '',
      sort_field: 'created_at',
      sort_direction: 'desc',
      page: 1,
      is_active: '',
      discount_type: '',
      is_featured: ''
    })
    
    const defaultFilters = {
      search: '',
      sort_field: 'created_at',
      sort_direction: 'desc',
      page: 1,
      is_active: '',
      discount_type: '',
      is_featured: ''
    }
    
    const sortOptions = [
      { value: 'id', label: 'ID' },
      { value: 'title', label: 'Nazwa' },
      { value: 'created_at', label: 'Data utworzenia' },
      { value: 'starts_at', label: 'Data rozpoczęcia' },
      { value: 'ends_at', label: 'Data zakończenia' },
      { value: 'discount_value', label: 'Wartość rabatu' }
    ]
    
    const pagination = reactive({
      current_page: 1,
      last_page: 1,
      per_page: 15,
      total: 0,
      from: 0,
      to: 0
    })

    const visiblePages = computed(() => {
      const pages = []
      const start = Math.max(1, pagination.current_page - 2)
      const end = Math.min(pagination.last_page, pagination.current_page + 2)
      
      for (let i = start; i <= end; i++) {
        pages.push(i)
      }
      
      return pages
    })

    const fetchPromotions = async () => {
      loading.value = true
      try {
        const params = new URLSearchParams({
          page: pagination.current_page,
          per_page: pagination.per_page
        })

        // Dodaj filtry
        Object.entries(filters).forEach(([key, value]) => {
          if (value !== '' && value !== null && value !== undefined) {
            params.append(key, value)
          }
        })

        const response = await window.axios.get(`/api/admin/promotions?${params}`)
        
        promotions.value = response.data.data.data || []
        Object.assign(pagination, {
          current_page: response.data.data.current_page,
          last_page: response.data.data.last_page,
          per_page: response.data.data.per_page,
          total: response.data.data.total,
          from: response.data.data.from,
          to: response.data.data.to
        })
      } catch (error) {
        console.error('Błąd podczas pobierania promocji:', error)
        alertStore.error('Błąd podczas pobierania promocji')
      } finally {
        loading.value = false
      }
    }

    const changePage = (page) => {
      if (page >= 1 && page <= pagination.last_page) {
        pagination.current_page = page
        fetchPromotions()
      }
    }

    const openCreateModal = () => {
      selectedPromotion.value = null
      showModal.value = true
    }

    const editPromotion = (promotion) => {
      selectedPromotion.value = promotion
      showModal.value = true
    }

    const closeModal = () => {
      showModal.value = false
      selectedPromotion.value = null
    }

    const handlePromotionSaved = () => {
      closeModal()
      fetchPromotions()
    }

    const toggleActive = async (promotion) => {
      try {
        await window.axios.post(`/api/admin/promotions/${promotion.id}/toggle-active`)
        promotion.is_active = !promotion.is_active
        alertStore.success(
          promotion.is_active ? 'Promocja została aktywowana' : 'Promocja została dezaktywowana'
        )
      } catch (error) {
        console.error('Błąd podczas zmiany statusu promocji:', error)
        alertStore.error('Błąd podczas zmiany statusu promocji')
      }
    }

    const deletePromotion = (promotion) => {
      promotionToDelete.value = promotion
      showDeleteModal.value = true
    }

    const confirmDelete = async () => {
      try {
        await window.axios.delete(`/api/admin/promotions/${promotionToDelete.value.id}`)
        alertStore.success('Promocja została usunięta')
        showDeleteModal.value = false
        fetchPromotions()
      } catch (error) {
        console.error('Błąd podczas usuwania promocji:', error)
        alertStore.error('Błąd podczas usuwania promocji')
      }
    }

    const formatDate = (dateString) => {
      return new Date(dateString).toLocaleDateString('pl-PL')
    }
    
    const resetFilters = () => {
      Object.assign(filters, defaultFilters)
      fetchPromotions()
    }

    onMounted(() => {
      fetchPromotions()
    })

    return {
      loading,
      promotions,
      showModal,
      selectedPromotion,
      showDeleteModal,
      promotionToDelete,
      filters,
      defaultFilters,
      sortOptions,
      pagination,
      visiblePages,
      fetchPromotions,
      changePage,
      openCreateModal,
      editPromotion,
      closeModal,
      handlePromotionSaved,
      toggleActive,
      deletePromotion,
      confirmDelete,
      formatDate,
      resetFilters
    }
  }
}
</script> 