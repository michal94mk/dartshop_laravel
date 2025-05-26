<template>
  <div v-if="showPagination" class="mt-5 flex justify-between items-center p-6">
    <div class="text-sm text-gray-700">
      Pokazuje {{ pagination.from || 0 }} do {{ pagination.to || 0 }} z {{ pagination.total || 0 }} {{ itemsLabel }}
    </div>
    <div>
      <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
        <button
          @click="onPageChange(pagination.current_page - 1)"
          :disabled="pagination.current_page === 1"
          :class="[
            pagination.current_page === 1 ? 'cursor-not-allowed opacity-50' : 'hover:bg-gray-50',
            'relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500'
          ]"
        >
          <span class="sr-only">Poprzednia</span>
          <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
            <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
          </svg>
        </button>
        <button
          v-for="page in paginationPages"
          :key="page"
          @click="onPageChange(page)"
          :class="[
            page === pagination.current_page
              ? 'z-10 bg-indigo-50 border-indigo-500 text-indigo-600'
              : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50',
            'relative inline-flex items-center px-4 py-2 border text-sm font-medium'
          ]"
        >
          {{ page }}
        </button>
        <button
          @click="onPageChange(pagination.current_page + 1)"
          :disabled="pagination.current_page === pagination.last_page"
          :class="[
            pagination.current_page === pagination.last_page ? 'cursor-not-allowed opacity-50' : 'hover:bg-gray-50',
            'relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500'
          ]"
        >
          <span class="sr-only">Następna</span>
          <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
          </svg>
        </button>
      </nav>
    </div>
  </div>
</template>

<script>
export default {
  name: 'Pagination',
  props: {
    pagination: {
      type: Object,
      required: true
      // Expected structure: { current_page, last_page, from, to, total, data }
    },
    itemsLabel: {
      type: String,
      default: 'elementów'
    },
    maxVisiblePages: {
      type: Number,
      default: 5
    }
  },
  computed: {
    showPagination() {
      return this.pagination && 
             this.pagination.data && 
             this.pagination.data.length > 0 && 
             this.pagination.last_page > 1;
    },
    paginationPages() {
      if (!this.pagination || !this.pagination.last_page) return [];
      
      const currentPage = this.pagination.current_page;
      const lastPage = this.pagination.last_page;
      const maxVisible = Math.min(this.maxVisiblePages, lastPage);
      
      // If we can show all pages
      if (lastPage <= maxVisible) {
        return Array.from({ length: lastPage }, (_, i) => i + 1);
      }
      
      // Calculate the start and end of the visible range
      let start = Math.max(currentPage - Math.floor(maxVisible / 2), 1);
      let end = start + maxVisible - 1;
      
      // Adjust if we're near the end
      if (end > lastPage) {
        end = lastPage;
        start = Math.max(end - maxVisible + 1, 1);
      }
      
      return Array.from({ length: end - start + 1 }, (_, i) => start + i);
    }
  },
  methods: {
    onPageChange(page) {
      // Validate the page before emitting
      if (page >= 1 && page <= this.pagination.last_page && page !== this.pagination.current_page) {
        this.$emit('page-change', page);
      }
    }
  }
}
</script> 