import axios from 'axios';

// Create a specific API instance for newsletter with proper debugging
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

  // Add request interceptor for CSRF token and debugging
  api.interceptors.request.use(config => {
    const token = document.head.querySelector('meta[name="csrf-token"]');
    if (token) {
      config.headers['X-CSRF-TOKEN'] = token.content;
    }
    console.log(`Newsletter API Request: ${config.method.toUpperCase()} ${config.baseURL}${config.url}`, {
      data: config.data,
      headers: config.headers
    });
    return config;
  }, error => {
    console.error('Newsletter API Request Error:', error);
    return Promise.reject(error);
  });

  // Add response interceptor for debugging
  api.interceptors.response.use(
    response => {
      console.log(`Newsletter API Response: ${response.status} from ${response.config.url}`, response.data);
      return response;
    },
    error => {
      console.error('Newsletter API Response Error:', {
        url: error.config?.url,
        method: error.config?.method,
        status: error.response?.status,
        statusText: error.response?.statusText,
        data: error.response?.data,
        message: error.message
      });
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
      console.log('Newsletter subscribe - starting request with:', { email });
      
      const response = await api.post('/newsletter/subscribe', {
        email
      });
      
      console.log('Newsletter subscribe - success response:', response.data);
      return response.data;
    } catch (error) {
      console.error('Newsletter subscribe - error caught:', error);
      
      if (error.response && error.response.data) {
        console.log('Newsletter subscribe - returning error response data:', error.response.data);
        return error.response.data;
      }
      
      console.log('Newsletter subscribe - rethrowing error');
      throw error;
    }
  },

  /**
   * Verify email subscription
   */
  async verify(token) {
    try {
      console.log('Newsletter verify - starting request with token:', token);
      
      const response = await api.get(`/newsletter/verify?token=${encodeURIComponent(token)}`);
      
      console.log('Newsletter verify - success response:', response.data);
      return response.data;
    } catch (error) {
      console.error('Newsletter verify - error caught:', error);
      
      if (error.response && error.response.data) {
        console.log('Newsletter verify - returning error response data:', error.response.data);
        return error.response.data;
      }
      
      console.log('Newsletter verify - rethrowing error');
      throw error;
    }
  },

  /**
   * Unsubscribe from newsletter
   */
  async unsubscribe(email) {
    try {
      console.log('Newsletter unsubscribe - starting request with email:', email);
      
      const response = await api.post('/newsletter/unsubscribe', {
        email
      });
      
      console.log('Newsletter unsubscribe - success response:', response.data);
      return response.data;
    } catch (error) {
      console.error('Newsletter unsubscribe - error caught:', error);
      
      if (error.response && error.response.data) {
        console.log('Newsletter unsubscribe - returning error response data:', error.response.data);
        return error.response.data;
      }
      
      console.log('Newsletter unsubscribe - rethrowing error');
      throw error;
    }
  },

  /**
   * Check subscription status
   */
  async checkStatus(email) {
    try {
      console.log('Newsletter checkStatus - starting request with email:', email);
      
      const response = await api.post('/newsletter/status', {
        email
      });
      
      console.log('Newsletter checkStatus - success response:', response.data);
      return response.data;
    } catch (error) {
      console.error('Newsletter checkStatus - error caught:', error);
      
      if (error.response && error.response.data) {
        console.log('Newsletter checkStatus - returning error response data:', error.response.data);
        return error.response.data;
      }
      
      console.log('Newsletter checkStatus - rethrowing error');
      throw error;
    }
  }
}; 