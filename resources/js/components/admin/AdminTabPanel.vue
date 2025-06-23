<template>
  <div 
    v-show="isActive" 
    class="admin-tab-panel"
    :class="panelClasses"
    role="tabpanel"
    :aria-labelledby="`tab-${tabId}`"
  >
    <!-- Panel Header -->
    <div v-if="title || $slots.header" class="panel-header mb-6">
      <div class="flex items-center justify-between">
        <div v-if="title">
          <h3 class="text-lg font-medium text-gray-900">{{ title }}</h3>
          <p v-if="description" class="mt-1 text-sm text-gray-600">{{ description }}</p>
        </div>
        <div v-if="$slots.header" class="flex items-center space-x-3">
          <slot name="header"></slot>
        </div>
      </div>
    </div>

    <!-- Panel Content -->
    <div class="panel-content">
      <slot></slot>
    </div>

    <!-- Panel Footer -->
    <div v-if="$slots.footer" class="panel-footer mt-6 pt-6 border-t border-gray-200">
      <div class="flex items-center justify-end space-x-3">
        <slot name="footer"></slot>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'AdminTabPanel',
  props: {
    tabId: {
      type: String,
      required: true
    },
    activeTab: {
      type: String,
      required: true
    },
    title: {
      type: String,
      default: null
    },
    description: {
      type: String,
      default: null
    },
    padding: {
      type: String,
      default: 'default',
      validator: (value) => ['none', 'sm', 'default', 'lg'].includes(value)
    },
    lazy: {
      type: Boolean,
      default: false
    }
  },
  computed: {
    isActive() {
      return this.activeTab === this.tabId
    },
    panelClasses() {
      const paddingClasses = {
        none: '',
        sm: 'p-4',
        default: 'p-6',
        lg: 'p-8'
      }
      
      return [
        'transition-opacity duration-200',
        paddingClasses[this.padding] || paddingClasses.default,
        this.isActive ? 'opacity-100' : 'opacity-0'
      ]
    }
  }
}
</script>

<style scoped>
.admin-tab-panel {
  @apply bg-white rounded-lg;
}

.panel-header {
  @apply border-b border-gray-100 pb-4;
}

.panel-content {
  @apply space-y-6;
}

.panel-footer {
  @apply bg-gray-50 -mx-6 -mb-6 px-6 py-4 rounded-b-lg;
}
</style> 