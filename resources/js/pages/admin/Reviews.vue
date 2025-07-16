<template>
  <div class="space-y-6 bg-white min-h-full lg:pr-6">
    <!-- Page Header -->
    <div class="px-6 py-4">
      <page-header 
        title="Recenzje produktów"
        add-button-label="Dodaj"
        @add="handleAddButtonClick"
      />
    </div>
    
    <!-- Featured Reviews Counter -->
    <div class="px-6 py-2">
      <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
        <div class="flex items-center">
          <div class="flex-shrink-0">
            <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
              <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
            </svg>
          </div>
          <div class="ml-3">
            <h3 class="text-sm font-medium text-blue-800">
              Wyróżnione recenzje: {{ featuredReviewsCount }}/6
            </h3>
            <div class="mt-1 text-sm text-blue-700">
              <span v-if="featuredReviewsCount < 6">
                Można wyróżnić jeszcze {{ 6 - featuredReviewsCount }} recenzji
              </span>
              <span v-else class="text-orange-600 font-medium">
                Osiągnięto maksymalną liczbę wyróżnionych recenzji
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Loading indicator -->
    <loading-spinner v-if="loading" />
    
    <!-- Search and Filters -->
    <search-filters
      v-else
      :filters="filters"
      :sort-options="sortOptions"
      :default-filters="defaultFilters"
      search-label="Wyszukaj"
      search-placeholder="Szukaj recenzji..."
      @update:filters="(newFilters) => { Object.assign(filters, newFilters); filters.page = 1; }"
      @filter-change="fetchReviews"
      @reset-filters="resetFilters"
    >
      <template v-slot:filters>
        <div class="w-full sm:w-auto">
          <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
          <select
            id="status"
            name="status"
            v-model="filters.approved"
            @change="fetchReviews"
            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
          >
            <option value="">Wszystkie</option>
            <option value="true">Zatwierdzone</option>
            <option value="false">Odrzucone</option>
          </select>
        </div>
        
        <div class="w-full sm:w-auto">
          <label for="featured" class="block text-sm font-medium text-gray-700">Wyróżnione</label>
          <select
            id="featured"
            name="featured"
            v-model="filters.featured"
            @change="fetchReviews"
            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
          >
            <option value="">Wszystkie</option>
            <option value="true">Wyróżnione</option>
            <option value="false">Zwykłe</option>
          </select>
        </div>
        
        <div class="w-full sm:w-auto">
          <label for="rating" class="block text-sm font-medium text-gray-700">Ocena</label>
          <select
            id="rating"
            name="rating"
            v-model="filters.rating"
            @change="fetchReviews"
            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
          >
            <option value="">Wszystkie</option>
            <option value="1">1 gwiazdka</option>
            <option value="2">2 gwiazdki</option>
            <option value="3">3 gwiazdki</option>
            <option value="4">4 gwiazdki</option>
            <option value="5">5 gwiazdek</option>
          </select>
        </div>
      </template>
    </search-filters>
    
    <!-- Reviews Custom Table -->
    <div v-if="!loading && reviews.data && reviews.data.length > 0" class="mt-6 bg-white shadow-sm rounded-lg overflow-hidden">
                      <div class="overflow-x-auto -mx-6 px-6" style="scrollbar-width: thin; scrollbar-color: #d1d5db #f3f4f6;">
          <table class="min-w-full divide-y divide-gray-200 whitespace-nowrap">
          <thead class="bg-gray-50">
            <tr>
              <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-80">
                Produkt
              </th>
              <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-48">
                Użytkownik
              </th>
              <th scope="col" class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-20">
                Ocena
              </th>
              <th scope="col" class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-24">
                Status
              </th>
              <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider w-32">
                Akcje
              </th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="item in reviews.data" :key="item.id" class="hover:bg-gray-50">
              <!-- Product Column -->
              <td class="px-4 py-4">
                <div class="text-sm font-medium text-gray-900">
                  {{ item.product.name }}
                </div>
                <div class="text-xs text-gray-500 mt-1" :title="item.title">
                  {{ item.title }}
                </div>
              </td>
              
              <!-- User Column -->
              <td class="px-4 py-4">
                <div class="flex items-center">
                  <div 
                    class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-800 flex items-center justify-center text-xs font-semibold cursor-help mr-3"
                    :title="item.user ? item.user.name : 'Anonim'"
                  >
                    {{ getUserInitials(item.user) }}
                  </div>
                  <div class="text-sm text-gray-900" :title="item.user ? item.user.name : 'Anonim'">
                    {{ item.user ? item.user.name : 'Anonim' }}
                  </div>
                </div>
              </td>
              
              <!-- Rating Column -->
              <td class="px-3 py-4 text-center">
                <div class="flex items-center justify-center">
                  <span class="text-sm font-medium text-gray-700">{{ item.rating }}</span>
                  <svg class="w-4 h-4 text-yellow-400 ml-1" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.176 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                  </svg>
                </div>
              </td>
              
              <!-- Status Column -->
              <td class="px-3 py-4">
                <div class="flex items-center justify-center space-x-1 cursor-pointer" @click="toggleApproval(item)">
                  <admin-badge 
                    :variant="item.is_approved ? 'green' : 'red'"
                    size="xs"
                    :title="item.is_approved ? 'Zatwierdzona - kliknij aby odrzucić' : 'Odrzucona - kliknij aby zatwierdzić'"
                  >
                    {{ item.is_approved ? '✓' : '✗' }}
                  </admin-badge>
                  <admin-badge 
                    :variant="item.is_featured ? 'blue' : 'gray'"
                    size="xs"
                    :title="item.is_featured ? 'Wyróżniona - kliknij aby usunąć wyróżnienie' : 'Zwykła - kliknij aby wyróżnić'"
                    @click.stop="toggleFeatured(item)"
                  >
                    {{ item.is_featured ? '★' : '☆' }}
                  </admin-badge>
                </div>
              </td>
              
              <!-- Actions Column -->
              <td class="px-4 py-4 text-right">
                <action-buttons 
                  :item="item" 
                  :show-details="true"
                  :show-edit="true"
                  @details="showReviewDetails"
                  @edit="editReview"
                  @delete="confirmDeleteReview"
                  justify="end"
                />
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    
    <!-- No data message -->
    <no-data-message v-if="!loading && (!reviews.data || reviews.data.length === 0)" message="Brak recenzji do wyświetlenia" />
    
    <!-- Pagination -->
    <pagination 
      v-if="!loading && reviews.data && reviews.data.length > 0" 
      :pagination="reviews" 
      items-label="recenzji" 
      @page-change="goToPage" 
    />
    
    <!-- Review Details Modal -->
    <admin-modal 
      :show="showDetailsModal" 
      title="Szczegóły recenzji"
      size="4xl"
      @close="showDetailsModal = false"
    >
      <div v-if="selectedReview" class="space-y-4">
        <div>
          <h4 class="text-sm font-medium text-gray-500">ID</h4>
          <p class="mt-1 text-sm text-gray-900">{{ selectedReview.id }}</p>
        </div>
        
        <div>
          <h4 class="text-sm font-medium text-gray-500">Produkt</h4>
          <p class="mt-1 text-sm text-gray-900">{{ selectedReview.product.name }}</p>
        </div>
        
        <div>
          <h4 class="text-sm font-medium text-gray-500">Użytkownik</h4>
          <p class="mt-1 text-sm text-gray-900">{{ selectedReview.user ? selectedReview.user.name : 'Anonim' }}</p>
        </div>
        
        <div>
          <h4 class="text-sm font-medium text-gray-500">Status</h4>
          <p class="mt-1">
            <span class="px-2 py-1 text-xs font-semibold rounded-full"
                  :class="{
                    'bg-green-100 text-green-800': selectedReview.is_approved,
                    'bg-red-100 text-red-800': !selectedReview.is_approved
                  }">
              {{ selectedReview.is_approved ? 'Zatwierdzona' : 'Odrzucona' }}
            </span>
          </p>
        </div>
        
        <div>
          <h4 class="text-sm font-medium text-gray-500">Wyróżnienie</h4>
          <p class="mt-1">
            <span class="px-2 py-1 text-xs font-semibold rounded-full"
                  :class="{
                    'bg-blue-100 text-blue-800': selectedReview.is_featured,
                    'bg-gray-100 text-gray-800': !selectedReview.is_featured
                  }">
              {{ selectedReview.is_featured ? 'Wyróżniona' : 'Zwykła' }}
            </span>
          </p>
        </div>
        
        <div>
          <h4 class="text-sm font-medium text-gray-500">Ocena</h4>
          <div class="mt-1 flex items-center">
            <span class="text-yellow-400">
              <span v-for="n in 5" :key="n" class="inline-block w-5">
                <svg v-if="n <= selectedReview.rating" class="h-5 w-5 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                  <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.176 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                </svg>
                <svg v-else class="h-5 w-5 fill-current text-gray-300" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                  <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                </svg>
              </span>
            </span>
            <span class="ml-2 text-sm text-gray-700">{{ selectedReview.rating }}/5</span>
          </div>
        </div>
        
        <div>
          <h4 class="text-sm font-medium text-gray-500">Data</h4>
          <p class="mt-1 text-sm text-gray-900">{{ formatDate(selectedReview.created_at) }}</p>
        </div>
        
        <div>
          <h4 class="text-sm font-medium text-gray-500">Tytuł</h4>
          <p class="mt-1 text-sm text-gray-900">{{ selectedReview.title }}</p>
        </div>
        
        <div>
          <h4 class="text-sm font-medium text-gray-500">Treść</h4>
          <div class="mt-1 bg-gray-50 p-3 rounded text-sm text-gray-900">
            <p>{{ selectedReview.content }}</p>
          </div>
        </div>
      </div>
      
      <template #footer>
        <admin-button-group justify="end">
          <admin-button 
            @click="showDetailsModal = false" 
            variant="secondary"
            outline
          >
            Zamknij
          </admin-button>
        </admin-button-group>
      </template>
    </admin-modal>
    
    <!-- Add Review Modal -->
    <admin-modal 
      :show="showCreateReviewModal" 
      title="Dodaj nową recenzję"
      size="4xl"
      @close="closeAddEditModal"
    >
      <div v-if="formDataLoading" class="py-10 text-center">
        <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-700"></div>
        <p class="mt-4 text-gray-600">Ładowanie danych formularza...</p>
      </div>
      
      <form v-else @submit.prevent="saveReview">
        <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
          <!-- Product selection -->
          <div class="sm:col-span-3">
            <label for="product" class="block text-sm font-medium text-gray-700">Produkt</label>
            <select 
              id="product" 
              v-model="editingReview.product_id" 
              class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
              required
            >
              <option v-for="product in formData.products" :key="product.id" :value="product.id">
                {{ product.name }}
              </option>
            </select>
          </div>
          
          <!-- User selection -->
          <div class="sm:col-span-3">
            <label for="user" class="block text-sm font-medium text-gray-700">Użytkownik</label>
            <select 
              id="user" 
              v-model="editingReview.user_id" 
              class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
              required
            >
              <option v-for="user in formData.users" :key="user.id" :value="user.id">
                {{ user.name }} ({{ user.email }})
              </option>
            </select>
          </div>
          
          <!-- Rating -->
          <div class="sm:col-span-2">
            <label for="rating" class="block text-sm font-medium text-gray-700">Ocena</label>
            <select 
              id="rating" 
              v-model="editingReview.rating" 
              class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
              required
            >
              <option v-for="n in 5" :key="n" :value="n">{{ n }} gwiazdek</option>
            </select>
          </div>
          
          <!-- Is Approved -->
          <div class="sm:col-span-2">
            <label for="is_approved" class="block text-sm font-medium text-gray-700">Status</label>
            <select 
              id="is_approved" 
              v-model="editingReview.is_approved" 
              class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
            >
              <option :value="true">Zatwierdzona</option>
              <option :value="false">Odrzucona</option>
            </select>
          </div>
          
          <!-- Is Featured -->
          <div class="sm:col-span-2">
            <label for="is_featured" class="block text-sm font-medium text-gray-700">Wyróżnienie</label>
            <select 
              id="is_featured" 
              v-model="editingReview.is_featured" 
              class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
            >
              <option :value="true">Wyróżniona</option>
              <option :value="false">Zwykła</option>
            </select>
          </div>
          
          <!-- Title -->
          <div class="sm:col-span-6">
            <label for="title" class="block text-sm font-medium text-gray-700">Tytuł recenzji</label>
            <input 
              type="text" 
              id="title" 
              v-model="editingReview.title" 
              class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
              required
            />
          </div>
          
          <!-- Content -->
          <div class="sm:col-span-6">
            <label for="content" class="block text-sm font-medium text-gray-700">Treść recenzji</label>
            <textarea 
              id="content" 
              v-model="editingReview.content" 
              rows="4" 
              class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
              required
            ></textarea>
          </div>
        </div>
      </form>
      
      <template #footer>
        <admin-button-group justify="end" spacing="sm">
          <admin-button 
            @click="closeAddEditModal" 
            variant="secondary"
            outline
          >
            Anuluj
          </admin-button>
          <admin-button 
            @click="saveReview" 
            variant="primary"
            :loading="formSubmitting"
          >
            Dodaj recenzję
          </admin-button>
        </admin-button-group>
      </template>
    </admin-modal>
    
    <!-- Edit Review Modal -->
    <admin-modal 
      :show="showEditModal" 
      title="Edytuj recenzję"
      size="4xl"
      @close="closeAddEditModal"
    >
      <div v-if="formDataLoading" class="py-10 text-center">
        <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-700"></div>
        <p class="mt-4 text-gray-600">Ładowanie danych formularza...</p>
      </div>
      
      <form v-else @submit.prevent="saveReview">
        <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
          <!-- Product selection -->
          <div class="sm:col-span-3">
            <label for="edit-product" class="block text-sm font-medium text-gray-700">Produkt</label>
            <select 
              id="edit-product" 
              v-model="editingReview.product_id" 
              class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
              required
            >
              <option v-for="product in formData.products" :key="product.id" :value="product.id">
                {{ product.name }}
              </option>
            </select>
          </div>
          
          <!-- User selection -->
          <div class="sm:col-span-3">
            <label for="edit-user" class="block text-sm font-medium text-gray-700">Użytkownik</label>
            <select 
              id="edit-user" 
              v-model="editingReview.user_id" 
              class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
              required
            >
              <option v-for="user in formData.users" :key="user.id" :value="user.id">
                {{ user.name }} ({{ user.email }})
              </option>
            </select>
          </div>
          
          <!-- Rating -->
          <div class="sm:col-span-2">
            <label for="edit-rating" class="block text-sm font-medium text-gray-700">Ocena</label>
            <select 
              id="edit-rating" 
              v-model="editingReview.rating" 
              class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
              required
            >
              <option v-for="n in 5" :key="n" :value="n">{{ n }} gwiazdek</option>
            </select>
          </div>
          
          <!-- Is Approved -->
          <div class="sm:col-span-2">
            <label for="edit-is_approved" class="block text-sm font-medium text-gray-700">Status</label>
            <select 
              id="edit-is_approved" 
              v-model="editingReview.is_approved" 
              class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
            >
              <option :value="true">Zatwierdzona</option>
              <option :value="false">Odrzucona</option>
            </select>
          </div>
          
          <!-- Is Featured -->
          <div class="sm:col-span-2">
            <label for="edit-is_featured" class="block text-sm font-medium text-gray-700">Wyróżnienie</label>
            <select 
              id="edit-is_featured" 
              v-model="editingReview.is_featured" 
              class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
            >
              <option :value="true">Wyróżniona</option>
              <option :value="false">Zwykła</option>
            </select>
          </div>
          
          <!-- Title -->
          <div class="sm:col-span-6">
            <label for="edit-title" class="block text-sm font-medium text-gray-700">Tytuł recenzji</label>
            <input 
              type="text" 
              id="edit-title" 
              v-model="editingReview.title" 
              class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
              required
            />
          </div>
          
          <!-- Content -->
          <div class="sm:col-span-6">
            <label for="edit-content" class="block text-sm font-medium text-gray-700">Treść recenzji</label>
            <textarea 
              id="edit-content" 
              v-model="editingReview.content" 
              rows="4" 
              class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
              required
            ></textarea>
          </div>
        </div>
      </form>
      
      <template #footer>
        <admin-button-group justify="end" spacing="sm">
          <admin-button 
            @click="closeAddEditModal" 
            variant="secondary"
            outline
          >
            Anuluj
          </admin-button>
          <admin-button 
            @click="saveReview" 
            variant="primary"
            :loading="formSubmitting"
          >
            Zapisz zmiany
          </admin-button>
        </admin-button-group>
      </template>
    </admin-modal>
    
    <!-- Delete Confirmation Modal -->
    <admin-modal 
      :show="showDeleteModal" 
      title="Potwierdź usunięcie"
      size="md"
      @close="showDeleteModal = false"
    >
      <p class="text-sm text-gray-500">
        Czy na pewno chcesz usunąć tę recenzję? Ta operacja jest nieodwracalna.
      </p>
      
      <template #footer>
        <admin-button-group justify="end" spacing="sm">
          <admin-button 
            @click="showDeleteModal = false" 
            variant="secondary"
            outline
          >
            Anuluj
          </admin-button>
          <admin-button 
            @click="deleteReview()" 
            variant="danger"
          >
            Usuń
          </admin-button>
        </admin-button-group>
      </template>
    </admin-modal>

    <!-- Product Details Modal -->
    <admin-modal 
      :show="showProductDetailsModal" 
      title="Szczegóły produktu"
      size="4xl"
      @close="showProductDetailsModal = false"
    >
      <div v-if="selectedProduct" class="space-y-6">
        <!-- Product Image -->
        <div class="flex justify-center">
          <img 
            :src="getProductImageUrl(selectedProduct.image_url, selectedProduct.name, 128, 128)" 
            :alt="selectedProduct.name"
            class="w-32 h-32 object-cover rounded-lg shadow-md"
            @error="(e) => handleImageError(e, selectedProduct.name, 128, 128)"
          />
        </div>
        
        <!-- Basic Info -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
          <div>
            <h4 class="text-sm font-medium text-gray-500">ID</h4>
            <p class="mt-1 text-sm text-gray-900">{{ selectedProduct.id }}</p>
          </div>
          
          <div>
            <h4 class="text-sm font-medium text-gray-500">Nazwa</h4>
            <p class="mt-1 text-sm text-gray-900 font-medium">{{ selectedProduct.name }}</p>
          </div>
          
          <div>
            <h4 class="text-sm font-medium text-gray-500">Cena</h4>
            <p class="mt-1 text-lg font-semibold text-green-600">{{ selectedProduct.price }} zł</p>
          </div>
          
          <div v-if="selectedProduct.discounted_price">
            <h4 class="text-sm font-medium text-gray-500">Cena po rabacie</h4>
            <p class="mt-1 text-lg font-semibold text-red-600">{{ selectedProduct.discounted_price }} zł</p>
          </div>
          
          <div>
            <h4 class="text-sm font-medium text-gray-500">Stan magazynowy</h4>
            <p class="mt-1 text-sm text-gray-900">{{ selectedProduct.stock_quantity }} szt.</p>
          </div>
          
          <div>
            <h4 class="text-sm font-medium text-gray-500">SKU</h4>
            <p class="mt-1 text-sm text-gray-900">{{ selectedProduct.sku || 'Brak' }}</p>
          </div>
        </div>
        
        <!-- Category and Brand -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
          <div>
            <h4 class="text-sm font-medium text-gray-500">Kategoria</h4>
            <p class="mt-1 text-sm text-gray-900">{{ selectedProduct.category ? selectedProduct.category.name : 'Brak kategorii' }}</p>
          </div>
          
          <div>
            <h4 class="text-sm font-medium text-gray-500">Marka</h4>
            <p class="mt-1 text-sm text-gray-900">{{ selectedProduct.brand ? selectedProduct.brand.name : 'Brak marki' }}</p>
          </div>
        </div>
        
        <!-- Description -->
        <div v-if="selectedProduct.description">
          <h4 class="text-sm font-medium text-gray-500">Opis</h4>
          <div class="mt-1 bg-gray-50 p-3 rounded text-sm text-gray-900">
            <p>{{ selectedProduct.description }}</p>
          </div>
        </div>
        
        <!-- Features -->
        <div v-if="selectedProduct.features">
          <h4 class="text-sm font-medium text-gray-500">Cechy</h4>
          <div class="mt-1 bg-gray-50 p-3 rounded text-sm text-gray-900">
            <p>{{ selectedProduct.features }}</p>
          </div>
        </div>
        
        <!-- Dates -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
          <div>
            <h4 class="text-sm font-medium text-gray-500">Data utworzenia</h4>
            <p class="mt-1 text-sm text-gray-900">{{ formatDate(selectedProduct.created_at) }}</p>
          </div>
          
          <div>
            <h4 class="text-sm font-medium text-gray-500">Ostatnia aktualizacja</h4>
            <p class="mt-1 text-sm text-gray-900">{{ formatDate(selectedProduct.updated_at) }}</p>
          </div>
        </div>
      </div>
      
      <template #footer>
        <admin-button-group justify="end">
          <admin-button 
            @click="showProductDetailsModal = false" 
            variant="secondary"
            outline
          >
            Zamknij
          </admin-button>
        </admin-button-group>
      </template>
    </admin-modal>
  </div>
