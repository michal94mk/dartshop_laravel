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
          this.latestReviews = response.data.reviews
        } else {
          throw new Error('Błąd podczas pobierania recenzji')
        }
      } catch (error) {
        console.error('Error fetching latest reviews:', error)
        this.error = 'Nie udało się pobrać najnowszych recenzji'
        this.latestReviews = []
      } finally {
        this.loading = false
      }
    },

    clearError() {
      this.error = null
    }
  },

  getters: {
    hasReviews: (state) => state.latestReviews.length > 0,
    reviewsCount: (state) => state.latestReviews.length
  }
}) 