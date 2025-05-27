<template>
  <div class="min-h-screen bg-gradient-to-br from-indigo-50 via-blue-50 to-purple-50">
    <!-- Hero section -->
    <div class="relative overflow-hidden bg-gradient-to-r from-indigo-600 to-purple-600 py-20">
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
          <div class="mt-12 lg:mt-0">
            <div class="relative">
              <img class="w-full rounded-2xl shadow-2xl" src="https://via.placeholder.com/800x600/indigo/fff?text=Dart+Shop" alt="Dart equipment">
              <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent rounded-2xl"></div>
              <!-- Decorative elements -->
              <div class="absolute -top-4 -right-4 w-24 h-24 bg-gradient-to-r from-yellow-400 to-orange-500 rounded-full opacity-20"></div>
              <div class="absolute -bottom-4 -left-4 w-16 h-16 bg-gradient-to-r from-indigo-400 to-purple-500 rounded-full opacity-30"></div>
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
          <div v-if="productStore.loading" class="text-center py-10">
            <div class="w-12 h-12 border-4 border-indigo-500 border-t-transparent rounded-full animate-spin mx-auto"></div>
            <p class="mt-2 text-gray-500">Ładowanie produktów...</p>
          </div>
          
          <div v-else-if="productStore.error" class="text-center py-10">
            <p class="text-red-500">{{ productStore.error }}</p>
            <button @click="loadFeaturedProducts" class="mt-4 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200">
              Spróbuj ponownie
            </button>
          </div>
          
          <div v-else class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
            <!-- Display API products if available -->
            <template v-if="productStore.featuredProducts && productStore.featuredProducts.length > 0">
              <div v-for="product in productStore.featuredProducts" :key="product.id" class="bg-white overflow-hidden shadow-lg rounded-2xl transition-all hover:shadow-xl group transform hover:-translate-y-2 duration-300 border border-gray-100 flex flex-col" style="aspect-ratio: 1 / 1.5;">
                <div class="relative h-4/5 overflow-hidden">
                  <img 
                    :src="product.image_url || 'https://via.placeholder.com/400x400/indigo/fff?text=' + product.name" 
                    :alt="product.name" 
                    class="h-full w-full object-cover group-hover:scale-110 transition-transform duration-500"
                    loading="lazy"
                  >
                  <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                  <!-- Product badge -->
                  <div class="absolute top-3 right-3 bg-white/90 backdrop-blur-sm px-2 py-1 rounded-full text-xs font-semibold text-indigo-600">
                    NOWOŚĆ
                  </div>
                </div>
                <div class="p-4 flex-1 flex flex-col justify-between">
                  <div>
                    <h3 class="text-base font-bold text-gray-900 line-clamp-2 mb-2 leading-tight">{{ product.name }}</h3>
                    <p class="text-xs text-gray-600 line-clamp-2 mb-3 leading-relaxed">{{ product.short_description || product.description }}</p>
                  </div>
                  <div v-if="product.id === cartMessageProductId" class="mt-2 p-2 rounded text-sm" :class="cartSuccess ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'">
                    <div v-if="cartSuccess" class="flex flex-col">
                      <span>{{ cartMessage }}</span>
                      <div class="mt-1 flex justify-end">
                        <router-link to="/cart" class="text-xs font-medium text-indigo-600 hover:text-indigo-500">
                          Przejdź do koszyka &rarr;
                        </router-link>
                      </div>
                    </div>
                    <div v-else>
                      {{ cartMessage }}
                    </div>
                  </div>
                  <div v-if="product.id === favoriteMessageProductId" class="mt-2 p-2 rounded text-sm" :class="favoriteSuccess ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'">
                    {{ favoriteMessage }}
                  </div>
                  <div>
                    <div class="flex items-center justify-between mb-3">
                      <span class="text-lg font-bold text-indigo-600">{{ formatPrice(product.price) }} zł</span>
                      <FavoriteButton 
                        :product="product"
                        buttonClasses="p-1.5 rounded-full border border-gray-300 text-gray-400 hover:text-red-500 hover:border-red-300 transition-colors duration-200"
                        @favorite-added="handleFavoriteAdded"
                        @favorite-removed="handleFavoriteRemoved"
                      />
                    </div>
                    <div class="space-y-2">
                      <button 
                        @click="addToCart(product)"
                        :disabled="cartStore.isLoadingProduct(product.id)" 
                        class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-medium py-2.5 px-4 rounded-lg transition-all duration-200 text-sm"
                      >
                        <template v-if="cartStore.isLoadingProduct(product.id)">
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
                        class="w-full block text-center py-2 px-4 border border-indigo-600 text-indigo-600 font-medium rounded-lg hover:bg-indigo-50 transition-colors duration-200 text-sm"
                      >
                        Zobacz szczegóły
                      </router-link>
                    </div>
                  </div>
                </div>
              </div>
            </template>
            
            <!-- Fallback products if API fails -->
            <template v-else>
              <div v-for="product in fallbackProducts" :key="product.id" class="bg-white overflow-hidden shadow-lg rounded-2xl transition-all hover:shadow-xl group transform hover:-translate-y-2 duration-300 border border-gray-100 flex flex-col" style="aspect-ratio: 1 / 1.5;">
                <div class="relative h-4/5 overflow-hidden">
                  <img 
                    :src="product.image_url" 
                    :alt="product.name" 
                    class="h-full w-full object-cover group-hover:scale-110 transition-transform duration-500"
                    loading="lazy"
                  >
                  <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                  <!-- Product badge -->
                  <div class="absolute top-3 right-3 bg-white/90 backdrop-blur-sm px-2 py-1 rounded-full text-xs font-semibold text-indigo-600">
                    NOWOŚĆ
                  </div>
                </div>
                <div class="p-4 flex-1 flex flex-col justify-between">
                  <div>
                    <h3 class="text-base font-bold text-gray-900 line-clamp-2 mb-2 leading-tight">{{ product.name }}</h3>
                    <p class="text-xs text-gray-600 line-clamp-2 mb-3 leading-relaxed">{{ product.description }}</p>
                  </div>
                  <div v-if="product.id === cartMessageProductId" class="mt-2 p-2 rounded text-sm" :class="cartSuccess ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'">
                    <div v-if="cartSuccess" class="flex flex-col">
                      <span>{{ cartMessage }}</span>
                      <div class="mt-1 flex justify-end">
                        <router-link to="/cart" class="text-xs font-medium text-indigo-600 hover:text-indigo-500">
                          Przejdź do koszyka &rarr;
                        </router-link>
                      </div>
                    </div>
                    <div v-else>
                      {{ cartMessage }}
                    </div>
                  </div>
                  <div v-if="product.id === favoriteMessageProductId" class="mt-2 p-2 rounded text-sm" :class="favoriteSuccess ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'">
                    {{ favoriteMessage }}
                  </div>
                  <div>
                    <div class="flex items-center justify-between mb-3">
                      <span class="text-lg font-bold text-indigo-600">{{ formatPrice(product.price) }} zł</span>
                      <FavoriteButton 
                        :product="product"
                        buttonClasses="p-1.5 rounded-full border border-gray-300 text-gray-400 hover:text-red-500 hover:border-red-300 transition-colors duration-200"
                        @favorite-added="handleFavoriteAdded"
                        @favorite-removed="handleFavoriteRemoved"
                      />
                    </div>
                    <div class="space-y-2">
                      <button 
                        @click="addToCart(product)"
                        class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-medium py-2.5 px-4 rounded-lg transition-all duration-200 text-sm"
                        :disabled="cartStore.isLoadingProduct(product.id)"
                      >
                        <template v-if="cartStore.isLoadingProduct(product.id)">
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
                        class="w-full block text-center py-2 px-4 border border-indigo-600 text-indigo-600 font-medium rounded-lg hover:bg-indigo-50 transition-colors duration-200 text-sm"
                      >
                        Zobacz szczegóły
                      </router-link>
                    </div>
                  </div>
                </div>
              </div>
            </template>
          </div>
          
          <div class="mt-16 bg-gradient-to-r from-indigo-600 to-purple-600 rounded-2xl shadow-xl p-8 text-center text-white">
            <h3 class="text-2xl font-bold mb-4">Nie możesz znaleźć tego, czego szukasz?</h3>
            <p class="text-indigo-100 mb-6 max-w-2xl mx-auto">
              Sprawdź pełną ofertę produktów lub skontaktuj się z nami - pomożemy Ci znaleźć idealne akcesoria do dart.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
              <router-link to="/products" 
                           class="inline-flex items-center px-8 py-4 border border-transparent text-base font-semibold rounded-xl text-indigo-600 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-white transition-all duration-200 shadow-lg">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M8 11v6a2 2 0 002 2h4a2 2 0 002-2v-6M8 11h8"/>
                </svg>
                Zobacz wszystkie produkty
              </router-link>
              <router-link to="/contact" 
                           class="inline-flex items-center px-8 py-4 border-2 border-white text-base font-semibold rounded-xl text-white bg-transparent hover:bg-white hover:text-indigo-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-white transition-all duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                Skontaktuj się z nami
              </router-link>
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

        <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3">
          <div v-for="category in categories" :key="category.id" class="group relative rounded-2xl overflow-hidden shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
            <div class="relative h-64 overflow-hidden">
              <img :src="category.image" alt="" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
              <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent"></div>
            </div>
            <div class="absolute bottom-0 left-0 right-0 p-6 text-white">
              <h3 class="text-2xl font-bold mb-2">{{ category.name }}</h3>
              <p class="text-gray-200 mb-4 text-sm leading-relaxed">{{ category.description }}</p>
              <router-link :to="`/products?category=${category.id}`" 
                           class="inline-flex items-center px-4 py-2 bg-white/20 backdrop-blur-sm text-white rounded-lg hover:bg-white/30 transition-all duration-200 font-medium">
                Zobacz produkty
                <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
              </router-link>
            </div>
            <!-- Decorative gradient overlay on hover -->
            <div class="absolute inset-0 bg-gradient-to-r from-indigo-500/20 to-purple-500/20 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
          </div>
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
          <div v-for="testimonial in testimonials" :key="testimonial.id" class="bg-white/10 backdrop-blur-sm p-6 rounded-2xl border border-white/20 hover:bg-white/20 transition-all duration-300">
            <div class="flex items-center mb-4">
              <div class="w-12 h-12 bg-gradient-to-r from-yellow-400 to-orange-500 rounded-full flex items-center justify-center text-white font-bold text-lg">
                {{ testimonial.name.charAt(0) }}
              </div>
              <div class="ml-4">
                <h3 class="text-lg font-semibold text-white">{{ testimonial.name }}</h3>
                <div class="flex text-yellow-400">
                  <span v-for="i in 5" :key="i" class="mr-1">
                    <svg class="w-4 h-4" :class="i <= testimonial.rating ? 'text-yellow-400' : 'text-gray-400'" fill="currentColor" viewBox="0 0 20 20">
                      <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                  </span>
                </div>
              </div>
            </div>
            <p class="text-indigo-100 leading-relaxed">"{{ testimonial.text }}"</p>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script>
