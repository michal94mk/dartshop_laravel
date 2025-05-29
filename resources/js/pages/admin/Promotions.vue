<template>
  <div class="px-6 py-4">
    <div class="sm:flex sm:items-center">
      <div class="sm:flex-auto">
        <h1 class="text-2xl font-semibold text-gray-900">Promocje produktowe</h1>
        <p class="mt-2 text-sm text-gray-700">
          Zarządzaj promocjami przypisanymi do produktów
        </p>
      </div>
      <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none">
        <button
          @click="openCreateModal"
          type="button"
          class="inline-flex items-center justify-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:w-auto"
        >
          Dodaj promocję
        </button>
      </div>
    </div>

    <!-- Filtry -->
    <div class="mt-6 bg-white shadow rounded-lg p-4">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700">Wyszukaj</label>
          <input
            v-model="filters.search"
            type="text"
            placeholder="Nazwa, tytuł..."
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
          />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700">Status</label>
          <select
            v-model="filters.is_active"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
          >
            <option value="">Wszystkie</option>
            <option value="true">Aktywne</option>
            <option value="false">Nieaktywne</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700">Typ rabatu</label>
          <select
            v-model="filters.discount_type"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
          >
            <option value="">Wszystkie</option>
            <option value="percentage">Procentowy</option>
            <option value="fixed">Kwotowy</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700">Wyróżnione</label>
          <select
            v-model="filters.is_featured"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
          >
            <option value="">Wszystkie</option>
            <option value="true">Wyróżnione</option>
            <option value="false">Zwykłe</option>
          </select>
        </div>
      </div>
    </div>

    <!-- Lista promocji -->
    <div class="mt-6 bg-white shadow overflow-hidden sm:rounded-md">
      <div v-if="loading" class="p-6 text-center">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600 mx-auto"></div>
        <p class="mt-2 text-sm text-gray-500">Ładowanie promocji...</p>
      </div>

      <ul v-else-if="promotions.length > 0" class="divide-y divide-gray-200">
        <li v-for="promotion in promotions" :key="promotion.id" class="p-6">
          <div class="flex items-center justify-between">
            <div class="flex-1">
              <div class="flex items-center">
                <h3 class="text-lg font-medium text-gray-900">
                  {{ promotion.title }}
                </h3>
                <span
                  v-if="promotion.is_featured"
                  class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800"
                >
                  ⭐ Wyróżniona
                </span>
                <span
                  :class="[
                    'ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                    promotion.is_active
                      ? 'bg-green-100 text-green-800'
                      : 'bg-red-100 text-red-800'
                  ]"
                >
                  {{ promotion.is_active ? 'Aktywna' : 'Nieaktywna' }}
                </span>
              </div>
              
              <p class="mt-1 text-sm text-gray-600">{{ promotion.description }}</p>
              
              <div class="mt-2 flex items-center space-x-4 text-sm text-gray-500">
                <span>
                  Rabat: {{ promotion.discount_value }}{{ promotion.discount_type === 'percentage' ? '%' : ' zł' }}
                </span>
                <span>
                  Produkty: {{ promotion.products?.length || 0 }}
                </span>
                <span>
                  Od: {{ formatDate(promotion.starts_at) }}
                </span>
                <span v-if="promotion.ends_at">
                  Do: {{ formatDate(promotion.ends_at) }}
                </span>
              </div>

              <!-- Produkty -->
              <div v-if="promotion.products && promotion.products.length > 0" class="mt-3">
                <div class="flex flex-wrap gap-2">
                  <span
                    v-for="product in promotion.products.slice(0, 3)"
                    :key="product.id"
                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800"
                  >
                    {{ product.name }}
                  </span>
                  <span
                    v-if="promotion.products.length > 3"
                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800"
                  >
                    +{{ promotion.products.length - 3 }} więcej
                  </span>
                </div>
              </div>
            </div>

            <div class="flex items-center space-x-2">
              <admin-button
                @click="toggleActive(promotion)"
                :variant="promotion.is_active ? 'danger' : 'success'"
                size="sm"
              >
                {{ promotion.is_active ? 'Dezaktywuj' : 'Aktywuj' }}
              </admin-button>
              
              <admin-button
                @click="editPromotion(promotion)"
                variant="warning"
                size="sm"
              >
                Edytuj
              </admin-button>
              
              <admin-button
                @click="deletePromotion(promotion)"
                variant="danger"
                size="sm"
              >
                Usuń
              </admin-button>
            </div>
          </div>
        </li>
      </ul>

      <div v-else class="p-6 text-center">
        <p class="text-gray-500">Brak promocji do wyświetlenia</p>
      </div>
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
                  Usuń promocję
                </h3>
                <div class="mt-2">
                  <p class="text-sm text-gray-500">
                    Czy na pewno chcesz usunąć promocję "{{ promotionToDelete?.title }}"? Ta operacja jest nieodwracalna i spowoduje usunięcie promocji ze wszystkich przypisanych produktów.
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
              Usuń
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
import { ref, reactive, computed, onMounted, watch } from 'vue'
import { PlusIcon, PencilIcon, TrashIcon, ChevronLeftIcon, ChevronRightIcon } from '@heroicons/vue/24/outline'
import PromotionModal from '../../components/admin/PromotionModal.vue'
import { useAlertStore } from '../../stores/alertStore'
import AdminButton from '../../components/admin/ui/AdminButton.vue'

export default {
  name: 'AdminPromotions',
  components: {
    PlusIcon,
    PencilIcon,
    TrashIcon,
    ChevronLeftIcon,
    ChevronRightIcon,
    PromotionModal,
    AdminButton
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
      is_active: '',
      discount_type: '',
      is_featured: ''
    })
    
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
          if (value !== '') {
            params.append(key, value)
          }
        })

        const response = await window.axios.get(`/api/admin/promotions?${params}`)
        
        promotions.value = response.data.data
        Object.assign(pagination, {
          current_page: response.data.current_page,
          last_page: response.data.last_page,
          per_page: response.data.per_page,
          total: response.data.total,
          from: response.data.from,
          to: response.data.to
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

    // Obserwuj zmiany filtrów
    watch(filters, () => {
      pagination.current_page = 1
      fetchPromotions()
    }, { deep: true })

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
      formatDate
    }
  }
}
</script> 