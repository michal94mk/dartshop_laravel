<template>
  <div class="relative" ref="searchContainer">
    <!-- Search Input -->
    <div class="relative">
      <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
        <svg class="h-6 w-6 text-indigo-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
        </svg>
      </div>
      <input
        ref="searchInput"
        v-model="searchQuery"
        type="text"
        placeholder="Wyszukaj produkty po nazwie..."
        class="block w-full pl-12 pr-12 py-4 text-lg border-2 border-white rounded-2xl leading-6 bg-white/90 backdrop-blur-sm placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 shadow-lg hover:shadow-xl transition-all duration-200"
        @input="onSearchInput"
        @focus="onFocus"
        @blur="onBlur"
        @keydown.down.prevent="navigateDown"
        @keydown.up.prevent="navigateUp"
        @keydown.enter.prevent="selectProduct"
        @keydown.escape="closeDropdown"
      />
      <!-- Loading spinner -->
      <div v-if="isLoading" class="absolute inset-y-0 right-0 pr-4 flex items-center">
        <svg class="animate-spin h-6 w-6 text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
      </div>
      <!-- Search hint -->
      <div v-if="!searchQuery && !isLoading" class="absolute inset-y-0 right-0 pr-4 flex items-center">
        <span class="text-sm text-gray-400 bg-gray-100 px-2 py-1 rounded-md">
          min. 3 znaki
        </span>
      </div>
    </div>

    <!-- Search Results Dropdown -->
    <transition
      enter-active-class="transition ease-out duration-200"
      enter-from-class="opacity-0 transform scale-95"
      enter-to-class="opacity-100 transform scale-100"
      leave-active-class="transition ease-in duration-150"
      leave-from-class="opacity-100 transform scale-100"
      leave-to-class="opacity-0 transform scale-95"
    >
      <div
        v-if="showDropdown && (searchResults.length > 0 || (searchQuery.length >= 3 && !isLoading))"
        class="absolute z-50 mt-2 w-full bg-white shadow-2xl max-h-96 rounded-2xl py-2 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none backdrop-blur-sm border border-gray-100"
      >
      <!-- Search Results -->
      <div v-if="searchResults.length > 0">
        <div class="px-4 py-2 border-b border-gray-100">
          <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">
            Znalezione produkty
          </p>
        </div>
        
        <div
          v-for="(product, index) in searchResults"
          :key="product.id"
          :class="[
            'cursor-pointer select-none relative py-4 px-4 hover:bg-gradient-to-r hover:from-indigo-50 hover:to-blue-50 transition-all duration-150',
            { 'bg-gradient-to-r from-indigo-50 to-blue-50 border-l-4 border-indigo-500': index === selectedIndex }
          ]"
          @click="goToProduct(product)"
          @mouseenter="selectedIndex = index"
        >
          <div class="flex items-center">
            <div class="flex-shrink-0 h-16 w-16">
              <img
                :src="getProductImageUrl(product.image_url, product.name, 64, 64)"
                :alt="product.name"
                class="h-16 w-16 rounded-xl object-cover shadow-md"
                @error="(e) => handleImageError(e, product.name, 64, 64)"
              />
            </div>
            <div class="ml-4 flex-1">
              <p class="text-base font-semibold text-gray-900 mb-1" v-html="highlightMatch(product.name)"></p>
              <div class="flex items-center justify-between">
                <p class="text-sm text-gray-500">{{ product.category?.name || 'Bez kategorii' }}</p>
                <!-- Price display with promotion support -->
                <div class="flex flex-col items-end">
                  <div v-if="hasPromotion(product)" class="space-y-1">
                    <!-- Original price (crossed out) -->
                    <div class="text-xs text-gray-400 line-through">
                      {{ formatPrice(product.price) }} zł
                    </div>
                    <!-- Promotional price -->
                    <div class="text-lg font-bold text-red-600">
                      {{ formatPrice(product.promotion_price) }} zł
                    </div>
                  </div>
                  <!-- Regular price (no promotion) -->
                  <div v-else class="text-lg font-bold text-indigo-600">
                    {{ formatPrice(product.price) }} zł
                  </div>
                </div>
              </div>
            </div>
            <div class="ml-2 flex-shrink-0">
              <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
              </svg>
            </div>
          </div>
        </div>
        
        <!-- View All Results Link -->
        <div class="border-t border-gray-100 py-3 px-4 bg-gradient-to-r from-gray-50 to-indigo-50">
          <button
            @click="viewAllResults"
            class="w-full text-center py-2 px-4 bg-gradient-to-r from-indigo-600 to-blue-600 text-white font-semibold rounded-xl hover:from-indigo-700 hover:to-blue-700 transition-all duration-200 shadow-md hover:shadow-lg"
          >
            Zobacz wszystkie wyniki ({{ totalResults }})
            <svg class="inline-block ml-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
            </svg>
          </button>
        </div>
      </div>

      <!-- No Results -->
      <div v-else-if="searchQuery.length >= 3 && !isLoading" class="py-8 px-4 text-center">
        <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        <p class="text-gray-500 font-medium">Brak wyników dla</p>
        <p class="text-gray-700 font-semibold text-lg">"{{ searchQuery }}"</p>
        <p class="text-sm text-gray-400 mt-2">Spróbuj użyć innych słów kluczowych</p>
      </div>
    </div>
    </transition>
  </div>
</template>

<script>
import { ref, watch, onMounted, onUnmounted, nextTick } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'
import { getProductImageUrl, handleImageError } from '../../utils/imageHelpers'

