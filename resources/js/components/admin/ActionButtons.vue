<template>
  <admin-button-group spacing="xs">
    <admin-button 
      v-if="showEdit"
      @click="$emit('edit', item)"
      variant="primary"
      size="sm"
      :title="editLabel"
    >
      {{ editLabel }}
    </admin-button>
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