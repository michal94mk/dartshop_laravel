<template>
  <admin-button-group spacing="xs">
    <!-- Details/View button -->
    <admin-button 
      v-if="showDetails"
      @click="$emit('details', item)"
      variant="secondary"
      size="sm"
      :title="detailsLabel"
    >
      {{ detailsLabel }}
    </admin-button>
    
    <!-- Edit button -->
    <admin-button 
      v-if="showEdit"
      @click="$emit('edit', item)"
      variant="warning"
      size="sm"
      :title="editLabel"
    >
      {{ editLabel }}
    </admin-button>
    
    <!-- Status buttons slot (for approve/reject etc.) -->
    <slot name="status-buttons" :item="item"></slot>
    
    <!-- Custom buttons slot -->
    <slot name="custom-buttons" :item="item"></slot>
    
    <!-- Delete button -->
    <admin-button 
      v-if="showDelete"
      @click="handleDelete"
      variant="danger"
      size="sm"
      :disabled="disableDelete"
      :title="deleteLabel"
    >
      {{ deleteLabel }}
    </admin-button>
    
    <!-- Additional buttons slot -->
    <slot></slot>
  </admin-button-group>
</template>

<script>
import AdminButton from './ui/AdminButton.vue';
import AdminButtonGroup from './ui/AdminButtonGroup.vue';

export default {
  name: 'ActionButtons',
  components: {
    AdminButton,
    AdminButtonGroup
  },
  props: {
    item: {
      type: Object,
      required: true
    },
    showDetails: {
      type: Boolean,
      default: false
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
    detailsLabel: {
      type: String,
      default: 'Szczegóły'
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
  emits: ['edit', 'delete', 'details'],
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