</template>

<script>
import { ref, computed, onMounted, reactive, watch } from 'vue'
import axios from 'axios'
import { useAlertStore } from '../../stores/alertStore'
import { getProductImageUrl, handleImageError } from '../../utils/imageHelpers'
import Modal from '../../components/admin/ui/AdminModal.vue'
import PageHeader from '../../components/admin/PageHeader.vue'
import AdminButtonGroup from '../../components/admin/ui/AdminButtonGroup.vue'
import AdminButton from '../../components/admin/ui/AdminButton.vue'
import AdminBadge from '../../components/admin/ui/AdminBadge.vue'
import SearchFilters from '../../components/admin/SearchFilters.vue'
import LoadingSpinner from '../../components/admin/LoadingSpinner.vue'
import NoDataMessage from '../../components/admin/NoDataMessage.vue'
import Pagination from '../../components/admin/Pagination.vue'
import ActionButtons from '../../components/admin/ActionButtons.vue'
import AdminModal from '../../components/admin/ui/AdminModal.vue'

export default {
  name: 'AdminReviews',
  components: {
    AdminButtonGroup,
    AdminButton,
    SearchFilters,
    LoadingSpinner,
    NoDataMessage,
    Pagination,
    PageHeader,
    ActionButtons,
    AdminModal: Modal,
    AdminBadge
  },
  setup() {
    const alertStore = useAlertStore()
    const loading = ref(true)
    const reviews = ref({
      data: [],
      current_page: 1,
      from: 1,
      to: 0,
      total: 0,
      last_page: 1,
      per_page: 10
    })
    const showDetailsModal = ref(false)
    const selectedReview = ref(null)
    const showCreateReviewModal = ref(false)
    const showEditModal = ref(false)
    const editingReview = ref(null)
    const isEditing = ref(false)
    const formData = ref({
      users: [],
      products: []
    })
    const formDataLoading = ref(false)
    const formSubmitting = ref(false)
    
    // Product details modal
    const showProductDetailsModal = ref(false)
    const selectedProduct = ref(null)
    
    // Sort options for filter component
    const sortOptions = [
      { value: 'created_at', label: 'Data dodania' },
      { value: 'rating', label: 'Ocena' },
      { value: 'product', label: 'Produkt' }
    ]
    
    // Default filters
    const defaultFilters = {
      approved: '',
      featured: '',
      rating: '',
      search: '',
      sort_field: 'created_at',
      sort_direction: 'desc',
      page: 1
    }
    
    // Filters
    const filters = reactive({ ...defaultFilters })
    
    // Fetch all reviews
    const fetchReviews = async () => {
      try {
        loading.value = true
        const response = await axios.get('/api/admin/reviews', {
          params: {
            page: filters.page,
            approved: filters.approved,
            featured: filters.featured,
            rating: filters.rating,
            search: filters.search,
            sort_field: filters.sort_field,
            sort_direction: filters.sort_direction
          }
        })
        reviews.value = response.data
      } catch (error) {
        console.error('Error fetching reviews:', error)
        alertStore.error('Wystąpił błąd podczas pobierania recenzji.')
      } finally {
        loading.value = false
      }
    }
    
    // Approve review
    const approveReview = async (review) => {
      try {
        const response = await axios.post(`/api/admin/reviews/${review.id}/approve`)
        // Update the review in the local array
        const index = reviews.value.data.findIndex(r => r.id === review.id)
        if (index !== -1) {
          reviews.value.data[index].is_approved = true
          
          // If this is the review being shown in the details modal, update it too
          if (selectedReview.value && selectedReview.value.id === review.id) {
            selectedReview.value.is_approved = true
          }
        }
        alertStore.success('Recenzja została zatwierdzona.')
        
        // Refresh featured count in case this affects featured reviews
        await fetchFeaturedCount()
      } catch (error) {
        console.error('Error approving review:', error)
        alertStore.error('Wystąpił błąd podczas zatwierdzania recenzji.')
      }
    }
    
    // Reject review
    const rejectReview = async (review) => {
      try {
        const response = await axios.post(`/api/admin/reviews/${review.id}/reject`)
        // Update the review in the local array
        const index = reviews.value.data.findIndex(r => r.id === review.id)
        if (index !== -1) {
          reviews.value.data[index].is_approved = false
          
          // If this is the review being shown in the details modal, update it too
          if (selectedReview.value && selectedReview.value.id === review.id) {
            selectedReview.value.is_approved = false
          }
        }
        alertStore.success('Recenzja została odrzucona.')
        
        // Refresh featured count in case this affects featured reviews
        await fetchFeaturedCount()
      } catch (error) {
        console.error('Error rejecting review:', error)
        alertStore.error('Wystąpił błąd podczas odrzucania recenzji.')
      }
    }
    
    // Show review details
    const showReviewDetails = (review) => {
      selectedReview.value = review
      showDetailsModal.value = true
    }
    
    // Show product details
    const showProductDetails = (product) => {
      selectedProduct.value = product
      showProductDetailsModal.value = true
    }
    
    // Format date
    const formatDate = (dateString) => {
      const options = { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' }
      return new Date(dateString).toLocaleDateString('pl-PL', options)
    }
    
    // Get status display name
    const getStatusName = (status) => {
      if (status === true || status === 'true') return 'Zatwierdzona';
      if (status === false || status === 'false') return 'Odrzucona';
      return status;
    }
    
    // Get user initials
    const getUserInitials = (user) => {
      if (!user || !user.name) return 'A';
      
      const nameParts = user.name.trim().split(' ');
      if (nameParts.length === 1) {
        return nameParts[0].charAt(0).toUpperCase();
      }
      
      return (nameParts[0].charAt(0) + nameParts[nameParts.length - 1].charAt(0)).toUpperCase();
    }
    
    // Filter reviews
    const filterReviews = () => {
      fetchReviews()
    }
    
    // Pagination
    const goToPage = (page) => {
      if (page === '...') return
      filters.page = page
      fetchReviews()
    }
    
    // Test to directly make an API request without interceptors
    const testFormDataAPI = async () => {
      try {
        console.log('Testing form data API directly')
        const response = await fetch('/api/admin/reviews/form-data')
        
        if (!response.ok) {
          console.error(`API response not OK: ${response.status} ${response.statusText}`)
          const text = await response.text()
          console.error('Response text:', text)
          return false
        }
        
        const data = await response.json()
        console.log('API test successful, received data:', data)
        return true
      } catch (error) {
        console.error('API test error:', error)
        return false
      }
    }
    
    // Handle the Add button click
    const handleAddButtonClick = async () => {
      console.log('Add button clicked')
      formDataLoading.value = true
      
      // Test the API first
      const apiTest = await testFormDataAPI()
      console.log('API test result:', apiTest)
      
      // Reset the form
      editingReview.value = {
        product_id: '',
        user_id: '',
        rating: 5,
        is_approved: true,
        is_featured: false,
        title: '',
        content: ''
      }
      
      // Set editing mode
      isEditing.value = false
      
      try {
        // Load form data for dropdowns
        await loadFormData()
        
        // Update defaults with loaded data
        if (formData.value.products && formData.value.products.length > 0) {
          editingReview.value.product_id = formData.value.products[0].id
        }
        
        if (formData.value.users && formData.value.users.length > 0) {
          editingReview.value.user_id = formData.value.users[0].id
        }
        
        // Show the modal
        showCreateReviewModal.value = true
        console.log('Add modal opened, form initialized with:', editingReview.value)
      } catch (error) {
        console.error('Error in handleAddButtonClick:', error)
        
        // Even if form data loading fails, we can still show the modal
        // This assumes the admin will manually select products and users
        showCreateReviewModal.value = true
        alertStore.error('Wystąpił błąd podczas ładowania danych formularza. Możesz kontynuować, ale będziesz musiał ręcznie wprowadzić wszystkie dane.')
      } finally {
        formDataLoading.value = false
      }
    }
    
    // Since we're using server-side filtering, no sorting function is needed
    // when we change the sort parameters, we'll just refetch the data
    const handleSortChange = () => {
      fetchReviews()
    }
    
    // Toggle featured status
    const toggleFeatured = async (review) => {
      try {
        const response = await axios.post(`/api/admin/reviews/${review.id}/toggle-featured`)
        
        // Update the review in the local array
        const index = reviews.value.data.findIndex(r => r.id === review.id)
        if (index !== -1) {
          reviews.value.data[index].is_featured = !reviews.value.data[index].is_featured
          
          // If this is the review being shown in the details modal, update it too
          if (selectedReview.value && selectedReview.value.id === review.id) {
            selectedReview.value.is_featured = !selectedReview.value.is_featured
          }
        }
        
        // Show success message from server response
        if (response.data && response.data.message) {
          alertStore.success(response.data.message)
        } else {
          alertStore.success('Status wyróżnienia został zmieniony.')
        }
        
        // Refresh featured count
        await fetchFeaturedCount()
      } catch (error) {
        console.error('Error toggling featured status:', error)
        
        // Handle specific error messages from server
        if (error.response && error.response.data && error.response.data.message) {
          alertStore.error(error.response.data.message)
        } else {
          alertStore.error('Wystąpił błąd podczas zmieniania statusu wyróżnienia.')
        }
      }
    }

    // Toggle approval status
    const toggleApproval = async (review) => {
      try {
        if (review.is_approved) {
          await rejectReview(review)
        } else {
          await approveReview(review)
        }
      } catch (error) {
        console.error('Error toggling approval status:', error)
        alertStore.error('Wystąpił błąd podczas zmieniania statusu zatwierdzenia.')
      }
    }
    
    // Close the add/edit modal
    const closeAddEditModal = () => {
      showCreateReviewModal.value = false
      showEditModal.value = false
      editingReview.value = {
        product_id: '',
        user_id: '',
        rating: 5,
        is_approved: true,
        is_featured: false,
        title: '',
        content: ''
      }
    }
    
    // Initialize new review
    const initNewReview = () => {
      console.log('Initializing new review')
      isEditing.value = false
      
      // Set default values, including a default product and user if available
      const defaultProductId = formData.value.products && formData.value.products.length > 0
        ? formData.value.products[0].id
        : null;
        
      const defaultUserId = formData.value.users && formData.value.users.length > 0
        ? formData.value.users[0].id
        : null;
        
      editingReview.value = {
        product_id: defaultProductId,
        user_id: defaultUserId,
        rating: 5,
        is_approved: true,
        is_featured: false,
        title: '',
        content: ''
      }
      
      console.log('New review initialized:', editingReview.value)
    }
    
    // Workaround function to get form data from separate calls
    const getFormDataWorkaround = async () => {
      console.log('Using workaround to get form data')
      try {
        // Get products 
        const productsResponse = await axios.get('/api/admin/products', { params: { per_page: 100 } })
        const products = productsResponse.data.data || productsResponse.data
        console.log('Products loaded:', products.length)
        
        // Get users
        const usersResponse = await axios.get('/api/admin/users', { params: { per_page: 100 } })
        const users = usersResponse.data.data || usersResponse.data
        console.log('Users loaded:', users.length)
        
        // Return the same format as the form-data endpoint would
        return {
          products,
          users
        }
      } catch (error) {
        console.error('Error in form data workaround:', error)
        throw error
      }
    }
    
    // Load form data for select options
    const loadFormData = async () => {
      console.log('Loading form data started')
      
      try {
        formDataLoading.value = true
        let formDataResponse
        
        try {
          // First try the official endpoint
          const apiUrl = '/api/admin/reviews/form-data'
          console.log('Fetching form data from API endpoint:', apiUrl)
          const response = await axios.get(apiUrl)
          formDataResponse = response.data
          console.log('Form data loaded from endpoint successfully')
        } catch (error) {
          console.error('Error loading from endpoint, using workaround:', error)
          // Fallback to our workaround method
          formDataResponse = await getFormDataWorkaround()
          console.log('Form data loaded via workaround')
        }
        
        if (formDataResponse) {
          console.log('Form data content:', {
            users: formDataResponse.users ? `${formDataResponse.users.length} users` : 'No users',
            products: formDataResponse.products ? `${formDataResponse.products.length} products` : 'No products'
          })
          
          if (formDataResponse.users && formDataResponse.products) {
            formData.value = formDataResponse
            console.log('Form data successfully loaded')
            return Promise.resolve()
          } else {
            console.error('Invalid form data format - missing users or products:', formDataResponse)
            alertStore.error('Nieprawidłowy format danych formularza - brak użytkowników lub produktów')
            return Promise.reject(new Error('Invalid form data format - missing users or products'))
          }
        } else {
          console.error('Invalid form data response - empty data')
          alertStore.error('Nieprawidłowy format odpowiedzi - brak danych')
          return Promise.reject(new Error('Invalid form data response - empty data'))
        }
      } catch (error) {
        console.error('Error loading form data:', error)
        
        // More detailed error messages
        if (error.response) {
          console.error('Error response:', {
            status: error.response.status,
            data: error.response.data,
            headers: error.response.headers
          })
          
          if (error.response.status === 404) {
            alertStore.error('Endpoint nie został znaleziony. Sprawdź adres URL formularza.')
          } else if (error.response.status === 500) {
            alertStore.error('Błąd serwera podczas ładowania danych formularza.')
          } else {
            alertStore.error(`Błąd (${error.response.status}) podczas ładowania danych formularza.`)
          }
        } else if (error.request) {
          console.error('Error request:', error.request)
          alertStore.error('Brak odpowiedzi z serwera. Sprawdź połączenie internetowe.')
        } else {
          alertStore.error(`Wystąpił błąd: ${error.message}`)
        }
        
        return Promise.reject(error)
      } finally {
        formDataLoading.value = false
        console.log('Form data loading completed')
      }
    }
    
    // Save review
    const saveReview = async () => {
      console.log('Saving review started:', editingReview.value)
      
      // Reset validation errors at the beginning
      const validationErrors = []
      
      if (!editingReview.value) {
        alertStore.error('Brak danych recenzji do zapisania.')
        return
      }
      
      // Validate required fields
      if (!editingReview.value.product_id) {
        validationErrors.push('Wybierz produkt dla recenzji.')
      }
      
      if (!editingReview.value.user_id) {
        validationErrors.push('Wybierz użytkownika dla recenzji.')
      }
      
      if (!editingReview.value.rating) {
        validationErrors.push('Wybierz ocenę dla recenzji.')
      }
      
      if (!editingReview.value.title || editingReview.value.title.trim() === '') {
        validationErrors.push('Tytuł recenzji jest wymagany.')
      }
      
      if (!editingReview.value.content || editingReview.value.content.trim() === '') {
        validationErrors.push('Treść recenzji jest wymagana.')
      }
      
      // If there are validation errors, show them and return
      if (validationErrors.length > 0) {
        console.log('Validation errors:', validationErrors)
        validationErrors.forEach(error => alertStore.error(error))
        return
      }
      
      formSubmitting.value = true
      
      try {
        let url = isEditing.value 
          ? `/api/admin/reviews/${editingReview.value.id}`
          : '/api/admin/reviews'
        
        let method = isEditing.value ? 'put' : 'post'
        
        console.log(`API Request: ${method.toUpperCase()} ${url}`)
        
        const response = await axios({
          method: method,
          url: url,
          data: editingReview.value
        })
        
        console.log('API Response:', response.status, response.data)
        
        alertStore.success(isEditing.value 
          ? 'Recenzja została zaktualizowana.' 
          : 'Nowa recenzja została dodana.'
        )
        
        closeAddEditModal()
        fetchReviews()
        fetchFeaturedCount()
      } catch (error) {
        console.error('Error saving review:', error)
        
        if (error.response && error.response.data && error.response.data.errors) {
          const errorMessages = Object.values(error.response.data.errors).flat().join(', ')
          alertStore.error(`Błąd walidacji: ${errorMessages}`)
        } else if (error.response && error.response.data && error.response.data.message) {
          alertStore.error(`Błąd: ${error.response.data.message}`)
        } else {
          alertStore.error('Wystąpił błąd podczas zapisywania recenzji.')
        }
      } finally {
        formSubmitting.value = false
      }
    }
    
    // Edit review
    const editReview = async (review) => {
      console.log('Editing review:', review)
      formDataLoading.value = true
      isEditing.value = true
      
      try {
        // First load the form data
        await loadFormData()
        
        // Create a copy of the review data to avoid modifying the original
        editingReview.value = { 
          id: review.id,
          product_id: review.product.id,
          user_id: review.user ? review.user.id : null,
          rating: review.rating,
          title: review.title,
          content: review.content,
          is_approved: review.is_approved,
          is_featured: review.is_featured
        }
        
        // Finally show the modal
        showEditModal.value = true
        console.log('Edit modal opened, form initialized with:', editingReview.value)
      } catch (error) {
        console.error('Error preparing review for edit:', error)
        alertStore.error('Wystąpił błąd podczas przygotowania formularza edycji.')
      } finally {
        formDataLoading.value = false
      }
    }
    
    // Confirm delete review
    const showDeleteModal = ref(false)
    const confirmDeleteReview = (review) => {
      selectedReview.value = review
      showDeleteModal.value = true
    }
    
    // Delete review
    const deleteReview = async () => {
      try {
        await axios.delete(`/api/admin/reviews/${selectedReview.value.id}`)
        alertStore.success('Recenzja została usunięta.')
        showDeleteModal.value = false
        fetchReviews()
        fetchFeaturedCount()
      } catch (error) {
        console.error('Error deleting review:', error)
        alertStore.error('Wystąpił błąd podczas usuwania recenzji.')
      }
    }
    
    // Watch for modal opening to load form data and initialize new review if needed
    watch(showCreateReviewModal, async (newValue) => {
      if (newValue) {
        console.log('Modal opened')
        // Data loading is now handled in handleAddButtonClick, so this watch is not needed
      }
    }, { immediate: false })
    
    const resetFilters = () => {
      Object.assign(filters, defaultFilters)
      fetchReviews()
    }
    
    // Featured reviews count
    const featuredReviewsCount = ref(0)
    
    // Fetch featured reviews count
    const fetchFeaturedCount = async () => {
      try {
        const response = await axios.get('/api/admin/reviews/featured-count')
        featuredReviewsCount.value = response.data.featured_count
      } catch (error) {
        console.error('Error fetching featured count:', error)
      }
    }
    
    onMounted(() => {
      fetchReviews()
      fetchFeaturedCount()
    })
    
    return {
      loading,
      reviews,
      filters,
      defaultFilters,
      sortOptions,
      fetchReviews,
      goToPage,
      approveReview,
      rejectReview,
      showReviewDetails,
      formatDate,
      getStatusName,
      filterReviews,
      toggleFeatured,
      toggleApproval,
      showCreateReviewModal,
      showEditModal,
      editingReview,
      initNewReview,
      saveReview,
      formData,
      loadFormData,
      getFormDataWorkaround,
      showDeleteModal,
      confirmDeleteReview,
      deleteReview,
      editReview,
      handleSortChange,
      showDetailsModal,
      selectedReview,
      closeAddEditModal,
      isEditing,
      formDataLoading,
      formSubmitting,
      handleAddButtonClick,
      testFormDataAPI,
      resetFilters,
      getUserInitials,
      showProductDetailsModal,
      selectedProduct,
      showProductDetails,
      getProductImageUrl,
      handleImageError,
      featuredReviewsCount,
      fetchFeaturedCount
    }
  }
}
</script> 