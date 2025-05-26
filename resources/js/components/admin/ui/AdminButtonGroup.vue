<template>
  <div :class="groupClasses">
    <slot></slot>
  </div>
</template>

<script>
export default {
  name: 'AdminButtonGroup',
  props: {
    spacing: {
      type: String,
      default: 'sm',
      validator: (value) => ['xs', 'sm', 'md', 'lg'].includes(value)
    },
    direction: {
      type: String,
      default: 'row',
      validator: (value) => ['row', 'col'].includes(value)
    },
    justify: {
      type: String,
      default: 'start',
      validator: (value) => ['start', 'center', 'end', 'between', 'around', 'evenly'].includes(value)
    },
    align: {
      type: String,
      default: 'center',
      validator: (value) => ['start', 'center', 'end', 'stretch', 'baseline'].includes(value)
    }
  },
  computed: {
    groupClasses() {
      const baseClasses = ['flex'];

      // Direction
      if (this.direction === 'col') {
        baseClasses.push('flex-col');
      } else {
        baseClasses.push('flex-row');
      }

      // Spacing
      const spacingClasses = {
        xs: this.direction === 'row' ? 'space-x-1' : 'space-y-1',
        sm: this.direction === 'row' ? 'space-x-2' : 'space-y-2',
        md: this.direction === 'row' ? 'space-x-3' : 'space-y-3',
        lg: this.direction === 'row' ? 'space-x-4' : 'space-y-4'
      };
      baseClasses.push(spacingClasses[this.spacing]);

      // Justify content
      const justifyClasses = {
        start: 'justify-start',
        center: 'justify-center',
        end: 'justify-end',
        between: 'justify-between',
        around: 'justify-around',
        evenly: 'justify-evenly'
      };
      baseClasses.push(justifyClasses[this.justify]);

      // Align items
      const alignClasses = {
        start: 'items-start',
        center: 'items-center',
        end: 'items-end',
        stretch: 'items-stretch',
        baseline: 'items-baseline'
      };
      baseClasses.push(alignClasses[this.align]);

      return baseClasses;
    }
  }
}
</script> 