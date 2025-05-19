<template>
  <div class="p-6">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-semibold text-gray-900">Zarządzanie recenzjami</h1>
    </div>
    
    <!-- Loading indicator -->
    <div v-if="loading" class="flex justify-center my-12">
      <svg class="animate-spin h-10 w-10 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
      </svg>
    </div>
    
    <!-- Filter Controls -->
    <div v-else class="mb-6 bg-white p-4 rounded-md shadow-sm">
      <div class="flex flex-wrap gap-4 items-center">
        <div>
          <label for="status-filter" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
          <select 
            id="status-filter"
            v-model="filters.status"
            @change="applyFilters"
            class="block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
          >
            <option value="">Wszystkie</option>
            <option value="pending">Oczekujące</option>
            <option value="approved">Zatwierdzone</option>
            <option value="rejected">Odrzucone</option>
          </select>
        </div>
        <div>
          <label for="product-filter" class="block text-sm font-medium text-gray-700 mb-1">Produkt</label>
          <input 
            type="text"
            id="product-filter"
            v-model="filters.product"
            @input="applyFilters"
            placeholder="Wyszukaj produkt..."
            class="block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
          >
        </div>
        <div>
          <label for="rating-filter" class="block text-sm font-medium text-gray-700 mb-1">Min. Ocena</label>
          <select 
            id="rating-filter"
            v-model="filters.minRating"
            @change="applyFilters"
            class="block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
          >
            <option value="">Wszystkie</option>
            <option value="1">1 gwiazdka</option>
            <option value="2">2 gwiazdki</option>
            <option value="3">3 gwiazdki</option>
            <option value="4">4 gwiazdki</option>
            <option value="5">5 gwiazdek</option>
          </select>
        </div>
        <div class="flex-1 text-right self-end">
          <button 
            @click="clearFilters"
            class="py-2 px-4 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-md"
          >
            Wyczyść filtry
          </button>
        </div>
      </div>
    </div>
    
    <!-- Reviews Table -->
    <div v-if="!loading && filteredReviews.length" class="bg-white shadow overflow-hidden sm:rounded-md">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produkt</th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Użytkownik</th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ocena</th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Komentarz</th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data</th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Akcje</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="review in filteredReviews" :key="review.id">
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ review.id }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
              <a :href="`/products/${review.product.id}`" class="text-indigo-600 hover:text-indigo-900" target="_blank">
                {{ review.product.name }}
              </a>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
              {{ review.user ? review.user.name : 'Anonim' }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="flex items-center">
                <span class="text-yellow-400">
                  <span v-for="n in 5" :key="n" class="inline-block w-5">
                    <svg v-if="n <= review.rating" class="h-5 w-5 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                      <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                    </svg>
                    <svg v-else class="h-5 w-5 fill-current text-gray-300" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                      <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                    </svg>
                  </span>
                </span>
                <span class="ml-2 text-sm text-gray-700">{{ review.rating }}/5</span>
              </div>
            </td>
            <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate">{{ review.comment }}</td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                   :class="{
                     'bg-green-100 text-green-800': review.status === 'approved',
                     'bg-yellow-100 text-yellow-800': review.status === 'pending',
                     'bg-red-100 text-red-800': review.status === 'rejected'
                   }">
                {{ getStatusName(review.status) }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ formatDate(review.created_at) }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
              <button 
                v-if="review.status !== 'approved'" 
                @click="approveReview(review)" 
                class="text-green-600 hover:text-green-900 mr-2"
              >
                Zatwierdź
              </button>
              <button 
                v-if="review.status !== 'rejected'" 
                @click="rejectReview(review)" 
                class="text-red-600 hover:text-red-900 mr-2"
              >
                Odrzuć
              </button>
              <button 
                @click="showReviewDetails(review)" 
                class="text-indigo-600 hover:text-indigo-900"
              >
                Szczegóły
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <div v-else-if="!loading" class="bg-white shadow overflow-hidden sm:rounded-md p-6 text-center text-gray-500">
      Brak recenzji do wyświetlenia
    </div>
    
    <!-- Review Details Modal -->
    <div v-if="showDetailsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full flex items-center justify-center">
      <div class="relative mx-auto p-5 border w-full max-w-lg shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
          <h3 class="text-lg font-medium text-gray-900">Szczegóły recenzji</h3>
          <button @click="showDetailsModal = false" class="text-gray-400 hover:text-gray-500">
            <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
        
        <div v-if="selectedReview" class="space-y-4">
          <div>
            <h4 class="text-sm font-medium text-gray-500">ID</h4>
            <p class="mt-1 text-sm text-gray-900">{{ selectedReview.id }}</p>
          </div>
          
          <div>
            <h4 class="text-sm font-medium text-gray-500">Produkt</h4>
            <p class="mt-1 text-sm text-gray-900">{{ selectedReview.product.name }}</p>
          </div>
          
          <div>
            <h4 class="text-sm font-medium text-gray-500">Użytkownik</h4>
            <p class="mt-1 text-sm text-gray-900">{{ selectedReview.user ? selectedReview.user.name : 'Anonim' }}</p>
          </div>
          
          <div>
            <h4 class="text-sm font-medium text-gray-500">Status</h4>
            <p class="mt-1">
              <span class="px-2 py-1 text-xs font-semibold rounded-full"
                    :class="{
                      'bg-green-100 text-green-800': selectedReview.status === 'approved',
                      'bg-yellow-100 text-yellow-800': selectedReview.status === 'pending',
                      'bg-red-100 text-red-800': selectedReview.status === 'rejected'
                    }">
                {{ getStatusName(selectedReview.status) }}
              </span>
            </p>
          </div>
          
          <div>
            <h4 class="text-sm font-medium text-gray-500">Ocena</h4>
            <div class="mt-1 flex items-center">
              <span class="text-yellow-400">
                <span v-for="n in 5" :key="n" class="inline-block w-5">
                  <svg v-if="n <= selectedReview.rating" class="h-5 w-5 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                  </svg>
                  <svg v-else class="h-5 w-5 fill-current text-gray-300" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                  </svg>
                </span>
              </span>
              <span class="ml-2 text-sm text-gray-700">{{ selectedReview.rating }}/5</span>
            </div>
          </div>
          
          <div>
            <h4 class="text-sm font-medium text-gray-500">Data</h4>
            <p class="mt-1 text-sm text-gray-900">{{ formatDate(selectedReview.created_at) }}</p>
          </div>
          
          <div>
            <h4 class="text-sm font-medium text-gray-500">Komentarz</h4>
            <div class="mt-1 bg-gray-50 p-3 rounded text-sm text-gray-900">
              <p>{{ selectedReview.comment }}</p>
            </div>
          </div>
          
          <div class="flex justify-end space-x-3 mt-6">
            <button 
              v-if="selectedReview.status !== 'approved'" 
              @click="approveReview(selectedReview)" 
              class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
            >
              Zatwierdź
            </button>
            <button 
              v-if="selectedReview.status !== 'rejected'" 
              @click="rejectReview(selectedReview)" 
              class="bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
            >
              Odrzuć
            </button>
            <button 
              @click="showDetailsModal = false" 
              class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            >
              Zamknij
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'
import { useAlertStore } from '../../stores/alertStore'

