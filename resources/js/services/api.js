import axios from 'axios';

const api = axios.create({
  baseURL: '/api',
  headers: {
    'Content-Type': 'application/json',
    'X-Requested-With': 'XMLHttpRequest',
    'Accept': 'application/json'
  },
  // Add timeout to prevent hanging requests
  timeout: 10000
});

// Add a request interceptor to include the CSRF token
api.interceptors.request.use(config => {
  const token = document.head.querySelector('meta[name="csrf-token"]');
  if (token) {
    config.headers['X-CSRF-TOKEN'] = token.content;
  }
  console.log(`API Request: ${config.method.toUpperCase()} ${config.url}`, config.params || {});
  return config;
}, error => {
  console.error('API Request Error:', error);
  return Promise.reject(error);
});

// Add a response interceptor for error handling
api.interceptors.response.use(
  response => {
    console.log(`API Response: ${response.status} from ${response.config.url}`, {
      data_type: typeof response.data,
      is_array: Array.isArray(response.data),
      has_data_prop: response.data && typeof response.data === 'object' && 'data' in response.data,
      data_sample: response.data && typeof response.data === 'object' ? 
        (Array.isArray(response.data) ? 
          (response.data.length > 0 ? '(array with items)' : '(empty array)') : 
          Object.keys(response.data).slice(0, 3)) : 
        '(primitive value)'
    });
    return response;
  },
  error => {
    console.error('API Response Error:', {
      url: error.config?.url,
      method: error.config?.method,
      status: error.response?.status,
      statusText: error.response?.statusText,
      data: error.response?.data,
      message: error.message
    });

    if (error.response) {
      // Authentication errors
      if (error.response.status === 401) {
        // Handle unauthorized errors (redirect to login)
        window.location.href = '/login';
      }
      
      // Validation errors
      if (error.response.status === 422) {
        return Promise.reject(error.response.data);
      }
      
      // 5xx errors
      if (error.response.status >= 500) {
        console.error('Server error:', error.response.data);
        // Could dispatch to a notification system or global error handler
      }
    } else if (error.request) {
      // The request was made but no response was received
      console.error('No response received:', error.request);
    } else {
      // Something happened in setting up the request
      console.error('Request setup error:', error.message);
    }
    
    return Promise.reject(error);
  }
);

// Helper function to add logging to API calls
const withLogging = (apiCall, name) => {
  return (...args) => {
    console.log(`Calling ${name} API endpoint with args:`, args);
    const url = args[0] || '';
    console.log(`Full URL for ${name}: ${window.location.origin}/api${url}`);
    
    return apiCall(...args)
      .then(response => {
        console.log(`Success in ${name} API call:`, response);
        return response;
      })
      .catch(error => {
        console.error(`Error in ${name} API call:`, error);
        throw error;
      });
  };
};

export default {
  // Products
  getProducts(params = {}) {
    return withLogging(api.get, 'getProducts')('/products', { params });
  },
  
  getProduct(id) {
    return withLogging(api.get, 'getProduct')(`/products/${id}`);
  },
  
  getFeaturedProducts() {
    return withLogging(api.get, 'getFeaturedProducts')('/products/featured');
  },
  
  // Categories
  getCategories() {
    return withLogging(api.get, 'getCategories')('/categories');
  },
  
  getCategory(id) {
    return withLogging(api.get, 'getCategory')(`/categories/${id}`);
  },
  
  getCategoryProducts(id, params = {}) {
    return withLogging(api.get, 'getCategoryProducts')(`/categories/${id}/products`, { params });
  },
  
  // Cart
  getCart() {
    return withLogging(api.get, 'getCart')('/cart');
  },
  
  addToCart(productId, quantity = 1) {
    return withLogging(api.post, 'addToCart')('/cart/add', { product_id: productId, quantity });
  },
  
  removeFromCart(productId) {
    return withLogging(api.post, 'removeFromCart')('/cart/remove', { product_id: productId });
  },
  
  updateCart(items) {
    return withLogging(api.post, 'updateCart')('/cart/update', { items });
  },
  
  clearCart() {
    return withLogging(api.post, 'clearCart')('/cart/clear');
  },
  
  // Auth
  login(credentials) {
    return withLogging(api.post, 'login')('/login', credentials);
  },
  
  register(userData) {
    return withLogging(api.post, 'register')('/register', userData);
  },
  
  logout() {
    // Use the full API path to ensure we're hitting the API route
    // Add a query parameter for debugging to ensure we're hitting the right endpoint
    return axios.post('/api/logout', {}, {
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      },
      withCredentials: true
    });
  },
  
  getUser() {
    return withLogging(api.get, 'getUser')('/user');
  },
  
  // Other endpoints
  getPromotions() {
    return withLogging(api.get, 'getPromotions')('/promotions');
  },
  
  getTutorials() {
    return withLogging(api.get, 'getTutorials')('/tutorials');
  },
  
  getTutorial(id) {
    return withLogging(api.get, 'getTutorial')(`/tutorials/${id}`);
  },
  
  // Contact
  sendContactForm(formData) {
    return withLogging(api.post, 'sendContactForm')('/contact', formData);
  },

  // Admin Newsletter
  getAdminNewsletter(params = {}) {
    return withLogging(api.get, 'getAdminNewsletter')('/admin/newsletter', { params });
  },

  deleteNewsletterSubscription(id) {
    return withLogging(api.delete, 'deleteNewsletterSubscription')(`/admin/newsletter/${id}`);
  },

  exportNewsletterSubscriptions(params = {}) {
    return withLogging(api.get, 'exportNewsletterSubscriptions')('/admin/newsletter/export', { 
      params, 
      responseType: 'blob' 
    });
  }
}; 