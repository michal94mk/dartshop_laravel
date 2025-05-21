<template>
  <div class="flex flex-row gap-1">
    <button 
      v-if="showEdit"
      @click="$emit('edit', item)"
      class="px-2 py-1 bg-indigo-600 text-white text-xs font-medium rounded hover:bg-indigo-700 transition-colors"
      title="Edytuj"
    >
      {{ editLabel }}
    </button>
    <button 
      v-if="showDelete"
      @click="handleDelete"
      class="px-2 py-1 bg-red-600 text-white text-xs font-medium rounded hover:bg-red-700 transition-colors"
      :disabled="disableDelete"
      :class="{ 'opacity-50 cursor-not-allowed': disableDelete }"
      title="Usuń"
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
  },
  methods: {
    handleDelete() {
      // Always emit the ID itself, not the entire object
      if (this.item && this.item.id !== undefined) {
        console.log('ActionButtons: Emitting delete with ID:', this.item.id);
        this.$emit('delete', this.item.id);
      } else {
        console.error('ActionButtons: Item has no ID property', this.item);
        this.$emit('delete', this.item);
      }
    }
  }
}
</script> 