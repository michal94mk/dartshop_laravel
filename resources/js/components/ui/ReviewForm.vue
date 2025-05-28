<template>
  <div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-lg shadow-lg p-6">
      <h3 class="text-xl font-bold text-gray-900 mb-6">Dodaj recenzję</h3>
      
      <form @submit.prevent="submitReview" class="space-y-6">
        <!-- Rating -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">
            Twoja ocena <span class="text-red-500">*</span>
          </label>
          <div class="flex items-center space-x-3">
            <StarRating
              v-model="form.rating"
              interactive
              size="lg"
              :disabled="loading"
              @change="clearRatingError"
            />
            <span v-if="form.rating > 0" class="text-sm text-gray-600">
              ({{ form.rating }}/5)
            </span>
          </div>
          <div v-if="errors.rating" class="mt-1 text-sm text-red-600">
            {{ errors.rating[0] }}
          </div>
        </div>

        <!-- Title -->
        <div>
          <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
            Tytuł recenzji <span class="text-red-500">*</span>
          </label>
          <input
            id="title"
            v-model="form.title"
            type="text"
            maxlength="255"
            :disabled="loading"
            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 disabled:bg-gray-100 disabled:cursor-not-allowed"
            placeholder="Podsumuj swoją opinię w kilku słowach..."
            @input="clearFieldError('title')"
          />
          <div class="flex justify-between mt-1">
            <div v-if="errors.title" class="text-sm text-red-600">
              {{ errors.title[0] }}
            </div>
            <div class="text-xs text-gray-500">
              {{ form.title.length }}/255
            </div>
          </div>
        </div>

        <!-- Content -->
        <div>
          <label for="content" class="block text-sm font-medium text-gray-700 mb-2">
            Treść recenzji <span class="text-red-500">*</span>
          </label>
          <textarea
            id="content"
            v-model="form.content"
            rows="5"
            maxlength="1000"
            :disabled="loading"
            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 disabled:bg-gray-100 disabled:cursor-not-allowed resize-none"
            placeholder="Opisz swoją opinię o produkcie. Co Ci się podobało, a co można by poprawić?"
            @input="clearFieldError('content')"
          ></textarea>
          <div class="flex justify-between mt-1">
            <div v-if="errors.content" class="text-sm text-red-600">
              {{ errors.content[0] }}
            </div>
            <div class="text-xs text-gray-500">
              {{ form.content.length }}/1000
            </div>
          </div>
        </div>

        <!-- Guidelines -->
        <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
          <h4 class="text-sm font-medium text-blue-900 mb-2">Wskazówki dotyczące recenzji:</h4>
          <ul class="text-xs text-blue-700 space-y-1">
            <li>• Bądź szczery i obiektywny w swojej ocenie</li>
            <li>• Opisz konkretne doświadczenia z produktem</li>
            <li>• Unikaj obraźliwego języka</li>
            <li>• Recenzja zostanie sprawdzona przed publikacją</li>
          </ul>
        </div>

        <!-- Error message -->
        <div v-if="errorMessage" class="bg-red-50 border border-red-200 rounded-md p-4">
          <div class="text-sm text-red-700">
            {{ errorMessage }}
          </div>
        </div>

        <!-- Success message -->
        <div v-if="successMessage" class="bg-green-50 border border-green-200 rounded-md p-4">
          <div class="text-sm text-green-700">
            {{ successMessage }}
          </div>
        </div>

        <!-- Submit buttons -->
        <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
          <button
            type="button"
            @click="$emit('cancel')"
            :disabled="loading"
            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            Anuluj
          </button>
          <button
            type="submit"
            :disabled="loading || !isFormValid"
            class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <template v-if="loading">
              <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              Dodawanie...
            </template>
            <template v-else>
              Dodaj recenzję
            </template>
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import { ref, computed } from 'vue'
import StarRating from './StarRating.vue'
import axios from 'axios'

export default {
  name: 'ReviewForm',
  components: {
    StarRating
  },
  props: {
    productId: {
      type: [String, Number],
      required: true
    }
  },
  emits: ['success', 'cancel'],
  setup(props, { emit }) {
    // Form data
    const form = ref({
      rating: 0,
      title: '',
      content: ''
    })

    // State
    const loading = ref(false)
    const errors = ref({})
    const errorMessage = ref('')
    const successMessage = ref('')

    // Computed
    const isFormValid = computed(() => {
      return form.value.rating > 0 && 
             form.value.title.trim().length > 0 && 
             form.value.content.trim().length > 0
    })

    // Methods
    const clearFieldError = (field) => {
      if (errors.value[field]) {
        delete errors.value[field]
      }
      errorMessage.value = ''
    }

    const clearRatingError = () => {
      clearFieldError('rating')
    }

    const resetForm = () => {
      form.value = {
        rating: 0,
        title: '',
        content: ''
      }
      errors.value = {}
      errorMessage.value = ''
      successMessage.value = ''
    }

    const submitReview = async () => {
      loading.value = true
      errors.value = {}
      errorMessage.value = ''
      successMessage.value = ''

      try {
        const response = await axios.post(`/api/products/${props.productId}/reviews`, {
          rating: form.value.rating,
          title: form.value.title.trim(),
          content: form.value.content.trim()
        })

        successMessage.value = response.data.message || 'Recenzja została dodana i czeka na zatwierdzenie przez administratora'
        
        // Emit success event with the new review
        emit('success', response.data.review)

        // Reset form after short delay
        setTimeout(() => {
          resetForm()
        }, 2000)

      } catch (error) {
        console.error('Error submitting review:', error)
        
        if (error.response?.data?.errors) {
          errors.value = error.response.data.errors
        } else if (error.response?.data?.error) {
          errorMessage.value = error.response.data.error
        } else {
          errorMessage.value = 'Wystąpił błąd podczas dodawania recenzji. Spróbuj ponownie.'
        }
      } finally {
        loading.value = false
      }
    }

    return {
      form,
      loading,
      errors,
      errorMessage,
      successMessage,
      isFormValid,
      clearFieldError,
      clearRatingError,
      resetForm,
      submitReview
    }
  }
}
</script>

<style scoped>
/* Add any custom styles if needed */
</style> 