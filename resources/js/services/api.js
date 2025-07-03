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
  return config;
}, error => {
  return Promise.reject(error);
});

// Add a response interceptor for error handling
api.interceptors.response.use(
  response => {
    return response;
  },
  error => {
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
        // Could dispatch to a notification system or global error handler
      }
    }
    
    return Promise.reject(error);
  }
);

// Helper function for API calls
const withLogging = (apiCall, name) => {
  return (...args) => {
    return apiCall(...args);
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