import { defineStore } from 'pinia'
import { apiService } from '../services/apiService'
import type { Review, ApiResponse } from '@/types'

// Type definitions
interface ReviewState {
  latestReviews: Review[];
  loading: boolean;
  error: string | null;
}

export const useReviewStore = defineStore('review', {
  state: (): ReviewState => ({
    latestReviews: [],
    loading: false,
    error: null
  }),

  actions: {
    async fetchLatestReviews(limit: number = 6): Promise<void> {
      this.loading = true
      this.error = null
      
      try {
        const response = await apiService.get<Review[]>(`/reviews/latest?limit=${limit}`)
        
        if (response && Array.isArray(response)) {
          this.latestReviews = response
        } else {
          console.warn('Unexpected API response format:', response)
          this.latestReviews = []
        }
      } catch (error: any) {
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

    clearError(): void {
      this.error = null
    },

    // Reset store state
    $reset(): void {
      this.latestReviews = []
      this.loading = false
      this.error = null
    }
  },

  getters: {
    hasReviews: (state): boolean => state.latestReviews.length > 0,
    reviewsCount: (state): number => state.latestReviews.length
  }
})
