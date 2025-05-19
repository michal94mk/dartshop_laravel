<template>
  <div class="mt-6 bg-white shadow px-4 py-5 sm:rounded-lg sm:px-6">
    <!-- Search Input (Full width, on top) -->
    <div class="mb-4">
      <label for="search" class="block text-sm font-medium text-gray-700">{{ searchLabel || 'Wyszukaj' }}</label>
      <div class="mt-1">
        <input
          type="text"
          name="search"
          id="search"
          v-model="filterModel.search"
          @input="onSearchInput"
          class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
          :placeholder="searchPlaceholder || 'Wyszukaj...'"
        />
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
    debounceTime: {
      type: Number,
      default: 300
    }
  },
  data() {
    return {
      filterModel: { ...this.filters },
      searchTimeout: null
    }
  },
  watch: {
    filters: {
      handler(newFilters) {
        this.filterModel = { ...newFilters };
      },
      deep: true
    }
  },
  methods: {
    onSearchInput() {
      if (this.searchTimeout) {
        clearTimeout(this.searchTimeout);
      }
      
      this.searchTimeout = setTimeout(() => {
        this.emitChange();
      }, this.debounceTime);
    },
    
    onFilterChange() {
      this.emitChange();
    },
    
    emitChange() {
      this.$emit('update:filters', { ...this.filterModel });
      this.$emit('filter-change');
    }
  }
}
</script> 