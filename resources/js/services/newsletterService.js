import { apiService } from './index';

export const newsletterService = {
  /**
   * Subscribe to newsletter
   */
  async subscribe(email) {
    try {
      return await apiService.post('/newsletter/subscribe', { email });
    } catch (error) {
      if (error.response && error.response.data) return error.response.data;
      throw error;
    }
  },

  /**
   * Verify email subscription
   */
  async verify(token) {
    try {
      return await apiService.get('/newsletter/verify', { token: token });
    } catch (error) {
      if (error.response && error.response.data) return error.response.data;
      throw error;
    }
  },

  /**
   * Unsubscribe from newsletter
   */
  async unsubscribe(email) {
    try {
      return await apiService.post('/newsletter/unsubscribe', { email });
    } catch (error) {
      if (error.response && error.response.data) return error.response.data;
      throw error;
    }
  },

  /**
   * Check subscription status
   */
  async checkStatus(email) {
    try {
      return await apiService.post('/newsletter/status', { email });
    } catch (error) {
      if (error.response && error.response.data) return error.response.data;
      throw error;
    }
  }
}; 