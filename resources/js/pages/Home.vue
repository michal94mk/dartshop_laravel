<template>
  <div class="min-h-screen bg-gradient-to-br from-indigo-50 via-blue-50 to-purple-50">
    <!-- Hero section -->
    <div class="relative overflow-hidden bg-gradient-to-r from-indigo-600 to-purple-600 py-8 lg:py-12">
      <div class="absolute inset-0 bg-black opacity-20"></div>
      <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="lg:grid lg:grid-cols-2 lg:gap-12 items-center">
          <div class="text-center lg:text-left">
            <h1 class="text-4xl md:text-6xl font-bold text-white mb-6 leading-tight">
              <span class="block">Najwyższej jakości</span>
              <span class="block text-transparent bg-clip-text bg-gradient-to-r from-yellow-400 to-orange-400">akcesoria do dart</span>
            </h1>
            <p class="text-xl text-indigo-100 mb-8 max-w-2xl lg:max-w-none">
              Odkryj profesjonalne lotki, tarcze i akcesoria do dart. Sprzęt dla początkujących i zaawansowanych graczy.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
              <router-link to="/products" 
                           class="inline-flex items-center px-8 py-4 border border-transparent text-base font-semibold rounded-xl text-indigo-600 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-white transition-all duration-200 shadow-lg hover:shadow-xl">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M8 11v6a2 2 0 002 2h4a2 2 0 002-2v-6M8 11h8"/>
                </svg>
                Przeglądaj produkty
              </router-link>
              <router-link to="/promotions" 
                           class="inline-flex items-center px-8 py-4 border-2 border-white text-base font-semibold rounded-xl text-white bg-transparent hover:bg-white hover:text-indigo-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-white transition-all duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                </svg>
                Zobacz promocje
              </router-link>
            </div>
          </div>
          <div class="mt-8 lg:mt-0">
            <div class="relative h-64 sm:h-80 lg:h-[400px]">
              <img 
                class="w-full h-full rounded-2xl shadow-2xl object-cover transition-opacity duration-300" 
                src="/img/hero_image.jpg" 
                alt="Profesjonalne akcesoria do dart - lotki, tarcze i akcesoria" 
                loading="eager"
                onerror="this.src='data:image/svg+xml;base64,' + btoa('<svg width=800 height=600 xmlns=http://www.w3.org/2000/svg><rect width=100% height=100% fill=#4f46e5/><text x=50% y=50% text-anchor=middle dy=.3em font-family=Arial font-size=48 fill=white>DartShop Hero</text></svg>');this.alt='DartShop - Profesjonalne akcesoria do dart'"
              >
              <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent rounded-2xl"></div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Featured products section -->
    <section class="py-16">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
          <div class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-indigo-100 to-purple-100 rounded-full text-indigo-700 font-semibold text-sm mb-4">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
            </svg>
            Najnowsze produkty
          </div>
          <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
            Najnowsze produkty w naszym sklepie
          </h2>
          <p class="text-xl text-gray-600 max-w-2xl mx-auto">
            Sprawdź nasze ostatnio dodane produkty i odkryj profesjonalny sprzęt do dart.
          </p>
        </div>

        <div class="mt-10">
          <!-- Test loading state -->
          <div v-if="productStore.loading" class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
            <!-- Simple loading spinner -->
            <div class="col-span-full text-center py-16">
              <div class="w-12 h-12 border-4 border-indigo-500 border-t-transparent rounded-full animate-spin mx-auto"></div>
              <p class="mt-4 text-gray-600">Ładowanie produktów...</p>
            </div>
          </div>
          
          <div v-else-if="productStore.error" class="text-center py-10">
            <p class="text-red-500">{{ productStore.error }}</p>
            <button @click="loadLatestProducts" class="mt-4 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200">
              Spróbuj ponownie
            </button>
          </div>
          
          <div v-else-if="productStore.latestProducts && productStore.latestProducts.length > 0" class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
            <!-- Display API products -->
            <div v-for="product in productStore.latestProducts" :key="product.id" class="bg-white overflow-hidden shadow-lg rounded-2xl transition-all hover:shadow-xl group transform hover:-translate-y-2 duration-300 border border-gray-100 flex flex-col" style="aspect-ratio: 1 / 1.5;">
              <div class="relative h-4/5 overflow-hidden">
                <img 
                  :src="getProductImageUrl(product.image_url, product.name)" 
                  :alt="product.name" 
                  class="h-full w-full object-cover group-hover:scale-110 transition-transform duration-500"
                  loading="lazy"
                  @error="(e) => handleImageError(e, product.name)"
                >
                <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                
                <!-- Promotion Badge -->
                <div v-if="hasPromotion(product)" class="absolute top-3 left-3">
                  <div 
                    class="px-2 py-1 rounded-full text-xs font-bold text-white shadow-lg"
                    :style="{ backgroundColor: getPromotionBadgeColor(product) }"
                  >
                    {{ getPromotionBadgeText(product) || `${getDiscountPercentage(product)}% OFF` }}
                  </div>
                </div>
                
                <!-- Product badge -->
                <div v-else class="absolute top-3 right-3 bg-white/90 backdrop-blur-sm px-2 py-1 rounded-full text-xs font-semibold text-blue-600">
                  {{ product.category ? product.category.name : 'PRODUKT' }}
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
                  <div v-else class="mb-2">
                    <div class="flex items-center text-gray-400 text-sm">
                      <StarRating 
                        :model-value="0" 
                        size="sm"
                        :precision="0.1"
                      />
                      <span class="ml-2 text-xs">Brak recenzji</span>
                    </div>
                  </div>
                  
                  <p class="text-xs text-gray-600 line-clamp-2 mb-3 leading-relaxed">{{ product.short_description || product.description }}</p>
                </div>
                
                <div>
                  <div class="flex items-center justify-between mb-3">
                    <!-- Price section with promotion support -->
                    <div class="flex flex-col">
                      <div v-if="hasPromotion(product)" class="space-y-1">
                        <!-- Original price (crossed out) -->
                        <div class="flex items-center space-x-2">
                          <span class="text-sm text-gray-500 line-through">
                            {{ formatPrice(product.price) }} zł
                          </span>
                          <span class="text-xs text-red-600 font-medium bg-red-100 px-1.5 py-0.5 rounded">
                            -{{ getDiscountPercentage(product) }}%
                          </span>
                        </div>
                        <!-- Promotional price -->
                        <div class="text-lg font-bold text-red-600">
                          {{ formatPrice(product.promotion_price) }} zł
                        </div>
                      </div>
                      <!-- Regular price (no promotion) -->
                      <div v-else class="text-lg font-bold text-blue-600">
                        {{ formatPrice(product.price) }} zł
                      </div>
                    </div>
                    <FavoriteButton 
                      :product="product"
                      buttonClasses="p-1.5 rounded-full border border-gray-300 text-gray-400 hover:text-red-500 hover:border-red-300 transition-colors duration-200"
                      @favorite-added="handleFavoriteAdded"
                      @favorite-removed="handleFavoriteRemoved"
                    />
                  </div>
                  <!-- Local success message for cart -->
                  <transition name="success-fade">
                    <div v-if="hasCartSuccessMessage(product.id)" class="mb-3 bg-green-50 border border-green-200 rounded-lg p-2 text-center">
                      <p class="text-green-700 text-xs font-medium">🛒 Dodano do koszyka!</p>
                    </div>
                  </transition>
                  
                  <!-- Local success message for favorites -->
                  <transition name="success-fade">
                    <div v-if="hasFavoriteSuccessMessage(product.id)" class="mb-3 bg-pink-50 border border-pink-200 rounded-lg p-2 text-center">
                      <p class="text-pink-700 text-xs font-medium">{{ favoriteSuccessMessages[product.id] }}</p>
                    </div>
                  </transition>
                  
                  <div class="space-y-2">
                    <button 
                      @click="addToCart(product)"
                      :disabled="isCartLoading(product.id)"
                      :class="{ 'opacity-75 cursor-not-allowed': isCartLoading(product.id) }"
                      class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-medium py-2.5 px-4 rounded-lg transition-all duration-200 text-sm"
                    >
                      <template v-if="isCartLoading(product.id)">
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
                      :to="{ name: 'product-details', params: { id: product.id }}"
                      class="block w-full text-center bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-2.5 px-4 rounded-lg transition-all duration-200 text-sm"
                    >
                      <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                      </svg>
                      Szczegóły
                    </router-link>
                  </div>
                </div>
              </div>
            </div>
          </div>
          
          <div v-else class="text-center py-16">
            <div class="max-w-md mx-auto">
              <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
              </svg>
              <h3 class="text-lg font-medium text-gray-900 mb-2">Brak produktów</h3>
              <p class="text-gray-500 mb-6">Nie znaleziono żadnych produktów do wyświetlenia.</p>
              <button @click="loadLatestProducts" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                Odśwież
              </button>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Categories section -->
    <section class="bg-white py-16">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
          <div class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-purple-100 to-indigo-100 rounded-full text-purple-700 font-semibold text-sm mb-4">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
            </svg>
            Kategorie produktów
          </div>
          <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
            Wszystko czego potrzebujesz
          </h2>
          <p class="text-xl text-gray-600 max-w-2xl mx-auto">
            Przeglądaj nasze kategorie i znajdź to, czego szukasz - od lotki po profesjonalne tarcze.
          </p>
        </div>

        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
          <router-link 
            v-for="category in categories" 
            :key="category.id" 
            :to="`/products?category=${category.id}`"
            class="group relative bg-white rounded-xl border border-gray-200 p-8 hover:shadow-lg hover:border-gray-300 transition-all duration-200 cursor-pointer"
          >
            <!-- Content -->
            <div class="space-y-4">
              <div>
                <h3 class="text-xl font-bold text-gray-900 group-hover:text-indigo-600 transition-colors duration-200">
                  {{ category.name }}
                </h3>
                <p class="text-sm text-gray-500 mt-1">
                  {{ getCategoryProductCount(category) }} dostępnych produktów
                </p>
              </div>
              
              <!-- Simple decorative line -->
              <div class="w-12 h-1 bg-gray-200 group-hover:bg-indigo-500 transition-colors duration-200 rounded-full"></div>
              
              <!-- Call to action -->
              <div class="flex items-center text-gray-600 group-hover:text-indigo-600 transition-colors duration-200">
                <span class="text-sm font-medium">Przeglądaj kategorię</span>
                <svg class="w-4 h-4 ml-2 transform group-hover:translate-x-1 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
              </div>
            </div>
          </router-link>
        </div>
      </div>
    </section>

    <!-- Testimonials section -->
    <section class="bg-gradient-to-r from-indigo-600 to-purple-600 py-16">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
          <div class="inline-flex items-center px-4 py-2 bg-white/20 backdrop-blur-sm rounded-full text-white font-semibold text-sm mb-4">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
            </svg>
            Opinie klientów
          </div>
          <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">
            Co mówią o nas klienci
          </h2>
          <p class="text-xl text-indigo-100 max-w-2xl mx-auto">
            Przeczytaj, co sądzą o naszych produktach i usługach zadowoleni klienci.
          </p>
        </div>

        <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3">
          <div v-if="reviewStore.loading" class="col-span-full text-center py-10">
            <div class="w-12 h-12 border-4 border-white border-t-transparent rounded-full animate-spin mx-auto"></div>
            <p class="mt-2 text-indigo-100">Ładowanie recenzji...</p>
          </div>
          
          <div v-else-if="reviewStore.error" class="col-span-full text-center py-10">
            <p class="text-red-200">{{ reviewStore.error }}</p>
            <button @click="loadLatestReviews" class="mt-4 inline-flex items-center px-4 py-2 border border-white text-sm font-medium rounded-md text-white bg-white/20 hover:bg-white/30">
              Spróbuj ponownie
            </button>
          </div>
          
          <div v-else-if="reviewStore.hasReviews" v-for="review in reviewStore.latestReviews" :key="review.id" class="bg-white/10 backdrop-blur-sm p-6 rounded-2xl border border-white/20 hover:bg-white/20 transition-all duration-300">
            <div class="flex items-center mb-4">
              <div class="w-12 h-12 bg-gradient-to-r from-yellow-400 to-orange-500 rounded-full flex items-center justify-center text-white font-bold text-lg">
                {{ review.user.name.charAt(0) }}
              </div>
              <div class="ml-4">
                <h3 class="text-lg font-semibold text-white">{{ review.user.name }}</h3>
                <div class="flex text-yellow-400 mb-1">
                  <span v-for="i in 5" :key="i" class="mr-1">
                    <svg class="w-4 h-4" :class="i <= review.rating ? 'text-yellow-400' : 'text-gray-400'" fill="currentColor" viewBox="0 0 20 20">
                      <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                  </span>
                </div>
                <p class="text-sm text-indigo-200">{{ review.product.name }}</p>
              </div>
            </div>
            <h4 class="text-white font-semibold mb-2">"{{ review.title }}"</h4>
            <p class="text-indigo-100 leading-relaxed">"{{ review.content }}"</p>
            <div class="mt-3 text-xs text-indigo-200">
              {{ formatDate(review.created_at) }}
            </div>
          </div>
          
          <!-- Fallback message if no reviews -->
          <div v-else class="col-span-full text-center py-10">
            <p class="text-indigo-100">Brak recenzji do wyświetlenia.</p>
          </div>
        </div>
      </div>
    </section>


  </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue';
