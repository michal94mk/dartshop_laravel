<template>
  <div class="min-h-screen bg-gradient-to-br from-orange-50 via-red-50 to-pink-50">
    <!-- Hero Section -->
    <div class="relative overflow-hidden bg-gradient-to-r from-red-600 to-pink-600 py-16">
      <div class="absolute inset-0 bg-black opacity-20"></div>
      <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl md:text-5xl font-bold text-white mb-4">
          Promocje & Oferty Specjalne
        </h1>
        <p class="text-xl text-red-100 max-w-2xl mx-auto">
          Odkryj najlepsze okazje i promocje na profesjonalny sprzęt do dart. Oszczędzaj kupując wysokiej jakości produkty.
        </p>
      </div>
    </div>

    <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
      <!-- Loading state -->
      <div v-if="loading" class="flex justify-center items-center py-20">
        <div class="relative">
          <div class="w-20 h-20 border-4 border-red-200 rounded-full animate-spin border-t-red-600"></div>
          <div class="absolute inset-0 flex items-center justify-center">
            <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
            </svg>
          </div>
        </div>
      </div>

      <!-- Error state -->
      <div v-else-if="error" class="text-center py-20">
        <div class="bg-white rounded-2xl shadow-lg p-8 max-w-md mx-auto border border-red-100">
          <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
          </div>
          <h3 class="text-lg font-semibold text-gray-900 mb-2">Ups! Coś poszło nie tak</h3>
          <p class="text-gray-600 mb-6">{{ error }}</p>
          <button @click="fetchPromotions" 
                  class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-xl text-white bg-gradient-to-r from-red-600 to-pink-600 hover:from-red-700 hover:to-pink-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all duration-200 shadow-lg hover:shadow-xl">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
            </svg>
            Spróbuj ponownie
          </button>
        </div>
      </div>

      <!-- No promotions -->
      <div v-else-if="promotions.length === 0" class="text-center py-20">
        <div class="bg-white rounded-2xl shadow-lg p-8 max-w-md mx-auto">
          <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
            </svg>
          </div>
          <h3 class="text-lg font-semibold text-gray-900 mb-2">Aktualnie brak promocji</h3>
          <p class="text-gray-600 mb-6">Nie mamy obecnie żadnych promocji, ale sprawdź ponownie wkrótce!</p>
          <router-link to="/products" 
                       class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-xl text-white bg-gradient-to-r from-orange-600 to-red-600 hover:from-orange-700 hover:to-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition-all duration-200 shadow-lg hover:shadow-xl">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M8 11v6a2 2 0 002 2h4a2 2 0 002-2v-6M8 11h8"/>
            </svg>
            Przejdź do produktów
          </router-link>
        </div>
      </div>

      <!-- Promotions content -->
      <div v-else>
        <!-- Stats Section -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
          <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100 text-center">
            <div class="w-12 h-12 bg-gradient-to-r from-red-400 to-pink-500 rounded-xl flex items-center justify-center mx-auto mb-4">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
              </svg>
            </div>
            <h3 class="text-2xl font-bold text-gray-900 mb-1">{{ promotions.length }}</h3>
            <p class="text-gray-600">Aktywnych promocji</p>
          </div>
          
          <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100 text-center">
            <div class="w-12 h-12 bg-gradient-to-r from-orange-400 to-red-500 rounded-xl flex items-center justify-center mx-auto mb-4">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
              </svg>
            </div>
            <h3 class="text-2xl font-bold text-gray-900 mb-1">{{ maxDiscount }}{{ maxDiscountType === 'percentage' ? '%' : ' zł' }}</h3>
            <p class="text-gray-600">Maksymalna zniżka</p>
          </div>
          
          <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100 text-center">
            <div class="w-12 h-12 bg-gradient-to-r from-yellow-400 to-orange-500 rounded-xl flex items-center justify-center mx-auto mb-4">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
              </svg>
            </div>
            <h3 class="text-2xl font-bold text-gray-900 mb-1">{{ totalProducts }}</h3>
            <p class="text-gray-600">Produktów w promocji</p>
          </div>
        </div>

        <!-- Promotions Section -->
        <div class="space-y-12">
          <div 
            v-for="promotion in promotions" 
            :key="promotion.id"
            class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100"
          >
            <!-- Promotion Header -->
            <div class="relative">
              <div 
                class="h-32 flex items-center justify-center"
                :style="{ background: `linear-gradient(135deg, ${promotion.badge_color || '#ef4444'}, ${adjustColor(promotion.badge_color || '#ef4444', -20)})` }"
              >
                <div class="text-center text-white">
                  <div class="text-3xl font-bold mb-1">
                    {{ promotion.discount_type === 'percentage' ? `-${promotion.discount_value}%` : `-${promotion.discount_value} zł` }}
                  </div>
                  <div class="text-xl">{{ promotion.title }}</div>
                </div>
              </div>
              <div v-if="promotion.badge_text" class="absolute top-4 right-4">
                <div 
                  class="px-3 py-1 rounded-full text-sm font-semibold text-gray-900"
                  :style="{ backgroundColor: lightenColor(promotion.badge_color || '#fbbf24') }"
                >
                  {{ promotion.badge_text }}
                </div>
              </div>
              <div v-if="promotion.is_featured" class="absolute top-4 left-4">
                <div class="bg-yellow-400 text-gray-900 px-3 py-1 rounded-full text-sm font-semibold flex items-center">
                  <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                  </svg>
                  WYRÓŻNIONA
                </div>
              </div>
            </div>
            
            <!-- Promotion Info -->
            <div class="p-6 border-b border-gray-100">
              <div class="flex items-center justify-between">
                <div>
                  <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ promotion.title }}</h3>
                  <p class="text-gray-600">{{ promotion.description || 'Sprawdź produkty objęte promocją i skorzystaj z atrakcyjnych cen!' }}</p>
                </div>
                <div class="text-right text-sm text-gray-500">
                  <div class="mb-1">
                    {{ promotion.ends_at ? `Ważne do: ${formatDate(promotion.ends_at)}` : 'Bez daty końcowej' }}
                  </div>
                  <div class="flex items-center justify-end">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                    {{ promotion.products?.length || 0 }} produktów
                  </div>
                </div>
              </div>
            </div>

            <!-- Products Grid -->
            <div v-if="promotion.products && promotion.products.length > 0" class="p-6">
              <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                <ProductCard
                  v-for="product in promotion.products.slice(0, 6)"
                  :key="product.id"
                  :product="product"
                />
              </div>
              
              <!-- Show More Button -->
              <div v-if="promotion.products.length > 6" class="mt-6 text-center">
                <button
                  @click="viewPromotionProducts(promotion)"
                  class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-xl text-white transition-all duration-200"
                  :style="{ background: `linear-gradient(135deg, ${promotion.badge_color || '#ef4444'}, ${adjustColor(promotion.badge_color || '#ef4444', -20)})` }"
                >
                  Zobacz wszystkie produkty ({{ promotion.products.length }})
                  <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                  </svg>
                </button>
              </div>
            </div>
            
            <!-- No Products Message -->
            <div v-else class="p-6 text-center text-gray-500">
              <p>Brak produktów w tej promocji</p>
            </div>
          </div>
        </div>

        <!-- Pagination -->
        <div v-if="pagination.total > pagination.per_page" class="mt-12 flex justify-center">
          <nav class="flex items-center space-x-2">
            <button
              @click="changePage(pagination.current_page - 1)"
              :disabled="pagination.current_page <= 1"
              class="px-3 py-2 rounded-md text-sm font-medium text-gray-500 hover:text-gray-700 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              Poprzednia
            </button>
            
            <button
              v-for="page in visiblePages"
              :key="page"
              @click="changePage(page)"
              :class="[
                'px-3 py-2 rounded-md text-sm font-medium',
                page === pagination.current_page
                  ? 'bg-red-600 text-white'
                  : 'text-gray-500 hover:text-gray-700 hover:bg-gray-100'
              ]"
            >
              {{ page }}
            </button>
            
            <button
              @click="changePage(pagination.current_page + 1)"
              :disabled="pagination.current_page >= pagination.last_page"
              class="px-3 py-2 rounded-md text-sm font-medium text-gray-500 hover:text-gray-700 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              Następna
            </button>
          </nav>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, reactive, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import ProductCard from '../components/ui/ProductCard.vue'

