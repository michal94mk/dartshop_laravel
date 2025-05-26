<template>
  <div class="mt-6 bg-white shadow px-4 py-5 sm:rounded-lg sm:px-6">
    <!-- Search Input (Full width, on top) -->
    <div class="mb-4">
      <label for="search" class="block text-sm font-medium text-gray-700">{{ searchLabel || 'Wyszukaj' }}</label>
      <div class="mt-1 flex rounded-md shadow-sm">
        <input
          ref="searchInput"
          type="text"
          name="search"
          id="search"
          key="search-input"
          v-model="localSearch"
          @keyup.enter="onSearchSubmit"
          @focus="onSearchFocus"
          @blur="onSearchBlur"
          class="flex-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full min-w-0 rounded-none rounded-l-md sm:text-sm border-gray-300"
          :placeholder="searchPlaceholder || 'Wyszukaj...'"
        />
        <button
          type="button"
          @click="onSearchSubmit"
          class="inline-flex items-center px-3 py-2 border border-l-0 border-gray-300 rounded-r-md bg-gray-50 text-gray-500 text-sm hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
        >
          <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
          </svg>
          <span class="ml-1">Szukaj</span>
        </button>
      </div>
    </div>
    
    <!-- Other Filters and Sort Options -->
    <div class="flex flex-wrap gap-4">
      <!-- Custom Filters -->
      <slot name="filters"></slot>
      
      <!-- Sort Field -->
      <div class="w-full sm:w-auto">
        <label for="sort" class="block text-sm font-medium text-gray-700">Sortuj</label>
        <select
          id="sort"
          name="sort"
          v-model="filterModel.sort_field"
          @change="onFilterChange"
          class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
        >
          <option v-for="option in sortOptions" :key="option.value" :value="option.value">
            {{ option.label }}
          </option>
        </select>
      </div>
      
      <!-- Sort Direction -->
      <div class="w-full sm:w-auto">
        <label for="direction" class="block text-sm font-medium text-gray-700">Kierunek</label>
        <select
          id="direction"
          name="direction"
          v-model="filterModel.sort_direction"
          @change="onFilterChange"
          class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
        >
          <option value="desc">Malejąco</option>
          <option value="asc">Rosnąco</option>
        </select>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, watch } from 'vue'

export default {
  name: 'SearchFilters',
  props: {
    filters: {
      type: Object,
      required: true
    },
    sortOptions: {
      type: Array,
      required: true
      // Example: [{ value: 'created_at', label: 'Data dodania' }, { value: 'name', label: 'Nazwa' }]
    },
    searchLabel: {
      type: String,
      default: 'Wyszukaj'
    },
    searchPlaceholder: {
      type: String,
      default: 'Wyszukaj...'
    },

  },
  emits: ['update:filters', 'filter-change'],
  setup(props, { emit }) {
    // Separate local search state from other filters
    const localSearch = ref(props.filters.search || '')
    const filterModel = ref({ 
      ...props.filters,
      search: localSearch.value // Ensure filterModel uses local search
    })
    const searchInput = ref(null)
    const isSearchFocused = ref(false)
    
    // Watch for changes in props.filters, but never touch the search field
    watch(() => props.filters, (newFilters) => {
      // Update everything except search (which is managed locally)
      filterModel.value = { 
        ...newFilters,
        search: localSearch.value // Always use local search value
      }
    }, { deep: true })
    
    // Watch for external search changes (like clearing filters)
    watch(() => props.filters.search, (newSearch) => {
      // Only update local search if it's different and we're not currently focused
      if (!isSearchFocused.value && newSearch !== localSearch.value) {
        localSearch.value = newSearch || ''
        filterModel.value.search = localSearch.value
      }
    })
    
    const onSearchSubmit = () => {
      // Update filter model with current search value
      filterModel.value.search = localSearch.value
      
      // Emit change immediately
      emitChange()
    }
    
    const onFilterChange = () => {
      emitChange()
    }
    
    const onSearchFocus = () => {
      isSearchFocused.value = true
    }
    
    const onSearchBlur = () => {
      isSearchFocused.value = false
    }
    
    const emitChange = () => {
      console.log('SearchFilters emitting change:', filterModel.value)
      emit('update:filters', { ...filterModel.value })
      emit('filter-change')
    }
    
    return {
      localSearch,
      filterModel,
      searchInput,
      isSearchFocused,
      onSearchSubmit,
      onSearchFocus,
      onSearchBlur,
      onFilterChange
    }
  }
}
</script> 