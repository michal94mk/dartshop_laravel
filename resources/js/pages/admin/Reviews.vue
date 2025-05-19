<template>
  <div class="p-6">
    <!-- Page Header -->
    <page-header 
      title="Zarządzanie recenzjami"
      subtitle="Lista wszystkich recenzji produktów z możliwością zatwierdzania i odrzucania."
    />
    
    <!-- Loading indicator -->
    <loading-spinner v-if="loading" />
    
    <!-- Search and Filters -->
    <search-filters
      v-else
      :filters="filters"
      :sort-options="sortOptions"
      search-label="Wyszukaj"
      search-placeholder="Szukaj recenzji..."
      @update:filters="filters = $event"
      @filter-change="filterReviews"
    >
      <template v-slot:filters>
        <div class="w-full sm:w-auto">
          <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
          <select
            id="status"
            name="status"
            v-model="filters.status"
            @change="filterReviews"
            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
          >
            <option value="">Wszystkie</option>
            <option value="pending">Oczekujące</option>
            <option value="approved">Zatwierdzone</option>
            <option value="rejected">Odrzucone</option>
          </select>
        </div>
        
        <div class="w-full sm:w-auto">
          <label for="rating" class="block text-sm font-medium text-gray-700">Min. Ocena</label>
          <select
            id="rating"
            name="rating"
            v-model="filters.minRating"
            @change="filterReviews"
            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
          >
            <option value="">Wszystkie</option>
            <option value="1">1 gwiazdka</option>
            <option value="2">2 gwiazdki</option>
            <option value="3">3 gwiazdki</option>
            <option value="4">4 gwiazdki</option>
            <option value="5">5 gwiazdek</option>
          </select>
        </div>
      </template>
    </search-filters>
    
    <!-- Reviews Table -->
    <div v-if="!loading && filteredReviews.length" class="bg-white shadow overflow-hidden sm:rounded-md mt-6">
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
              <div class="flex space-x-2">
                <button 
                  v-if="review.status !== 'approved'" 
                  @click="approveReview(review)" 
                  class="text-green-600 hover:text-green-900"
                >
                  Zatwierdź
                </button>
                <button 
                  v-if="review.status !== 'rejected'" 
                  @click="rejectReview(review)" 
                  class="text-red-600 hover:text-red-900"
                >
                  Odrzuć
                </button>
                <button 
                  @click="showReviewDetails(review)" 
                  class="text-indigo-600 hover:text-indigo-900"
                >
                  Szczegóły
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    
    <!-- No data message -->
    <no-data-message v-else-if="!loading" message="Brak recenzji do wyświetlenia" />
    
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
import { ref, computed, onMounted, reactive } from 'vue'
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
    
    // Sort options for filter component
    const sortOptions = [
      { value: 'created_at', label: 'Data dodania' },
      { value: 'rating', label: 'Ocena' },
      { value: 'product', label: 'Produkt' }
    ]
    
    // Filters
    const filters = reactive({
      status: '',
      search: '',
      minRating: '',
      sort_field: 'created_at',
      sort_direction: 'desc'
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
    
    // Filter reviews
    const filterReviews = () => {
      // Just triggers computed property updates
      console.log('Filtering with:', filters)
    }
    
    // Sort function
    const sortReviews = (a, b) => {
      const direction = filters.sort_direction === 'asc' ? 1 : -1
      
      switch (filters.sort_field) {
        case 'rating':
          return (a.rating - b.rating) * direction
        case 'product':
          return a.product.name.localeCompare(b.product.name) * direction
        case 'created_at':
        default:
          return (new Date(a.created_at) - new Date(b.created_at)) * direction
      }
    }
    
    // Filtered reviews
    const filteredReviews = computed(() => {
      return reviews.value
        .filter(review => {
          // Filter by status
          if (filters.status && review.status !== filters.status) {
            return false
          }
          
          // Filter by search text (product name or comment)
          if (filters.search) {
            const searchText = filters.search.toLowerCase()
            const productName = review.product.name.toLowerCase()
            const comment = review.comment.toLowerCase()
            
            if (!productName.includes(searchText) && !comment.includes(searchText)) {
              return false
            }
          }
          
          // Filter by minimum rating
          if (filters.minRating && review.rating < parseInt(filters.minRating)) {
            return false
          }
          
          return true
        })
        .sort(sortReviews)
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
      sortOptions,
      fetchReviews,
      approveReview,
      rejectReview,
      showReviewDetails,
      formatDate,
      getStatusName,
      filterReviews
    }
  }
}
</script> 