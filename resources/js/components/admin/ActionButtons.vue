<template>
  <div class="flex space-x-2">
    <button 
      v-if="showEdit"
      @click="$emit('edit', item)"
      class="px-3 py-1.5 bg-indigo-600 text-white text-sm font-medium rounded hover:bg-indigo-700 transition-colors"
    >
      {{ editLabel }}
    </button>
    <button 
      v-if="showDelete"
      @click="$emit('delete', item)"
      class="px-3 py-1.5 bg-red-600 text-white text-sm font-medium rounded hover:bg-red-700 transition-colors"
      :disabled="disableDelete"
      :class="{ 'opacity-50 cursor-not-allowed': disableDelete }"
    >
      {{ deleteLabel }}
    </button>
    <slot></slot>
  </div>
</template>

<script>
export default {
  name: 'ActionButtons',
  props: {
    item: {
      type: Object,
      required: true
    },
    showEdit: {
      type: Boolean,
      default: true
    },
    showDelete: {
      type: Boolean,
      default: true
    },
    disableDelete: {
      type: Boolean,
      default: false
    },
    editLabel: {
      type: String,
      default: 'Edytuj'
    },
    deleteLabel: {
      type: String,
      default: 'Usuń'
    }
  }
}
</script> 