<template>
  <div class="space-y-6 p-4 bg-white rounded-lg shadow-sm min-h-full">
    <!-- Page Header -->
    <div class="px-6 py-4">
      <page-header
        title="Zarządzanie produktami"
      >
        <template #actions>
          <admin-button
            variant="primary"
            @click="openModal()"
          >
            Dodaj
          </admin-button>
        </template>
      </page-header>
    </div>

    <!-- Filters -->
    <search-filters
      v-if="!loading"
      :filters="filters"
      :sort-options="sortOptions"
      :default-filters="defaultFilters"
      search-label="Wyszukaj"
      search-placeholder="Nazwa produktu..."
      @update:filters="(newFilters) => { Object.assign(filters, newFilters); filters.page = 1; }"
      @filter-change="fetchProducts"
      @reset-filters="resetFilters"
    >
      <template v-slot:filters>
        <div class="w-full sm:w-auto">
          <label for="category" class="block text-sm font-medium text-gray-700">Kategoria</label>
          <select
            id="category"
            name="category"
            v-model="filters.category_id"
            @change="() => { filters.page = 1; fetchProducts(); }"
            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
          >
            <option value="">Wszystkie kategorie</option>
            <option v-for="category in categories" :key="category.id" :value="category.id">
              {{ category.name }}
            </option>
          </select>
        </div>
        
        <div class="w-full sm:w-auto">
          <label for="brand" class="block text-sm font-medium text-gray-700">Marka</label>
          <select
            id="brand"
            name="brand"
            v-model="filters.brand_id"
            @change="() => { filters.page = 1; fetchProducts(); }"
            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
          >
            <option value="">Wszystkie marki</option>
            <option v-for="brand in brands" :key="brand.id" :value="brand.id">
              {{ brand.name }}
            </option>
          </select>
        </div>
      </template>
    </search-filters>

    <!-- Content -->
    <div class="bg-white shadow rounded-lg">
      <!-- Loading indicator -->
      <loading-spinner v-if="loading" />

      <!-- Products Custom Table -->
      <div v-if="!loading && products.data && products.data.length > 0" class="mt-6 bg-white shadow-sm rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-56">
                  Produkt
                </th>
                <th scope="col" class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-24">
                  Kategoria
                </th>
                <th scope="col" class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-24">
                  Marka
                </th>
                <th scope="col" class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-20">
                  Cena
                </th>
                <th scope="col" class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-28">
                  Stan magazynu
                </th>
                <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider w-36">
                  Akcje
                </th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="item in products.data" :key="item.id" class="hover:bg-gray-50">
                <!-- Product Column -->
                <td class="px-4 py-4">
                  <div class="flex items-center">
                    <div class="h-10 w-10 flex-shrink-0">
                      <img 
                        v-if="item.image && !item.imageError" 
                        :src="item.fallbackSrc || getImageSrc(item.image)" 
                        class="h-10 w-10 rounded-full object-cover" 
                        @error="tryFallbackImage(item)"
                        alt="Product image" 
                      />
                      <div v-else class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                        <svg class="h-6 w-6 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                      </div>
                    </div>
                    <div class="ml-4">
                      <div class="text-sm font-medium text-gray-900">{{ item.name }}</div>
                      <div class="text-xs text-gray-500 truncate max-w-[180px]" :title="item.description">{{ truncate(item.description, 40) }}</div>
                    </div>
                  </div>
                </td>
                
                <!-- Category Column -->
                <td class="px-3 py-4 text-center">
                  <admin-badge v-if="item.category" variant="secondary" size="xs">
                    {{ item.category.name }}
                  </admin-badge>
                  <span v-else class="text-gray-400 text-xs">-</span>
                </td>
                
                <!-- Brand Column -->
                <td class="px-3 py-4 text-center">
                  <admin-badge v-if="item.brand" variant="secondary" size="xs">
                    {{ item.brand.name }}
                  </admin-badge>
                  <span v-else class="text-gray-400 text-xs">-</span>
                </td>
                
                <!-- Price Column -->
                <td class="px-3 py-4 text-center">
                  <span class="text-sm font-medium text-gray-900">{{ item.price }} PLN</span>
                </td>
                
                <!-- Stock Status Column -->
                <td class="px-3 py-4 text-center">
                  <admin-badge :variant="getStockStatusVariant(item.stock_quantity)" size="xs">
                    {{ getStockStatusLabel(item.stock_quantity) }}
                  </admin-badge>
                </td>
                
                <!-- Actions Column -->
                <td class="px-4 py-4 text-right">
                  <action-buttons 
                    :item="item" 
                    :show-details="true"
                    @details="showProductDetails"
                    @edit="openModal" 
                    @delete="deleteProduct"
                    justify="end"
                  />
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      
      <!-- Pagination -->
      <pagination 
        v-if="products.data && products.data.length > 0 && products.last_page > 1"
        :pagination="products" 
        items-label="produktów" 
        @page-change="goToPage" 
      />
      
      <!-- No data message -->
      <no-data-message 
        v-if="!loading && (!products.data || products.data.length === 0)" 
        message="Brak produktów do wyświetlenia" 
      />
    </div>
  </div>

  <!-- Product Modal -->
  <div v-if="showModal" class="fixed z-10 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
      <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" @click="showModal = false"></div>
      <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
      <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
        <form @submit.prevent="saveProduct">
          <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
            <div class="sm:flex sm:items-start">
              <div class="w-full">
                <div class="flex justify-between items-center mb-4">
                  <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                    {{ currentProduct.id ? 'Edytuj produkt' : 'Dodaj nowy produkt' }}
                  </h3>
                  <button @click="showModal = false" class="text-gray-400 hover:text-gray-500">
                    <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                  </button>
                </div>
                
                <div class="grid grid-cols-1 gap-4">
                  <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Nazwa produktu</label>
                    <input
                      type="text"
                      id="name"
                      v-model="currentProduct.name"
                      required
                      class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                    />
                  </div>
                  
                  <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">Opis</label>
                    <textarea
                      id="description"
                      v-model="currentProduct.description"
                      required
                      rows="3"
                      class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                    ></textarea>
                  </div>
                  
                  <div class="grid grid-cols-2 gap-4">
                    <div>
                      <label for="price" class="block text-sm font-medium text-gray-700">Cena</label>
                      <input
                        type="number"
                        id="price"
                        v-model="currentProduct.price"
                        required
                        min="0"
                        step="0.01"
                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                      />
                    </div>
                  </div>
                  
                  <div class="grid grid-cols-2 gap-4">
                    <div>
                      <label for="category_id" class="block text-sm font-medium text-gray-700">Kategoria</label>
                      <select
                        id="category_id"
                        v-model="currentProduct.category_id"
                        required
                        class="mt-1 block w-full bg-white border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                      >
                        <option v-for="category in categories" :key="category.id" :value="category.id">
                          {{ category.name }}
                        </option>
                      </select>
                    </div>
                    
                    <div>
                      <label for="brand_id" class="block text-sm font-medium text-gray-700">Marka</label>
                      <select
                        id="brand_id"
                        v-model="currentProduct.brand_id"
                        required
                        class="mt-1 block w-full bg-white border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                      >
                        <option v-for="brand in brands" :key="brand.id" :value="brand.id">
                          {{ brand.name }}
                        </option>
                      </select>
                    </div>
                  </div>
                  
                  <div>
                    <label class="block text-sm font-medium text-gray-700">Zdjęcie produktu</label>
                    <div class="mt-1 flex items-center">
                      <span v-if="currentProduct.image && !isFileObject(currentProduct.image) && !currentProductImageError" class="inline-block h-12 w-12 rounded-md overflow-hidden bg-gray-100">
                        <img 
                          :src="currentProduct.fallbackSrc || getImageSrc(currentProduct.image)" 
                          class="h-full w-full object-cover modal-product-image" 
                          @error="tryModalFallbackImage()" 
                          alt="Product image"
                        />
                      </span>
                      <span v-else-if="currentProduct.image && isFileObject(currentProduct.image)" class="inline-block h-12 w-12 rounded-md overflow-hidden bg-gray-100">
                        <img 
                          :src="getImagePreviewUrl(currentProduct.image)" 
                          class="h-full w-full object-cover"
                          alt="Uploaded image"
                        />
                      </span>
                      <span v-else class="inline-block h-12 w-12 rounded-md overflow-hidden bg-gray-100">
                        <svg class="h-full w-full text-gray-300" fill="currentColor" viewBox="0 0 24 24">
                          <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                      </span>
                      <input
                        type="file"
                        id="file-upload"
                        class="hidden"
                        accept="image/*"
                        @change="handleFileChange"
                      />
                      <button
                        type="button"
                        @click="triggerFileUpload"
                        class="ml-5 bg-white py-2 px-3 border border-gray-300 rounded-md shadow-sm text-sm leading-4 font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                      >
                        {{ currentProduct.image ? 'Zmień zdjęcie' : 'Dodaj zdjęcie' }}
                      </button>
                      <button
                        v-if="currentProduct.image"
                        type="button"
                        @click="removeImage"
                        class="ml-2 bg-white py-2 px-3 border border-gray-300 rounded-md shadow-sm text-sm leading-4 font-medium text-red-600 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                      >
                        Usuń
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
            <button
              type="submit"
              class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm"
            >
              {{ currentProduct.id ? 'Zapisz zmiany' : 'Dodaj produkt' }}
            </button>
            <button
              type="button"
              @click="showModal = false"
              class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
            >
              Anuluj
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Delete confirmation modal -->
  <div v-if="showDeleteModal" class="fixed z-10 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
      <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" @click="showDeleteModal = false"></div>
      <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
      <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
          <div class="sm:flex sm:items-start">
            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
              <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
              </svg>
            </div>
            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
              <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                Usuń produkt
              </h3>
              <div class="mt-2">
                <p class="text-sm text-gray-500">
                  Czy na pewno chcesz usunąć ten produkt? Ta operacja jest nieodwracalna.
                </p>
              </div>
            </div>
          </div>
        </div>
        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
          <button @click="confirmDelete" type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
            Usuń produkt
          </button>
          <button @click="showDeleteModal = false" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
            Anuluj
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Product Details Modal -->
  <div v-if="showProductDetailsModal" class="fixed z-10 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
      <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" @click="showProductDetailsModal = false"></div>
      <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
      <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
          <div class="sm:flex sm:items-start">
            <div class="w-full">
              <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4" id="modal-title">
                Szczegóły produktu
              </h3>
              
              <div v-if="selectedProductForDetails" class="space-y-6">
                <!-- Product Image -->
                <div v-if="selectedProductForDetails.image" class="flex justify-center">
                  <img 
                    :src="selectedProductForDetails.fallbackSrc || getImageSrc(selectedProductForDetails.image)" 
                    :alt="selectedProductForDetails.name"
                    class="w-32 h-32 object-cover rounded-lg shadow-md"
                    @error="tryFallbackImage(selectedProductForDetails)"
                  />
                </div>
                <div v-else class="flex justify-center">
                  <div class="w-32 h-32 bg-gray-200 rounded-lg flex items-center justify-center">
                    <svg class="h-16 w-16 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                  </div>
                </div>
                
                <!-- Basic Info -->
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                  <div>
                    <h4 class="text-sm font-medium text-gray-500">ID</h4>
                    <p class="mt-1 text-sm text-gray-900">{{ selectedProductForDetails.id }}</p>
                  </div>
                  
                  <div>
                    <h4 class="text-sm font-medium text-gray-500">SKU</h4>
                    <p class="mt-1 text-sm text-gray-900">{{ selectedProductForDetails.sku || 'Brak' }}</p>
                  </div>
                  
                  <div class="sm:col-span-2">
                    <h4 class="text-sm font-medium text-gray-500">Nazwa</h4>
                    <p class="mt-1 text-lg font-semibold text-gray-900">{{ selectedProductForDetails.name }}</p>
                  </div>
                  
                  <div>
                    <h4 class="text-sm font-medium text-gray-500">Cena</h4>
                    <p class="mt-1 text-lg font-semibold text-green-600">{{ selectedProductForDetails.price }} PLN</p>
                  </div>
                  
                  <div>
                    <h4 class="text-sm font-medium text-gray-500">Stan magazynowy</h4>
                    <p class="mt-1 text-sm text-gray-900">
                      <admin-badge :variant="getStockStatusVariant(selectedProductForDetails.stock_quantity)">
                        {{ getStockStatusLabel(selectedProductForDetails.stock_quantity) }}
                      </admin-badge>
                    </p>
                  </div>
                </div>
                
                <!-- Category and Brand -->
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                  <div>
                    <h4 class="text-sm font-medium text-gray-500">Kategoria</h4>
                    <p class="mt-1 text-sm text-gray-900">
                      <admin-badge v-if="selectedProductForDetails.category" variant="secondary">
                        {{ selectedProductForDetails.category.name }}
                      </admin-badge>
                      <span v-else class="text-gray-400">Brak kategorii</span>
                    </p>
                  </div>
                  
                  <div>
                    <h4 class="text-sm font-medium text-gray-500">Marka</h4>
                    <p class="mt-1 text-sm text-gray-900">
                      <admin-badge v-if="selectedProductForDetails.brand" variant="secondary">
                        {{ selectedProductForDetails.brand.name }}
                      </admin-badge>
                      <span v-else class="text-gray-400">Brak marki</span>
                    </p>
                  </div>
                </div>
                
                <!-- Description -->
                <div v-if="selectedProductForDetails.description">
                  <h4 class="text-sm font-medium text-gray-500">Opis</h4>
                  <div class="mt-1 bg-gray-50 p-3 rounded text-sm text-gray-900">
                    <p>{{ selectedProductForDetails.description }}</p>
                  </div>
                </div>
                
                <!-- Additional Info -->
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                  <div>
                    <h4 class="text-sm font-medium text-gray-500">Data utworzenia</h4>
                    <p class="mt-1 text-sm text-gray-900">{{ new Date(selectedProductForDetails.created_at).toLocaleDateString('pl-PL') }}</p>
                  </div>
                  
                  <div>
                    <h4 class="text-sm font-medium text-gray-500">Ostatnia aktualizacja</h4>
                    <p class="mt-1 text-sm text-gray-900">{{ new Date(selectedProductForDetails.updated_at).toLocaleDateString('pl-PL') }}</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
          <button 
            @click="showProductDetailsModal = false" 
            class="w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:w-auto sm:text-sm"
          >
            Zamknij
          </button>
          <button 
            @click="() => { showProductDetailsModal = false; openModal(selectedProductForDetails); }" 
            class="mt-3 w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:mr-3 sm:w-auto sm:text-sm"
          >
            Edytuj
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, reactive, computed, onMounted, watch } from 'vue'
import { useAlertStore } from '../../stores/alertStore'
import { useAuthStore } from '../../stores/authStore'
import axios from 'axios'
import debounce from 'lodash/debounce'
import AdminButtonGroup from '../../components/admin/ui/AdminButtonGroup.vue'
import AdminButton from '../../components/admin/ui/AdminButton.vue'
import SearchFilters from '../../components/admin/SearchFilters.vue'
import LoadingSpinner from '../../components/admin/LoadingSpinner.vue'
import NoDataMessage from '../../components/admin/NoDataMessage.vue'
import Pagination from '../../components/admin/Pagination.vue'
import PageHeader from '../../components/admin/PageHeader.vue'
import ActionButtons from '../../components/admin/ActionButtons.vue'
import AdminBadge from '../../components/admin/ui/AdminBadge.vue'