export default {
  name: 'Promotions',
  components: {
    ProductCard
  },
  setup() {
    const router = useRouter()
    const loading = ref(false)
    const error = ref(null)
    const promotions = ref([])
    
    const pagination = reactive({
      current_page: 1,
      last_page: 1,
      per_page: 12,
      total: 0
    })

    const maxDiscount = computed(() => {
      if (promotions.value.length === 0) return 0
      return Math.max(...promotions.value.map(p => p.discount_value))
    })

    const maxDiscountType = computed(() => {
      if (promotions.value.length === 0) return 'percentage'
      const maxPromotion = promotions.value.find(p => p.discount_value === maxDiscount.value)
      return maxPromotion?.discount_type || 'percentage'
    })

    const totalProducts = computed(() => {
      return promotions.value.reduce((total, promotion) => {
        return total + (promotion.products?.length || 0)
      }, 0)
    })

    const visiblePages = computed(() => {
      const pages = []
      const start = Math.max(1, pagination.current_page - 2)
      const end = Math.min(pagination.last_page, pagination.current_page + 2)
      
      for (let i = start; i <= end; i++) {
        pages.push(i)
      }
      
      return pages
    })

    const fetchPromotions = async () => {
      loading.value = true
      error.value = null
      
      try {
        const params = new URLSearchParams({
          page: pagination.current_page,
          per_page: pagination.per_page
        })

        const response = await window.axios.get(`/api/promotions?${params}`)
        
        promotions.value = response.data.data
        Object.assign(pagination, {
          current_page: response.data.current_page,
          last_page: response.data.last_page,
          per_page: response.data.per_page,
          total: response.data.total
        })
      } catch (err) {
        console.error('Błąd podczas pobierania promocji:', err)
        error.value = 'Nie udało się pobrać promocji. Spróbuj ponownie później.'
      } finally {
        loading.value = false
      }
    }

    const changePage = (page) => {
      if (page >= 1 && page <= pagination.last_page) {
        pagination.current_page = page
        fetchPromotions()
      }
    }

    const viewPromotionProducts = (promotion) => {
      // Przekieruj do strony produktów z filtrem promocji
      router.push({
        name: 'products',
        query: { promotion: promotion.id }
      })
    }



    const formatDate = (dateString) => {
      return new Date(dateString).toLocaleDateString('pl-PL')
    }

    // Funkcje pomocnicze do kolorów
    const hexToRgb = (hex) => {
      const result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex)
      return result ? {
        r: parseInt(result[1], 16),
        g: parseInt(result[2], 16),
        b: parseInt(result[3], 16)
      } : null
    }

    const rgbToHex = (r, g, b) => {
      return "#" + ((1 << 24) + (r << 16) + (g << 8) + b).toString(16).slice(1)
    }

    const adjustColor = (hex, amount) => {
      const rgb = hexToRgb(hex)
      if (!rgb) return hex
      
      const r = Math.max(0, Math.min(255, rgb.r + amount))
      const g = Math.max(0, Math.min(255, rgb.g + amount))
      const b = Math.max(0, Math.min(255, rgb.b + amount))
      
      return rgbToHex(r, g, b)
    }

    const lightenColor = (hex) => {
      return adjustColor(hex, 60)
    }

    onMounted(() => {
      fetchPromotions()
    })

    return {
      loading,
      error,
      promotions,
      pagination,
      maxDiscount,
      maxDiscountType,
      totalProducts,
      visiblePages,
      fetchPromotions,
      changePage,
      viewPromotionProducts,
      formatDate,
      adjustColor,
      lightenColor
    }
  }
}
</script> 