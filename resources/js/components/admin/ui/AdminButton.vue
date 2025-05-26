<template>
  <component
    :is="tag"
    :type="tag === 'button' ? type : undefined"
    :href="tag === 'a' ? href : undefined"
    :target="tag === 'a' ? target : undefined"
    :disabled="tag === 'button' ? (disabled || loading) : undefined"
    :class="buttonClasses"
    @click="$emit('click', $event)"
  >
    <svg
      v-if="loading"
      class="animate-spin -ml-1 mr-2 h-4 w-4 text-white"
      xmlns="http://www.w3.org/2000/svg"
      fill="none"
      viewBox="0 0 24 24"
    >
      <circle
        class="opacity-25"
        cx="12"
        cy="12"
        r="10"
        stroke="currentColor"
        stroke-width="4"
      ></circle>
      <path
        class="opacity-75"
        fill="currentColor"
        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
      ></path>
    </svg>
    <slot></slot>
  </component>
</template>

<script>
export default {
  name: 'AdminButton',
  emits: ['click'],
  props: {
    tag: {
      type: String,
      default: 'button',
      validator: (value) => ['button', 'a'].includes(value)
    },
    variant: {
      type: String,
      default: 'primary',
      validator: (value) => [
        'primary', 'secondary', 'success', 'warning', 'danger', 'info', 'light', 'dark'
      ].includes(value)
    },
    size: {
      type: String,
      default: 'md',
      validator: (value) => ['xs', 'sm', 'md', 'lg'].includes(value)
    },
    type: {
      type: String,
      default: 'button'
    },
    href: {
      type: String,
      default: null
    },
    target: {
      type: String,
      default: null
    },
    disabled: {
      type: Boolean,
      default: false
    },
    loading: {
      type: Boolean,
      default: false
    },
    outline: {
      type: Boolean,
      default: false
    }
  },
  computed: {
    buttonClasses() {
      const baseClasses = [
        'inline-flex',
        'items-center',
        'justify-center',
        'font-medium',
        'rounded-md',
        'transition-colors',
        'duration-200',
        'focus:outline-none',
        'focus:ring-2',
        'focus:ring-offset-2',
        'no-underline'
      ];

      // Add disabled classes only for buttons
      if (this.tag === 'button') {
        baseClasses.push('disabled:opacity-50', 'disabled:cursor-not-allowed');
      }

      // Size classes
      const sizeClasses = {
        xs: ['px-2', 'py-1', 'text-xs'],
        sm: ['px-2.5', 'py-1.5', 'text-sm'],
        md: ['px-3', 'py-2', 'text-sm'],
        lg: ['px-4', 'py-2.5', 'text-base']
      };

      // Variant classes
      const variantClasses = {
        primary: this.outline 
          ? ['border', 'border-indigo-600', 'text-indigo-600', 'bg-white', 'hover:bg-indigo-50', 'focus:ring-indigo-500']
          : ['bg-indigo-600', 'text-white', 'hover:bg-indigo-700', 'focus:ring-indigo-500'],
        secondary: this.outline
          ? ['border', 'border-gray-300', 'text-gray-700', 'bg-white', 'hover:bg-gray-50', 'focus:ring-gray-500']
          : ['bg-gray-600', 'text-white', 'hover:bg-gray-700', 'focus:ring-gray-500'],
        success: this.outline
          ? ['border', 'border-green-600', 'text-green-600', 'bg-white', 'hover:bg-green-50', 'focus:ring-green-500']
          : ['bg-green-600', 'text-white', 'hover:bg-green-700', 'focus:ring-green-500'],
        warning: this.outline
          ? ['border', 'border-yellow-600', 'text-yellow-600', 'bg-white', 'hover:bg-yellow-50', 'focus:ring-yellow-500']
          : ['bg-yellow-600', 'text-white', 'hover:bg-yellow-700', 'focus:ring-yellow-500'],
        danger: this.outline
          ? ['border', 'border-red-600', 'text-red-600', 'bg-white', 'hover:bg-red-50', 'focus:ring-red-500']
          : ['bg-red-600', 'text-white', 'hover:bg-red-700', 'focus:ring-red-500'],
        info: this.outline
          ? ['border', 'border-blue-600', 'text-blue-600', 'bg-white', 'hover:bg-blue-50', 'focus:ring-blue-500']
          : ['bg-blue-600', 'text-white', 'hover:bg-blue-700', 'focus:ring-blue-500'],
        light: this.outline
          ? ['border', 'border-gray-200', 'text-gray-600', 'bg-white', 'hover:bg-gray-50', 'focus:ring-gray-500']
          : ['bg-gray-100', 'text-gray-800', 'hover:bg-gray-200', 'focus:ring-gray-500'],
        dark: this.outline
          ? ['border', 'border-gray-800', 'text-gray-800', 'bg-white', 'hover:bg-gray-50', 'focus:ring-gray-500']
          : ['bg-gray-800', 'text-white', 'hover:bg-gray-900', 'focus:ring-gray-500']
      };

      return [
        ...baseClasses,
        ...sizeClasses[this.size],
        ...variantClasses[this.variant]
      ];
    }
  }
}
</script> 