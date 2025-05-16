<template>
  <div>
    <div class="sm:flex sm:items-center">
      <div class="sm:flex-auto">
        <h1 class="text-2xl font-semibold text-gray-900">Zarządzanie kategoriami</h1>
        <p class="mt-2 text-sm text-gray-700">Lista wszystkich kategorii produktów z możliwością dodawania, edycji i usuwania.</p>
      </div>
      <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none">
        <button @click="openModal()" type="button" class="inline-flex items-center justify-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:w-auto">
          Dodaj kategorię
        </button>
      </div>
    </div>
    
    <!-- Loading indicator -->
    <div v-if="loading" class="flex justify-center my-12">
      <svg class="animate-spin h-10 w-10 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
      </svg>
    </div>
    
    <!-- Categories list -->
    <div v-else class="mt-8 flex flex-col">
      <div class="-my-2 -mx-4 overflow-x-auto sm:-mx-6 lg:-mx-8">
        <div class="inline-block min-w-full py-2 align-middle md:px-6 lg:px-8">
          <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
            <table class="min-w-full divide-y divide-gray-300">
              <thead class="bg-gray-50">
                <tr>
                  <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">Nazwa</th>
                  <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Opis</th>
                  <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Liczba produktów</th>
                  <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Data utworzenia</th>
                  <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                    <span class="sr-only">Akcje</span>
                  </th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-200 bg-white">
                <tr v-for="category in categories" :key="category.id">
                  <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">
                    {{ category.name }}
                  </td>
                  <td class="px-3 py-4 text-sm text-gray-500">
                    <div class="max-w-xs truncate">{{ category.description || 'Brak opisu' }}</div>
                  </td>
                  <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                    {{ category.products_count || 0 }}
                  </td>
                  <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                    {{ formatDate(category.created_at) }}
                  </td>
                  <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                    <button @click="openModal(category)" class="text-indigo-600 hover:text-indigo-900 mr-3">Edytuj</button>
                    <button @click="deleteCategory(category.id)" class="text-red-600 hover:text-red-900">Usuń</button>
                  </td>
                </tr>
                <tr v-if="categories.length === 0">
                  <td colspan="5" class="px-3 py-4 text-sm text-gray-500 text-center">Brak kategorii</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Category Modal -->
    <div v-if="showModal" class="fixed z-10 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
      <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" @click="showModal = false"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
          <form @submit.prevent="saveCategory">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
              <div class="sm:flex sm:items-start">
                <div class="w-full">
                  <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4" id="modal-title">
                    {{ currentCategory.id ? 'Edytuj kategorię' : 'Dodaj nową kategorię' }}
                  </h3>
                  
                  <div class="grid grid-cols-1 gap-4">
                    <div>
                      <label for="name" class="block text-sm font-medium text-gray-700">Nazwa kategorii</label>
                      <input
                        type="text"
                        id="name"
                        v-model="currentCategory.name"
                        required
                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                      />
                    </div>
                    
                    <div>
                      <label for="description" class="block text-sm font-medium text-gray-700">Opis (opcjonalnie)</label>
                      <textarea
                        id="description"
                        v-model="currentCategory.description"
                        rows="3"
                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                      ></textarea>
                    </div>
                    
                    <div>
                      <label for="slug" class="block text-sm font-medium text-gray-700">Slug (opcjonalnie)</label>
                      <div class="mt-1 flex rounded-md shadow-sm">
                        <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500 sm:text-sm">
                          /kategoria/
                        </span>
                        <input
                          type="text"
                          id="slug"
                          v-model="currentCategory.slug"
                          class="flex-1 min-w-0 block w-full px-3 py-2 rounded-none rounded-r-md focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm border-gray-300"
                          placeholder="nazwa-kategorii"
                        />
                      </div>
                      <p class="mt-1 text-sm text-gray-500">
                        Pozostaw puste, aby automatycznie wygenerować na podstawie nazwy.
                      </p>
                    </div>
                    
                    <div>
                      <label for="parent_id" class="block text-sm font-medium text-gray-700">Kategoria nadrzędna (opcjonalnie)</label>
                      <select
                        id="parent_id"
                        v-model="currentCategory.parent_id"
                        class="mt-1 block w-full bg-white border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                      >
                        <option :value="null">Brak (kategoria główna)</option>
                        <option 
                          v-for="category in availableParentCategories" 
                          :key="category.id" 
                          :value="category.id"
                        >
                          {{ category.name }}
                        </option>
                      </select>
                    </div>
                    
                    <div>
                      <label class="block text-sm font-medium text-gray-700">Ikona kategorii (opcjonalnie)</label>
                      <div class="mt-1 flex items-center">
                        <span v-if="currentCategory.icon" class="inline-block h-12 w-12 rounded-md overflow-hidden bg-gray-100">
                          <img :src="currentCategory.icon" class="h-full w-full object-cover" />
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
                {{ currentCategory.id ? 'Zapisz zmiany' : 'Dodaj kategorię' }}
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
                  Usuń kategorię
                </h3>
                <div class="mt-2">
                  <p class="text-sm text-gray-500">
                    Czy na pewno chcesz usunąć tę kategorię? Wszystkie produkty w tej kategorii zostaną pozbawione kategorii.
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
import { ref, computed, onMounted } from 'vue'
import { useAlertStore } from '../../stores/alertStore'
import axios from 'axios'

