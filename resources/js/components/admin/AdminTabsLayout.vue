<template>
  <div class="admin-tabs-layout">
    <!-- Page Header -->
    <div class="border-b border-gray-200 bg-white px-6 py-4 mb-6">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-bold text-gray-900">{{ title }}</h1>
          <p v-if="subtitle" class="mt-1 text-sm text-gray-600">{{ subtitle }}</p>
        </div>
        <div v-if="$slots.header" class="flex items-center space-x-3">
          <slot name="header"></slot>
        </div>
      </div>
    </div>

    <!-- Tabs Navigation -->
    <div class="border-b border-gray-200 bg-white px-6">
      <nav class="flex space-x-8" aria-label="Tabs">
        <button
          v-for="tab in tabs"
          :key="tab.id"
          @click="setActiveTab(tab.id)"
          class="py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-200 whitespace-nowrap"
          :class="[
            activeTab === tab.id
              ? 'border-indigo-500 text-indigo-600'
              : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
          ]"
          :disabled="tab.disabled"
        >
          <div class="flex items-center">
            <component
              v-if="tab.icon"
              :is="tab.icon"
              class="w-4 h-4 mr-2"
            />
            <svg
              v-else-if="tab.iconClass"
              class="w-4 h-4 mr-2"
              :class="tab.iconClass"
              fill="currentColor"
              viewBox="0 0 20 20"
            >
              <path v-if="tab.iconPath" :d="tab.iconPath" />
            </svg>
            {{ tab.label }}
            <span
              v-if="tab.badge"
              class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium"
              :class="getBadgeClasses(tab.badge)"
            >
              {{ tab.badge.value }}
            </span>
          </div>
        </button>
      </nav>
    </div>

    <!-- Tab Content -->
    <div class="bg-white">
      <div class="px-6 py-6">
        <!-- Toolbar -->
        <div v-if="$slots.toolbar" class="mb-6">
          <div class="flex items-center justify-between">
            <slot name="toolbar"></slot>
          </div>
        </div>

        <!-- Main Content -->
        <div class="tab-content">
          <slot :activeTab="activeTab"></slot>
        </div>
      </div>
    </div>

    <!-- Action Bar -->
    <div v-if="$slots.actions" class="border-t border-gray-200 bg-gray-50 px-6 py-4">
      <div class="flex items-center justify-end space-x-3">
        <slot name="actions"></slot>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'AdminTabsLayout',
  props: {
    title: {
      type: String,
      required: true
    },
    subtitle: {
      type: String,
      default: null
    },
    tabs: {
      type: Array,
      required: true,
      validator: (tabs) => {
        return tabs.every(tab => 
          tab.id && 
          tab.label && 
          typeof tab.id === 'string' && 
          typeof tab.label === 'string'
        )
      }
    },
    defaultTab: {
      type: String,
      default: null
    },
    modelValue: {
      type: String,
      default: null
    }
  },
  emits: ['update:modelValue', 'tab-change'],
  data() {
    return {
      activeTab: this.modelValue || this.defaultTab || (this.tabs.length > 0 ? this.tabs[0].id : null)
    }
  },
  watch: {
    modelValue(newValue) {
      if (newValue && newValue !== this.activeTab) {
        this.activeTab = newValue
      }
    },
    activeTab(newTab, oldTab) {
      if (newTab !== oldTab) {
        this.$emit('update:modelValue', newTab)
        this.$emit('tab-change', newTab, oldTab)
      }
    }
  },
  mounted() {
    if (this.modelValue) {
      this.activeTab = this.modelValue
    }
  },
  methods: {
    setActiveTab(tabId) {
      const tab = this.tabs.find(t => t.id === tabId)
      if (tab && !tab.disabled) {
        this.activeTab = tabId
      }
    },
    getBadgeClasses(badge) {
      const baseClasses = 'text-xs font-medium'
      
      const variantClasses = {
        success: 'bg-green-100 text-green-800',
        warning: 'bg-yellow-100 text-yellow-800',
        danger: 'bg-red-100 text-red-800',
        info: 'bg-blue-100 text-blue-800',
        primary: 'bg-indigo-100 text-indigo-800',
        secondary: 'bg-gray-100 text-gray-800'
      }
      
      return [
        baseClasses,
        variantClasses[badge.variant] || variantClasses.secondary
      ].join(' ')
    }
  }
}
</script>

<style scoped>
.admin-tabs-layout {
  @apply min-h-full bg-gray-50;
}

.tab-content {
  @apply min-h-[400px];
}

/* Disabled tab styles */
button:disabled {
  @apply opacity-50 cursor-not-allowed;
}

/* Focus styles for accessibility */
button:focus {
  @apply outline-none ring-2 ring-indigo-500 ring-offset-2;
}
</style> 