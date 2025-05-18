<template>
  <div>
    <div class="sm:flex sm:items-center">
      <div class="sm:flex-auto">
        <h1 class="text-2xl font-semibold text-gray-900">Zarządzanie produktami</h1>
        <p class="mt-2 text-sm text-gray-700">Lista wszystkich produktów w sklepie z możliwością dodawania, edycji i usuwania.</p>
      </div>
      <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none">
        <button @click="openModal()" type="button" class="inline-flex items-center justify-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:w-auto">
          Dodaj produkt
        </button>
      </div>
    </div>

    <!-- Search and filters -->
    <div class="mt-6 bg-white shadow px-4 py-5 sm:rounded-lg sm:px-6">
      <div class="flex flex-wrap gap-4">
        <div class="flex-1 min-w-[200px]">
          <label for="search" class="block text-sm font-medium text-gray-700">Wyszukaj</label>
          <div class="mt-1">
            <input
              type="text"
              name="search"
              id="search"
              v-model="filters.search"
              @input="debouncedFetchProducts"
              class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
              placeholder="Nazwa produktu..."
            />
          </div>
        </div>
        
        <div class="w-full sm:w-auto">
          <label for="category" class="block text-sm font-medium text-gray-700">Kategoria</label>
          <select
            id="category"
            name="category"
            v-model="filters.category_id"
            @change="fetchProducts"
            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
          >
            <option value="">Wszystkie kategorie</option>
            <option v-for="category in categories" :key="category.id" :value="category.id">
              {{ category.name }}
            </option>
          </select>
        </div>
        
        <div class="w-full sm:w-auto">
          <label for="brand" class="block text-sm font-medium text-gray-700">Marka</label>
          <select
            id="brand"
            name="brand"
            v-model="filters.brand_id"
            @change="fetchProducts"
            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
          >
            <option value="">Wszystkie marki</option>
            <option v-for="brand in brands" :key="brand.id" :value="brand.id">
              {{ brand.name }}
            </option>
          </select>
        </div>
        
        <div class="w-full sm:w-auto">
          <label for="sort" class="block text-sm font-medium text-gray-700">Sortuj</label>
          <select
            id="sort"
            name="sort"
            v-model="filters.sort_field"
            @change="fetchProducts"
            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
          >
            <option value="created_at">Data dodania</option>
            <option value="name">Nazwa</option>
            <option value="price">Cena</option>
          </select>
        </div>
        
        <div class="w-full sm:w-auto">
          <label for="direction" class="block text-sm font-medium text-gray-700">Kierunek</label>
          <select
            id="direction"
            name="direction"
            v-model="filters.sort_direction"
            @change="fetchProducts"
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

    <!-- Products list -->
    <div v-else class="mt-8 flex flex-col">
      <div class="-my-2 -mx-4 overflow-x-auto sm:-mx-6 lg:-mx-8">
        <div class="inline-block min-w-full py-2 align-middle md:px-6 lg:px-8">
          <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
            <table class="min-w-full divide-y divide-gray-300">
              <thead class="bg-gray-50">
                <tr>
                  <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">Produkt</th>
                  <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Kategoria</th>
                  <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Marka</th>
                  <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Cena</th>
                  <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                    <span class="sr-only">Akcje</span>
                  </th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-200 bg-white">
                <tr v-for="product in products.data" :key="product.id">
                  <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm sm:pl-6">
                    <div class="flex items-center">
                      <div class="h-10 w-10 flex-shrink-0">
                        <img v-if="product.image" :src="product.image" class="h-10 w-10 rounded-full object-cover" />
                        <div v-else class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                          <svg class="h-6 w-6 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                          </svg>
                        </div>
                      </div>
                      <div class="ml-4">
                        <div class="font-medium text-gray-900">{{ product.name }}</div>
                        <div class="text-gray-500 truncate max-w-xs">{{ truncate(product.description, 50) }}</div>
                      </div>
                    </div>
                  </td>
                  <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                    {{ product.category ? product.category.name : '-' }}
                  </td>
                  <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                    {{ product.brand ? product.brand.name : '-' }}
                  </td>
                  <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ product.price }} PLN</td>
                  <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                    <button @click="openModal(product)" class="text-indigo-600 hover:text-indigo-900 mr-3">Edytuj</button>
                    <button @click="deleteProduct(product.id)" class="text-red-600 hover:text-red-900">Usuń</button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
      
      <!-- Pagination -->
      <div v-if="products.data && products.data.length > 0" class="mt-5 flex justify-between items-center">
        <div class="text-sm text-gray-700">
          Pokazuje {{ products.from }} do {{ products.to }} z {{ products.total }} produktów
        </div>
        <div>
          <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
            <button
              @click="goToPage(products.current_page - 1)"
              :disabled="products.current_page === 1"
              :class="[
                products.current_page === 1 ? 'cursor-not-allowed opacity-50' : 'hover:bg-gray-50',
                'relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500'
              ]"
            >
              <span class="sr-only">Poprzednia</span>
              <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
              </svg>
            </button>
            <button
              v-for="page in paginationPages"
              :key="page"
              @click="goToPage(page)"
              :class="[
                page === products.current_page
                  ? 'z-10 bg-indigo-50 border-indigo-500 text-indigo-600'
                  : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50',
                'relative inline-flex items-center px-4 py-2 border text-sm font-medium'
              ]"
            >
              {{ page }}
            </button>
            <button
              @click="goToPage(products.current_page + 1)"
              :disabled="products.current_page === products.last_page"
              :class="[
                products.current_page === products.last_page ? 'cursor-not-allowed opacity-50' : 'hover:bg-gray-50',
                'relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500'
              ]"
            >
              <span class="sr-only">Następna</span>
              <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
              </svg>
            </button>
          </nav>
        </div>
      </div>
    </div>

    <!-- Product Modal -->
    <div v-if="showModal" class="fixed z-10 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
      <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" @click="showModal = false"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
          <form @submit.prevent="saveProduct">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
              <div class="sm:flex sm:items-start">
                <div class="w-full">
                  <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4" id="modal-title">
                    {{ currentProduct.id ? 'Edytuj produkt' : 'Dodaj nowy produkt' }}
                  </h3>
                  
                  <div class="grid grid-cols-1 gap-4">
                    <div>
                      <label for="name" class="block text-sm font-medium text-gray-700">Nazwa produktu</label>
                      <input
                        type="text"
                        id="name"
                        v-model="currentProduct.name"
                        required
                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                      />
                    </div>
                    
                    <div>
                      <label for="description" class="block text-sm font-medium text-gray-700">Opis</label>
                      <textarea
                        id="description"
                        v-model="currentProduct.description"
                        required
                        rows="3"
                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                      ></textarea>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                      <div>
                        <label for="price" class="block text-sm font-medium text-gray-700">Cena</label>
                        <input
                          type="number"
                          id="price"
                          v-model="currentProduct.price"
                          required
                          min="0"
                          step="0.01"
                          class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                        />
                      </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                      <div>
                        <label for="category_id" class="block text-sm font-medium text-gray-700">Kategoria</label>
                        <select
                          id="category_id"
                          v-model="currentProduct.category_id"
                          required
                          class="mt-1 block w-full bg-white border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        >
                          <option v-for="category in categories" :key="category.id" :value="category.id">
                            {{ category.name }}
                          </option>
                        </select>
                      </div>
                      
                      <div>
                        <label for="brand_id" class="block text-sm font-medium text-gray-700">Marka</label>
                        <select
                          id="brand_id"
                          v-model="currentProduct.brand_id"
                          required
                          class="mt-1 block w-full bg-white border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        >
                          <option v-for="brand in brands" :key="brand.id" :value="brand.id">
                            {{ brand.name }}
                          </option>
                        </select>
                      </div>
                    </div>
                    
                    <div>
                      <label class="block text-sm font-medium text-gray-700">Zdjęcie produktu</label>
                      <div class="mt-1 flex items-center">
                        <span v-if="currentProduct.image" class="inline-block h-12 w-12 rounded-md overflow-hidden bg-gray-100">
                          <img :src="currentProduct.image" class="h-full w-full object-cover" />
                        </span>
                        <span v-else class="inline-block h-12 w-12 rounded-md overflow-hidden bg-gray-100">
                          <svg class="h-full w-full text-gray-300" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
                          </svg>
                        </span>
                        <button
                          type="button"
                          class="ml-5 bg-white py-2 px-3 border border-gray-300 rounded-md shadow-sm text-sm leading-4 font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                        >
                          Zmień
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
              <button
                type="submit"
                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm"
              >
                {{ currentProduct.id ? 'Zapisz zmiany' : 'Dodaj produkt' }}
              </button>
              <button
                type="button"
                @click="showModal = false"
                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
              >
                Anuluj
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

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
                  Usuń produkt
                </h3>
                <div class="mt-2">
                  <p class="text-sm text-gray-500">
                    Czy na pewno chcesz usunąć ten produkt? Ta operacja jest nieodwracalna.
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
import { useAlertStore } from '../../stores/alertStore'
import axios from 'axios'
import debounce from 'lodash/debounce'