import { useProductStore } from '../stores/productStore';
import { useCartStore } from '../stores/cartStore';
import { useFavoriteStore } from '../stores/favoriteStore';
import { useCategoryStore } from '../stores/categoryStore';
import { useAlertStore } from '../stores/alertStore';
import { useReviewStore } from '../stores/reviewStore';
import { getProductImageUrl, handleImageError } from '../utils/imageHelpers';
import FavoriteButton from '../components/ui/FavoriteButton.vue';
import StarRating from '../components/ui/StarRating.vue';
import { useToast } from 'vue-toastification';

export default {
  name: 'HomePage',
  components: {
    FavoriteButton,
    StarRating
  },
  setup() {
    const cartSuccessMessages = ref({}); // Track success messages per product
    const favoriteSuccessMessages = ref({}); // Track favorite messages per product
    
    // Stores
    const productStore = useProductStore();
    const cartStore = useCartStore();
    const favoriteStore = useFavoriteStore();
    const categoryStore = useCategoryStore();
    const toast = useToast();
    const alertStore = useAlertStore();
    const reviewStore = useReviewStore();
    
    // Computed
    const categories = computed(() => {
      return categoryStore.categoriesWithProducts;
    });
    
    // Methods
    const loadLatestProducts = () => {
      productStore.fetchLatestProducts();
    };
    
    const loadLatestReviews = () => {
      reviewStore.fetchLatestReviews();
    };
    
    const getCategoryProductCount = (category) => {
      return category.products_count || 0;
    };
    
    const formatPrice = (price) => {
      if (price === null || price === undefined || isNaN(price)) {
        return '0.00';
      }
      return parseFloat(price).toFixed(2);
    };
    
    const addToCart = async (product) => {
      try {
        const success = await cartStore.addToCart(product.id);
        if (success) {
          cartSuccessMessages.value[product.id] = true;
          setTimeout(() => {
            delete cartSuccessMessages.value[product.id];
          }, 2000);
        } else {
          alertStore.error('😞 Ups! Nie udało się dodać produktu do koszyka. Spróbuj ponownie!', 5000)
        }
      } catch (error) {
        alertStore.error('😞 Wystąpił błąd podczas dodawania produktu. Spróbuj ponownie!', 5000)
        console.error('Error adding product to cart:', error);
      }
    };
    
    const isCartLoading = (productId) => {
      return cartStore.isLoadingProduct(productId);
    };
    
    const toggleFavorite = (product) => {
      favoriteStore.toggleFavoriteItem(product);
    };
    
    const isInFavorites = (productId) => {
      return favoriteStore.isInFavorites(productId);
    };
    
    const handleFavoriteAdded = (product) => {
      favoriteSuccessMessages.value[product.id] = '😍 Dodano do ulubionych!';
      setTimeout(() => {
        delete favoriteSuccessMessages.value[product.id];
      }, 2500);
    };
    
    const handleFavoriteRemoved = (product) => {
      favoriteSuccessMessages.value[product.id] = '💭 Usunięto z ulubionych';
      setTimeout(() => {
        delete favoriteSuccessMessages.value[product.id];
      }, 2000);
    };
    
    const hasPromotion = (product) => {
      return product.promotion_price && parseFloat(product.promotion_price) < parseFloat(product.price);
    };
    
    const getDiscountPercentage = (product) => {
      if (!hasPromotion(product)) return 0;
      const originalPrice = parseFloat(product.price);
      const promotionalPrice = parseFloat(product.promotion_price);
      return Math.round(((originalPrice - promotionalPrice) / originalPrice) * 100);
    };
    
    const getPromotionBadgeColor = (product) => {
      if (hasPromotion(product)) {
        const discountPercentage = getDiscountPercentage(product);
        if (discountPercentage > 50) return '#ef4444';
        if (discountPercentage > 30) return '#f97316';
        if (discountPercentage > 10) return '#eab308';
        return '#22c55e';
      }
      return '#6b7280';
    };
    
    const getPromotionBadgeText = (product) => {
      if (hasPromotion(product)) {
        const discountPercentage = getDiscountPercentage(product);
        if (discountPercentage > 50) return 'Duża zniżka';
        if (discountPercentage > 30) return 'Zniżka';
        if (discountPercentage > 10) return 'Okazja';
        return 'Promocja';
      }
      return null;
    };
    
    const formatDate = (date) => {
      const formattedDate = new Date(date).toLocaleDateString('pl-PL', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
      });
      return formattedDate;
    };
    
    const hasCartSuccessMessage = (productId) => {
      return cartSuccessMessages.value[productId] || false;
    };
    
    const hasFavoriteSuccessMessage = (productId) => {
      return favoriteSuccessMessages.value[productId] || false;
    };
    
    const forceReload = () => {
      productStore.forceReloadProducts();
    };
    
    // Lifecycle hooks
    onMounted(async () => {
      console.log('Home.vue mounted');
      
      // Load categories first
      try {
        await categoryStore.fetchCategories();
        console.log('Categories loaded in Home.vue:', categoryStore.categories.length);
      } catch (error) {
        console.error('Error loading categories in Home.vue:', error);
      }
      
      // Load products and other data
      await loadLatestProducts();
      await loadLatestReviews();
    });
    
    return {
      cartSuccessMessages,
      favoriteSuccessMessages,
      categories,
      productStore,
      cartStore,
      favoriteStore,
      categoryStore,
      reviewStore,
      loadLatestProducts,
      loadLatestReviews,
      getCategoryProductCount,
      formatPrice,
      addToCart,
      isCartLoading,
      toggleFavorite,
      isInFavorites,
      handleFavoriteAdded,
      handleFavoriteRemoved,
      hasPromotion,
      getDiscountPercentage,
      getPromotionBadgeColor,
      getPromotionBadgeText,
      formatDate,
      hasCartSuccessMessage,
      hasFavoriteSuccessMessage,
      getProductImageUrl,
      handleImageError,
      forceReload
    };
  }
}
</script>

<style scoped>
.aspect-square {
  aspect-ratio: 1 / 1;
}



/* Success message animation */
.success-fade-enter-active,
.success-fade-leave-active {
  transition: all 0.3s ease;
}

.success-fade-enter-from {
  opacity: 0;
  transform: translateY(-10px) scale(0.95);
}

.success-fade-leave-to {
  opacity: 0;
  transform: translateY(-5px) scale(0.95);
}
</style>