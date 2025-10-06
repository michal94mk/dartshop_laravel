<template>
  <div>
    <header class="bg-white shadow-sm border-b border-gray-200">
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
          <div class="relative flex items-center">
            <button 
              @click="toggleProductsDropdown($event)"
              class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium h-16 focus:outline-none"
              :class="[$route.path.includes('/categories') || $route.path.includes('/products') ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700']"
            >
              Produkty
              <svg class="ml-1 h-4 w-4 transition-transform duration-200" :class="{ 'rotate-180': showProductsDropdown }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
              </svg>
            </button>
            
            <!-- Dropdown Menu -->
            <div 
              v-show="showProductsDropdown"
              class="products-dropdown absolute left-0 top-full mt-1 w-56 origin-top-left bg-white border border-gray-200 divide-y divide-gray-100 rounded-md shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none z-50"
            >
              <div class="py-1">
                <router-link 
                  to="/products" 
                  class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-600"
                  @click.prevent="navigateTo('/products', $event)"
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
                  @click.prevent="navigateToCategory(category.id, $event)"
                >
                  <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                  </svg>
                  {{ category.name }}
                  <span v-if="category.products_count > 0" class="ml-auto text-xs text-gray-400">({{ category.products_count }})</span>
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
  </header>

  <!-- Mobile menu - Teleported to body for proper fixed positioning -->
  <Teleport to="body">
    <div class="lg:hidden">
      <!-- Backdrop/Overlay -->
      <div 
        class="fixed inset-0 bg-black transition-opacity duration-300 z-[99998]"
        :class="mobileMenuOpen ? 'opacity-50 pointer-events-auto' : 'opacity-0 pointer-events-none'" 
        @click="mobileMenuOpen = false"
      ></div>
      
      <!-- Slide-out Menu -->
      <div 
        class="fixed top-0 bottom-0 left-0 w-80 bg-white shadow-xl transform transition-transform duration-300 ease-in-out overflow-y-auto z-[99999]"
        :class="mobileMenuOpen ? 'translate-x-0' : '-translate-x-full'"
      >
        
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
                        :src="getProductImageUrl(product.image_url, product.name, 48, 48)"
                        :alt="product.name"
                        class="h-12 w-12 rounded-lg object-cover shadow-md"
                        @error="(e) => handleImageError(e, product.name, 48, 48)"
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
            <button
              @click="toggleMobileProductsDropdown"
              class="flex items-center justify-between w-full px-4 py-3 text-base font-medium text-left focus:outline-none"
              :class="[$route.path.includes('/categories') || $route.path.includes('/products') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-800']"
            >
              <div class="flex items-center">
                <i class="fas fa-box w-5 mr-3 text-gray-400"></i>
                Produkty
              </div>
              <svg class="h-4 w-4 transition-transform duration-200" :class="{ 'rotate-180': showMobileProductsDropdown }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
              </svg>
            </button>
            
            <!-- Mobile categories submenu -->
            <div v-show="showMobileProductsDropdown" class="ml-8 space-y-1 border-l border-gray-200 pl-4">
              <router-link
                to="/products"
                class="block py-2 text-sm text-gray-500 hover:text-gray-700 hover:bg-gray-50 rounded px-2"
                @click.prevent="navigateTo('/products', $event)"
              >
                <i class="fas fa-list w-4 mr-2 text-gray-400"></i>
                Wszystkie produkty
              </router-link>
              <router-link
                v-for="category in categoryStore.orderedCategories.filter(cat => cat.is_active !== false && cat.products_count > 0)"
                :key="`mobile-${category.id}`"
                :to="`/products?category=${category.id}`"
                class="block py-2 text-sm text-gray-500 hover:text-gray-700 hover:bg-gray-50 rounded px-2"
                @click.prevent="navigateToCategory(category.id, $event)"
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
  </Teleport>
  </div>
</template>

<script>
import { useAuthStore } from '../../stores/authStore';
import { useCartStore } from '../../stores/cartStore';
import { useCategoryStore } from '../../stores/categoryStore';
import { useAlertStore } from '../../stores/alertStore';
import { useProductStore } from '../../stores/productStore';
import { storeToRefs } from 'pinia';
import { useRouter, useRoute } from 'vue-router';
import { ref, computed, onMounted, watch, onUnmounted } from 'vue';
import ProductSearch from '../ui/ProductSearch.vue';
import { getProductImageUrl, handleImageError } from '../../utils/imageHelpers';
import axios from 'axios';