import { useProductStore } from '../stores/productStore';
import { useCartStore } from '../stores/cartStore';
import { useFavoriteStore } from '../stores/favoriteStore';
import FavoriteButton from '../components/ui/FavoriteButton.vue';

export default {
  name: 'HomePage',
  components: {
    FavoriteButton
  },
  data() {
    return {
      categories: [
        {
          id: 1,
          name: 'Lotki',
          description: 'Profesjonalne lotki różnych marek',
          image: 'https://via.placeholder.com/600x400/indigo/fff?text=Lotki'
        },
        {
          id: 2,
          name: 'Tarcze',
          description: 'Tarcze elektroniczne i klasyczne',
          image: 'https://via.placeholder.com/600x400/indigo/fff?text=Tarcze'
        },
        {
          id: 3,
          name: 'Akcesoria',
          description: 'Wszelkie akcesoria do gry w dart',
          image: 'https://via.placeholder.com/600x400/indigo/fff?text=Akcesoria'
        }
      ],
      fallbackProducts: [
        {
          id: 1,
          name: 'Lotki Target Agora A30',
          description: 'Profesjonalne lotki ze stali wolframowej 90%',
          price: 149.99,
          image_url: '/img/product01.png'
        },
        {
          id: 2,
          name: 'Tarcza elektroniczna Winmau Blade 6',
          description: 'Zaawansowana tarcza dla profesjonalistów',
          price: 299.99,
          image_url: '/img/product02.png'
        },
        {
          id: 3,
          name: 'Zestaw punktowy XQ Max',
          description: 'Zestaw do zapisywania punktów z kredą i ścierką',
          price: 49.99,
          image_url: '/img/product03.png'
        },
        {
          id: 4,
          name: 'Lotki Red Dragon Razor Edge',
          description: 'Lotki z wysokiej jakości stali wolframowej',
          price: 129.99,
          image_url: '/img/product04.png'
        }
      ],
      testimonials: [
        {
          id: 1,
          name: 'Jan Kowalski',
          avatar: 'https://via.placeholder.com/150/indigo/fff?text=JK',
          rating: 5,
          text: 'Świetny sklep z szerokim asortymentem. Polecam każdemu, kto szuka profesjonalnego sprzętu do dart.'
        },
        {
          id: 2,
          name: 'Anna Nowak',
          avatar: 'https://via.placeholder.com/150/indigo/fff?text=AN',
          rating: 4,
          text: 'Dobra jakość i szybka dostawa. Jedyny minus za nieco wysokie ceny, ale jakość warta swojej ceny.'
        },
        {
          id: 3,
          name: 'Piotr Wiśniewski',
          avatar: 'https://via.placeholder.com/150/indigo/fff?text=PW',
          rating: 5,
          text: 'Profesjonalna obsługa i świetne doradztwo. Polecam szczególnie początkującym graczom.'
        }
      ],
      cartMessage: '',
      cartSuccess: false,
      cartMessageProductId: null,
      favoriteMessage: '',
      favoriteSuccess: false,
      favoriteMessageProductId: null
    }
  },
  created() {
    this.productStore = useProductStore();
    this.cartStore = useCartStore();
    this.favoriteStore = useFavoriteStore();
  },
  mounted() {
    this.loadFeaturedProducts();
  },
  methods: {
    loadFeaturedProducts() {
      this.productStore.fetchFeaturedProducts();
    },
    formatPrice(price) {
      // Check if price is a valid number
      if (price === null || price === undefined || isNaN(price)) {
        return '0.00';
      }
      return parseFloat(price).toFixed(2);
    },
    addToCart(product) {
      this.cartMessageProductId = product.id;
      this.cartStore.addToCart(product.id)
        .then(() => {
          this.cartMessage = `Produkt "${product.name || 'wybrany'}" został dodany do koszyka.`;
          this.cartSuccess = true;
          
          // Clear message after 3 seconds
          setTimeout(() => {
            if (this.cartMessageProductId === product.id) {
              this.cartMessage = '';
              this.cartMessageProductId = null;
            }
          }, 3000);
        })
        .catch(error => {
          console.error('Failed to add to cart:', error);
          this.cartMessage = 'Nie udało się dodać produktu do koszyka';
          this.cartSuccess = false;
          
          // Clear message after 3 seconds
          setTimeout(() => {
            if (this.cartMessageProductId === product.id) {
              this.cartMessage = '';
              this.cartMessageProductId = null;
            }
          }, 3000);
        });
    },
    toggleFavorite(product) {
      this.favoriteStore.toggleFavoriteItem(product);
    },
    isInFavorites(productId) {
      return this.favoriteStore.isInFavorites(productId);
    },
    handleFavoriteAdded(product) {
      this.favoriteMessage = `Produkt "${product.name}" został dodany do ulubionych.`;
      this.favoriteSuccess = true;
      this.favoriteMessageProductId = product.id;
      
      // Clear the message after 3 seconds
      setTimeout(() => {
        if (this.favoriteMessageProductId === product.id) {
          this.favoriteMessageProductId = null;
        }
      }, 3000);
    },
    handleFavoriteRemoved(product) {
      this.favoriteMessage = `Produkt "${product.name}" został usunięty z ulubionych.`;
      this.favoriteSuccess = false;
      this.favoriteMessageProductId = product.id;
      
      // Clear the message after 3 seconds
      setTimeout(() => {
        if (this.favoriteMessageProductId === product.id) {
          this.favoriteMessageProductId = null;
        }
      }, 3000);
    }
  }
}
</script>

<style scoped>
.aspect-square {
  aspect-ratio: 1 / 1;
}
</style> 