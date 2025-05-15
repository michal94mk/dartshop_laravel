import { defineStore } from 'pinia';
import api from '../services/api';

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
      // This is just a placeholder. In a real app, filtering would be done on the server side
      return state.products;
    }
  },
  
  actions: {
    async fetchProducts(params = {}) {
      console.log('fetchProducts started with params:', params);
      this.loading = true;
      this.error = null;
      
      return new Promise(async (resolve, reject) => {
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
          
          const response = await api.getProducts(requestParams);
          console.log('Raw API response for products:', response);
          
          // Handle the response regardless of its structure
          if (response && response.data) {
            console.log('Products API response data type:', typeof response.data);
            console.log('Products API response has data property?', 'data' in response.data);
            
            let productsArray = [];
            let paginationInfo = {};
            
            // Case 1: Laravel paginated response format
            if (response.data.data && Array.isArray(response.data.data)) {
              console.log('Processing Laravel paginated response format');
              productsArray = response.data.data;
              paginationInfo = {
                currentPage: response.data.current_page || 1,
                totalPages: response.data.last_page || 1,
                total: response.data.total || response.data.data.length
              };
            } 
            // Case 2: Simple array response
            else if (Array.isArray(response.data)) {
              console.log('Processing simple array response format');
              productsArray = response.data;
              paginationInfo = {
                currentPage: 1,
                totalPages: 1,
                total: response.data.length
              };
            } 
            // Case 3: Object with items property (some APIs use this format)
            else if (response.data.items && Array.isArray(response.data.items)) {
              console.log('Processing object with items array format');
              productsArray = response.data.items;
              paginationInfo = {
                currentPage: response.data.page || 1,
                totalPages: response.data.pages || 1,
                total: response.data.total || response.data.items.length
              };
            }
            // Case 4: When response.data is an object but not following any known format
            else if (typeof response.data === 'object') {
              console.log('Processing unknown object format, attempting to extract products');
              // Try to find arrays in the response that might contain products
              const possibleArrays = Object.values(response.data).filter(val => Array.isArray(val));
              
              if (possibleArrays.length > 0) {
                // Use the largest array as it's likely to be the products
                productsArray = possibleArrays.reduce((a, b) => a.length > b.length ? a : b, []);
                paginationInfo = {
                  currentPage: 1,
                  totalPages: 1,
                  total: productsArray.length
                };
              } else {
                // Last resort: try to treat the object properties as products
                const possibleProducts = Object.values(response.data).filter(
                  val => val && typeof val === 'object' && 'id' in val
                );
                
                if (possibleProducts.length > 0) {
                  productsArray = possibleProducts;
                  paginationInfo = {
                    currentPage: 1,
                    totalPages: 1,
                    total: possibleProducts.length
                  };
                } else {
                  throw new Error('Could not identify products in the response');
                }
              }
            } else {
              throw new Error('Nieoczekiwany format odpowiedzi z API: ' + JSON.stringify(response.data));
            }
            
            // Ensure each product has required properties
            this.products = productsArray.map(product => ({
              ...product,
              price: product.price || 0,
              name: product.name || 'Unnamed Product',
              image_url: product.image_url || product.image || '/images/products/default.jpg'
            }));
            
            // Update pagination
            this.pagination = {
              ...this.pagination,
              ...paginationInfo
            };
            
            console.log('Processed products count:', this.products.length);
            console.log('Updated pagination:', this.pagination);
            
            this.loading = false;
            resolve(this.products);
          } else {
            console.error('No data in response:', response);
            this.products = [];
            this.error = 'Brak danych w odpowiedzi API';
            this.loading = false;
            reject(new Error('Brak danych w odpowiedzi API'));
          }
        } catch (error) {
          this.error = error.message || 'Failed to fetch products';
          console.error('Error fetching products:', error);
          
          // No longer provide fallback products if API fails
          this.products = [];
          
          this.pagination = {
            currentPage: 1,
            totalPages: 1,
            total: 0
          };
          
          this.loading = false;
          reject(error);
        }
        
        console.log('fetchProducts completed, products count:', this.products.length);
      });
    },
    
    async fetchFeaturedProducts() {
      console.log('fetchFeaturedProducts started');
      this.loading = true;
      this.error = null;
      
      try {
        const response = await api.getFeaturedProducts();
        console.log('Raw featured products response:', response);
        
        if (!response || !response.data) {
          throw new Error('No data in featured products response');
        }
        
        console.log('Featured products data type:', typeof response.data);
        
        // Handle different response formats
        let productsArray = [];
        
        if (Array.isArray(response.data)) {
          console.log('Processing array response for featured products');
          productsArray = response.data;
        } else if (response.data.data && Array.isArray(response.data.data)) {
          console.log('Processing object with data array for featured products');
          productsArray = response.data.data;
        } else if (typeof response.data === 'object') {
          console.log('Processing object response for featured products');
          // Try to find arrays in the response
          const possibleArrays = Object.values(response.data).filter(val => Array.isArray(val));
          
          if (possibleArrays.length > 0) {
            // Use the largest array as it's likely to be the products
            productsArray = possibleArrays.reduce((a, b) => a.length > b.length ? a : b, []);
          } else {
            // Last resort: try to treat the object properties as products
            const possibleProducts = Object.values(response.data).filter(
              val => val && typeof val === 'object' && 'id' in val
            );
            
            if (possibleProducts.length > 0) {
              productsArray = possibleProducts;
            } else {
              throw new Error('Could not identify featured products in the response');
            }
          }
        } else {
          throw new Error('Unexpected featured products response format: ' + JSON.stringify(response.data));
        }
        
        // Ensure each product has required properties
        this.featuredProducts = productsArray.map(product => ({
          ...product,
          price: product.price || 0,
          name: product.name || 'Unnamed Product',
          image_url: product.image_url || product.image || '/images/products/default.jpg'
        }));
        
        console.log('Processed featured products count:', this.featuredProducts.length);
      } catch (error) {
        this.error = error.message || 'Failed to fetch featured products';
        console.error('Error fetching featured products:', error);
        
        // Provide fallback featured products
        this.featuredProducts = [
          {
            id: 998,
            name: 'Featured Fallback Product',
            description: 'This featured product appears when API fails',
            price: 149.99,
            image_url: '/images/products/default.jpg'
          }
        ];
      } finally {
        this.loading = false;
        console.log('fetchFeaturedProducts completed');
      }
    },
    
    async fetchProductById(id) {
      this.loading = true;
      this.error = null;
      
      try {
        const response = await api.getProduct(id);
        this.currentProduct = response.data;
        return response.data;
      } catch (error) {
        this.error = error.message || `Failed to fetch product with ID: ${id}`;
        console.error(`Error fetching product with ID ${id}:`, error);
        return null;
      } finally {
        this.loading = false;
      }
    },
    
    setFilters(filters) {
      this.filters = { ...this.filters, ...filters };
      this.pagination.currentPage = 1; // Reset to page 1 when filters change
      this.fetchProducts();
    },
    
    setPage(page) {
      this.pagination.currentPage = page;
      this.fetchProducts();
    }
  }
}); 