export default {
  name: 'SiteHeader',
  components: {
    ProductSearch
  },
  setup() {
    const router = useRouter()
    const route = useRoute()
    const authStore = useAuthStore()
    const cartStore = useCartStore()
    const categoryStore = useCategoryStore()
    const alertStore = useAlertStore()
    const productStore = useProductStore()

    const showProductsDropdown = ref(false)
    const userMenuOpen = ref(false)
    const mobileMenuOpen = ref(false)
    const mobileProductsOpen = ref(false)
    const showMobileProductsDropdown = ref(false)
    const mobileSearchQuery = ref('')
    const mobileSearchFocused = ref(false)
    const showMobileSearchDropdown = ref(false)
    const mobileSearchResults = ref([])
    const mobileSearchLoading = ref(false)
    const mobileSearchTotalResults = ref(0)

    // Używamy storeToRefs, aby zachować reaktywność getterów i state w Pinia
    const { isLoggedIn, isAdmin, userName, userEmail, userInitial } = storeToRefs(authStore)
    const { totalItems: cartItemsCount } = storeToRefs(cartStore)
    
    // Computed property for top categories (show all categories, regardless of product count)
    const topCategories = computed(() => {
      return categoryStore.orderedCategories
        .filter(cat => cat.is_active !== false) // Only check is_active, not products_count
        .slice(0, 10) // Show up to 10 categories instead of 5
    })

    // Close dropdowns when clicking outside
    const handleClickOutside = (event) => {
      const productsDropdown = document.querySelector('.products-dropdown')
      const toggleButton = event.target.closest('button')
      
      // Don't close if clicking the toggle button or inside dropdown
      if (toggleButton && toggleButton.textContent.includes('Produkty')) {
        return
      }
      
      if (productsDropdown && !productsDropdown.contains(event.target)) {
        showProductsDropdown.value = false
      }
    }

    // Watch for route changes to refresh categories when navigating to/from admin
    watch(
      () => route.fullPath,
      async (newPath) => {
        showProductsDropdown.value = false
        userMenuOpen.value = false
        mobileMenuOpen.value = false
        showMobileProductsDropdown.value = false
        
        // Force refresh categories when coming from admin routes
        if (!newPath.startsWith('/admin') && route.fullPath.startsWith('/admin')) {
          await categoryStore.forceRefreshCategories()
        }
      }
    )

    // Watch for auth state changes
    watch(() => authStore.isLoggedIn, async (newValue, oldValue) => {
      if (newValue !== oldValue) {
        console.log('Auth state changed in SiteHeader.vue, reloading data...');
        // Clear search results and reload if needed
        mobileSearchResults.value = [];
        mobileSearchQuery.value = '';
      }
    });

    // Mobile search functionality
    let searchTimeout = null

    const onMobileSearchInput = () => {
      if (searchTimeout) {
        clearTimeout(searchTimeout)
      }
      
      if (mobileSearchQuery.value.length >= 3) {
        mobileSearchLoading.value = true
        searchTimeout = setTimeout(async () => {
          try {
            const response = await axios.get(`/api/products`, {
              params: {
                search: mobileSearchQuery.value,
                per_page: 5
              }
            })
            
            // Handle new API response format (BaseApiController)
            if (response.data.success && response.data.data) {
              // New format: { success: true, data: { data: [...], total: number } }
              const responseData = response.data.data;
              
              if (responseData.data && Array.isArray(responseData.data)) {
                mobileSearchResults.value = responseData.data;
                mobileSearchTotalResults.value = responseData.total;
              } else if (Array.isArray(responseData)) {
                mobileSearchResults.value = responseData;
                mobileSearchTotalResults.value = responseData.length;
              } else {
                mobileSearchResults.value = [];
                mobileSearchTotalResults.value = 0;
              }
            } else if (response.data.data && Array.isArray(response.data.data)) {
              // Fallback for old format: { data: [...], total: number }
              mobileSearchResults.value = response.data.data;
              mobileSearchTotalResults.value = response.data.total;
            } else if (Array.isArray(response.data)) {
              // Direct array response
              mobileSearchResults.value = response.data;
              mobileSearchTotalResults.value = response.data.length;
            } else {
              mobileSearchResults.value = [];
              mobileSearchTotalResults.value = 0;
            }
            showMobileSearchDropdown.value = true
          } catch (error) {
            console.error('Error searching products:', error)
          } finally {
            mobileSearchLoading.value = false
          }
        }, 300)
      } else {
        mobileSearchResults.value = []
        showMobileSearchDropdown.value = false
      }
    }

    const onMobileSearchBlur = () => {
      setTimeout(() => {
        showMobileSearchDropdown.value = false
        mobileSearchFocused.value = false
      }, 200)
    }

    const performMobileSearch = () => {
      if (mobileSearchQuery.value.trim()) {
        router.push({
          path: '/products',
          query: { search: mobileSearchQuery.value }
        })
        mobileMenuOpen.value = false
        showMobileSearchDropdown.value = false
        mobileSearchQuery.value = ''
      }
    }

    const goToMobileProduct = (product) => {
      router.push(`/products/${product.id}`)
      mobileMenuOpen.value = false
      showMobileSearchDropdown.value = false
      mobileSearchQuery.value = ''
    }

    const viewAllMobileResults = () => {
      router.push({
        path: '/products',
        query: { search: mobileSearchQuery.value }
      })
      mobileMenuOpen.value = false
      showMobileSearchDropdown.value = false
      mobileSearchQuery.value = ''
    }

    // Navigation functions
    const navigateTo = (path, event) => {
      if (event) {
        event.preventDefault()
      }
      router.push(path)
      // Close any open dropdowns
      showProductsDropdown.value = false
      userMenuOpen.value = false
      mobileMenuOpen.value = false
    }

    const navigateToCategory = (categoryId, event) => {
      if (event) {
        event.preventDefault()
      }
      router.push(`/products?category=${categoryId}`)
      showProductsDropdown.value = false
    }

    const toggleProductsDropdown = (event) => {
      if (event) {
        event.stopPropagation()
      }
      
      showProductsDropdown.value = !showProductsDropdown.value
      userMenuOpen.value = false
    }

    const toggleUserMenu = () => {
      userMenuOpen.value = !userMenuOpen.value
      showProductsDropdown.value = false
    }

    const toggleMobileMenu = () => {
      mobileMenuOpen.value = !mobileMenuOpen.value
    }

    // Watch for mobile menu state changes to control body scroll
    watch(mobileMenuOpen, (isOpen) => {
      if (isOpen) {
        // Lock body scroll - menu has position fixed so it will always be visible
        document.body.classList.add('overflow-hidden')
        // Prevent iOS bounce scrolling
        document.body.style.touchAction = 'none'
      } else {
        // Restore body scroll
        document.body.classList.remove('overflow-hidden')
        document.body.style.touchAction = ''
      }
    })

    const toggleMobileProductsDropdown = () => {
      showMobileProductsDropdown.value = !showMobileProductsDropdown.value
    }

    const logout = async () => {
      try {
        // Show logout message immediately
        alertStore.success('👋 Do zobaczenia! Zostałeś pomyślnie wylogowany.', 5000);
        
        // Small delay to show message
        await new Promise(resolve => setTimeout(resolve, 100));
        
        // Set loading state to prevent API calls during logout
        authStore.isLoading = true;
        
        const success = await authStore.logout()
        
        if (success) {
          // Wait for state to update before redirecting
          setTimeout(() => {
            router.push('/')
          }, 200)
        }
      } catch (error) {
        console.error('Logout error:', error)
        // Don't show error message during logout
        setTimeout(() => {
          router.push('/')
        }, 200)
      } finally {
        // Reset loading state
        authStore.isLoading = false;
      }
    }

    // Initialize stores - only one onMounted hook
    onMounted(async () => {
      await cartStore.initCart()
      await categoryStore.fetchCategories()
      document.addEventListener('click', handleClickOutside)
    })

    // Cleanup on component unmount
    onUnmounted(() => {
      document.removeEventListener('click', handleClickOutside)
      // Make sure to restore scroll when component is unmounted
      document.body.classList.remove('overflow-hidden')
      document.body.style.touchAction = ''
    })

    return {
      // Reactive refs
      showProductsDropdown,
      userMenuOpen,
      mobileMenuOpen,
      mobileProductsOpen,
      showMobileProductsDropdown,
      mobileSearchQuery,
      mobileSearchFocused,
      showMobileSearchDropdown,
      mobileSearchResults,
      mobileSearchLoading,
      mobileSearchTotalResults,
      
      // Computed properties
      topCategories,
      
      // Store refs
      isLoggedIn,
      isAdmin,
      userName,
      userEmail,
      userInitial,
      cartItemsCount,
      
      // Store instances
      categoryStore,
      
      // Methods
      navigateTo,
      navigateToCategory,
      toggleProductsDropdown,
      toggleUserMenu,
      toggleMobileMenu,
      toggleMobileProductsDropdown,
      logout,
      onMobileSearchInput,
      onMobileSearchBlur,
      performMobileSearch,
      goToMobileProduct,
      viewAllMobileResults,
      
      // Helper functions
      hasPromotion: (product) => {
        return product.promotion_price && product.promotion_price < product.price
      },
      formatPrice: (price) => {
        return Number(price).toFixed(2)
      },
              getProductImageUrl,
        handleImageError
    }
  }
}
</script> 