import axios from 'axios';

const api = axios.create({
  baseURL: '/api',
  headers: {
    'Content-Type': 'application/json',
    'X-Requested-With': 'XMLHttpRequest',
    'Accept': 'application/json'
  }
});

// Add a request interceptor to include the CSRF token
api.interceptors.request.use(config => {
  const token = document.head.querySelector('meta[name="csrf-token"]');
  if (token) {
    config.headers['X-CSRF-TOKEN'] = token.content;
  }
  return config;
});

// Add a response interceptor for error handling
api.interceptors.response.use(
  response => response,
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
    }
    return Promise.reject(error);
  }
);

export default {
  // Products
  getProducts(params = {}) {
    return api.get('/products', { params });
  },
  
  getProduct(id) {
    return api.get(`/products/${id}`);
  },
  
  getFeaturedProducts() {
    console.log('Calling featured products API endpoint');
    return api.get('/products/featured')
      .then(response => {
        console.log('Raw API response:', response);
        return response;
      })
      .catch(error => {
        console.error('Error in featured products API call:', error);
        throw error;
      });
  },
  
  // Categories
  getCategories() {
    return api.get('/categories');
  },
  
  getCategory(id) {
    return api.get(`/categories/${id}`);
  },
  
  getCategoryProducts(id, params = {}) {
    return api.get(`/categories/${id}/products`, { params });
  },
  
  // Cart
  getCart() {
    return api.get('/cart');
  },
  
  addToCart(productId, quantity = 1) {
    return api.post('/cart/add', { product_id: productId, quantity });
  },
  
  removeFromCart(productId) {
    return api.post('/cart/remove', { product_id: productId });
  },
  
  updateCart(items) {
    return api.post('/cart/update', { items });
  },
  
  clearCart() {
    return api.post('/cart/clear');
  },
  
  // Auth
  login(credentials) {
    return api.post('/login', credentials);
  },
  
  register(userData) {
    return api.post('/register', userData);
  },
  
  logout() {
    return api.post('/logout');
  },
  
  getUser() {
    return api.get('/user');
  },
  
  // Other endpoints
  getPromotions() {
    return api.get('/promotions');
  },
  
  getTutorials() {
    return api.get('/tutorials');
  },
  
  getTutorial(id) {
    return api.get(`/tutorials/${id}`);
  },
  
  // Contact
  sendContactForm(formData) {
    return api.post('/contact', formData);
  }
}; 