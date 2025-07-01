<template>
  <div class="min-h-screen bg-gradient-to-br from-indigo-50 via-blue-50 to-purple-50">
    <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
      <!-- Header -->
      <div class="text-center mb-12">
        <div class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-indigo-100 to-purple-100 rounded-full text-indigo-700 font-semibold text-sm mb-4">
          <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M8 11v6a2 2 0 002 2h4a2 2 0 002-2v-6M8 11h8"/>
          </svg>
          Szczegóły produktu
        </div>
      </div>

      <div v-if="loading" class="text-center py-16">
        <div class="bg-white shadow-xl rounded-2xl p-12 max-w-md mx-auto border border-gray-100">
          <div class="w-16 h-16 border-4 border-indigo-500 border-t-transparent rounded-full animate-spin mx-auto mb-6"></div>
          <h2 class="text-xl font-bold text-gray-900 mb-3">Ładowanie produktu...</h2>
          <p class="text-gray-600">Proszę czekać, pobieramy informacje o produkcie.</p>
        </div>
      </div>
      
      <div v-else-if="error" class="text-center py-16">
        <div class="bg-white shadow-xl rounded-2xl p-12 max-w-md mx-auto border border-gray-100">
          <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-6">
            <svg class="w-10 h-10 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </div>
          <h2 class="text-2xl font-bold text-red-800 mb-4">Wystąpił problem</h2>
          <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-lg mb-6">
            <p class="font-medium">{{ error }}</p>
          </div>
          <button 
            @click="fetchProduct" 
            class="inline-flex items-center px-6 py-3 border border-transparent text-sm font-medium rounded-lg text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 shadow-lg hover:shadow-xl"
          >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
            </svg>
            Spróbuj ponownie
          </button>
        </div>
      </div>
      
      <div v-else-if="product">
        <div class="bg-white shadow-xl rounded-2xl overflow-hidden border border-gray-100">
          <!-- Breadcrumbs -->
          <div class="px-8 py-6 border-b border-gray-200 bg-gray-50">
            <nav class="flex" aria-label="Breadcrumb">
              <ol role="list" class="flex items-center space-x-2">
                <li>
                  <router-link to="/" class="text-indigo-600 hover:text-indigo-700 transition-colors duration-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    <span class="sr-only">Strona główna</span>
                  </router-link>
                </li>
                <li>
                  <div class="flex items-center">
                    <svg class="w-3 h-3 text-gray-400 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                    <router-link to="/products" class="text-indigo-600 hover:text-indigo-700 font-medium transition-colors duration-200">Produkty</router-link>
                  </div>
                </li>
                <li v-if="product.category">
                  <div class="flex items-center">
                    <svg class="w-3 h-3 text-gray-400 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                    <router-link :to="`/products?category=${product.category.id}`" class="text-indigo-600 hover:text-indigo-700 font-medium transition-colors duration-200">
                      {{ product.category.name }}
                    </router-link>
                  </div>
                </li>
                <li>
                  <div class="flex items-center">
                    <svg class="w-3 h-3 text-gray-400 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                    <span class="text-gray-900 font-semibold">{{ product.name }}</span>
                  </div>
                </li>
              </ol>
            </nav>
          </div>
        
          <!-- Product details -->
          <div class="lg:flex">
            <!-- Product image -->
            <div class="lg:w-1/2">
              <div class="relative bg-gray-50 rounded-lg overflow-hidden" style="height: 500px;">
                <img 
                  :src="product.image_url || `https://via.placeholder.com/800x600/indigo/fff?text=${encodeURIComponent(product.name)}`" 
                  :alt="product.name" 
                  class="w-full h-full object-contain p-8"
                >
              </div>
            </div>
            
            <!-- Product info -->
            <div class="lg:w-1/2 p-8">
              <div class="mb-6 flex justify-between items-start">
                <div>
                  <h1 class="text-3xl font-bold text-gray-900">{{ product.name }}</h1>
                  <p v-if="product.brand" class="mt-1 text-sm text-gray-500">
                    {{ product.brand.name }}
                  </p>
                </div>
                <FavoriteButton 
                  :product="product" 
                  buttonClasses="p-2 bg-white border border-gray-200 rounded-full shadow hover:bg-gray-100 transition-colors duration-200" 
                  :showText="false"
                  @favorite-added="handleFavoriteAdded"
                  @favorite-removed="handleFavoriteRemoved"
                />
              </div>
              
              <!-- Reviews rating -->
              <div class="mb-6">
                <div v-if="product.reviews_count > 0">
                  <StarRating 
                    :model-value="product.average_rating" 
                    :review-count="product.reviews_count"
                    size="md"
                    show-text
                    :precision="0.1"
                  />
                </div>
                <div v-else class="text-sm text-gray-500">
                  Brak recenzji
                </div>
              </div>
            
              <div class="mb-6">
                <!-- Price section with promotion support -->
                <div v-if="hasPromotion(product)" class="space-y-2">
                  <!-- Original price (crossed out) -->
                  <div class="text-lg text-gray-500 line-through">
                    {{ formatPrice(product.price) }} zł
                  </div>
                  <!-- Promotional price -->
                  <div class="flex items-center space-x-3">
                    <p class="text-2xl font-bold text-red-600">{{ formatPrice(product.promotion_price) }} zł</p>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                      -{{ getDiscountPercentage(product) }}%
                    </span>
                  </div>
                  <!-- Savings amount -->
                  <div class="text-sm text-green-600 font-medium">
                    Oszczędzasz {{ formatPrice(product.savings) }} zł
                  </div>
                  <!-- Promotion badge -->
                  <div v-if="product.promotion && product.promotion.badge_text" class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold text-white" :style="{ backgroundColor: product.promotion.badge_color || '#ef4444' }">
                    {{ product.promotion.badge_text }}
                  </div>
                </div>
                <!-- Regular price (no promotion) -->
                <div v-else>
                  <p class="text-2xl font-bold text-indigo-600">{{ formatPrice(product.price) }} zł</p>
                </div>
              </div>
              
              <div class="mb-6">
                <h2 class="text-lg font-medium text-gray-900">Opis</h2>
                <div class="mt-2 text-gray-600 space-y-4">
                  <p>{{ product.description }}</p>
                </div>
              </div>
              
              <div class="mb-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-3 flex items-center">
                  <svg class="w-5 h-5 text-indigo-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                  </svg>
                  Dostępność
                </h2>
                <div class="flex items-center text-green-600 bg-green-50 rounded-lg p-3">
                  <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                  </svg>
                  <span class="font-medium">Produkt dostępny</span>
                </div>
              </div>
              
              <div class="pt-8 border-t border-gray-200">
                <div class="flex flex-col sm:flex-row gap-4">
                  <div class="sm:w-1/3">
                    <label for="quantity" class="block text-sm font-semibold text-gray-700 mb-2">Ilość</label>
                    <select 
                      id="quantity" 
                      v-model="quantity" 
                      class="block w-full py-3 px-4 border border-gray-300 bg-white rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm font-medium transition-colors duration-200"
                    >
                      <option v-for="i in 10" :key="i" :value="i">{{ i }}</option>
                    </select>
                  </div>
                  
                  <div class="flex-1 flex items-end">
                    <button 
                      @click="addToCart"
                      :disabled="cartLoading"
                      class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 border border-transparent rounded-lg py-4 px-6 flex items-center justify-center text-base font-semibold text-white hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5"
                    >
                      <template v-if="cartLoading">
                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Dodawanie...
                      </template>
                      <template v-else>
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m0 0L17 18"/>
                        </svg>
                        Dodaj do koszyka
                      </template>
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Reviews Section -->
        <div class="mt-12">
          <!-- Reviews List -->
          <ReviewsList
            :reviews="reviews"
            :statistics="reviewStatistics"
            :loading="reviewsLoading"
            :can-add-review="canAddReview"
            @add-review="showReviewForm = true"
          />
          
          <!-- Review Form Modal -->
          <div v-if="showReviewForm" class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
              <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
              </div>
              
              <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
              
              <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                <ReviewForm
                  :product-id="product.id"
                  @success="handleReviewSuccess"
                  @cancel="showReviewForm = false"
                />
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <div v-else class="text-center py-16">
        <div class="bg-white shadow-xl rounded-2xl p-12 max-w-md mx-auto border border-gray-100">
          <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
            <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M8 11v6a2 2 0 002 2h4a2 2 0 002-2v-6M8 11h8"/>
            </svg>
          </div>
          <h3 class="text-xl font-bold text-gray-900 mb-3">Nie znaleziono produktu</h3>
          <p class="text-gray-600 mb-6">Produkt o podanym identyfikatorze nie istnieje.</p>
          <router-link 
            to="/products" 
            class="inline-flex items-center px-6 py-3 border border-transparent text-sm font-medium rounded-lg text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 shadow-lg hover:shadow-xl"
          >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Wróć do listy produktów
          </router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted, watch } from 'vue'
