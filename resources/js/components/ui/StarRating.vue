<template>
  <div class="flex items-center">
    <div class="flex space-x-1">
      <template v-for="star in 5" :key="star">
        <button
          v-if="interactive"
          type="button"
          @click="setRating(star)"
          @mouseenter="setHoverRating(star)"
          @mouseleave="setHoverRating(0)"
          :class="[
            'transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500',
            starClasses
          ]"
          :disabled="disabled"
        >
          <svg
            :class="[
              'fill-current transition-colors duration-150',
              getStarColor(star),
              size === 'sm' ? 'w-4 h-4' : size === 'lg' ? 'w-6 h-6' : 'w-5 h-5'
            ]"
            viewBox="0 0 20 20"
            xmlns="http://www.w3.org/2000/svg"
          >
            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
          </svg>
        </button>
        <div v-else :class="['flex items-center', starClasses]">
          <svg
            :class="[
              'fill-current',
              getStarColor(star),
              size === 'sm' ? 'w-4 h-4' : size === 'lg' ? 'w-6 h-6' : 'w-5 h-5'
            ]"
            viewBox="0 0 20 20"
            xmlns="http://www.w3.org/2000/svg"
          >
            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
          </svg>
        </div>
      </template>
    </div>
    
    <!-- Rating text and count -->
    <div v-if="showText" class="ml-2 flex items-center space-x-2">
      <span :class="[
        'font-medium',
        size === 'sm' ? 'text-sm' : size === 'lg' ? 'text-lg' : 'text-base'
      ]">
        {{ displayRating }}
      </span>
      <span v-if="reviewCount > 0" :class="[
        'text-gray-500',
        size === 'sm' ? 'text-xs' : size === 'lg' ? 'text-base' : 'text-sm'
      ]">
        ({{ reviewCount }} {{ reviewCount === 1 ? 'recenzja' : reviewCount < 5 ? 'recenzje' : 'recenzji' }})
      </span>
    </div>
  </div>
</template>

<script>
import { ref, computed } from 'vue'

export default {
  name: 'StarRating',
  props: {
    modelValue: {
      type: Number,
      default: 0
    },
    interactive: {
      type: Boolean,
      default: false
    },
    disabled: {
      type: Boolean,
      default: false
    },
    size: {
      type: String,
      default: 'md', // sm, md, lg
      validator: (value) => ['sm', 'md', 'lg'].includes(value)
    },
    showText: {
      type: Boolean,
      default: false
    },
    reviewCount: {
      type: Number,
      default: 0
    },
    precision: {
      type: Number,
      default: 1, // 1 for whole numbers, 0.1 for decimal
      validator: (value) => [1, 0.5, 0.1].includes(value)
    },
    starClasses: {
      type: String,
      default: ''
    }
  },
  emits: ['update:modelValue', 'change'],
  setup(props, { emit }) {
    const hoverRating = ref(0)
    
    const displayRating = computed(() => {
      if (props.precision === 1) {
        return props.modelValue.toString()
      }
      return props.modelValue.toFixed(1)
    })
    
    const currentRating = computed(() => {
      return props.interactive && hoverRating.value > 0 ? hoverRating.value : props.modelValue
    })
    
    const setRating = (rating) => {
      if (props.disabled) return
      
      emit('update:modelValue', rating)
      emit('change', rating)
    }
    
    const setHoverRating = (rating) => {
      if (props.disabled) return
      hoverRating.value = rating
    }
    
    const getStarColor = (star) => {
      const rating = currentRating.value
      
      if (rating >= star) {
        return 'text-yellow-400'
      } else if (rating > star - 1 && rating < star) {
        // Partial star (for decimal ratings)
        return 'text-yellow-300'
      } else {
        return 'text-gray-300'
      }
    }
    
    return {
      hoverRating,
      displayRating,
      currentRating,
      setRating,
      setHoverRating,
      getStarColor
    }
  }
}
</script>

<style scoped>
button:disabled {
  cursor: not-allowed;
  opacity: 0.6;
}

button:hover:not(:disabled) svg {
  transform: scale(1.1);
}
</style> 