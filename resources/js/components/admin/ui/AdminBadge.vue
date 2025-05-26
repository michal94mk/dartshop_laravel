<template>
  <span :class="badgeClasses">
    <slot></slot>
  </span>
</template>

<script>
export default {
  name: 'AdminBadge',
  props: {
    variant: {
      type: String,
      default: 'gray',
      validator: (value) => [
        'gray', 'red', 'yellow', 'green', 'blue', 'indigo', 'purple', 'pink'
      ].includes(value)
    },
    size: {
      type: String,
      default: 'sm',
      validator: (value) => ['xs', 'sm', 'md', 'lg'].includes(value)
    },
    rounded: {
      type: String,
      default: 'md',
      validator: (value) => ['sm', 'md', 'lg', 'full'].includes(value)
    }
  },
  computed: {
    badgeClasses() {
      const baseClasses = [
        'inline-flex',
        'items-center',
        'font-medium'
      ];

      // Size classes
      const sizeClasses = {
        xs: ['px-1.5', 'py-0.5', 'text-xs'],
        sm: ['px-2', 'py-1', 'text-xs'],
        md: ['px-2.5', 'py-1.5', 'text-sm'],
        lg: ['px-3', 'py-2', 'text-base']
      };

      // Rounded classes
      const roundedClasses = {
        sm: ['rounded'],
        md: ['rounded-md'],
        lg: ['rounded-lg'],
        full: ['rounded-full']
      };

      // Variant classes
      const variantClasses = {
        gray: ['bg-gray-100', 'text-gray-800'],
        red: ['bg-red-100', 'text-red-800'],
        yellow: ['bg-yellow-100', 'text-yellow-800'],
        green: ['bg-green-100', 'text-green-800'],
        blue: ['bg-blue-100', 'text-blue-800'],
        indigo: ['bg-indigo-100', 'text-indigo-800'],
        purple: ['bg-purple-100', 'text-purple-800'],
        pink: ['bg-pink-100', 'text-pink-800']
      };

      return [
        ...baseClasses,
        ...sizeClasses[this.size],
        ...roundedClasses[this.rounded],
        ...variantClasses[this.variant]
      ];
    }
  }
}
</script> 