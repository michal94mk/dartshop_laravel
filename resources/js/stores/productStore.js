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
          ...params
        };
        
        console.log('Sending API request with params:', requestParams);
        
        const response = await axios.get('/api/products', { params: requestParams });
        
        console.log('Raw API response for products:', response);
        
        if (response && response.data) {
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
              total: response.data.total || response.data.data.length
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
          }
          
          console.log('Processed products count:', this.products.length);
          console.log('First product sample:', this.products.length > 0 ? this.products[0] : 'No products found');
        } else {
          throw new Error('Invalid API response format');
        }
      } catch (error) {
        console.error('Error fetching products:', error);
        this.error = error.message || 'Failed to fetch products';
        
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
        const response = await axios.get(`/api/products/${id}`);
        
        if (response && response.data) {
          this.currentProduct = {
            ...response.data,
            price: response.data.price || 0,
            name: response.data.name || 'Unnamed Product',
            image_url: response.data.image_url || response.data.image || 'https://via.placeholder.com/300x300/indigo/fff?text=Default'
          };
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