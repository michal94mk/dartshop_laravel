<template>
  <header class="bg-white shadow">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between h-16">
        <!-- Logo -->
        <div class="flex-shrink-0 flex items-center">
          <router-link to="/" class="text-xl font-bold text-indigo-600">
            <span>Dart</span><span class="text-gray-800">Shop</span>
          </router-link>
        </div>
        
        <!-- Navigation Links -->
        <div class="hidden lg:ml-6 lg:flex lg:space-x-8">
          <router-link 
            to="/" 
            class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium"
            :class="[$route.path === '/' ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700']"
            @click.prevent="navigateTo('/', $event)"
          >
            Home
          </router-link>
          
          <!-- Products with dropdown -->
          <div class="relative flex items-center" @mouseenter="showProductsDropdown = true" @mouseleave="showProductsDropdown = false">
            <router-link 
              to="/products" 
              class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium h-16"
              :class="[$route.path.includes('/categories') || $route.path.includes('/products') ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700']"
              @click.prevent="navigateTo('/products', $event)"
            >
              Produkty
              <svg class="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
              </svg>
            </router-link>
            
            <!-- Dropdown Menu -->
            <div 
              v-show="showProductsDropdown"
              class="absolute left-0 top-full mt-1 w-56 origin-top-left bg-white border border-gray-200 divide-y divide-gray-100 rounded-md shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none z-50"
            >
              <div class="py-1">
                <router-link 
                  to="/products" 
                  class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-600"
                  @click="showProductsDropdown = false"
                >
                  <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                  </svg>
                  Wszystkie produkty
                </router-link>
              </div>
              <div class="py-1">
                <div class="px-4 py-2">
                  <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Kategorie</p>
                </div>
                <router-link 
                  v-for="category in topCategories"
                  :key="category.id"
                  :to="`/products?category=${category.id}`" 
                  class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-600"
                  @click="showProductsDropdown = false"
                >
                  <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                  </svg>
                  {{ category.name }}
                  <span class="ml-auto text-xs text-gray-400">({{ category.products_count }})</span>
                </router-link>
              </div>
            </div>
          </div>
          
          <router-link 
            to="/promotions" 
            class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium"
            :class="[$route.path === '/promotions' ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700']"
            @click.prevent="navigateTo('/promotions', $event)"
          >
            Promocje
          </router-link>
          <router-link 
            to="/about" 
            class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium"
            :class="[$route.path === '/about' ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700']"
            @click.prevent="navigateTo('/about', $event)"
          >
            O nas
          </router-link>
          <router-link 
            to="/tutorials" 
            class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium"
            :class="[$route.path.includes('/tutorials') ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700']"
            @click.prevent="navigateTo('/tutorials', $event)"
          >
            Poradniki
          </router-link>
          <router-link 
            to="/contact" 
            class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium"
            :class="[$route.path === '/contact' ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700']"
            @click.prevent="navigateTo('/contact', $event)"
          >
            Kontakt
          </router-link>
        </div>

        <!-- Right side buttons -->
        <div class="flex items-center">
          <!-- Cart -->
          <router-link 
            to="/cart" 
            class="ml-4 px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:text-gray-900 flex items-center" 
            @click.prevent="navigateTo('/cart', $event)"
          >
            <i class="fas fa-shopping-cart mr-1"></i>
            <span class="bg-indigo-600 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs">
              {{ cartItemsCount }}
            </span>
          </router-link>
          
          <!-- User menu -->
          <div v-if="isLoggedIn" class="ml-3 relative">
            <div>
              <button @click="toggleUserMenu" type="button" class="max-w-xs bg-white flex items-center text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" id="user-menu-button" aria-expanded="false" aria-haspopup="true">
                <span class="sr-only">Open user menu</span>
                <span class="h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center">
                  {{ userInitial }}
                </span>
              </button>
            </div>
            <div v-show="userMenuOpen" class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-50" role="menu" aria-orientation="vertical" aria-labelledby="user-menu-button" tabindex="-1">
              <router-link v-if="isAdmin" to="/admin" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem" @click.prevent="navigateTo('/admin', $event)">
                Panel Administratora
              </router-link>
              <router-link to="/profile" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem" @click.prevent="navigateTo('/profile', $event)">
                Twój Profil
              </router-link>
              <button @click="logout" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">Wyloguj</button>
            </div>
          </div>
          <div v-else class="hidden lg:flex lg:items-center lg:ml-6">
            <router-link to="/login" class="text-sm text-gray-700 hover:text-indigo-600 mr-4">Logowanie</router-link>
            <router-link to="/register" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
              Rejestracja
            </router-link>
          </div>
          
          <!-- Mobile menu button -->
          <div class="flex items-center lg:hidden">
            <button @click="toggleMobileMenu" type="button" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500" aria-controls="mobile-menu" aria-expanded="false">
              <span class="sr-only">Open main menu</span>
              <svg class="block h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
              </svg>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Search Bar Section -->
    <div class="bg-gradient-to-r from-indigo-50 via-blue-50 to-purple-50 border-t border-gray-100 hidden lg:block">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <div class="flex justify-center">
          <div class="w-full max-w-2xl">
            <product-search />
          </div>
        </div>
      </div>
    </div>
    
    <!-- Mobile menu -->
    <div v-show="mobileMenuOpen" class="lg:hidden fixed inset-0 z-[99999]" id="mobile-menu">
      <!-- Backdrop/Overlay -->
      <div class="fixed inset-0 bg-black bg-opacity-50" @click="mobileMenuOpen = false"></div>
      
      <!-- Slide-out Menu -->
      <div class="fixed top-0 left-0 h-full w-80 bg-white shadow-xl transform transition-transform duration-300 ease-in-out overflow-y-auto z-[99999]"
           :class="mobileMenuOpen ? 'translate-x-0' : '-translate-x-full'">
        
        <!-- Header -->
        <div class="flex items-center justify-between p-4 border-b border-gray-200">
          <router-link to="/" class="text-xl font-bold text-indigo-600" @click="mobileMenuOpen = false">
            <span>Dart</span><span class="text-gray-800">Shop</span>
          </router-link>
          <button @click="mobileMenuOpen = false" class="p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <!-- Mobile Search Bar -->
        <div class="p-4 bg-gradient-to-br from-indigo-600 via-blue-600 to-purple-600 border-b border-indigo-500 shadow-lg">
          <div class="relative flex items-center">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <svg class="h-5 w-5 text-white/70" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
              </svg>
            </div>
            <input
              v-model="mobileSearchQuery"
              type="text"
              placeholder="Wyszukaj produkty..."
              class="block w-full pl-10 pr-16 py-3 text-base border-0 rounded-xl leading-6 bg-white/95 backdrop-blur-sm placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-2 focus:ring-white focus:bg-white shadow-lg hover:shadow-xl transition-all duration-200 font-medium"
              @input="onMobileSearchInput"
              @keydown.enter.prevent="performMobileSearch"
              @focus="mobileSearchFocused = true"
              @blur="onMobileSearchBlur"
            />
            <button
              @click="performMobileSearch"
              :disabled="!mobileSearchQuery.trim()"
              class="absolute inset-y-0 right-0 flex items-center pr-2"
            >
              <div 
                class="h-10 w-12 rounded-lg flex items-center justify-center transition-all duration-200"
                :class="mobileSearchQuery.trim() ? 'bg-blue-600 hover:bg-blue-700 text-white shadow-md hover:shadow-lg transform hover:scale-105' : 'bg-gray-300 text-gray-500 cursor-not-allowed'"
              >
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
              </div>
            </button>
            
            <!-- Mobile Search Results Dropdown -->
            <div
              v-if="showMobileSearchDropdown && (mobileSearchResults.length > 0 || (mobileSearchQuery.length >= 3 && !mobileSearchLoading))"
              class="absolute top-full mt-2 w-full bg-white shadow-2xl max-h-80 rounded-xl py-2 text-base ring-1 ring-black ring-opacity-5 overflow-auto z-50 border border-gray-100"
            >
              <!-- Search Results -->
              <div v-if="mobileSearchResults.length > 0">
                <div class="px-4 py-2 border-b border-gray-100">
                  <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">
                    Znalezione produkty
                  </p>
                </div>
                
                <div
                  v-for="(product, index) in mobileSearchResults"
                  :key="product.id"
                  class="cursor-pointer select-none relative py-3 px-4 hover:bg-gradient-to-r hover:from-indigo-50 hover:to-blue-50 transition-all duration-150"
                  @click="goToMobileProduct(product)"
                >
                  <div class="flex items-center">
                    <div class="flex-shrink-0 h-12 w-12">
                      <img
                        :src="product.image_url || 'https://via.placeholder.com/48x48/indigo/fff?text=P'"
                        :alt="product.name"
                        class="h-12 w-12 rounded-lg object-cover shadow-md"
                      />
                    </div>
                    <div class="ml-3 flex-1">
                      <p class="text-sm font-semibold text-gray-900 mb-1">{{ product.name }}</p>
                      <div class="flex items-center justify-between">
                        <p class="text-xs text-gray-500">{{ product.category?.name || 'Bez kategorii' }}</p>
                        <!-- Price display with promotion support -->
                        <div class="flex flex-col items-end">
                          <div v-if="hasPromotion(product)" class="space-y-1">
                            <!-- Original price (crossed out) -->
                            <div class="text-xs text-gray-400 line-through">
                              {{ formatPrice(product.price) }} zł
                            </div>
                            <!-- Promotional price -->
                            <div class="text-sm font-bold text-red-600">
                              {{ formatPrice(product.promotion_price) }} zł
                            </div>
                          </div>
                          <!-- Regular price (no promotion) -->
                          <div v-else class="text-sm font-bold text-indigo-600">
                            {{ formatPrice(product.price) }} zł
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="ml-2 flex-shrink-0">
                      <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                      </svg>
                    </div>
                  </div>
                </div>
                
                <!-- View All Results Link -->
                <div class="border-t border-gray-100 py-2 px-4 bg-gradient-to-r from-gray-50 to-indigo-50">
                  <button
                    @click="viewAllMobileResults"
                    class="w-full text-center py-2 px-4 bg-gradient-to-r from-indigo-600 to-blue-600 text-white font-semibold rounded-lg hover:from-indigo-700 hover:to-blue-700 transition-all duration-200 shadow-md hover:shadow-lg text-sm"
                  >
                    Zobacz wszystkie wyniki ({{ mobileSearchTotalResults }})
                  </button>
                </div>
              </div>

              <!-- No Results -->
              <div v-else-if="mobileSearchQuery.length >= 3 && !mobileSearchLoading" class="py-6 px-4 text-center">
                <svg class="mx-auto h-8 w-8 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <p class="text-gray-500 font-medium text-sm">Brak wyników dla</p>
                <p class="text-gray-700 font-semibold">"{{ mobileSearchQuery }}"</p>
              </div>
            </div>
          </div>
        </div>
        
        <div class="py-2">
          <router-link
            to="/"
            class="flex items-center px-4 py-3 border-l-4 text-base font-medium"
            :class="[$route.path === '/' ? 'bg-indigo-50 border-indigo-500 text-indigo-700' : 'border-transparent text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800']"
            @click.prevent="navigateTo('/', $event)"
          >
            <i class="fas fa-home w-5 mr-3 text-gray-400"></i>
            Home
          </router-link>
          
          <!-- Products with mobile dropdown -->
          <div class="border-l-4 border-transparent">
            <router-link
              to="/products"
              class="flex items-center px-4 py-3 text-base font-medium"
              :class="[$route.path.includes('/categories') || $route.path.includes('/products') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-800']"
              @click.prevent="navigateTo('/products', $event)"
            >
              <i class="fas fa-box w-5 mr-3 text-gray-400"></i>
              Produkty
            </router-link>
            
            <!-- Mobile categories submenu -->
            <div class="ml-8 space-y-1 border-l border-gray-200 pl-4">
              <router-link
                to="/products"
                class="block py-2 text-sm text-gray-500 hover:text-gray-700 hover:bg-gray-50 rounded px-2"
                @click="mobileMenuOpen = false"
              >
                <i class="fas fa-list w-4 mr-2 text-gray-400"></i>
                Wszystkie produkty
              </router-link>
              <router-link
                v-for="category in topCategories"
                :key="`mobile-${category.id}`"
                :to="`/products?category=${category.id}`"
                class="block py-2 text-sm text-gray-500 hover:text-gray-700 hover:bg-gray-50 rounded px-2"
                @click="mobileMenuOpen = false"
              >
                <i class="fas fa-folder w-4 mr-2 text-gray-400"></i>
                {{ category.name }}
                <span class="text-xs text-gray-400 ml-1">({{ category.products_count }})</span>
              </router-link>
            </div>
          </div>
          
          <router-link
            to="/promotions"
            class="flex items-center px-4 py-3 border-l-4 text-base font-medium"
            :class="[$route.path === '/promotions' ? 'bg-indigo-50 border-indigo-500 text-indigo-700' : 'border-transparent text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800']"
            @click.prevent="navigateTo('/promotions', $event)"
          >
            <i class="fas fa-tags w-5 mr-3 text-gray-400"></i>
            Promocje
          </router-link>
          
          <router-link
            to="/about"
            class="flex items-center px-4 py-3 border-l-4 text-base font-medium"
            :class="[$route.path === '/about' ? 'bg-indigo-50 border-indigo-500 text-indigo-700' : 'border-transparent text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800']"
            @click.prevent="navigateTo('/about', $event)"
          >
            <i class="fas fa-info-circle w-5 mr-3 text-gray-400"></i>
            O nas
          </router-link>
          
          <router-link
            to="/tutorials"
            class="flex items-center px-4 py-3 border-l-4 text-base font-medium"
            :class="[$route.path.includes('/tutorials') ? 'bg-indigo-50 border-indigo-500 text-indigo-700' : 'border-transparent text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800']"
            @click.prevent="navigateTo('/tutorials', $event)"
          >
            <i class="fas fa-book w-5 mr-3 text-gray-400"></i>
            Poradniki
          </router-link>
          
          <router-link
            to="/contact"
            class="flex items-center px-4 py-3 border-l-4 text-base font-medium"
            :class="[$route.path === '/contact' ? 'bg-indigo-50 border-indigo-500 text-indigo-700' : 'border-transparent text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800']"
            @click.prevent="navigateTo('/contact', $event)"
          >
            <i class="fas fa-envelope w-5 mr-3 text-gray-400"></i>
            Kontakt
          </router-link>
          
          <div class="mt-4 pt-4 border-t border-gray-200">
            <div v-if="isLoggedIn">
              <div class="flex items-center px-4 py-2">
                <div class="flex-shrink-0">
                  <span class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-semibold">
                    {{ userInitial }}
                  </span>
                </div>
                <div class="ml-3">
                  <div class="text-base font-medium text-gray-800">{{ userName }}</div>
                  <div class="text-sm font-medium text-gray-500">{{ userEmail }}</div>
                </div>
              </div>
              <div class="mt-3 space-y-1">
                <router-link v-if="isAdmin" to="/admin" class="flex items-center px-4 py-2 text-base font-medium text-gray-500 hover:text-gray-800 hover:bg-gray-100" @click.prevent="navigateTo('/admin', $event)">
                  <i class="fas fa-cog w-5 mr-3 text-gray-400"></i>
                  Panel Administratora
                </router-link>
                <router-link to="/profile" class="flex items-center px-4 py-2 text-base font-medium text-gray-500 hover:text-gray-800 hover:bg-gray-100" @click.prevent="navigateTo('/profile', $event)">
                  <i class="fas fa-user w-5 mr-3 text-gray-400"></i>
                  Twój Profil
                </router-link>
                <button @click="logout" class="flex items-center w-full text-left px-4 py-2 text-base font-medium text-gray-500 hover:text-gray-800 hover:bg-gray-100">
                  <i class="fas fa-sign-out-alt w-5 mr-3 text-gray-400"></i>
                  Wyloguj
                </button>
              </div>
            </div>
            <div v-else class="space-y-1">
              <router-link to="/login" class="flex items-center px-4 py-3 text-base font-medium text-gray-500 hover:text-gray-800 hover:bg-gray-100" @click.prevent="navigateTo('/login', $event)">
                <i class="fas fa-sign-in-alt w-5 mr-3 text-gray-400"></i>
                Logowanie
              </router-link>
              <router-link to="/register" class="flex items-center px-4 py-3 text-base font-medium text-white bg-indigo-600 hover:bg-indigo-700 mx-4 rounded-md" @click.prevent="navigateTo('/register', $event)">
                <i class="fas fa-user-plus w-5 mr-3"></i>
                Rejestracja
              </router-link>
            </div>
          </div>
        </div>
      </div>
    </div>
  </header>