import { useRoute } from 'vue-router'
import { useAuthStore } from '../stores/authStore'
import { useProductStore } from '../stores/productStore'
import { useCartStore } from '../stores/cartStore'
import { useFavoriteStore } from '../stores/favoriteStore'
import { useAlertStore } from '../stores/alertStore'
import FavoriteButton from '../components/ui/FavoriteButton.vue'
import StarRating from '../components/ui/StarRating.vue'
import ReviewsList from '../components/ui/ReviewsList.vue'
import ReviewForm from '../components/ui/ReviewForm.vue'
import axios from 'axios'

export default {
  name: 'ProductDetails',
  components: {
    FavoriteButton,
    StarRating,
    ReviewsList,
    ReviewForm
  },
  props: {
    id: {
      type: String,
      required: true
    }
  },
  setup(props) {
    const route = useRoute()
    const alertStore = useAlertStore()
    const authStore = useAuthStore()
    const productStore = useProductStore()
    const cartStore = useCartStore()
    const favoriteStore = useFavoriteStore()

    // State
    const product = ref(null)
    const loading = ref(true)
    const error = ref(null)
    const quantity = ref(1)
    const cartLoading = ref(false)
    
    // Reviews state
    const reviews = ref([])
    const reviewStatistics = ref({})
    const reviewsLoading = ref(false)
    const canAddReview = ref(false)
    const showReviewForm = ref(false)
    const existingReviewInfo = ref(null)

    // Methods
    const fetchProduct = async () => {
      loading.value = true
      error.value = null
      
      try {
        console.log('Fetching product with ID:', route.params.id)
        const fetchedProduct = await productStore.fetchProductById(route.params.id)
        console.log('Product returned from store:', fetchedProduct)
        
        if (fetchedProduct) {
          product.value = fetchedProduct
          document.title = `${fetchedProduct.name} - DartShop`
          await fetchReviews()
          await checkCanReview()
        } else {
          error.value = 'Nie udało się załadować produktu. Produkt nie został znaleziony.'
        }
      } catch (err) {
        error.value = 'Wystąpił błąd podczas ładowania produktu.'
        console.error('Error fetching product:', err)
      } finally {
        loading.value = false
      }
    }

    const fetchReviews = async () => {
      if (!product.value) return
      
      reviewsLoading.value = true
      try {
        const response = await axios.get(`/api/products/${product.value.id}/reviews`)
        reviews.value = response.data.reviews
        reviewStatistics.value = response.data.statistics
      } catch (error) {
        console.error('Error fetching reviews:', error)
      } finally {
        reviewsLoading.value = false
      }
    }

    const checkCanReview = async () => {
      if (!authStore.isLoggedIn || !product.value) {
        canAddReview.value = false;
        return;
      }

      try {
        const response = await axios.get(`/api/products/${product.value.id}/can-review`);
        canAddReview.value = response.data.can_review;
        if (response.data.existing_review) {
          existingReviewInfo.value = response.data.existing_review;
        }
      } catch (error) {
        console.error('Error checking review permissions:', error);
        canAddReview.value = false;
      }
    }

    const formatPrice = (price) => {
      return parseFloat(price).toFixed(2)
    }

    const addToCart = async () => {
      cartLoading.value = true
      
      try {
        const success = await cartStore.addToCart(product.value.id, quantity.value)
        if (success) {
          // Reset quantity after successful add
          quantity.value = 1
          alertStore.success(`🛒 Świetnie! "${product.value.name}" został dodany do koszyka (${quantity.value > 1 ? quantity.value + ' szt.' : ''})!`, 4000)
        } else {
          alertStore.error('😞 Ups! Nie udało się dodać produktu do koszyka. Spróbuj ponownie!', 5000)
        }
      } catch (error) {
        alertStore.error('😞 Wystąpił błąd podczas dodawania produktu. Spróbuj ponownie!', 5000)
        console.error('Error adding product to cart:', error)
      } finally {
        cartLoading.value = false
      }
    }

    const handleFavoriteAdded = (product) => {
      alertStore.success(`😍 Udało się! "${product.name}" jest teraz w Twoich ulubionych!`, 3500)
    }

    const handleFavoriteRemoved = (product) => {
      alertStore.info(`💭 Produkt "${product.name}" został usunięty z ulubionych.`, 3000)
    }

    const handleReviewSuccess = (newReview) => {
      showReviewForm.value = false
      alertStore.success('🎉 Świetnie! Twoja recenzja została dodana i czeka na zatwierdzenie!', 4000)
      // Refresh reviews and check permissions
      fetchReviews()
      checkCanReview()
    }

    // Promotion helper functions
    const hasPromotion = (product) => {
      return product && product.promotion_price !== undefined && 
             product.promotion_price !== null && 
             parseFloat(product.promotion_price) < parseFloat(product.price)
    }

    const getDiscountPercentage = (product) => {
      if (!hasPromotion(product)) return 0
      const originalPrice = parseFloat(product.price)
      const promotionalPrice = parseFloat(product.promotion_price)
      return Math.round(((originalPrice - promotionalPrice) / originalPrice) * 100)
    }

    // Lifecycle
    onMounted(async () => {
      await favoriteStore.initializeFavorites()
      fetchProduct()
    })

    watch(() => route.params.id, () => {
      fetchProduct()
    })

    // Watch for auth state changes
    watch(() => authStore.isLoggedIn, () => {
      checkCanReview();
    })

    return {
      product,
      loading,
      error,
      quantity,
      cartLoading,
      reviews,
      reviewStatistics,
      reviewsLoading,
      canAddReview,
      showReviewForm,
      authStore,
      fetchProduct,
      formatPrice,
      addToCart,
      handleFavoriteAdded,
      handleFavoriteRemoved,
      handleReviewSuccess,
      hasPromotion,
      getDiscountPercentage,
      existingReviewInfo
    }
  }
}
</script>

<style scoped>
/* Custom styles for product details */
</style> 