<template>
  <div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow-lg p-6">
      <!-- Header -->
      <div class="flex items-center justify-between mb-6">
        <h3 class="text-xl font-bold text-gray-900">Recenzje produktu</h3>
        <button
          v-if="canAddReview"
          @click="$emit('add-review')"
          class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
        >
          <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
          </svg>
          Dodaj recenzję
        </button>
      </div>

      <!-- Loading state -->
      <div v-if="loading" class="flex items-center justify-center py-8">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div>
        <span class="ml-2 text-gray-600">Ładowanie recenzji...</span>
      </div>

      <!-- Content -->
      <div v-else>
        <!-- Statistics Overview -->
        <div v-if="statistics && statistics.reviews_count > 0" class="mb-8 bg-gray-50 rounded-lg p-6">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Overall Rating -->
            <div class="text-center md:text-left">
              <div class="flex items-center justify-center md:justify-start mb-2">
                <span class="text-3xl font-bold text-gray-900 mr-2">{{ statistics.average_rating }}</span>
                <StarRating 
                  :model-value="statistics.average_rating" 
                  size="lg"
                  :precision="0.1"
                />
              </div>
              <p class="text-gray-600">
                Na podstawie {{ statistics.reviews_count }} 
                {{ statistics.reviews_count === 1 ? 'recenzji' : statistics.reviews_count < 5 ? 'recenzji' : 'recenzji' }}
              </p>
            </div>

            <!-- Rating Distribution -->
            <div class="space-y-2">
              <h4 class="font-medium text-gray-900 mb-3">Rozkład ocen</h4>
              <div v-for="rating in [5, 4, 3, 2, 1]" :key="rating" class="flex items-center">
                <span class="text-sm text-gray-600 w-3">{{ rating }}</span>
                <svg class="w-4 h-4 mx-1 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                </svg>
                <div class="flex-1 mx-2">
                  <div class="bg-gray-200 rounded-full h-2">
                    <div 
                      class="bg-yellow-400 h-2 rounded-full transition-all duration-300"
                      :style="{ width: getDistributionPercentage(rating) + '%' }"
                    ></div>
                  </div>
                </div>
                <span class="text-sm text-gray-600 w-8 text-right">{{ statistics.distribution[rating] }}</span>
              </div>
            </div>
          </div>
        </div>

        <!-- No Reviews State -->
        <div v-if="!statistics || statistics.reviews_count === 0" class="text-center py-12">
          <div class="mx-auto h-12 w-12 text-gray-400 mb-4">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-3.582 8-8 8a8.959 8.959 0 01-4.906-1.445L3 21l2.545-5.094A8.959 8.959 0 013 12c0-4.418 3.582-8 8-8s8 3.582 8 8z"></path>
            </svg>
          </div>
          <h3 class="text-lg font-medium text-gray-900 mb-2">Brak recenzji</h3>
          <p class="text-gray-600 mb-4">Ten produkt nie ma jeszcze żadnych recenzji.</p>
          <button
            v-if="canAddReview"
            @click="$emit('add-review')"
            class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
          >
            Bądź pierwszy - dodaj recenzję!
          </button>
        </div>

        <!-- Reviews List -->
        <div v-if="reviews && reviews.length > 0" class="space-y-6">
          <div
            v-for="review in reviews"
            :key="review.id"
            class="border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow duration-200"
          >
            <!-- Review Header -->
            <div class="flex items-start justify-between mb-4">
              <div class="flex items-center space-x-3">
                <div class="flex-shrink-0">
                  <div class="h-10 w-10 rounded-full bg-indigo-600 flex items-center justify-center">
                    <span class="text-sm font-medium text-white">
                      {{ getInitials(review.user.name) }}
                    </span>
                  </div>
                </div>
                <div>
                  <h4 class="font-medium text-gray-900">{{ review.user.name }}</h4>
                  <div class="flex items-center space-x-2">
                    <StarRating :model-value="review.rating" size="sm" />
                    <span class="text-sm text-gray-500">{{ formatDate(review.created_at) }}</span>
                  </div>
                </div>
              </div>
              
              <!-- Admin badge for featured reviews -->
              <div v-if="review.is_featured" class="flex-shrink-0">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                  <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                  </svg>
                  Wyróżniona
                </span>
              </div>
            </div>

            <!-- Review Content -->
            <div class="space-y-3">
              <h5 class="font-semibold text-gray-900">{{ review.title }}</h5>
              <p class="text-gray-700 leading-relaxed">{{ review.content }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { computed } from 'vue'
import StarRating from './StarRating.vue'

export default {
  name: 'ReviewsList',
  components: {
    StarRating
  },
  props: {
    reviews: {
      type: Array,
      default: () => []
    },
    statistics: {
      type: Object,
      default: () => ({})
    },
    loading: {
      type: Boolean,
      default: false
    },
    canAddReview: {
      type: Boolean,
      default: false
    }
  },
  emits: ['add-review'],
  setup(props) {
    // Methods
    const getInitials = (name) => {
      return name
        .split(' ')
        .map(word => word.charAt(0))
        .join('')
        .toUpperCase()
        .slice(0, 2)
    }

    const formatDate = (dateString) => {
      const date = new Date(dateString)
      return date.toLocaleDateString('pl-PL', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
      })
    }

    const getDistributionPercentage = (rating) => {
      if (!props.statistics.distribution || props.statistics.reviews_count === 0) {
        return 0
      }
      return Math.round((props.statistics.distribution[rating] / props.statistics.reviews_count) * 100)
    }

    return {
      getInitials,
      formatDate,
      getDistributionPercentage
    }
  }
}
</script>

<style scoped>
/* Add any custom styles if needed */
</style> 