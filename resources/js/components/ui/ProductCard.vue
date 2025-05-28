<template>
  <div class="bg-white overflow-hidden shadow-lg rounded-2xl transition-all hover:shadow-xl group transform hover:-translate-y-2 duration-300 border border-gray-100 flex flex-col" style="aspect-ratio: 1 / 1.5;">
    <div class="relative h-4/5 overflow-hidden">
      <img 
        :src="product.image_url || 'https://via.placeholder.com/400x400/indigo/fff?text=' + encodeURIComponent(product.name)" 
        :alt="product.name" 
        class="h-full w-full object-cover group-hover:scale-110 transition-transform duration-500"
        loading="lazy"
      >
      <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
      
      <!-- Promotion Badge -->
      <div v-if="hasPromotion" class="absolute top-3 left-3">
        <div 
          class="px-2 py-1 rounded-full text-xs font-bold text-white shadow-lg"
          :style="{ backgroundColor: promotionBadgeColor }"
        >
          {{ promotionBadgeText || `${discountPercentage}% OFF` }}
        </div>
      </div>
      
      <!-- Product badge -->
      <div v-else class="absolute top-3 right-3 bg-white/90 backdrop-blur-sm px-2 py-1 rounded-full text-xs font-semibold text-blue-600">
        PRODUKT
      </div>
    </div>
    
    <div class="p-4 flex-1 flex flex-col justify-between">
      <div>
        <h3 class="text-base font-bold text-gray-900 line-clamp-2 mb-2 leading-tight">{{ product.name }}</h3>
        
        <!-- Reviews rating -->
        <div v-if="product.reviews_count > 0" class="mb-2">
          <StarRating 
            :model-value="product.average_rating" 
            :review-count="product.reviews_count"
            size="sm"
            show-text
            :precision="0.1"
          />
        </div>
        
        <p class="text-xs text-gray-600 line-clamp-2 mb-3 leading-relaxed">{{ product.short_description || product.description }}</p>
      </div>
      
      <div>
        <div class="flex items-center justify-between mb-3">
          <!-- Price section with promotion support -->
          <div class="flex flex-col">
            <div v-if="hasPromotion" class="space-y-1">
              <!-- Original price (crossed out) -->
              <div class="flex items-center space-x-2">
                <span class="text-sm text-gray-500 line-through">
                  {{ formatPrice(originalPrice) }} zł
                </span>
                <span class="text-xs text-red-600 font-medium bg-red-100 px-1.5 py-0.5 rounded">
                  -{{ discountPercentage }}%
                </span>
              </div>
              <!-- Promotional price -->
              <div class="text-lg font-bold text-red-600">
                {{ formatPrice(promotionalPrice) }} zł
              </div>
            </div>
            <!-- Regular price (no promotion) -->
            <div v-else class="text-lg font-bold text-blue-600">
              {{ formatPrice(originalPrice) }} zł
            </div>
          </div>
          <FavoriteButton 
            :product="product"
            buttonClasses="p-1.5 rounded-full border border-gray-300 text-gray-400 hover:text-red-500 hover:border-red-300 transition-colors duration-200"
            @favorite-added="handleFavoriteAdded"
            @favorite-removed="handleFavoriteRemoved"
          />
        </div>
        <div class="space-y-2">
          <button 
            @click="addToCart"
            :disabled="cartLoading" 
            class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-medium py-2.5 px-4 rounded-lg transition-all duration-200 text-sm"
          >
            <template v-if="cartLoading">
              <svg class="animate-spin w-4 h-4 mr-2 inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              Dodawanie...
            </template>
            <template v-else>
              <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 2.5M7 13l2.5 2.5m4.5-2.5V13"/>
              </svg>
              Dodaj do koszyka
            </template>
          </button>
          <router-link 
            :to="`/products/${product.id}`" 
            class="w-full block text-center py-2 px-4 border border-blue-600 text-blue-600 font-medium rounded-lg hover:bg-blue-50 transition-colors duration-200 text-sm"
          >
            Zobacz szczegóły
          </router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { computed, ref } from 'vue'
