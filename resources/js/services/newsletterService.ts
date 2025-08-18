import apiService from './apiService'

interface NewsletterResponse {
  message: string
  success?: boolean
}

interface SubscriptionStatus {
  subscribed: boolean
  verified: boolean
  status: 'active' | 'pending' | 'unsubscribed'
}

/**
 * Newsletter-related API calls
 */
export const newsletterService = {
  /**
   * Subscribe to newsletter
   */
  async subscribe(email: string): Promise<NewsletterResponse> {
    try {
      return await apiService.post<NewsletterResponse>('/newsletter/subscribe', { email })
    } catch (error: any) {
      if (error.response?.data) return error.response.data
      throw error
    }
  },

  /**
   * Verify email subscription
   */
  async verify(token: string): Promise<NewsletterResponse> {
    try {
      return await apiService.get<NewsletterResponse>('/newsletter/verify', { token })
    } catch (error: any) {
      if (error.response?.data) return error.response.data
      throw error
    }
  },

  /**
   * Unsubscribe from newsletter
   */
  async unsubscribe(email: string): Promise<NewsletterResponse> {
    try {
      return await apiService.post<NewsletterResponse>('/newsletter/unsubscribe', { email })
    } catch (error: any) {
      if (error.response?.data) return error.response.data
      throw error
    }
  },

  /**
   * Check subscription status
   */
  async checkStatus(email: string): Promise<SubscriptionStatus> {
    try {
      return await apiService.post<SubscriptionStatus>('/newsletter/status', { email })
    } catch (error: any) {
      if (error.response?.data) return error.response.data
      throw error
    }
  }
}

export default newsletterService
