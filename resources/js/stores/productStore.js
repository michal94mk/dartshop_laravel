import { defineStore } from 'pinia';
import axios from 'axios';

export const useProductStore = defineStore('product', {
  state: () => ({
    products: [],
    latestProducts: [],
    currentProduct: null,
    loading: true,
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
          _t: new Date().getTime(),
          _r: Math.random().toString(36).substring(7)
        };
        
        console.log('Sending API request with params:', requestParams);
        
        let apiUrl = '/api/products';
        if (window.Laravel && window.Laravel.apiUrl) {
          apiUrl = `${window.Laravel.apiUrl}/products`;
        }
        
        const response = await axios.get(apiUrl, { params: requestParams });
        
        if (typeof response.data === 'string' && response.data.includes('<!DOCTYPE html>')) {
          console.error('Received HTML instead of JSON. API route issue detected.');
          throw new Error('API returned HTML instead of JSON. Check routes and CSRF configuration.');
        }
        
        // Handle new API response format (BaseApiController)
        if (response.data.success && response.data.data) {
          // New format: { success: true, data: { data: [...], current_page, last_page, ... } }
          const responseData = response.data.data;
          
          if (responseData.data && Array.isArray(responseData.data)) {
            this.products = responseData.data.map(product => ({
              ...product,
              price: product.price || 0,
              name: product.name || 'Unnamed Product',
              image_url: product.image_url || null
            }));
            
            this.pagination = {
              currentPage: parseInt(responseData.current_page) || 1,
              totalPages: parseInt(responseData.last_page) || 1,
              perPage: parseInt(responseData.per_page) || 12,
              total: parseInt(responseData.total) || 0
            };
          } else if (Array.isArray(responseData)) {
            this.products = responseData.map(product => ({
              ...product,
              price: product.price || 0,
              name: product.name || 'Unnamed Product',
              image_url: product.image_url || null
            }));
            
            this.pagination = {
              currentPage: 1,
              totalPages: 1,
              perPage: this.pagination.perPage,
              total: responseData.length
            };
          } else {
            console.error('Unexpected API response format:', response.data);
            throw new Error('Invalid API response format - Unexpected structure');
          }
        } else if (response.data.data && Array.isArray(response.data.data)) {
          // Fallback for old format: { data: [...], current_page, last_page, ... }
          this.products = response.data.data.map(product => ({
            ...product,
            price: product.price || 0,
            name: product.name || 'Unnamed Product',
            image_url: product.image_url || null
          }));
          
          this.pagination = {
            currentPage: parseInt(response.data.current_page) || 1,
            totalPages: parseInt(response.data.last_page) || 1,
            perPage: parseInt(response.data.per_page) || 12,
            total: parseInt(response.data.total) || 0
          };
        } else if (Array.isArray(response.data)) {
          // Fallback for direct array response
          this.products = response.data.map(product => ({
            ...product,
            price: product.price || 0,
            name: product.name || 'Unnamed Product',
            image_url: product.image_url || null
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
        
        this.loading = false;
        this.error = null;
        
      } catch (error) {
        console.error('Error fetching products:', error);
        this.loading = false;
        this.error = 'Wystąpił błąd podczas ładowania produktów. Spróbuj ponownie później.';
        this.products = [];
      }
    },

    setPage(page) {
      if (page >= 1 && page <= this.pagination.totalPages) {
        this.pagination.currentPage = page;
        this.fetchProducts();
      }
    },

    async fetchLatestProducts() {
      this.loading = true;
      this.error = null;
      
      try {
        const response = await axios.get('/api/products/latest');
        console.log('Latest products API response:', response.data);
        
        // Handle new API response format (BaseApiController)
        if (response.data.success && response.data.data) {
          // New format: { success: true, data: { data: [...], meta: {...} } }
          const responseData = response.data.data;
          
          if (responseData.data && Array.isArray(responseData.data)) {
            this.latestProducts = responseData.data.map(product => ({
              ...product,
              price: product.price || 0,
              name: product.name || 'Unnamed Product',
              image_url: product.image_url || null
            }));
          } else if (Array.isArray(responseData)) {
            this.latestProducts = responseData.map(product => ({
              ...product,
              price: product.price || 0,
              name: product.name || 'Unnamed Product',
              image_url: product.image_url || null
            }));
          } else {
            console.error('Unexpected latest products API response format:', response.data);
            this.latestProducts = [];
          }
        } else if (response.data.data && Array.isArray(response.data.data)) {
          // Fallback for old format: { data: [...], meta: {...} }
          this.latestProducts = response.data.data.map(product => ({
            ...product,
            price: product.price || 0,
            name: product.name || 'Unnamed Product',
            image_url: product.image_url || null
          }));
        } else if (Array.isArray(response.data)) {
          // Fallback for direct array response
          this.latestProducts = response.data.map(product => ({
            ...product,
            price: product.price || 0,
            name: product.name || 'Unnamed Product',
            image_url: product.image_url || null
          }));
        } else {
          console.error('Unexpected latest products API response format:', response.data);
          this.latestProducts = [];
        }
        
        console.log('Processed latest products:', this.latestProducts);
      } catch (error) {
        console.error('Error fetching latest products:', error);
        this.latestProducts = [];
        this.error = 'Wystąpił błąd podczas ładowania produktów. Spróbuj ponownie później.';
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
          // Handle new API response format (BaseApiController)
          let productData;
          
          if (response.data.success && response.data.data) {
            // New format: { success: true, data: {...} }
            productData = response.data.data;
          } else if (response.data.data) {
            // Fallback for old format: { data: {...} }
            productData = response.data.data;
          } else {
            // Direct product response
            productData = response.data;
          }
          
          this.currentProduct = {
            ...productData,
            price: productData.price || 0,
            name: productData.name || 'Unnamed Product',
            image_url: productData.image_url || null
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
          image_url: null
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
    
    clearAllFilters() {
      this.filters = {
        category: null,
        brand: null,
        search: '',
        priceRange: [0, 1000],
        sort: 'newest'
      };
    },
    
    clearSearchFilter() {
      this.filters.search = '';
    },
    
    forceReloadProducts() {
      this.latestProducts = [];
      this.loading = true;
      this.error = null;
      this.fetchLatestProducts();
    }
  }
}); 