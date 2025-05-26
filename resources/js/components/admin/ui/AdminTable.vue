<template>
  <div class="admin-table-container">
    <!-- Table wrapper with horizontal scroll -->
    <div class="-my-2 -mx-4 overflow-x-auto sm:-mx-6 lg:-mx-8" style="scrollbar-width: thin; scrollbar-color: #d1d5db #f3f4f6;">
      <div class="inline-block min-w-full py-2 align-middle md:px-6 lg:px-8">
        <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
          <table 
            class="min-w-full divide-y divide-gray-300" 
            :style="forceHorizontalScroll ? 'min-width: 1400px;' : 'min-width: 1200px;'"
          >
            <!-- Header -->
            <thead class="bg-gray-50">
              <tr class="h-12">
                <th
                  v-for="(column, index) in columns"
                  :key="index"
                  scope="col"
                  :class="getHeaderClasses(column, index)"
                  :style="column.width ? { width: column.width, minWidth: column.width, maxWidth: column.width } : {}"
                >
                  <div class="flex items-center h-full">
                    <span class="text-sm font-semibold text-gray-900">{{ column.label }}</span>
                  </div>
                </th>
              </tr>
            </thead>
            
            <!-- Body -->
            <tbody class="bg-white divide-y divide-gray-200">
              <slot name="body" :items="items">
                <tr 
                  v-for="(item, index) in items" 
                  :key="getItemKey(item, index)"
                  class="h-16 hover:bg-gray-50 transition-colors duration-150"
                >
                  <td
                    v-for="(column, colIndex) in columns"
                    :key="colIndex"
                    :class="getCellClasses(column, colIndex)"
                    :style="column.width ? { width: column.width, minWidth: column.width, maxWidth: column.width } : {}"
                  >
                    <div class="flex items-center h-full min-h-[3rem]">
                      <slot
                        :name="`cell-${column.key}`"
                        :item="item"
                        :value="getNestedValue(item, column.key)"
                        :index="index"
                      >
                        <span class="text-sm text-gray-900">{{ formatCellValue(item, column) }}</span>
                      </slot>
                    </div>
                  </td>
                </tr>
              </slot>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Custom scrollbar styles for WebKit browsers */
.admin-table-container div::-webkit-scrollbar {
  height: 8px;
}

.admin-table-container div::-webkit-scrollbar-track {
  background: #f3f4f6;
  border-radius: 4px;
}

.admin-table-container div::-webkit-scrollbar-thumb {
  background: #d1d5db;
  border-radius: 4px;
}

.admin-table-container div::-webkit-scrollbar-thumb:hover {
  background: #9ca3af;
}
</style>

<script>
export default {
  name: 'AdminTable',
  props: {
    columns: {
      type: Array,
      required: true,
      validator: (columns) => {
        return columns.every(col => 
          col.key && col.label && 
          (!col.align || ['left', 'center', 'right'].includes(col.align))
        );
      }
    },
    items: {
      type: Array,
      default: () => []
    },
    keyField: {
      type: String,
      default: 'id'
    },
    forceHorizontalScroll: {
      type: Boolean,
      default: false
    }
  },
  methods: {
    getHeaderClasses(column, index) {
      const baseClasses = [
        'py-3',
        'h-12'
      ];

      // First column padding
      if (index === 0) {
        baseClasses.push('pl-6', 'pr-3');
      } else if (index === this.columns.length - 1) {
        // Last column (usually actions)
        baseClasses.push('relative', 'pl-3', 'pr-6');
      } else {
        baseClasses.push('px-3');
      }

      // Alignment
      const alignment = column.align || 'left';
      if (alignment === 'center') {
        baseClasses.push('text-center');
      } else if (alignment === 'right') {
        baseClasses.push('text-right');
      } else {
        baseClasses.push('text-left');
      }

      return baseClasses;
    },

    getCellClasses(column, index) {
      const baseClasses = [
        'py-3',
        'h-16',
        'align-middle'
      ];

      // First column padding and styling
      if (index === 0) {
        baseClasses.push('pl-6', 'pr-3');
      } else if (index === this.columns.length - 1) {
        // Last column (usually actions)
        baseClasses.push('relative', 'pl-3', 'pr-6');
      } else {
        baseClasses.push('px-3');
      }

      // Alignment
      const alignment = column.align || 'left';
      if (alignment === 'center') {
        baseClasses.push('text-center');
      } else if (alignment === 'right') {
        baseClasses.push('text-right');
      } else {
        baseClasses.push('text-left');
      }

      // Whitespace handling
      if (column.nowrap !== false) {
        baseClasses.push('whitespace-nowrap');
      }

      return baseClasses;
    },



    getItemKey(item, index) {
      return item[this.keyField] || index;
    },

    getNestedValue(obj, path) {
      return path.split('.').reduce((current, key) => {
        return current && current[key] !== undefined ? current[key] : null;
      }, obj);
    },

    formatCellValue(item, column) {
      const value = this.getNestedValue(item, column.key);
      
      if (value === null || value === undefined) {
        return '-';
      }

      // Apply formatter if provided
      if (column.formatter && typeof column.formatter === 'function') {
        return column.formatter(value, item);
      }

      // Default formatting based on type
      if (column.type === 'date' && value) {
        return new Date(value).toLocaleDateString('pl-PL');
      }

      if (column.type === 'datetime' && value) {
        return new Date(value).toLocaleString('pl-PL');
      }

      if (column.type === 'currency' && typeof value === 'number') {
        return `${value.toFixed(2)} PLN`;
      }

      if (column.type === 'boolean') {
        return value ? 'Tak' : 'Nie';
      }

      return value;
    }
  }
}
</script>

<style scoped>
.admin-table-container {
  @apply bg-white;
}

/* Ensure consistent row heights */
.admin-table-container tbody tr {
  min-height: 4rem;
}

/* Consistent cell content alignment */
.admin-table-container td > div {
  min-height: 3rem;
}

/* Smooth transitions for interactive elements */
.admin-table-container tbody tr:hover {
  background-color: rgb(249 250 251);
}

/* Ensure buttons and badges are properly aligned */
.admin-table-container :deep(.admin-button-group) {
  @apply flex items-center justify-end gap-2;
}

.admin-table-container :deep(.admin-badge) {
  @apply inline-flex items-center;
}

/* Consistent text sizing */
.admin-table-container td {
  @apply text-sm;
}

.admin-table-container th {
  @apply text-sm;
}
</style> 