export default {
  name: 'AdminReviews',
  setup() {
    const alertStore = useAlertStore()
    const loading = ref(true)
    const reviews = ref([])
    const showDetailsModal = ref(false)
    const selectedReview = ref(null)
    
    // Filters
    const filters = ref({
      status: '',
      product: '',
      minRating: ''
    })
    
    // Fetch all reviews
    const fetchReviews = async () => {
      try {
        loading.value = true
        const response = await axios.get('/api/admin/reviews')
        reviews.value = response.data
      } catch (error) {
        console.error('Error fetching reviews:', error)
        alertStore.error('Wystąpił błąd podczas pobierania recenzji.')
      } finally {
        loading.value = false
      }
    }
    
    // Approve review
    const approveReview = async (review) => {
      try {
        await axios.post(`/api/admin/reviews/${review.id}/approve`)
        const index = reviews.value.findIndex(r => r.id === review.id)
        if (index !== -1) {
          reviews.value[index].status = 'approved'
        }
        alertStore.success('Recenzja została zatwierdzona.')
        if (showDetailsModal.value) {
          showDetailsModal.value = false
        }
      } catch (error) {
        console.error('Error approving review:', error)
        alertStore.error('Wystąpił błąd podczas zatwierdzania recenzji.')
      }
    }
    
    // Reject review
    const rejectReview = async (review) => {
      try {
        await axios.post(`/api/admin/reviews/${review.id}/reject`)
        const index = reviews.value.findIndex(r => r.id === review.id)
        if (index !== -1) {
          reviews.value[index].status = 'rejected'
        }
        alertStore.success('Recenzja została odrzucona.')
        if (showDetailsModal.value) {
          showDetailsModal.value = false
        }
      } catch (error) {
        console.error('Error rejecting review:', error)
        alertStore.error('Wystąpił błąd podczas odrzucania recenzji.')
      }
    }
    
    // Show review details
    const showReviewDetails = (review) => {
      selectedReview.value = review
      showDetailsModal.value = true
    }
    
    // Format date
    const formatDate = (dateString) => {
      const options = { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' }
      return new Date(dateString).toLocaleDateString('pl-PL', options)
    }
    
    // Get status display name
    const getStatusName = (status) => {
      switch (status) {
        case 'approved': return 'Zatwierdzona'
        case 'pending': return 'Oczekująca'
        case 'rejected': return 'Odrzucona'
        default: return status
      }
    }
    
    // Apply filters
    const applyFilters = () => {
      // This is just a UI function to trigger the filteredReviews computed property
      console.log('Applying filters:', filters.value)
    }
    
    // Clear filters
    const clearFilters = () => {
      filters.value = {
        status: '',
        product: '',
        minRating: ''
      }
    }
    
    // Filtered reviews
    const filteredReviews = computed(() => {
      return reviews.value.filter(review => {
        // Filter by status
        if (filters.value.status && review.status !== filters.value.status) {
          return false
        }
        
        // Filter by product name
        if (filters.value.product && !review.product.name.toLowerCase().includes(filters.value.product.toLowerCase())) {
          return false
        }
        
        // Filter by minimum rating
        if (filters.value.minRating && review.rating < parseInt(filters.value.minRating)) {
          return false
        }
        
        return true
      })
    })
    
    onMounted(() => {
      fetchReviews()
    })
    
    return {
      loading,
      reviews,
      filteredReviews,
      showDetailsModal,
      selectedReview,
      filters,
      fetchReviews,
      approveReview,
      rejectReview,
      showReviewDetails,
      formatDate,
      getStatusName,
      applyFilters,
      clearFilters
    }
  }
}
</script> 