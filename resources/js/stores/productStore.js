import { defineStore } from 'pinia';
import axios from 'axios';

export const useProductStore = defineStore('product', {
  state: () => ({
    products: [],
    featuredProducts: [],
    currentProduct: null,
    loading: false,
    error: null,
    filters: {
      category: null,
      brand: null,
      search: '',
      priceRange: [0, 1000],
      sort: 'newest'
    },
    pagination: {
      currentPage: 1,
      totalPages: 1,
      perPage: 12,
      total: 0
    }
  }),
  
  getters: {
    getProductById: (state) => (id) => {
      return state.products.find(product => product.id === parseInt(id));
    },
    filteredProducts: (state) => {
      return state.products;
    }
  },
  
  actions: {
    async fetchProducts(params = {}) {
      console.log('fetchProducts started with params:', params);
      this.loading = true;
      this.error = null;
      
      try {
        const requestParams = {
          page: this.pagination.currentPage,
          per_page: this.pagination.perPage,
          category_id: this.filters.category,
          brand_id: this.filters.brand,
          search: this.filters.search,
          price_min: this.filters.priceRange && this.filters.priceRange[0] ? parseFloat(this.filters.priceRange[0]) : null,
          price_max: this.filters.priceRange && this.filters.priceRange[1] ? parseFloat(this.filters.priceRange[1]) : null,
          sort_by: this.filters.sort === 'newest' ? 'created_at' : 
                  this.filters.sort === 'price_asc' ? 'price' :
                  this.filters.sort === 'price_desc' ? 'price' :
                  this.filters.sort === 'name_asc' ? 'name' :
                  this.filters.sort === 'name_desc' ? 'name' : 'created_at',
          sort_direction: this.filters.sort === 'price_desc' || this.filters.sort === 'name_desc' ? 'desc' : 
                         this.filters.sort === 'newest' ? 'desc' : 'asc',
          ...params,
          // Add a timestamp to prevent caching
          _nocache: new Date().getTime()
        };
        
        console.log('Sending API request with params:', requestParams);
        
        // Get API URL (absolute or relative)
        let apiUrl = '/api/products';
        
        // Use window.Laravel.apiUrl if available
        if (window.Laravel && window.Laravel.apiUrl) {
          apiUrl = `${window.Laravel.apiUrl}/products`;
          console.log('Using full API URL from window.Laravel:', apiUrl);
        } else {
          // Try using a relative URL with explicit API prefix
          apiUrl = '/api/products';
          console.log('Using relative API URL:', apiUrl);
        }
        
        // Try the simple test endpoint first to verify API connectivity
        try {
          console.log('Testing API connectivity...');
          const testResponse = await axios.get('/api/test');
          console.log('API test endpoint response:', testResponse.data);
        } catch (error) {
          console.error('API test endpoint failed:', error);
        }
        
        const response = await axios.get(apiUrl, { 
          params: requestParams,
          headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
          }
        });
        
        console.log('Raw API response for products:', response);
        
        if (response && response.data) {
          // Check if the response is HTML instead of JSON
          if (typeof response.data === 'string' && response.data.includes('<!DOCTYPE html>')) {
            console.error('Received HTML instead of JSON. API route issue detected.');
            throw new Error('API returned HTML instead of JSON. Check routes and CSRF configuration.');
          }
          
          // Handle standard Laravel pagination format
          if (response.data.data && Array.isArray(response.data.data)) {
            this.products = response.data.data.map(product => ({
              ...product,
              price: product.price || 0,
              name: product.name || 'Unnamed Product',
              image_url: product.image_url || product.image || 'https://via.placeholder.com/300x300/indigo/fff?text=Default'
            }));
            
            this.pagination = {
              currentPage: response.data.current_page || 1,
              totalPages: response.data.last_page || 1,
              perPage: response.data.per_page || 12,
              total: response.data.total || 0
            };
          } 
          // Handle standard array response
          else if (Array.isArray(response.data)) {
            this.products = response.data.map(product => ({
              ...product,
              price: product.price || 0,
              name: product.name || 'Unnamed Product',
              image_url: product.image_url || product.image || 'https://via.placeholder.com/300x300/indigo/fff?text=Default'
            }));
            
            this.pagination = {
              currentPage: 1,
              totalPages: 1,
              perPage: this.pagination.perPage,
              total: response.data.length
            };
          } else {
            console.error('Unexpected API response format:', response.data);
            throw new Error('Invalid API response format - Unexpected structure');
          }
          
          console.log('Processed products count:', this.products.length);
          console.log('First product sample:', this.products.length > 0 ? this.products[0] : 'No products found');
        } else {
          console.error('Invalid API response:', response);
          throw new Error('Invalid API response format - Empty or null response data');
        }
      } catch (error) {
        console.error('Error fetching products:', error);
        
        // Add more detailed error information
        let errorMessage = 'Failed to fetch products';
        if (error.response) {
          // The request was made and the server responded with a status code that falls out of the range of 2xx
          console.error('API Error Status:', error.response.status);
          console.error('API Error Data:', error.response.data);
          errorMessage += ` - Server responded with ${error.response.status}`;
        } else if (error.request) {
          // The request was made but no response was received
          console.error('API Error: No response received', error.request);
          errorMessage += ' - No response from server';
        } else {
          // Something happened in setting up the request that triggered an Error
          console.error('API Request Setup Error:', error.message);
          errorMessage += ` - ${error.message}`;
        }
        
        this.error = errorMessage;
        
        // Use fallback mock products for demonstration
        this.products = [
          {
            id: 1,
            name: 'Fallback Product 1',
            description: 'This is a fallback product due to API error',
            price: 99.99,
            image_url: 'https://via.placeholder.com/300x300/indigo/fff?text=Fallback+Product'
          },
          {
            id: 2,
            name: 'Fallback Product 2',
            description: 'This is another fallback product due to API error',
            price: 149.99,
            image_url: 'https://via.placeholder.com/300x300/indigo/fff?text=Fallback+Product'
          }
        ];
        
        this.pagination = {
          currentPage: 1,
          totalPages: 1,
          perPage: this.pagination.perPage,
          total: this.products.length
        };
      } finally {
        this.loading = false;
      }
    },
    
    async fetchFeaturedProducts() {
      this.loading = true;
      this.error = null;
      
      try {
        const response = await axios.get('/api/products/featured');
        
        if (response && response.data) {
          if (Array.isArray(response.data)) {
            this.featuredProducts = response.data;
          } else if (response.data.data && Array.isArray(response.data.data)) {
            this.featuredProducts = response.data.data;
          } else {
            throw new Error('Invalid featured products response format');
          }
          
          // Ensure each product has required properties
          this.featuredProducts = this.featuredProducts.map(product => ({
            ...product,
            price: product.price || 0,
            name: product.name || 'Unnamed Product',
            image_url: product.image_url || product.image || 'https://via.placeholder.com/300x300/indigo/fff?text=Default'
          }));
        } else {
          throw new Error('Invalid API response format');
        }
      } catch (error) {
        console.error('Error fetching featured products:', error);
        this.error = error.message || 'Failed to fetch featured products';
        
        // Use fallback featured products
        this.featuredProducts = [
          {
            id: 1,
            name: 'Fallback Featured Product 1',
            description: 'This is a fallback featured product due to API error',
            price: 99.99,
            image_url: 'https://via.placeholder.com/300x300/indigo/fff?text=Fallback+Featured'
          },
          {
            id: 2,
            name: 'Fallback Featured Product 2',
            description: 'This is another fallback featured product due to API error',
            price: 149.99,
            image_url: 'https://via.placeholder.com/300x300/indigo/fff?text=Fallback+Featured'
          }
        ];
      } finally {
        this.loading = false;
      }
    },
    
    async fetchProductById(id) {
      this.loading = true;
      this.error = null;
      
      try {
        console.log(`Fetching product with ID: ${id}`);
        const response = await axios.get(`/api/products/${id}`);
        console.log('API Response:', response);
        
        if (response && response.data) {
          // The API may return the product directly or wrapped in a 'data' property
          const productData = response.data.data || response.data;
          
          this.currentProduct = {
            ...productData,
            price: productData.price || 0,
            name: productData.name || 'Unnamed Product',
            image_url: productData.image_url || productData.image || 'https://via.placeholder.com/300x300/indigo/fff?text=Default'
          };
          
          console.log('Current product set:', this.currentProduct);
          
          // Return the current product for the component to use
          return this.currentProduct;
        } else {
          throw new Error('Invalid API response format');
        }
      } catch (error) {
        console.error('Error fetching product by ID:', error);
        this.error = error.message || 'Failed to fetch product details';
        
        // Create a fallback product
        this.currentProduct = {
          id: parseInt(id),
          name: 'Fallback Product',
          description: 'This is a fallback product due to API error',
          price: 99.99,
          image_url: 'https://via.placeholder.com/300x300/indigo/fff?text=Fallback+Product'
        };
        
        // Return the fallback product
        return this.currentProduct;
      } finally {
        this.loading = false;
      }
    },
    
    setFilters(filters) {
      this.filters = {
        ...this.filters,
        ...filters
      };
    },
    
    setPage(page) {
      this.pagination.currentPage = page;
    }
  }
}); 