<template>
  <div class="sm:flex sm:items-center">
    <div class="sm:flex-auto">
      <h1 class="text-2xl font-semibold text-gray-900">{{ title }}</h1>
      <p v-if="subtitle" class="mt-2 text-sm text-gray-700">{{ subtitle }}</p>
    </div>
    <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none">
      <!-- Named slot for custom actions -->
      <slot name="actions">
        <!-- Fallback to simple button if using legacy props -->
        <button 
          v-if="showAddButton"
          @click="$emit('add')" 
          type="button" 
          class="px-3 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700 transition-colors"
        >
          {{ addButtonLabel }}
        </button>
      </slot>
      <!-- Default slot for additional content -->
      <slot></slot>
    </div>
  </div>
</template>

<script>
export default {
  name: 'PageHeader',
  props: {
    title: {
      type: String,
      required: true
    },
    subtitle: {
      type: String,
      default: ''
    },
    showAddButton: {
      type: Boolean,
      default: true
    },
    addButtonLabel: {
      type: String,
      default: 'Dodaj'
    }
  }
}
</script> 