</template>

<script>
import { useAuthStore } from '../../stores/authStore';
import { useCartStore } from '../../stores/cartStore';
import { useCategoryStore } from '../../stores/categoryStore';
import { storeToRefs } from 'pinia';
import { useRouter } from 'vue-router';
import { computed } from 'vue';
import ProductSearch from '../ui/ProductSearch.vue';
import axios from 'axios';

export default {
  name: 'SiteHeader',
  components: {
    ProductSearch
  },
  data() {
    return {
      userMenuOpen: false,
      showProductsDropdown: false,
      mobileMenuOpen: false,
      mobileSearchQuery: '',
      searchTimeout: null,
      mobileSearchFocused: false,
      showMobileSearchDropdown: false,
      mobileSearchResults: [],
      mobileSearchLoading: false,
      mobileSearchTotalResults: 0
    }
  },
  setup() {
    const authStore = useAuthStore();
    const cartStore = useCartStore();
    const categoryStore = useCategoryStore();
    const router = useRouter();
    
    // Używamy storeToRefs, aby zachować reaktywność getterów i state w Pinia
    const { isLoggedIn, isAdmin, userName, userEmail, userInitial } = storeToRefs(authStore);
    const { totalItems: cartItemsCount } = storeToRefs(cartStore);
    
    // Computed property for top categories (first 5 categories with products)
    const topCategories = computed(() => {
      return categoryStore.orderedCategories
        .filter(cat => cat.is_active && cat.products_count > 0)
        .slice(0, 5); // Show only first 5 categories in dropdown
    });
    
    return {
      authStore,
      cartStore,
      categoryStore,
      router,
      isLoggedIn,
      isAdmin,
      userName,
      userEmail,
      userInitial,
      cartItemsCount,
      topCategories
    }
  },
  mounted() {
    // Inicjalizacja auth store i cart store
    this.authStore.initAuth();
    this.cartStore.initCart();
    this.categoryStore.fetchCategories();
    
    // Zamykaj dropdown przy kliknięciu poza nim
    document.addEventListener('click', this.closeDropdowns);
  },
  beforeUnmount() {
    document.removeEventListener('click', this.closeDropdowns);
  },
  methods: {
    toggleUserMenu() {
      this.userMenuOpen = !this.userMenuOpen;
    },
    closeDropdowns(event) {
      // Zamknij user menu jeśli kliknięto poza nim
      if (this.userMenuOpen && !event.target.closest('#user-menu-button') && !event.target.closest('[role="menuitem"]')) {
        this.userMenuOpen = false;
      }
      
      // Zamknij products dropdown jeśli kliknięto poza nim
      if (this.showProductsDropdown && !event.target.closest('.relative')) {
        this.showProductsDropdown = false;
      }
    },
    logout() {
      try {
        console.log('Starting logout from SiteHeader...');
        this.authStore.logout().then(() => {
          this.userMenuOpen = false;
          
          // Użyj routera zamiast window.location
          this.$router.push('/');
        }).catch(error => {
          console.error('Logout error in SiteHeader:', error);
          alert('Wystąpił błąd podczas wylogowywania.');
        });
      } catch (error) {
        console.error('Logout error in SiteHeader:', error);
        alert('Wystąpił błąd podczas wylogowywania.');
      }
    },
    navigateTo(path, event) {
      // Prevent default behavior
      if (event) {
        event.preventDefault();
      }
      
      // Close menus
      this.userMenuOpen = false;
      this.mobileMenuOpen = false;

      // Log navigation attempt
      console.log('Router navigating from', this.$route.path, 'to', path);
      
      // If navigating to same route as current, do nothing
      if (this.$route.path === path) {
        console.log('Already on path:', path);
        return;
      }
      
      // Navigate to new route
      this.$router.push(path).catch(err => {
        console.error('Navigation error:', err);
      });
    },
    toggleMobileMenu() {
      this.mobileMenuOpen = !this.mobileMenuOpen;
    },
    onMobileSearchInput(event) {
      // Clear existing timeout
      if (this.searchTimeout) {
        clearTimeout(this.searchTimeout);
      }
      
      if (this.mobileSearchQuery.length < 3) {
        this.mobileSearchResults = [];
        this.showMobileSearchDropdown = false;
        this.mobileSearchLoading = false;
        return;
      }

      this.mobileSearchLoading = true;
      this.showMobileSearchDropdown = true;
      
      // Add debounce for better performance
      this.searchTimeout = setTimeout(() => {
        this.performMobileSearchAPI();
      }, 300);
    },
    async performMobileSearchAPI() {
      if (this.mobileSearchQuery.length < 3) {
        this.mobileSearchLoading = false;
        return;
      }

      try {
        const response = await axios.get('/api/products', {
          params: {
            search: this.mobileSearchQuery,
            per_page: 6 // Limit results for mobile dropdown
          }
        });

        if (response.data.data && Array.isArray(response.data.data)) {
          this.mobileSearchResults = response.data.data;
          this.mobileSearchTotalResults = response.data.total || response.data.data.length;
        } else if (Array.isArray(response.data)) {
          this.mobileSearchResults = response.data;
          this.mobileSearchTotalResults = response.data.length;
        } else {
          this.mobileSearchResults = [];
          this.mobileSearchTotalResults = 0;
        }
      } catch (error) {
        console.error('Mobile search error:', error);
        this.mobileSearchResults = [];
        this.mobileSearchTotalResults = 0;
      } finally {
        this.mobileSearchLoading = false;
      }
    },
    performMobileSearch() {
      if (this.mobileSearchQuery.trim().length > 0) {
        // Close mobile menu and dropdown
        this.mobileMenuOpen = false;
        this.showMobileSearchDropdown = false;
        
        // Navigate to products page with search query
        this.router.push({
          path: '/products',
          query: { search: this.mobileSearchQuery.trim() }
        });
        
        // Clear search query and results
        this.mobileSearchQuery = '';
        this.mobileSearchResults = [];
      }
    },
    onMobileSearchBlur() {
      // Delay hiding dropdown to allow clicks on results
      setTimeout(() => {
        this.showMobileSearchDropdown = false;
      }, 200);
    },
    goToMobileProduct(product) {
      // Close mobile menu and dropdown
      this.mobileMenuOpen = false;
      this.showMobileSearchDropdown = false;
      
      // Navigate to product page
      this.router.push(`/products/${product.id}`);
      
      // Clear search query and results
      this.mobileSearchQuery = '';
      this.mobileSearchResults = [];
    },
    viewAllMobileResults() {
      if (this.mobileSearchQuery.trim().length > 0) {
        // Close mobile menu and dropdown
        this.mobileMenuOpen = false;
        this.showMobileSearchDropdown = false;
        
        // Navigate to products page with search query
        this.router.push({
          path: '/products',
          query: { search: this.mobileSearchQuery.trim() }
        });
        
        // Clear search query and results
        this.mobileSearchQuery = '';
        this.mobileSearchResults = [];
      }
    },
    formatPrice(price) {
      if (price === null || price === undefined || isNaN(price)) {
        return '0.00';
      }
      return parseFloat(price).toFixed(2);
    },
    hasPromotion(product) {
      return product.promotion_price && product.promotion_price < product.price;
    }
  }
}
</script> 