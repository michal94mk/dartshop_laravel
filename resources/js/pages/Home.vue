<template>
  <div>
    <!-- Hero section -->
    <div class="relative bg-white overflow-hidden">
      <div class="max-w-7xl mx-auto">
        <div class="relative z-10 pb-8 bg-white sm:pb-16 md:pb-20 lg:max-w-2xl lg:w-full lg:pb-28 xl:pb-32">
          <svg class="hidden lg:block absolute right-0 inset-y-0 h-full w-48 text-white transform translate-x-1/2" fill="currentColor" viewBox="0 0 100 100" preserveAspectRatio="none" aria-hidden="true">
            <polygon points="50,0 100,0 50,100 0,100" />
          </svg>

          <div class="pt-10 sm:pt-16 lg:pt-8 lg:pb-14 lg:overflow-hidden">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
              <div class="lg:grid lg:grid-cols-2 lg:gap-8">
                <div class="mx-auto max-w-md px-4 sm:max-w-2xl sm:px-6 sm:text-center lg:px-0 lg:text-left lg:flex lg:items-center">
                  <div class="lg:py-24">
                    <h1 class="text-4xl tracking-tight font-extrabold text-gray-900 sm:text-5xl md:text-6xl">
                      <span class="block xl:inline">Najwyższej jakości</span>
                      <span class="block text-indigo-600 xl:inline">akcesoria do dart</span>
                    </h1>
                    <p class="mt-3 text-base text-gray-500 sm:mt-5 sm:text-lg sm:max-w-xl sm:mx-auto lg:mx-0">
                      Odkryj profesjonalne lotki, tarcze i akcesoria do dart. Sprzęt dla początkujących i zaawansowanych graczy.
                    </p>
                    <div class="mt-8 sm:mt-12">
                      <router-link to="/products" class="inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                        Przeglądaj produkty
                      </router-link>
                    </div>
                  </div>
                </div>
                <div class="mt-12 lg:m-0">
                  <div class="mx-auto max-w-md px-4 sm:max-w-2xl sm:px-6 lg:px-0">
                    <img class="w-full rounded-lg shadow-xl" src="https://via.placeholder.com/800x600/indigo/fff?text=Dart+Shop" alt="Dart equipment">
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Featured products section -->
    <section class="bg-white py-12">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="lg:text-center">
          <h2 class="text-base text-indigo-600 font-semibold tracking-wide uppercase">Najnowsze produkty</h2>
          <p class="mt-2 text-3xl leading-8 font-extrabold tracking-tight text-gray-900 sm:text-4xl">
            Najnowsze produkty w naszym sklepie
          </p>
          <p class="mt-4 max-w-2xl text-xl text-gray-500 lg:mx-auto">
            Sprawdź nasze ostatnio dodane produkty.
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
          
          <div v-else class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            <!-- Display API products if available -->
            <template v-if="productStore.featuredProducts && productStore.featuredProducts.length > 0">
              <div v-for="product in productStore.featuredProducts" :key="product.id" class="bg-white overflow-hidden shadow-sm rounded-lg transition-all hover:shadow-md flex flex-col h-full group">
                <div class="relative">
                  <div class="aspect-square overflow-hidden">
                    <img 
                      :src="product.image_url || 'https://via.placeholder.com/300x300/indigo/fff?text=' + product.name" 
                      :alt="product.name" 
                      class="h-full w-full object-cover group-hover:scale-105 transition-transform duration-300"
                      loading="lazy"
                    >
                    <div class="absolute inset-0 bg-black bg-opacity-10 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                  </div>
                </div>
                <div class="p-5 flex-grow flex flex-col">
                  <h3 class="text-lg font-medium text-gray-900 line-clamp-2 min-h-[3.5rem]">{{ product.name }}</h3>
                  <p class="mt-2 text-sm text-gray-500 line-clamp-2 flex-grow min-h-[2.5rem]">{{ product.short_description || product.description }}</p>
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
                  <div class="mt-4 pt-3 border-t border-gray-100">
                    <span class="text-indigo-600 font-bold text-xl block mb-3">{{ formatPrice(product.price) }} zł</span>
                    <div class="mt-4 flex flex-col space-y-2">
                      <button 
                        @click="addToCart(product)"
                        :disabled="cartStore.isLoadingProduct(product.id)" 
                        class="w-full h-10 inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 transition-colors duration-200"
                      >
                        <template v-if="cartStore.isLoadingProduct(product.id)">
                          <svg class="animate-spin w-4 h-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                          </svg>
                          Dodawanie...
                        </template>
                        <template v-else>
                          <i class="fas fa-shopping-cart mr-1"></i>
                          Dodaj do koszyka
                        </template>
                      </button>
                      <div class="flex space-x-2">
                        <router-link 
                          :to="`/products/${product.id}`" 
                          class="flex-1 h-10 inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 transition-colors duration-200"
                        >
                          Szczegóły produktu
                        </router-link>
                        <FavoriteButton 
                          :product="product"
                          buttonClasses="h-10 inline-flex items-center justify-center px-3 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition-colors duration-200"
                          @favorite-added="handleFavoriteAdded"
                          @favorite-removed="handleFavoriteRemoved"
                        />
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </template>
            
            <!-- Fallback products if API fails -->
            <template v-else>
              <div v-for="product in fallbackProducts" :key="product.id" class="bg-white overflow-hidden shadow-sm rounded-lg transition-all hover:shadow-md flex flex-col h-full group">
                <div class="relative">
                  <div class="aspect-square overflow-hidden">
                    <img 
                      :src="product.image_url" 
                      :alt="product.name" 
                      class="h-full w-full object-cover group-hover:scale-105 transition-transform duration-300"
                      loading="lazy"
                    >
                    <div class="absolute inset-0 bg-black bg-opacity-10 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                  </div>
                </div>
                <div class="p-5 flex-grow flex flex-col">
                  <h3 class="text-lg font-medium text-gray-900 line-clamp-2 min-h-[3.5rem]">{{ product.name }}</h3>
                  <p class="mt-2 text-sm text-gray-500 line-clamp-2 flex-grow min-h-[2.5rem]">{{ product.description }}</p>
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
                  <div class="mt-4 pt-3 border-t border-gray-100">
                    <span class="text-indigo-600 font-bold text-xl block mb-3">{{ formatPrice(product.price) }} zł</span>
                    <div class="mt-4 flex flex-col space-y-2">
                      <button 
                        @click="addToCart(product)"
                        class="w-full h-10 inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 transition-colors duration-200"
                        :disabled="cartStore.isLoadingProduct(product.id)"
                      >
                        <template v-if="cartStore.isLoadingProduct(product.id)">
                          <svg class="animate-spin w-4 h-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                          </svg>
                          Dodawanie...
                        </template>
                        <template v-else>
                          <i class="fas fa-shopping-cart mr-1"></i>
                          Dodaj do koszyka
                        </template>
                      </button>
                      <div class="flex space-x-2">
                        <router-link 
                          :to="`/products/${product.id}`" 
                          class="flex-1 h-10 inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 transition-colors duration-200"
                        >
                          Szczegóły produktu
                        </router-link>
                        <FavoriteButton 
                          :product="product"
                          buttonClasses="h-10 inline-flex items-center justify-center px-3 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition-colors duration-200"
                          @favorite-added="handleFavoriteAdded"
                          @favorite-removed="handleFavoriteRemoved"
                        />
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </template>
          </div>
          
          <div class="mt-12 text-center">
            <router-link to="/products" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
              Zobacz wszystkie produkty
            </router-link>
          </div>
        </div>
      </div>
    </section>

    <!-- Categories section -->
    <section class="bg-gray-50 py-12">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="lg:text-center">
          <h2 class="text-base text-indigo-600 font-semibold tracking-wide uppercase">Kategorie</h2>
          <p class="mt-2 text-3xl leading-8 font-extrabold tracking-tight text-gray-900 sm:text-4xl">
            Wszystko czego potrzebujesz
          </p>
          <p class="mt-4 max-w-2xl text-xl text-gray-500 lg:mx-auto">
            Przeglądaj nasze kategorie i znajdź to, czego szukasz.
          </p>
        </div>

        <div class="mt-10">
          <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3">
            <div v-for="category in categories" :key="category.id" class="relative rounded-lg overflow-hidden">
              <img :src="category.image" alt="" class="w-full h-56 object-cover">
              <div class="absolute inset-0 bg-gradient-to-t from-gray-900 to-transparent"></div>
              <div class="absolute bottom-0 left-0 p-6">
                <h3 class="text-xl font-bold text-white">{{ category.name }}</h3>
                <p class="mt-2 text-sm text-gray-300">{{ category.description }}</p>
                <router-link :to="`/products?category=${category.id}`" class="mt-4 inline-flex items-center text-sm font-medium text-white">
                  Zobacz produkty
                  <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                  </svg>
                </router-link>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Testimonials section -->
    <section class="bg-white py-12">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="lg:text-center">
          <h2 class="text-base text-indigo-600 font-semibold tracking-wide uppercase">Opinie klientów</h2>
          <p class="mt-2 text-3xl leading-8 font-extrabold tracking-tight text-gray-900 sm:text-4xl">
            Co mówią o nas klienci
          </p>
        </div>

        <div class="mt-10">
          <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3">
            <div v-for="testimonial in testimonials" :key="testimonial.id" class="bg-gray-50 p-6 rounded-lg">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <img class="h-12 w-12 rounded-full" :src="testimonial.avatar" alt="">
                </div>
                <div class="ml-4">
                  <h3 class="text-lg font-medium text-gray-900">{{ testimonial.name }}</h3>
                  <div class="mt-1 flex items-center">
                    <div class="flex text-yellow-400">
                      <span v-for="i in 5" :key="i" class="mr-1">
                        <i :class="i <= testimonial.rating ? 'fas fa-star' : 'far fa-star'"></i>
                      </span>
                    </div>
                  </div>
                </div>
              </div>
              <p class="mt-4 text-gray-600">{{ testimonial.text }}</p>
            </div>
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
          image_url: 'https://via.placeholder.com/300x300/indigo/fff?text=Lotki+Target'
        },
        {
          id: 2,
          name: 'Tarcza elektroniczna Winmau Blade 6',
          description: 'Zaawansowana tarcza dla profesjonalistów',
          price: 299.99,
          image_url: 'https://via.placeholder.com/300x300/indigo/fff?text=Tarcza+Winmau'
        },
        {
          id: 3,
          name: 'Zestaw punktowy XQ Max',
          description: 'Zestaw do zapisywania punktów z kredą i ścierką',
          price: 49.99,
          image_url: 'https://via.placeholder.com/300x300/indigo/fff?text=Zestaw+XQ+Max'
        },
        {
          id: 4,
          name: 'Lotki Red Dragon Razor Edge',
          description: 'Lotki z wysokiej jakości stali wolframowej',
          price: 129.99,
          image_url: 'https://via.placeholder.com/300x300/indigo/fff?text=Lotki+Red+Dragon'
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