export default {
  name: 'AdminCategories',
  setup() {
    const alertStore = useAlertStore()
    
    // Data
    const loading = ref(true)
    const categories = ref([])
    
    // Modals
    const showModal = ref(false)
    const showDeleteModal = ref(false)
    const categoryToDelete = ref(null)
    const currentCategory = ref({
      id: null,
      name: '',
      description: '',
      slug: '',
      parent_id: null,
      icon: null
    })
    
    // Computed properties
    const availableParentCategories = computed(() => {
      // Filter out current category and its children to prevent circular references
      if (!currentCategory.value.id) {
        return categories.value
      }
      
      return categories.value.filter(category => {
        return category.id !== currentCategory.value.id
      })
    })
    
    // Methods
    const fetchCategories = async () => {
      try {
        loading.value = true
        const response = await axios.get('/api/admin/categories')
        categories.value = response.data
      } catch (error) {
        console.error('Error fetching categories:', error)
        alertStore.setErrorMessage('Wystąpił błąd podczas pobierania kategorii.')
      } finally {
        loading.value = false
      }
    }
    
    const openModal = (category = null) => {
      if (category) {
        currentCategory.value = { ...category }
      } else {
        currentCategory.value = {
          id: null,
          name: '',
          description: '',
          slug: '',
          parent_id: null,
          icon: null
        }
      }
      showModal.value = true
    }
    
    const saveCategory = async () => {
      try {
        if (currentCategory.value.id) {
          // Update existing category
          await axios.put(`/api/admin/categories/${currentCategory.value.id}`, currentCategory.value)
          alertStore.setSuccessMessage('Kategoria została zaktualizowana.')
        } else {
          // Create new category
          await axios.post('/api/admin/categories', currentCategory.value)
          alertStore.setSuccessMessage('Kategoria została dodana.')
        }
        
        showModal.value = false
        fetchCategories()
      } catch (error) {
        console.error('Error saving category:', error)
        alertStore.setErrorMessage('Wystąpił błąd podczas zapisywania kategorii.')
      }
    }
    
    const deleteCategory = (id) => {
      categoryToDelete.value = id
      showDeleteModal.value = true
    }
    
    const confirmDelete = async () => {
      try {
        await axios.delete(`/api/admin/categories/${categoryToDelete.value}`)
        alertStore.setSuccessMessage('Kategoria została usunięta.')
        showDeleteModal.value = false
        fetchCategories()
      } catch (error) {
        console.error('Error deleting category:', error)
        alertStore.setErrorMessage('Wystąpił błąd podczas usuwania kategorii.')
      }
    }
    
    const formatDate = (dateString) => {
      const options = { year: 'numeric', month: 'short', day: 'numeric' }
      return new Date(dateString).toLocaleDateString('pl-PL', options)
    }
    
    // Lifecycle
    onMounted(() => {
      fetchCategories()
    })
    
    return {
      loading,
      categories,
      availableParentCategories,
      showModal,
      showDeleteModal,
      currentCategory,
      fetchCategories,
      openModal,
      saveCategory,
      deleteCategory,
      confirmDelete,
      formatDate
    }
  }
}
</script> 