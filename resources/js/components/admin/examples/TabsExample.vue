<template>
  <admin-tabs-layout
    title="Zarządzanie produktami"
    subtitle="Pełne zarządzanie produktami, kategoriami i ustawieniami"
    :tabs="tabs"
    v-model="activeTab"
    @tab-change="handleTabChange"
  >
    <!-- Header slot - przyciski globalne -->
    <template #header>
      <admin-button
        variant="primary"
        @click="openCreateModal"
      >
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
        </svg>
        Dodaj produkt
      </admin-button>
    </template>

    <!-- Toolbar slot - filtry i wyszukiwanie -->
    <template #toolbar>
      <div class="flex items-center space-x-4">
        <div class="flex-1">
          <input
            type="text"
            v-model="searchQuery"
            placeholder="Szukaj..."
            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
          />
        </div>
        <select
          v-model="selectedCategory"
          class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
        >
          <option value="">Wszystkie kategorie</option>
          <option value="lotki">Lotki</option>
          <option value="tarcze">Tarcze</option>
        </select>
      </div>
    </template>

    <!-- Główna zawartość zakładek -->
    <template #default="{ activeTab }">
      <!-- Lista produktów -->
      <admin-tab-panel
        tab-id="products"
        :active-tab="activeTab"
        title="Lista produktów"
        description="Zarządzaj wszystkimi produktami w sklepie"
      >
        <template #header>
          <admin-button variant="secondary" outline>
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            Eksportuj
          </admin-button>
        </template>

        <!-- Tabela produktów -->
        <admin-table
          :columns="productColumns"
          :items="products"
        >
          <template #cell-image="{ item }">
            <div class="flex justify-center">
              <img 
                v-if="item.image_url"
                :src="item.image_url" 
                :alt="item.name" 
                class="h-12 w-12 object-cover rounded-lg shadow-sm"
              />
              <div v-else class="h-12 w-12 bg-gray-200 rounded-lg flex items-center justify-center">
                <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
              </div>
            </div>
          </template>

          <template #cell-status="{ item }">
            <admin-badge 
              :variant="item.is_active ? 'success' : 'danger'"
            >
              {{ item.is_active ? 'Aktywny' : 'Nieaktywny' }}
            </admin-badge>
          </template>

          <template #cell-actions="{ item }">
            <action-buttons 
              :item="item"
              :show-details="true"
              @details="showProductDetails"
              @edit="editProduct"
              @delete="deleteProduct"
            >
              <!-- Dodatkowe przyciski dla produktów -->
              <template #custom-buttons="{ item }">
                <admin-button
                  variant="info"
                  size="sm"
                  @click="duplicateProduct(item)"
                  title="Duplikuj produkt"
                >
                  Duplikuj
                </admin-button>
              </template>
            </action-buttons>
          </template>
        </admin-table>

        <template #footer>
          <admin-button variant="secondary" outline>
            Anuluj zmiany
          </admin-button>
          <admin-button variant="primary">
            Zapisz zmiany
          </admin-button>
        </template>
      </admin-tab-panel>

      <!-- Kategorie -->
      <admin-tab-panel
        tab-id="categories"
        :active-tab="activeTab"
        title="Kategorie produktów"
        description="Zarządzaj kategoriami i ich hierarchią"
      >
        <div class="grid grid-cols-1 gap-6">
          <div class="bg-gray-50 rounded-lg p-6">
            <h4 class="text-sm font-medium text-gray-900 mb-4">Szybkie akcje</h4>
            <div class="flex space-x-3">
              <admin-button variant="primary" size="sm">
                Dodaj kategorię główną
              </admin-button>
              <admin-button variant="secondary" size="sm" outline>
                Import kategorii
              </admin-button>
            </div>
          </div>

          <!-- Lista kategorii -->
          <admin-table
            :columns="categoryColumns"
            :items="categories"
          >
            <template #cell-actions="{ item }">
              <action-buttons 
                :item="item"
                @edit="editCategory"
                @delete="deleteCategory"
              />
            </template>
          </admin-table>
        </div>
      </admin-tab-panel>

      <!-- Ustawienia -->
      <admin-tab-panel
        tab-id="settings"
        :active-tab="activeTab"
        title="Ustawienia produktów"
        description="Konfiguracja wyświetlania i zarządzania produktami"
      >
        <div class="space-y-6">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-4">
              <h4 class="text-lg font-medium text-gray-900">Wyświetlanie</h4>
              
              <div>
                <label class="flex items-center">
                  <input type="checkbox" v-model="settings.showOutOfStock" class="rounded border-gray-300">
                  <span class="ml-2 text-sm text-gray-700">Pokazuj produkty niedostępne</span>
                </label>
              </div>

              <div>
                <label class="flex items-center">
                  <input type="checkbox" v-model="settings.showPrices" class="rounded border-gray-300">
                  <span class="ml-2 text-sm text-gray-700">Pokazuj ceny publicznie</span>
                </label>
              </div>
            </div>

            <div class="space-y-4">
              <h4 class="text-lg font-medium text-gray-900">Zarządzanie</h4>
              
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Domyślny status nowego produktu
                </label>
                <select v-model="settings.defaultStatus" class="block w-full rounded-md border-gray-300">
                  <option value="draft">Szkic</option>
                  <option value="active">Aktywny</option>
                  <option value="inactive">Nieaktywny</option>
                </select>
              </div>
            </div>
          </div>
        </div>

        <template #footer>
          <admin-button variant="secondary" outline>
            Przywróć domyślne
          </admin-button>
          <admin-button variant="primary">
            Zapisz ustawienia
          </admin-button>
        </template>
      </admin-tab-panel>
    </template>

    <!-- Action bar - globalne akcje -->
    <template #actions>
      <admin-button variant="danger" outline>
        Usuń zaznaczone
      </admin-button>
      <admin-button variant="primary">
        Zapisz wszystkie zmiany
      </admin-button>
    </template>
  </admin-tabs-layout>
