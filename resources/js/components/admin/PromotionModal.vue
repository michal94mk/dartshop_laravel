<template>
  <admin-modal
    :show="true"
    :title="isEditing ? 'Edytuj promocję' : 'Dodaj nową promocję'"
    size="4xl"
    @close="$emit('close')"
  >
    <form>
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
                :class="[
                  'mt-1 block w-full rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm',
                  formErrors.title 
                    ? 'border-red-300 focus:border-red-500 focus:ring-red-500' 
                    : 'border-gray-300 focus:border-indigo-500 focus:ring-indigo-500'
                ]"
                placeholder="np. Wielka wyprzedaż"
              />
              <p v-if="formErrors.title" class="mt-1 text-sm text-red-600">{{ Array.isArray(formErrors.title) ? formErrors.title[0] : formErrors.title }}</p>
            </div>
            
            <div>
              <label for="name" class="block text-sm font-medium text-gray-700">Nazwa wewnętrzna *</label>
              <input
                id="name"
                v-model="form.name"
                type="text"
                required
                :class="[
                  'mt-1 block w-full rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm',
                  formErrors.name 
                    ? 'border-red-300 focus:border-red-500 focus:ring-red-500' 
                    : 'border-gray-300 focus:border-indigo-500 focus:ring-indigo-500'
                ]"
                placeholder="np. summer_sale_2024"
              />
              <p v-if="formErrors.name" class="mt-1 text-sm text-red-600">{{ Array.isArray(formErrors.name) ? formErrors.name[0] : formErrors.name }}</p>
            </div>
          </div>
          
          <div class="mt-4">
            <label for="description" class="block text-sm font-medium text-gray-700">Opis promocji</label>
            <textarea
              id="description"
              v-model="form.description"
              rows="3"
              :class="[
                'mt-1 block w-full rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm',
                formErrors.description 
                  ? 'border-red-300 focus:border-red-500 focus:ring-red-500' 
                  : 'border-gray-300 focus:border-indigo-500 focus:ring-indigo-500'
              ]"
              placeholder="Opis promocji widoczny dla klientów"
            ></textarea>
            <p v-if="formErrors.description" class="mt-1 text-sm text-red-600">{{ Array.isArray(formErrors.description) ? formErrors.description[0] : formErrors.description }}</p>
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
                :class="[
                  'mt-1 block w-full rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm',
                  formErrors.discount_type 
                    ? 'border-red-300 focus:border-red-500 focus:ring-red-500' 
                    : 'border-gray-300 focus:border-indigo-500 focus:ring-indigo-500'
                ]"
              >
                <option value="percentage">Procentowy</option>
                <option value="fixed">Kwotowy</option>
              </select>
              <p v-if="formErrors.discount_type" class="mt-1 text-sm text-red-600">{{ Array.isArray(formErrors.discount_type) ? formErrors.discount_type[0] : formErrors.discount_type }}</p>
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
                :class="[
                  'mt-1 block w-full rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm',
                  formErrors.discount_value 
                    ? 'border-red-300 focus:border-red-500 focus:ring-red-500' 
                    : 'border-gray-300 focus:border-indigo-500 focus:ring-indigo-500'
                ]"
              />
              <p v-if="formErrors.discount_value" class="mt-1 text-sm text-red-600">{{ Array.isArray(formErrors.discount_value) ? formErrors.discount_value[0] : formErrors.discount_value }}</p>
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
                :class="[
                  'mt-1 block w-full rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm',
                  formErrors.starts_at 
                    ? 'border-red-300 focus:border-red-500 focus:ring-red-500' 
                    : 'border-gray-300 focus:border-indigo-500 focus:ring-indigo-500'
                ]"
              />
              <p v-if="formErrors.starts_at" class="mt-1 text-sm text-red-600">{{ Array.isArray(formErrors.starts_at) ? formErrors.starts_at[0] : formErrors.starts_at }}</p>
            </div>
            
            <div>
              <label for="ends_at" class="block text-sm font-medium text-gray-700">Data zakończenia</label>
              <input
                id="ends_at"
                v-model="form.ends_at"
                type="datetime-local"
                :class="[
                  'mt-1 block w-full rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm',
                  formErrors.ends_at 
                    ? 'border-red-300 focus:border-red-500 focus:ring-red-500' 
                    : 'border-gray-300 focus:border-indigo-500 focus:ring-indigo-500'
                ]"
              />
              <p v-if="formErrors.ends_at" class="mt-1 text-sm text-red-600">{{ Array.isArray(formErrors.ends_at) ? formErrors.ends_at[0] : formErrors.ends_at }}</p>
              <p v-else class="mt-1 text-xs text-gray-500">Pozostaw puste dla promocji bez daty końcowej</p>
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
                :class="[
                  'mt-1 block w-full rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm',
                  formErrors.badge_text 
                    ? 'border-red-300 focus:border-red-500 focus:ring-red-500' 
                    : 'border-gray-300 focus:border-indigo-500 focus:ring-indigo-500'
                ]"
                placeholder="np. PROMOCJA, NOWOŚĆ"
              />
              <p v-if="formErrors.badge_text" class="mt-1 text-sm text-red-600">{{ Array.isArray(formErrors.badge_text) ? formErrors.badge_text[0] : formErrors.badge_text }}</p>
            </div>
            
            <div>
              <label for="badge_color" class="block text-sm font-medium text-gray-700">Kolor badge</label>
              <input
                id="badge_color"
                v-model="form.badge_color"
                type="color"
                :class="[
                  'mt-1 block w-full h-10 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500',
                  formErrors.badge_color 
                    ? 'border-red-300 focus:border-red-500 focus:ring-red-500' 
                    : 'border-gray-300 focus:border-indigo-500 focus:ring-indigo-500'
                ]"
              />
              <p v-if="formErrors.badge_color" class="mt-1 text-sm text-red-600">{{ Array.isArray(formErrors.badge_color) ? formErrors.badge_color[0] : formErrors.badge_color }}</p>
            </div>
            
            <div>
              <label for="display_order" class="block text-sm font-medium text-gray-700">Kolejność wyświetlania</label>
              <input
                id="display_order"
                v-model.number="form.display_order"
                type="number"
                min="0"
                :class="[
                  'mt-1 block w-full rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm',
                  formErrors.display_order 
                    ? 'border-red-300 focus:border-red-500 focus:ring-red-500' 
                    : 'border-gray-300 focus:border-indigo-500 focus:ring-indigo-500'
                ]"
              />
              <p v-if="formErrors.display_order" class="mt-1 text-sm text-red-600">{{ Array.isArray(formErrors.display_order) ? formErrors.display_order[0] : formErrors.display_order }}</p>
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

      </form>
      
      <template #footer>
        <admin-button-group justify="end" spacing="sm">
          <admin-button
            @click="$emit('close')"
            variant="secondary"
            outline
          >
            Anuluj
          </admin-button>
          <admin-button
            @click="savePromotion"
            variant="primary"
            :disabled="submitting"
          >
            {{ submitting ? 'Zapisywanie...' : (isEditing ? 'Aktualizuj' : 'Utwórz') }}
          </admin-button>
        </admin-button-group>
      </template>
    </admin-modal>
</template>

<script>
import { ref, reactive, computed, onMounted, watch } from 'vue'
import { XMarkIcon } from '@heroicons/vue/24/outline'
import { useAlertStore } from '../../stores/alertStore'
import AdminModal from './ui/AdminModal.vue'
import AdminButton from './ui/AdminButton.vue'
import AdminButtonGroup from './ui/AdminButtonGroup.vue'

export default {
  name: 'PromotionModal',
  components: {
    XMarkIcon,
    AdminModal,
    AdminButton,
    AdminButtonGroup
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
    const formErrors = ref({})
    
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
      formErrors.value = {} // Reset errors
      
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
          formErrors.value = error.response.data.errors || {}
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
      formErrors,
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