import { useRouter } from 'vue-router'
import FavoriteButton from './FavoriteButton.vue'
import StarRating from './StarRating.vue'
import { useCartStore } from '../../stores/cartStore'
import { useToast } from 'vue-toastification'

export default {
  name: 'ProductCard',
  components: {
    FavoriteButton,
    StarRating
  },
  props: {
    product: {
      type: Object,
      required: true
    }
  },
  setup(props) {
    const router = useRouter()
    const cartStore = useCartStore()
    const toast = useToast()
    
    // State
    const cartLoading = ref(false)

    // Computed properties for promotion logic
    const hasPromotion = computed(() => {
      return props.product.promotion_price && props.product.promotion_price < props.product.price
    })

    const originalPrice = computed(() => {
      return parseFloat(props.product.price)
    })

    const promotionalPrice = computed(() => {
      return hasPromotion.value ? parseFloat(props.product.promotion_price) : originalPrice.value
    })

    const savings = computed(() => {
      return hasPromotion.value ? originalPrice.value - promotionalPrice.value : 0
    })

    const discountPercentage = computed(() => {
      if (!hasPromotion.value) return 0
      return Math.round((savings.value / originalPrice.value) * 100)
    })

    const promotionBadgeText = computed(() => {
      return props.product.promotion?.badge_text
    })

    const promotionBadgeColor = computed(() => {
      return props.product.promotion?.badge_color || '#ef4444'
    })

    // Methods
    const formatPrice = (price) => {
      return parseFloat(price).toFixed(2)
    }

    const addToCart = async () => {
      cartLoading.value = true
      
      try {
        const success = await cartStore.addToCart(props.product.id, 1)
        if (success) {
          toast.success(`🛒 Dodano do koszyka: "${props.product.name}"`, {
            position: "top-center",
            timeout: 4000,
            closeOnClick: true,
            pauseOnFocusLoss: true,
            pauseOnHover: true,
            draggable: true,
            showCloseButtonOnHover: false,
            hideProgressBar: false,
            toastClassName: "cart-success-toast",
            bodyClassName: "cart-success-body",
            progressClassName: "cart-success-progress"
          })
        } else {
          toast.error('❌ Nie udało się dodać produktu do koszyka', {
            position: "top-center",
            timeout: 5000,
            closeOnClick: true,
            pauseOnHover: true,
            toastClassName: "cart-error-toast",
            bodyClassName: "cart-error-body"
          })
        }
      } catch (error) {
        toast.error('⚠️ Wystąpił błąd podczas dodawania produktu do koszyka', {
          position: "top-center",
          timeout: 5000,
          closeOnClick: true,
          pauseOnHover: true,
          toastClassName: "cart-error-toast",
          bodyClassName: "cart-error-body"
        })
        console.error('Error adding product to cart:', error)
      } finally {
        cartLoading.value = false
      }
    }

    const handleFavoriteAdded = (product) => {
      toast.success(`💖 Dodano do ulubionych: "${product.name}"`, {
        position: "top-center",
        timeout: 3500,
        closeOnClick: true,
        pauseOnHover: true,
        toastClassName: "favorite-success-toast",
        bodyClassName: "favorite-success-body",
        progressClassName: "favorite-success-progress"
      })
    }

    const handleFavoriteRemoved = (product) => {
      toast.info(`💔 Usunięto z ulubionych: "${product.name}"`, {
        position: "top-center",
        timeout: 3000,
        closeOnClick: true,
        pauseOnHover: true,
        toastClassName: "favorite-info-toast",
        bodyClassName: "favorite-info-body",
        progressClassName: "favorite-info-progress"
      })
    }

    return {
      hasPromotion,
      originalPrice,
      promotionalPrice,
      savings,
      discountPercentage,
      promotionBadgeText,
      promotionBadgeColor,
      cartLoading,
      formatPrice,
      addToCart,
      handleFavoriteAdded,
      handleFavoriteRemoved
    }
  }
}
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style> 