</template>

<script>
import ActionButtons from '../ActionButtons.vue'

export default {
  name: 'TabsExample',
  components: {
    ActionButtons
  },
  data() {
    return {
      activeTab: 'products',
      searchQuery: '',
      selectedCategory: '',
      settings: {
        showOutOfStock: true,
        showPrices: true,
        defaultStatus: 'draft'
      },
      tabs: [
        {
          id: 'products',
          label: 'Produkty',
          iconPath: 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
          badge: {
            value: 124,
            variant: 'primary'
          }
        },
        {
          id: 'categories',
          label: 'Kategorie',
          iconPath: 'M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z',
          badge: {
            value: 12,
            variant: 'secondary'
          }
        },
        {
          id: 'settings',
          label: 'Ustawienia',
          iconPath: 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z'
        }
      ],
      productColumns: [
        { key: 'image', label: 'Zdjęcie', sortable: false, width: '80px' },
        { key: 'name', label: 'Nazwa', sortable: true },
        { key: 'category', label: 'Kategoria', sortable: true },
        { key: 'price', label: 'Cena', sortable: true },
        { key: 'stock', label: 'Stan', sortable: true },
        { key: 'status', label: 'Status', sortable: false },
        { key: 'actions', label: 'Akcje', sortable: false, width: '200px' }
      ],
      categoryColumns: [
        { key: 'name', label: 'Nazwa', sortable: true },
        { key: 'products_count', label: 'Produkty', sortable: true },
        { key: 'sort_order', label: 'Kolejność', sortable: true },
        { key: 'actions', label: 'Akcje', sortable: false }
      ],
      products: [
        {
          id: 1,
          name: 'Profesjonalne lotki stalowe',
          category: 'Lotki',
          price: '89.99 zł',
          stock: 15,
          is_active: true,
          image_url: '/images/products/dart1.jpg'
        },
        {
          id: 2,
          name: 'Tarcza elektroniczna Premium',
          category: 'Tarcze',
          price: '499.99 zł',
          stock: 3,
          is_active: true,
          image_url: null
        }
      ],
      categories: [
        { id: 1, name: 'Lotki', products_count: 45, sort_order: 1 },
        { id: 2, name: 'Tarcze', products_count: 12, sort_order: 2 },
        { id: 3, name: 'Akcesoria', products_count: 67, sort_order: 3 }
      ]
    }
  },
  methods: {
    handleTabChange(newTab, oldTab) {
      console.log(`Zmiana zakładki z ${oldTab} na ${newTab}`)
    },
    openCreateModal() {
      console.log('Otwórz modal tworzenia produktu')
    },
    showProductDetails(product) {
      console.log('Pokaż szczegóły produktu:', product)
    },
    editProduct(product) {
      console.log('Edytuj produkt:', product)
    },
    deleteProduct(productId) {
      console.log('Usuń produkt:', productId)
    },
    duplicateProduct(product) {
      console.log('Duplikuj produkt:', product)
    },
    editCategory(category) {
      console.log('Edytuj kategorię:', category)
    },
    deleteCategory(categoryId) {
      console.log('Usuń kategorię:', categoryId)
    }
  }
}
</script> 