export default {
  name: 'ProductSearch',
  setup() {
    const router = useRouter()
    const searchContainer = ref(null)
    const searchInput = ref(null)
    const searchQuery = ref('')
    const searchResults = ref([])
    const isLoading = ref(false)
    const showDropdown = ref(false)
    const selectedIndex = ref(-1)
    const totalResults = ref(0)
    const searchTimeout = ref(null)

    // No computed properties needed

    // Methods
    const onSearchInput = () => {
      if (searchTimeout.value) {
        clearTimeout(searchTimeout.value)
      }

      if (searchQuery.value.length < 3) {
        searchResults.value = []
        showDropdown.value = false
        isLoading.value = false
        return
      }

      isLoading.value = true
      searchTimeout.value = setTimeout(() => {
        performSearch()
      }, 300) // 300ms debounce
    }

    const performSearch = async () => {
      if (searchQuery.value.length < 3) {
        isLoading.value = false
        return
      }

      try {
        const response = await axios.get('/api/products', {
          params: {
            search: searchQuery.value,
            per_page: 8 // Limit results for dropdown
          }
        })

        // Handle new API response format (BaseApiController)
        if (response.data.success && response.data.data) {
          // New format: { success: true, data: { data: [...], total: number } }
          const responseData = response.data.data;
          
          if (responseData.data && Array.isArray(responseData.data)) {
            searchResults.value = responseData.data;
            totalResults.value = responseData.total || responseData.data.length;
          } else if (Array.isArray(responseData)) {
            searchResults.value = responseData;
            totalResults.value = responseData.length;
          } else {
            searchResults.value = [];
            totalResults.value = 0;
          }
        } else if (response.data.data && Array.isArray(response.data.data)) {
          // Fallback for old format: { data: [...], total: number }
          searchResults.value = response.data.data;
          totalResults.value = response.data.total || response.data.data.length;
        } else if (Array.isArray(response.data)) {
          // Direct array response
          searchResults.value = response.data;
          totalResults.value = response.data.length;
        } else {
          searchResults.value = [];
          totalResults.value = 0;
        }

        showDropdown.value = true
        selectedIndex.value = -1
      } catch (error) {
        console.error('Search error:', error)
        searchResults.value = []
        totalResults.value = 0
      } finally {
        isLoading.value = false
      }
    }

    const onFocus = () => {
      if (searchQuery.value.length >= 3 && searchResults.value.length > 0) {
        showDropdown.value = true
      }
    }

    const onBlur = () => {
      // Delay hiding dropdown to allow clicks on results
      setTimeout(() => {
        showDropdown.value = false
        selectedIndex.value = -1
      }, 200)
    }

    const navigateDown = () => {
      if (selectedIndex.value < searchResults.value.length - 1) {
        selectedIndex.value++
      }
    }

    const navigateUp = () => {
      if (selectedIndex.value > 0) {
        selectedIndex.value--
      } else if (selectedIndex.value === 0) {
        selectedIndex.value = -1
      }
    }

    const selectProduct = () => {
      if (selectedIndex.value >= 0 && searchResults.value[selectedIndex.value]) {
        goToProduct(searchResults.value[selectedIndex.value])
      } else if (searchQuery.value.length >= 3) {
        viewAllResults()
      }
    }

    const goToProduct = (product) => {
      closeDropdown()
      router.push(`/products/${product.id}`)
    }

    const viewAllResults = () => {
      closeDropdown()
      router.push({
        path: '/products',
        query: { search: searchQuery.value }
      })
    }

    const closeDropdown = () => {
      showDropdown.value = false
      selectedIndex.value = -1
      searchInput.value?.blur()
    }

    const highlightMatch = (text) => {
      if (!searchQuery.value || searchQuery.value.length < 3) return text
      
      const regex = new RegExp(`(${searchQuery.value})`, 'gi')
      return text.replace(regex, '<mark class="bg-yellow-200">$1</mark>')
    }

    const formatPrice = (price) => {
      return parseFloat(price || 0).toFixed(2)
    }

    const hasPromotion = (product) => {
      return product.promotion_price && product.promotion_price < product.price
    }

    // Click outside handler
    const handleClickOutside = (event) => {
      if (searchContainer.value && !searchContainer.value.contains(event.target)) {
        closeDropdown()
      }
    }

    // Lifecycle
    onMounted(() => {
      document.addEventListener('click', handleClickOutside)
    })

    onUnmounted(() => {
      document.removeEventListener('click', handleClickOutside)
      if (searchTimeout.value) {
        clearTimeout(searchTimeout.value)
      }
    })

    return {
      searchContainer,
      searchInput,
      searchQuery,
      searchResults,
      isLoading,
      showDropdown,
      selectedIndex,
      totalResults,
      onSearchInput,
      onFocus,
      onBlur,
      navigateDown,
      navigateUp,
      selectProduct,
      goToProduct,
      viewAllResults,
      closeDropdown,
      highlightMatch,
      formatPrice,
      handleImageError,
      hasPromotion,
      getProductImageUrl // Added to expose the new helper
    }
  }
}
</script>

<style scoped>
mark {
  background: linear-gradient(120deg, #fef08a 0%, #fde047 100%);
  padding: 2px 4px;
  border-radius: 4px;
  font-weight: 600;
  color: #92400e;
}

/* Custom scrollbar for dropdown */
.overflow-auto::-webkit-scrollbar {
  width: 6px;
}

.overflow-auto::-webkit-scrollbar-track {
  background: #f1f5f9;
  border-radius: 3px;
}

.overflow-auto::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 3px;
}

.overflow-auto::-webkit-scrollbar-thumb:hover {
  background: #94a3b8;
}
</style> 