export default {
  name: 'AdminProducts',
  setup() {
    const alertStore = useAlertStore()
    
    // Data
    const loading = ref(true)
    const products = ref({
      data: [],
      current_page: 1,
      from: 1,
      to: 0,
      total: 0,
      last_page: 1,
      per_page: 10
    })
    const categories = ref([])
    const brands = ref([])
    const filters = reactive({
      search: '',
      category_id: '',
      brand_id: '',
      sort_field: 'created_at',
      sort_direction: 'desc',
      page: 1
    })
    
    // Modals
    const showModal = ref(false)
    const showDeleteModal = ref(false)
    const productToDelete = ref(null)
    const currentProduct = ref({
      id: null,
      name: '',
      description: '',
      price: 0,
      category_id: '',
      brand_id: '',
      image: null
    })
    
    // Computed
    const paginationPages = computed(() => {
      const total = products.value.last_page
      const current = products.value.current_page
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
    const fetchProducts = async () => {
      try {
        loading.value = true
        
        // Simplifying to match Categories component
        const response = await axios.get('/api/admin/products')
        console.log('Products response:', response)
        products.value = response.data
      } catch (error) {
        console.error('Error fetching products:', error)
        console.error('Error details:', error.response?.data)
        alertStore.error('Wystąpił błąd podczas pobierania produktów.')
      } finally {
        loading.value = false
      }
    }
    
    const debouncedFetchProducts = debounce(fetchProducts, 300)
    
    const fetchFormData = async () => {
      try {
        console.log('Fetching form data')
        
        const response = await axios.get('/api/admin/products/form-data')
        console.log('Form data response:', response)
        categories.value = response.data.categories
        brands.value = response.data.brands
      } catch (error) {
        console.error('Error fetching form data:', error)
        console.error('Error details:', error.response?.data)
        alertStore.error('Wystąpił błąd podczas pobierania danych formularza.')
      }
    }
    
    const goToPage = (page) => {
      if (page === '...') return
      filters.page = page
      fetchProducts()
    }
    
    const openModal = (product = null) => {
      if (product) {
        currentProduct.value = { ...product }
      } else {
        currentProduct.value = {
          id: null,
          name: '',
          description: '',
          price: 0,
          category_id: categories.value.length ? categories.value[0].id : '',
          brand_id: brands.value.length ? brands.value[0].id : '',
          image: null
        }
      }
      showModal.value = true
    }
    
    const saveProduct = async () => {
      try {
        if (currentProduct.value.id) {
          // Update existing product
          await axios.put(`/api/admin/products/${currentProduct.value.id}`, currentProduct.value)
          alertStore.success('Produkt został zaktualizowany.')
        } else {
          // Create new product
          await axios.post('/api/admin/products', currentProduct.value)
          alertStore.success('Produkt został dodany.')
        }
        
        showModal.value = false
        fetchProducts()
      } catch (error) {
        console.error('Error saving product:', error)
        alertStore.error('Wystąpił błąd podczas zapisywania produktu.')
      }
    }
    
    const deleteProduct = (id) => {
      productToDelete.value = id
      showDeleteModal.value = true
    }
    
    const confirmDelete = async () => {
      try {
        await axios.delete(`/api/admin/products/${productToDelete.value}`)
        alertStore.success('Produkt został usunięty.')
        showDeleteModal.value = false
        fetchProducts()
      } catch (error) {
        console.error('Error deleting product:', error)
        alertStore.error('Wystąpił błąd podczas usuwania produktu.')
      }
    }
    
    const truncate = (text, length) => {
      if (!text) return ''
      return text.length > length ? text.substring(0, length) + '...' : text
    }
    
    // Watch for filter changes
    watch(() => filters.page, () => {
      fetchProducts()
    })
    
    // Lifecycle
    onMounted(() => {
      fetchFormData()
      fetchProducts()
    })
    
    return {
      loading,
      products,
      categories,
      brands,
      filters,
      paginationPages,
      showModal,
      showDeleteModal,
      currentProduct,
      fetchProducts,
      debouncedFetchProducts,
      goToPage,
      openModal,
      saveProduct,
      deleteProduct,
      confirmDelete,
      truncate
    }
  }
}
</script> 