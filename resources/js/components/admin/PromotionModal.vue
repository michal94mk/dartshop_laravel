<template>
  <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full flex items-center justify-center z-50">
    <div class="relative mx-auto p-6 border w-full max-w-4xl shadow-lg rounded-md bg-white max-h-[90vh] overflow-y-auto">
      <div class="flex justify-between items-center mb-6">
        <h3 class="text-lg font-medium text-gray-900">
          {{ isEditing ? 'Edytuj promocję' : 'Dodaj nową promocję' }}
        </h3>
        <button @click="$emit('close')" class="text-gray-400 hover:text-gray-500">
          <XMarkIcon class="h-6 w-6" />
        </button>
      </div>

      <form @submit.prevent="savePromotion">
        <!-- Podstawowe informacje -->
        <div class="mb-6">
          <h4 class="text-md font-medium text-gray-900 mb-4">Podstawowe informacje</h4>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label for="title" class="block text-sm font-medium text-gray-700">Tytuł promocji *</label>
              <input
                id="title"
                v-model="form.title"
                type="text"
                required
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                placeholder="np. Wielka wyprzedaż"
              />
            </div>
            
            <div>
              <label for="name" class="block text-sm font-medium text-gray-700">Nazwa wewnętrzna *</label>
              <input
                id="name"
                v-model="form.name"
                type="text"
                required
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                placeholder="np. summer_sale_2024"
              />
            </div>
          </div>
          
          <div class="mt-4">
            <label for="description" class="block text-sm font-medium text-gray-700">Opis promocji</label>
            <textarea
              id="description"
              v-model="form.description"
              rows="3"
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
              placeholder="Opis promocji widoczny dla klientów"
            ></textarea>
          </div>
        </div>

        <!-- Parametry promocji -->
        <div class="mb-6">
          <h4 class="text-md font-medium text-gray-900 mb-4">Parametry promocji</h4>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label for="discount_type" class="block text-sm font-medium text-gray-700">Typ rabatu *</label>
              <select
                id="discount_type"
                v-model="form.discount_type"
                required
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
              >
                <option value="percentage">Procentowy</option>
                <option value="fixed">Kwotowy</option>
              </select>
            </div>
            
            <div>
              <label for="discount_value" class="block text-sm font-medium text-gray-700">
                {{ form.discount_type === 'percentage' ? 'Wartość procentowa (%) *' : 'Kwota rabatu (zł) *' }}
              </label>
              <input
                id="discount_value"
                v-model.number="form.discount_value"
                type="number"
                min="0"
                :max="form.discount_type === 'percentage' ? 100 : null"
                step="0.01"
                required
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
              />
            </div>
            

          </div>
        </div>

        <!-- Okres obowiązywania -->
        <div class="mb-6">
          <h4 class="text-md font-medium text-gray-900 mb-4">Okres obowiązywania</h4>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label for="starts_at" class="block text-sm font-medium text-gray-700">Data rozpoczęcia *</label>
              <input
                id="starts_at"
                v-model="form.starts_at"
                type="datetime-local"
                required
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
              />
            </div>
            
            <div>
              <label for="ends_at" class="block text-sm font-medium text-gray-700">Data zakończenia</label>
              <input
                id="ends_at"
                v-model="form.ends_at"
                type="datetime-local"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
              />
              <p class="mt-1 text-xs text-gray-500">Pozostaw puste dla promocji bez daty końcowej</p>
            </div>
          </div>
        </div>

        <!-- Wygląd i ustawienia -->
        <div class="mb-6">
          <h4 class="text-md font-medium text-gray-900 mb-4">Wygląd i ustawienia</h4>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
              <label for="badge_text" class="block text-sm font-medium text-gray-700">Tekst na badge</label>
              <input
                id="badge_text"
                v-model="form.badge_text"
                type="text"
                maxlength="50"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                placeholder="np. PROMOCJA, NOWOŚĆ"
              />
            </div>
            
            <div>
              <label for="badge_color" class="block text-sm font-medium text-gray-700">Kolor badge</label>
              <input
                id="badge_color"
                v-model="form.badge_color"
                type="color"
                class="mt-1 block w-full h-10 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
              />
            </div>
            
            <div>
              <label for="display_order" class="block text-sm font-medium text-gray-700">Kolejność wyświetlania</label>
              <input
                id="display_order"
                v-model.number="form.display_order"
                type="number"
                min="0"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
              />
            </div>
          </div>
          
          <div class="mt-4 space-y-3">
            <div class="flex items-center">
              <input
                id="is_active"
                v-model="form.is_active"
                type="checkbox"
                class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
              />
              <label for="is_active" class="ml-2 block text-sm text-gray-900">Promocja aktywna</label>
            </div>
            
            <div class="flex items-center">
              <input
                id="is_featured"
                v-model="form.is_featured"
                type="checkbox"
                class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
              />
              <label for="is_featured" class="ml-2 block text-sm text-gray-900">Promocja wyróżniona</label>
            </div>
          </div>
        </div>

        <!-- Produkty -->
        <div class="mb-6">
          <h4 class="text-md font-medium text-gray-900 mb-4">Produkty w promocji</h4>
          
          <!-- Wyszukiwanie produktów -->
          <div class="mb-4">
            <div class="flex gap-2">
              <input
                v-model="productSearch"
                type="text"
                placeholder="Wyszukaj produkty..."
                class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                @input="searchProducts"
              />
              <button
                type="button"
                @click="loadAvailableProducts"
                class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
              >
                Odśwież
              </button>
            </div>
          </div>

          <!-- Lista dostępnych produktów -->
          <div v-if="availableProducts.length > 0" class="mb-4">
            <h5 class="text-sm font-medium text-gray-700 mb-2">Dostępne produkty:</h5>
            <div class="max-h-40 overflow-y-auto border border-gray-200 rounded-md">
              <div
                v-for="product in availableProducts"
                :key="product.id"
                class="flex items-center justify-between p-2 hover:bg-gray-50 border-b border-gray-100 last:border-b-0"
              >
                <div class="flex items-center">
                  <img
                    v-if="product.image_url"
                    :src="product.image_url"
                    :alt="product.name"
                    class="w-8 h-8 object-cover rounded mr-2"
                  />
                  <div>
                    <p class="text-sm font-medium text-gray-900">{{ product.name }}</p>
                    <p class="text-xs text-gray-500">{{ product.price }} zł</p>
                  </div>
                </div>
                <button
                  type="button"
                  @click="addProduct(product)"
                  class="px-2 py-1 text-xs bg-indigo-600 text-white rounded hover:bg-indigo-700"
                >
                  Dodaj
                </button>
              </div>
            </div>
          </div>

          <!-- Wybrane produkty -->
          <div v-if="selectedProducts.length > 0">
            <h5 class="text-sm font-medium text-gray-700 mb-2">Wybrane produkty ({{ selectedProducts.length }}):</h5>
            <div class="space-y-2">
              <div
                v-for="product in selectedProducts"
                :key="product.id"
                class="flex items-center justify-between p-3 bg-blue-50 border border-blue-200 rounded-md"
              >
                <div class="flex items-center">
                  <img
                    v-if="product.image_url"
                    :src="product.image_url"
                    :alt="product.name"
                    class="w-10 h-10 object-cover rounded mr-3"
                  />
                  <div>
                    <p class="text-sm font-medium text-gray-900">{{ product.name }}</p>
                    <p class="text-xs text-gray-500">{{ product.price }} zł</p>
                  </div>
                </div>
                <button
                  type="button"
                  @click="removeProduct(product.id)"
                  class="px-2 py-1 text-xs bg-red-600 text-white rounded hover:bg-red-700"
                >
                  Usuń
                </button>
              </div>
            </div>
          </div>

          <div v-else class="text-center py-4 text-gray-500">
            Nie wybrano żadnych produktów
          </div>
        </div>

        <!-- Przyciski -->
        <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
          <button
            type="button"
            @click="$emit('close')"
            class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
          >
            Anuluj
          </button>
          <button
            type="submit"
            :disabled="submitting"
            class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50"
          >
            {{ submitting ? 'Zapisywanie...' : (isEditing ? 'Aktualizuj' : 'Utwórz') }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import { ref, reactive, computed, onMounted, watch } from 'vue'
import { XMarkIcon } from '@heroicons/vue/24/outline'
import { useAlertStore } from '../../stores/alertStore'

export default {
  name: 'PromotionModal',
  components: {
    XMarkIcon
  },
  props: {
    promotion: {
      type: Object,
      default: null
    }
  },
  emits: ['close', 'saved'],
  setup(props, { emit }) {
    const alertStore = useAlertStore()
    
    const submitting = ref(false)
    const productSearch = ref('')
    const availableProducts = ref([])
    const selectedProducts = ref([])
    
    const form = reactive({
      title: '',
      name: '',
      description: '',
      discount_type: 'percentage',
      discount_value: 0,
      starts_at: '',
      ends_at: '',
      is_active: true,
      display_order: 0,
      is_featured: false,
      badge_text: '',
      badge_color: '#ff0000',
      product_ids: []
    })

    const isEditing = computed(() => !!props.promotion)

    const initializeForm = () => {
      if (props.promotion) {
        Object.assign(form, {
          title: props.promotion.title || '',
          name: props.promotion.name || '',
          description: props.promotion.description || '',
          discount_type: props.promotion.discount_type || 'percentage',
          discount_value: props.promotion.discount_value || 0,
          starts_at: props.promotion.starts_at ? new Date(props.promotion.starts_at).toISOString().slice(0, 16) : '',
          ends_at: props.promotion.ends_at ? new Date(props.promotion.ends_at).toISOString().slice(0, 16) : '',
          is_active: props.promotion.is_active ?? true,
          display_order: props.promotion.display_order || 0,
          is_featured: props.promotion.is_featured || false,
          badge_text: props.promotion.badge_text || '',
          badge_color: props.promotion.badge_color || '#ff0000',
          product_ids: props.promotion.products ? props.promotion.products.map(p => p.id) : []
        })
        
        selectedProducts.value = props.promotion.products || []
      } else {
        // Ustaw domyślną datę rozpoczęcia na teraz
        const now = new Date()
        form.starts_at = now.toISOString().slice(0, 16)
      }
    }

    const loadAvailableProducts = async () => {
      try {
        const params = new URLSearchParams({
          search: productSearch.value,
          per_page: 20
        })
        
        if (isEditing.value) {
          params.append('exclude_promotion_id', props.promotion.id)
        }

        const response = await window.axios.get(`/api/admin/available-products?${params}`)
        availableProducts.value = response.data.data
      } catch (error) {
        console.error('Błąd podczas pobierania produktów:', error)
        alertStore.error('Błąd podczas pobierania produktów')
      }
    }

    const searchProducts = () => {
      // Debounce search
      clearTimeout(searchProducts.timeout)
      searchProducts.timeout = setTimeout(() => {
        loadAvailableProducts()
      }, 300)
    }

    const addProduct = (product) => {
      if (!selectedProducts.value.find(p => p.id === product.id)) {
        selectedProducts.value.push(product)
        form.product_ids.push(product.id)
        
        // Usuń z dostępnych produktów
        availableProducts.value = availableProducts.value.filter(p => p.id !== product.id)
      }
    }

    const removeProduct = (productId) => {
      selectedProducts.value = selectedProducts.value.filter(p => p.id !== productId)
      form.product_ids = form.product_ids.filter(id => id !== productId)
      
      // Odśwież listę dostępnych produktów
      loadAvailableProducts()
    }

    const savePromotion = async () => {
      submitting.value = true
      
      try {
        const url = isEditing.value 
          ? `/api/admin/promotions/${props.promotion.id}`
          : '/api/admin/promotions'
        
        const method = isEditing.value ? 'put' : 'post'
        
        await window.axios[method](url, form)
        
        alertStore.success(
          isEditing.value ? 'Promocja została zaktualizowana' : 'Promocja została utworzona'
        )
        
        emit('saved')
      } catch (error) {
        console.error('Błąd podczas zapisywania promocji:', error)
        
        if (error.response?.status === 422) {
          const errors = error.response.data.errors || {}
          const firstError = Object.values(errors)[0]?.[0] || error.response.data.message
          alertStore.error(firstError)
        } else {
          alertStore.error('Błąd podczas zapisywania promocji')
        }
      } finally {
        submitting.value = false
      }
    }

    // Obserwuj zmiany product_ids w formularzu
    watch(() => form.product_ids, (newIds) => {
      selectedProducts.value = selectedProducts.value.filter(p => newIds.includes(p.id))
    })

    onMounted(() => {
      initializeForm()
      loadAvailableProducts()
    })

    return {
      submitting,
      productSearch,
      availableProducts,
      selectedProducts,
      form,
      isEditing,
      loadAvailableProducts,
      searchProducts,
      addProduct,
      removeProduct,
      savePromotion
    }
  }
}
</script> 