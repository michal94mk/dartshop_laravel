<template>
  <div class="p-6">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-semibold text-gray-900">Zarządzanie markami</h1>
      <button 
        @click="showAddForm = true" 
        class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
      >
        Dodaj markę
      </button>
    </div>
    
    <!-- Search bar -->
    <div class="mt-4 flex justify-between">
      <div class="w-full max-w-lg lg:max-w-xs">
        <label for="search" class="sr-only">Szukaj</label>
        <div class="relative">
          <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
            <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
              <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
            </svg>
          </div>
          <input
            id="search"
            name="search"
            class="block w-full rounded-md border border-gray-300 bg-white py-2 pl-10 pr-3 leading-5 placeholder-gray-500 focus:border-indigo-500 focus:placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-indigo-500 sm:text-sm"
            placeholder="Szukaj marek..."
            type="search"
            v-model="searchQuery"
            @input="onSearchChange"
          />
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
    
    <!-- Brands Table -->
    <div v-else-if="filteredBrands.length" class="bg-white shadow overflow-hidden sm:rounded-md mt-4">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nazwa</th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Slug</th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Liczba produktów</th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Akcje</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="brand in filteredBrands" :key="brand.id">
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ brand.id }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ brand.name }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ brand.slug }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ brand.products_count }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
              <button 
                @click="editBrand(brand)" 
                class="text-indigo-600 hover:text-indigo-900 mr-4"
              >
                Edytuj
              </button>
              <button 
                @click="confirmDelete(brand)" 
                class="text-red-600 hover:text-red-900"
              >
                Usuń
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <div v-else class="bg-white shadow overflow-hidden sm:rounded-md p-6 text-center text-gray-500 mt-4">
      Brak marek do wyświetlenia
    </div>
    
    <!-- Add/Edit Modal -->
    <div v-if="showAddForm || showEditForm" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full flex items-center justify-center">
      <div class="relative mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
          <h3 class="text-lg font-medium text-gray-900">{{ showEditForm ? 'Edytuj markę' : 'Dodaj nową markę' }}</h3>
          <button @click="closeForm" class="text-gray-400 hover:text-gray-500">
            <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
        
        <form @submit.prevent="showEditForm ? updateBrand() : addBrand()">
          <div class="mb-4">
            <label for="name" class="block text-sm font-medium text-gray-700">Nazwa</label>
            <input 
              type="text" 
              id="name" 
              v-model="form.name" 
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
              required
            >
          </div>
          
          <div class="mb-4">
            <label for="slug" class="block text-sm font-medium text-gray-700">Slug</label>
            <input 
              type="text" 
              id="slug" 
              v-model="form.slug" 
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
              required
            >
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
              {{ showEditForm ? 'Zapisz zmiany' : 'Dodaj markę' }}
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
              Czy na pewno chcesz usunąć markę "{{ brandToDelete.name }}"?
              <span v-if="brandToDelete.products_count > 0" class="mt-2 block font-semibold text-red-600">
                Uwaga: Ta marka jest przypisana do {{ brandToDelete.products_count }} produktów.
              </span>
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
              @click="deleteBrand" 
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
import { ref, onMounted, computed } from 'vue'
import axios from 'axios'
import { useAlertStore } from '../../stores/alertStore'

export default {
  name: 'AdminBrands',
  setup() {
    const alertStore = useAlertStore()
    const loading = ref(true)
    const brands = ref([])
    const showAddForm = ref(false)
    const showEditForm = ref(false)
    const showDeleteModal = ref(false)
    const brandToDelete = ref({})
    const searchQuery = ref('')
    const searchTimeout = ref(null)
    const form = ref({
      name: '',
      slug: ''
    })
    
    // Computed property for filtered brands
    const filteredBrands = computed(() => {
      if (!searchQuery.value) return brands.value
      
      const query = searchQuery.value.toLowerCase()
      return brands.value.filter(brand => 
        brand.name.toLowerCase().includes(query) || 
        brand.slug.toLowerCase().includes(query)
      )
    })
    
    // Debounced search
    const onSearchChange = () => {
      if (searchTimeout.value) {
        clearTimeout(searchTimeout.value)
      }
      
      searchTimeout.value = setTimeout(() => {
        // We're filtering client-side, so no need to fetch
        // Just let the computed property handle it
      }, 300)
    }
    
    // Fetch all brands
    const fetchBrands = async () => {
      try {
        loading.value = true
        const response = await axios.get('/api/admin/brands')
        brands.value = response.data
      } catch (error) {
        console.error('Error fetching brands:', error)
        alertStore.error('Wystąpił błąd podczas pobierania marek.')
      } finally {
        loading.value = false
      }
    }
    
    // Add new brand
    const addBrand = async () => {
      try {
        const response = await axios.post('/api/admin/brands', form.value)
        brands.value.push(response.data)
        alertStore.success('Marka została dodana.')
        closeForm()
      } catch (error) {
        console.error('Error adding brand:', error)
        alertStore.error('Wystąpił błąd podczas dodawania marki.')
      }
    }
    
    // Edit brand
    const editBrand = (brand) => {
      form.value = {
        id: brand.id,
        name: brand.name,
        slug: brand.slug
      }
      showEditForm.value = true
    }
    
    // Update brand
    const updateBrand = async () => {
      try {
        const response = await axios.put(`/api/admin/brands/${form.value.id}`, form.value)
        const index = brands.value.findIndex(brand => brand.id === form.value.id)
        if (index !== -1) {
          brands.value[index] = response.data
        }
        alertStore.success('Marka została zaktualizowana.')
        closeForm()
      } catch (error) {
        console.error('Error updating brand:', error)
        alertStore.error('Wystąpił błąd podczas aktualizacji marki.')
      }
    }
    
    // Confirm delete
    const confirmDelete = (brand) => {
      brandToDelete.value = brand
      showDeleteModal.value = true
    }
    
    // Delete brand
    const deleteBrand = async () => {
      try {
        await axios.delete(`/api/admin/brands/${brandToDelete.value.id}`)
        brands.value = brands.value.filter(brand => brand.id !== brandToDelete.value.id)
        alertStore.success('Marka została usunięta.')
        showDeleteModal.value = false
      } catch (error) {
        console.error('Error deleting brand:', error)
        alertStore.error('Wystąpił błąd podczas usuwania marki.')
      }
    }
    
    // Close form
    const closeForm = () => {
      form.value = {
        name: '',
        slug: ''
      }
      showAddForm.value = false
      showEditForm.value = false
    }
    
    // Generate slug
    const generateSlug = (name) => {
      return name.toLowerCase()
        .replace(/[^\w ]+/g, '')
        .replace(/ +/g, '-')
    }
    
    onMounted(() => {
      fetchBrands()
    })
    
    return {
      loading,
      brands,
      filteredBrands,
      showAddForm,
      showEditForm,
      showDeleteModal,
      brandToDelete,
      form,
      searchQuery,
      onSearchChange,
      fetchBrands,
      addBrand,
      editBrand,
      updateBrand,
      confirmDelete,
      deleteBrand,
      closeForm,
      generateSlug
    }
  }
}
</script> 