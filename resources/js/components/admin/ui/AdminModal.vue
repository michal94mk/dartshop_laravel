<template>
  <teleport to="body">
    <div
      v-if="show"
      class="fixed inset-0 z-50 overflow-y-auto"
      aria-labelledby="modal-title"
      role="dialog"
      aria-modal="true"
    >
      <!-- Background overlay -->
      <div class="flex items-center justify-center min-h-screen px-4 text-center sm:block sm:p-0">
        <div
          class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
          aria-hidden="true"
          @click="handleBackdropClick"
        ></div>

        <!-- This element is to trick the browser into centering the modal contents. -->
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <!-- Modal panel -->
        <div
          :class="modalClasses"
          class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle"
        >
          <!-- Header -->
          <div v-if="$slots.header || title" class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
              <div class="flex items-center">
                <div
                  v-if="icon"
                  :class="iconClasses"
                  class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full sm:mx-0 sm:h-10 sm:w-10"
                >
                  <component :is="icon" class="h-6 w-6" aria-hidden="true" />
                </div>
                <div class="mt-3 text-center sm:mt-0 sm:text-left" :class="{ 'sm:ml-4': icon }">
                  <h3 v-if="title" class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                    {{ title }}
                  </h3>
                  <slot name="header"></slot>
                </div>
              </div>
              <button
                v-if="closable"
                @click="$emit('close')"
                class="rounded-md text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500"
              >
                <span class="sr-only">Zamknij</span>
                <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>
          </div>

          <!-- Body -->
          <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
            <div class="sm:flex sm:items-start">
              <div class="w-full mt-3 text-center sm:mt-0 sm:text-left">
                <slot name="body">
                  <slot></slot>
                </slot>
              </div>
            </div>
          </div>

          <!-- Footer -->
          <div v-if="$slots.footer || showDefaultFooter" class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
            <slot name="footer">
              <admin-button
                v-if="showDefaultFooter"
                variant="primary"
                @click="$emit('confirm')"
                class="w-full sm:w-auto sm:ml-3"
              >
                {{ confirmText }}
              </admin-button>
              <admin-button
                v-if="showDefaultFooter"
                variant="secondary"
                outline
                @click="$emit('close')"
                class="mt-3 w-full sm:mt-0 sm:w-auto"
              >
                {{ cancelText }}
              </admin-button>
            </slot>
          </div>
        </div>
      </div>
    </div>
  </teleport>
</template>

<script>
import AdminButton from './AdminButton.vue';

export default {
  name: 'AdminModal',
  components: {
    AdminButton
  },
  emits: ['close', 'confirm'],
  props: {
    show: {
      type: Boolean,
      default: false
    },
    title: {
      type: String,
      default: ''
    },
    size: {
      type: String,
      default: 'md',
      validator: (value) => ['xs', 'sm', 'md', 'lg', 'xl', '2xl', '3xl', '4xl', '5xl', '6xl', '7xl'].includes(value)
    },
    closable: {
      type: Boolean,
      default: true
    },
    closeOnBackdrop: {
      type: Boolean,
      default: true
    },
    icon: {
      type: [String, Object],
      default: null
    },
    iconVariant: {
      type: String,
      default: 'info',
      validator: (value) => ['info', 'success', 'warning', 'danger'].includes(value)
    },
    showDefaultFooter: {
      type: Boolean,
      default: false
    },
    confirmText: {
      type: String,
      default: 'Potwierdź'
    },
    cancelText: {
      type: String,
      default: 'Anuluj'
    }
  },
  computed: {
    modalClasses() {
      const sizeClasses = {
        xs: 'sm:max-w-xs',
        sm: 'sm:max-w-sm',
        md: 'sm:max-w-md',
        lg: 'sm:max-w-lg',
        xl: 'sm:max-w-xl',
        '2xl': 'sm:max-w-2xl',
        '3xl': 'sm:max-w-3xl',
        '4xl': 'sm:max-w-4xl',
        '5xl': 'sm:max-w-5xl',
        '6xl': 'sm:max-w-6xl',
        '7xl': 'sm:max-w-7xl'
      };

      return [
        'sm:w-full',
        sizeClasses[this.size]
      ];
    },
    iconClasses() {
      const variantClasses = {
        info: 'bg-blue-100 text-blue-600',
        success: 'bg-green-100 text-green-600',
        warning: 'bg-yellow-100 text-yellow-600',
        danger: 'bg-red-100 text-red-600'
      };

      return variantClasses[this.iconVariant];
    }
  },
  methods: {
    handleBackdropClick() {
      if (this.closeOnBackdrop) {
        this.$emit('close');
      }
    }
  },
  watch: {
    show(newVal) {
      if (newVal) {
        document.body.style.overflow = 'hidden';
      } else {
        document.body.style.overflow = '';
      }
    }
  },
  beforeUnmount() {
    document.body.style.overflow = '';
  }
}
</script> 