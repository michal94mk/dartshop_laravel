import { defineStore } from 'pinia'
import axios from 'axios'

export const useReviewStore = defineStore('review', {
  state: () => ({
    latestReviews: [],
    loading: false,
    error: null
  }),

  actions: {
    async fetchLatestReviews(limit = 6) {
      this.loading = true
      this.error = null
      
      try {
        const response = await axios.get(`/api/reviews/latest?limit=${limit}`)
        
        if (response.data.success) {
          this.latestReviews = response.data.reviews || []
        } else if (response.data.reviews) {
          // Some APIs might return data directly without success flag
          this.latestReviews = response.data.reviews
        } else {
          console.warn('Unexpected API response format:', response.data)
          this.latestReviews = []
        }
      } catch (error) {
        console.error('Error fetching latest reviews:', error)
        // Don't show error for 401 (unauthorized) as it's expected for guests
        if (error.response?.status !== 401) {
          this.error = 'Nie udało się pobrać najnowszych recenzji'
        }
        this.latestReviews = []
      } finally {
        this.loading = false
      }
    },

    clearError() {
      this.error = null
    },

    // Reset store state
    $reset() {
      this.latestReviews = []
      this.loading = false
      this.error = null
    }
  },

  getters: {
    hasReviews: (state) => state.latestReviews.length > 0,
    reviewsCount: (state) => state.latestReviews.length
  }
}) 