export default {
  name: 'AdminProducts',
  components: {
    AdminButtonGroup,
    AdminButton,
    SearchFilters,
    LoadingSpinner,
    NoDataMessage,
    Pagination,
    PageHeader,
    ActionButtons,
    AdminBadge
  },
  setup() {
    const alertStore = useAlertStore()
    const authStore = useAuthStore()
    
    // Simple settings for stock status calculation
    const settings = reactive({
      lowStockThreshold: 10
    })

    // Other reactive data
    const submitting = ref(false)
    
    // Data
    const loading = ref(true)
    const products = ref({
      data: [],
      current_page: 1,
      from: 1,
      to: 0,
      total: 0,
      last_page: 1,
      per_page: 10
    })
    const categories = ref([])
    const brands = ref([])
    
    // Sort options for the filter component
    const sortOptions = [
      { value: 'created_at', label: 'Data dodania' },
      { value: 'name', label: 'Nazwa' },
      { value: 'price', label: 'Cena' }
    ]
    
    // Default filters
    const defaultFilters = {
      search: '',
      category_id: '',
      brand_id: '',
      sort_field: 'created_at',
      sort_direction: 'desc',
      page: 1
    }
    
    const filters = reactive({ ...defaultFilters })
    
    // Modals
    const showModal = ref(false)
    const showDeleteModal = ref(false)
    const showProductDetailsModal = ref(false)
    const selectedProductForDetails = ref(null)
    const productToDelete = ref(null)
    const currentProduct = ref({
      id: null,
      name: '',
      description: '',
      price: 0,
      category_id: '',
      brand_id: '',
      image: null
    })
    
    // Flag to track if the current product image failed to load
    const currentProductImageError = ref(false)
    
    // File input ref
    const fileInput = ref(null)
    
    // Computed
    const paginationPages = computed(() => {
      const total = products.value.last_page
      const current = products.value.current_page
      const pages = []
      
      if (total <= 7) {
        for (let i = 1; i <= total; i++) {
          pages.push(i)
        }
      } else {
        if (current <= 3) {
          pages.push(1, 2, 3, 4, '...', total)
        } else if (current >= total - 2) {
          pages.push(1, '...', total - 3, total - 2, total - 1, total)
        } else {
          pages.push(1, '...', current - 1, current, current + 1, '...', total)
        }
      }
      
      return pages
    })
    
    // Methods
    const fetchProducts = async () => {
      try {
        loading.value = true
        
        const params = {
          page: filters.page,
          search: filters.search,
          category_id: filters.category_id,
          brand_id: filters.brand_id,
          sort_field: filters.sort_field,
          sort_direction: filters.sort_direction
        }
        
        console.log('Fetching products with params:', params)
        console.log('Auth state:', {
          isLoggedIn: authStore.isLoggedIn,
          isAdmin: authStore.isAdmin,
          user: authStore.user
        })
        
        const response = await axios.get('/api/admin/products', { params })
        
        console.log('Products API response:', response.data)
        
        // Initialize imageError property for each product
        if (response.data && response.data.data) {
          response.data.data.forEach(product => {
            // Add the imageError property to track image loading failures
            product.imageError = false;
            
            // Pre-transform image paths for directly uploaded files
            if (product.image && (
                product.image.includes('/storage/products/') || 
                product.image.includes('storage/products/')
            )) {
              // This is a directly uploaded file, should work with the /storage symlink
              product.fallbackSrc = getApiBaseUrl() + (product.image.startsWith('/') ? product.image : `/${product.image}`);
            }
          });
        }
        
        products.value = response.data
      } catch (error) {
        console.error('Error fetching products:', error)
        console.error('Error details:', error.response?.data)
        console.error('Status:', error.response?.status)
        console.error('Status text:', error.response?.statusText)
        alertStore.error('Wystąpił błąd podczas pobierania produktów: ' + (error.response?.data?.message || error.message))
      } finally {
        loading.value = false
      }
    }
    
    const debouncedFetchProducts = debounce(fetchProducts, 300)
    
    const getImagePreviewUrl = (file) => {
      // Create a URL for File objects
      if (isFileObject(file) && typeof URL !== 'undefined') {
        return URL.createObjectURL(file);
      }
      return '';
    }
    
    // Setup API base URL for image references
    const getApiBaseUrl = () => {
      // Get from axios defaults if available
      if (axios.defaults.baseURL) {
        // Remove the /api part if present
        return axios.defaults.baseURL.replace(/\/api$/, '');
      }
      
      // Fallback to window.location origin
      return window.location.origin;
    };
    
    // Array of possible paths to try for product images
    const imageFallbackPaths = [
      // For user-uploaded images (which have /storage/products/ paths)
      (imageName) => `/storage/products/${imageName}`,
      
      // For the default product images (either directly in storage or in img)
      (imageName) => `/img/${imageName}`,
      (imageName) => `/storage/${imageName}`,
      
      // Less likely but worth trying
      (imageName) => `/images/${imageName}`,
      (imageName) => `/${imageName}`
    ];
    
    const getImageSrc = (imagePath) => {
      if (!imagePath) return '';
      
      // Get the API base URL (without /api) for full URLs
      const apiBaseUrl = getApiBaseUrl();
      
      // If it's already a full URL, return it as is
      if (imagePath.startsWith('http://') || imagePath.startsWith('https://')) {
        return imagePath;
      }
      
      // If it starts with '/storage/' already, make it absolute
      if (imagePath.startsWith('/storage/')) {
        // Ensure the path is absolute by prefixing with API base URL if needed
        return imagePath.startsWith('http') ? imagePath : `${apiBaseUrl}${imagePath}`;
      }
      
      // If it starts with a slash, make it absolute
      if (imagePath.startsWith('/')) {
        return `${apiBaseUrl}${imagePath}`;
      }
      
      // Check if it starts with 'storage/' (without leading slash)
      if (imagePath.startsWith('storage/')) {
        return `${apiBaseUrl}/${imagePath}`;
      }
      
      // For "simple" filenames like "product.jpg" try the img directory first
      const imgPath = `${apiBaseUrl}/img/${imagePath}`;
      return imgPath;
    };
    
    const isFileObject = (obj) => {
      // Check if it's a File object by looking for typical File properties
      return obj && 
        typeof obj === 'object' && 
        typeof obj.name === 'string' && 
        typeof obj.size === 'number' && 
        typeof obj.type === 'string';
    }
    
    const triggerFileUpload = () => {
      const fileInputElement = document.getElementById('file-upload');
      if (fileInputElement) {
        fileInputElement.click();
      }
    }
    
    const handleFileChange = (event) => {
      const file = event.target.files[0]
      if (file) {
        currentProduct.value.image = file
      }
    }
    
    const removeImage = () => {
      // Reset the image in the currentProduct
      currentProduct.value.image = null;
      
      // Reset the file input safely
      const fileInputElement = document.getElementById('file-upload');
      if (fileInputElement) {
        try {
          fileInputElement.value = '';
        } catch (e) {
          console.error('Error resetting file input:', e);
        }
      }
    }
    
    const fetchFormData = async () => {
      try {
        const response = await axios.get('/api/admin/products/form-data')
        categories.value = response.data.categories
        brands.value = response.data.brands
      } catch (error) {
        console.error('Error fetching form data:', error)
        console.error('Error details:', error.response?.data)
        alertStore.error('Wystąpił błąd podczas pobierania danych formularza.')
      }
    }
    
    const goToPage = (page) => {
      if (page === '...') return
      filters.page = page
      fetchProducts()
    }
    
    const openModal = (product = null) => {
      // Reset the image error flag and fallback index
      currentProductImageError.value = false
      modalFallbackPathIndex.value = 0
      
      if (product) {
        // Make a deep copy of the product to prevent reactivity issues
        currentProduct.value = {
          id: product.id,
          name: product.name,
          description: product.description,
          price: product.price,
          category_id: product.category_id,
          brand_id: product.brand_id,
          image: product.image,
          fallbackSrc: product.fallbackSrc // Keep the fallback source if already found
        }
      } else {
        // Default values for new product
        currentProduct.value = {
          id: null,
          name: '',
          description: '',
          price: 0,
          category_id: categories.value.length ? categories.value[0].id : '',
          brand_id: brands.value.length ? brands.value[0].id : '',
          image: null
        }
      }
      
      showModal.value = true
    }
    
    const saveProduct = async () => {
      try {
        loading.value = true
        
        // Check if we're dealing with a file upload
        const hasFileUpload = isFileObject(currentProduct.value.image);
        
        let response;
        
        if (hasFileUpload) {
          // Use FormData for file uploads
          const formData = new FormData();
          formData.append('name', currentProduct.value.name);
          formData.append('description', currentProduct.value.description);
          formData.append('price', parseFloat(currentProduct.value.price));
          formData.append('category_id', parseInt(currentProduct.value.category_id));
          formData.append('brand_id', parseInt(currentProduct.value.brand_id));
          formData.append('image', currentProduct.value.image);
          
          if (currentProduct.value.id) {
            // Update with file upload
            formData.append('_method', 'PUT'); // Laravel method spoofing
            
            response = await axios.post(`/api/admin/products/${currentProduct.value.id}`, formData, {
              headers: {
                'Content-Type': 'multipart/form-data',
              }
            });
          } else {
            // Create with file upload
            response = await axios.post('/api/admin/products', formData, {
              headers: {
                'Content-Type': 'multipart/form-data',
              }
            });
          }
        } else {
          // Regular JSON update (no file)
          const productData = { 
            name: currentProduct.value.name,
            description: currentProduct.value.description,
            price: parseFloat(currentProduct.value.price),
            category_id: parseInt(currentProduct.value.category_id),
            brand_id: parseInt(currentProduct.value.brand_id)
          };
          
          // Ensure CSRF token is available
          const csrfToken = document.cookie.match('(^|;)\\s*XSRF-TOKEN\\s*=\\s*([^;]+)')
          
          if (currentProduct.value.id) {
            // Update existing product
            const url = `/api/admin/products/${currentProduct.value.id}`;
            
            // Try with explicit headers and X-HTTP-Method-Override
            response = await axios({
              method: 'post',
              url: url,
              data: { 
                ...productData,
                _method: 'PUT'  // Laravel method spoofing
              },
              headers: {
                'Content-Type': 'application/json',
                'X-HTTP-Method-Override': 'PUT',
                'Accept': 'application/json'
              }
            });
          } else {
            // Create new product
            response = await axios.post('/api/admin/products', productData);
          }
        }
        
        if (currentProduct.value.id) {
          alertStore.success('Produkt został zaktualizowany.');
        } else {
          alertStore.success('Produkt został dodany.');
        }
        
        // Close modal and refresh products
        showModal.value = false;
        await fetchProducts();
      } catch (error) {
        console.error('Error saving product:', error);
        console.error('Error details:', error.response?.data);
        
        if (error.response?.data?.errors) {
          // Display validation errors
          const errors = Object.values(error.response.data.errors).flat();
          errors.forEach(err => alertStore.error(err));
        } else if (error.response?.data?.message) {
          alertStore.error(error.response.data.message);
        } else {
          alertStore.error('Wystąpił błąd podczas zapisywania produktu: ' + (error.message || 'Unknown error'));
        }
      } finally {
        loading.value = false;
      }
    }
    
    const deleteProduct = (product) => {
      // Handle both ID and object from ActionButtons
      let productId;
      if (typeof product === 'object' && product !== null) {
        productId = product.id;
      } else {
        productId = product;
      }
      
      if (!productId) {
        alertStore.error('Brak ID produktu do usunięcia');
        return;
      }
      
      productToDelete.value = productId;
      showDeleteModal.value = true;
    }
    
    const confirmDelete = async () => {
      try {
        loading.value = true
        
        const productId = productToDelete.value;
        
        if (!productId) {
          alertStore.error('Nie można usunąć produktu: brak ID.')
          return
        }
        
        // Send delete request
        const response = await axios.delete(`/api/admin/products/${productId}`)
        
        // Show success message
        alertStore.success('Produkt został usunięty.')
        
        // Close modal and refresh data
        showDeleteModal.value = false
        productToDelete.value = null
        await fetchProducts()
      } catch (error) {
        console.error('Error deleting product:', error)
        console.error('Error details:', error.response?.data)
        
        // Handle different error cases
        if (error.response?.status === 404) {
          alertStore.error('Produkt nie został znaleziony.')
        } else if (error.response?.data?.message) {
          alertStore.error(error.response.data.message)
        } else {
          alertStore.error('Wystąpił błąd podczas usuwania produktu: ' + (error.message || 'Unknown error'))
        }
        
        // Close modal
        showDeleteModal.value = false
      } finally {
        loading.value = false
      }
    }
    
    const truncate = (text, length) => {
      if (!text) return ''
      return text.length > length ? text.substring(0, length) + '...' : text
    }
    
    // Track which fallback path we're trying for each product
    const fallbackPathIndex = new Map();
    
    // Try fallback image paths when the primary one fails
    const tryFallbackImage = (product) => {
      if (!product.image) {
        product.imageError = true;
        return;
      }
      
      // Extract just the filename without any path
      let imageName = product.image;
      if (imageName.includes('/')) {
        imageName = imageName.substring(imageName.lastIndexOf('/') + 1);
      }
      
      // Initialize fallback index if not already set
      if (!fallbackPathIndex.has(product.id)) {
        fallbackPathIndex.set(product.id, 0);
      }
      
      // Get current index and increment for next fallback
      const currentIndex = fallbackPathIndex.get(product.id);
      fallbackPathIndex.set(product.id, currentIndex + 1);
      
      // If we've tried all fallbacks, give up and show placeholder
      if (currentIndex >= imageFallbackPaths.length) {
        product.imageError = true;
        return;
      }
      
      // Try the next fallback path
      const fallbackPathRelative = imageFallbackPaths[currentIndex](imageName);
      const apiBaseUrl = getApiBaseUrl();
      const fallbackPath = `${apiBaseUrl}${fallbackPathRelative}`;
      
      // Update the product's image path to use fallback
      product.fallbackSrc = fallbackPath;
      
      // Slight delay to prevent immediate error trigger again
      setTimeout(() => {
        const img = document.querySelector(`[data-product-id="${product.id}"] img`);
        if (img) {
          img.src = fallbackPath;
        }
      }, 100); // Increased timeout for more reliable loading
    }
    
    // Track which fallback path we're trying for the modal
    const modalFallbackPathIndex = ref(0);
    
    // Try fallback image paths for the current product in the modal
    const tryModalFallbackImage = () => {
      if (!currentProduct.value.image) {
        currentProductImageError.value = true;
        return;
      }
      
      // Extract just the filename without any path
      let imageName = currentProduct.value.image;
      if (imageName.includes('/')) {
        imageName = imageName.substring(imageName.lastIndexOf('/') + 1);
      }
      
      // Get current index and increment for next fallback
      const currentIndex = modalFallbackPathIndex.value;
      modalFallbackPathIndex.value++;
      
      // If we've tried all fallbacks, give up and show placeholder
      if (currentIndex >= imageFallbackPaths.length) {
        currentProductImageError.value = true;
        return;
      }
      
      // Try the next fallback path
      const fallbackPathRelative = imageFallbackPaths[currentIndex](imageName);
      const apiBaseUrl = getApiBaseUrl();
      const fallbackPath = `${apiBaseUrl}${fallbackPathRelative}`;
      
      // Update the current product's fallback source
      currentProduct.value.fallbackSrc = fallbackPath;
      
      // Slight delay to prevent immediate error trigger again
      setTimeout(() => {
        const img = document.querySelector('.modal-product-image');
        if (img) {
          img.src = fallbackPath;
        }
      }, 100);
    }
    
    const resetFilters = () => {
      Object.assign(filters, defaultFilters)
      fetchProducts()
    }
    
    // Watch for filter changes
    watch(() => filters.page, () => {
      fetchProducts()
    })
    
    // Lifecycle
    onMounted(() => {
      fetchFormData()
      fetchProducts()
    })
    
    // New helper functions for stock status
    const getStockStatusVariant = (stockQuantity) => {
      if (stockQuantity === undefined || stockQuantity === null) return 'secondary'
      if (stockQuantity === 0) return 'danger'
      if (stockQuantity <= settings.lowStockThreshold) return 'warning'
      return 'success'
    }

    const getStockStatusLabel = (stockQuantity) => {
      if (stockQuantity === undefined || stockQuantity === null) return 'Brak danych'
      if (stockQuantity === 0) return 'Niedostępny'
      if (stockQuantity <= settings.lowStockThreshold) return `Niski stan (${stockQuantity})`
      return `Dostępny (${stockQuantity})`
    }

    const showProductDetails = (product) => {
      console.log('Pokazuj szczegóły produktu:', product)
      selectedProductForDetails.value = product
      showProductDetailsModal.value = true
    }

    return {
      loading,
      products,
      categories,
      brands,
      filters,
      defaultFilters,
      sortOptions,
      paginationPages,
      showModal,
      showDeleteModal,
      showProductDetailsModal,
      selectedProductForDetails,
      currentProduct,
      currentProductImageError,
      settings,
      submitting,
      fetchProducts,
      debouncedFetchProducts,
      goToPage,
      openModal,
      saveProduct,
      deleteProduct,
      confirmDelete,
      truncate,
      handleFileChange,
      removeImage,
      isFileObject,
      getImagePreviewUrl,
      getImageSrc,
      triggerFileUpload,
      tryFallbackImage,
      tryModalFallbackImage,
      resetFilters,
      getStockStatusVariant,
      getStockStatusLabel,
      showProductDetails
    }
  }
}
</script> 