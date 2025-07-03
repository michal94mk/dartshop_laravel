import axios from 'axios';

// Create a specific API instance for newsletter
const createNewsletterAPI = () => {
  const api = axios.create({
    baseURL: '/api',
    headers: {
      'Content-Type': 'application/json',
      'X-Requested-With': 'XMLHttpRequest',
      'Accept': 'application/json'
    },
    timeout: 10000
  });

  // Add request interceptor for CSRF token
  api.interceptors.request.use(config => {
    const token = document.head.querySelector('meta[name="csrf-token"]');
    if (token) {
      config.headers['X-CSRF-TOKEN'] = token.content;
    }
    return config;
  }, error => {
    return Promise.reject(error);
  });

  // Add response interceptor
  api.interceptors.response.use(
    response => {
      return response;
    },
    error => {
      return Promise.reject(error);
    }
  );

  return api;
};

const api = createNewsletterAPI();

export const newsletterService = {
  /**
   * Subscribe to newsletter
   */
  async subscribe(email) {
    try {
      const response = await api.post('/newsletter/subscribe', {
        email
      });
      
      return response.data;
    } catch (error) {
      if (error.response && error.response.data) {
        return error.response.data;
      }
      
      throw error;
    }
  },

  /**
   * Verify email subscription
   */
  async verify(token) {
    try {
      const response = await api.get(`/newsletter/verify?token=${encodeURIComponent(token)}`);
      
      return response.data;
    } catch (error) {
      if (error.response && error.response.data) {
        return error.response.data;
      }
      
      throw error;
    }
  },

  /**
   * Unsubscribe from newsletter
   */
  async unsubscribe(email) {
    try {
      const response = await api.post('/newsletter/unsubscribe', {
        email
      });
      
      return response.data;
    } catch (error) {
      if (error.response && error.response.data) {
        return error.response.data;
      }
      
      throw error;
    }
  },

  /**
   * Check subscription status
   */
  async checkStatus(email) {
    try {
      const response = await api.post('/newsletter/status', {
        email
      });
      
      return response.data;
    } catch (error) {
      if (error.response && error.response.data) {
        return error.response.data;
      }
      
      throw error;
    }
  }
}; 