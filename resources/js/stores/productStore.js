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
      this.loading = true;
      this.error = null;
      
      try {
        const response = await api.getProducts({
          page: this.pagination.currentPage,
          per_page: this.pagination.perPage,
          category_id: this.filters.category,
          brand_id: this.filters.brand,
          search: this.filters.search,
          sort_by: this.filters.sort === 'newest' ? 'created_at' : 'price',
          sort_direction: this.filters.sort === 'price_desc' ? 'desc' : 'asc',
          ...params
        });
        
        this.products = response.data.data;
        this.pagination.currentPage = response.data.current_page;
        this.pagination.totalPages = response.data.last_page;
        this.pagination.total = response.data.total;
      } catch (error) {
        this.error = error.message || 'Failed to fetch products';
        console.error('Error fetching products:', error);
      } finally {
        this.loading = false;
      }
    },
    
    async fetchFeaturedProducts() {
      this.loading = true;
      this.error = null;
      
      try {
        const response = await api.getFeaturedProducts();
        console.log('Featured products data:', response.data);
        
        // Handle different response formats
        let productsArray = [];
        
        if (Array.isArray(response.data)) {
          // If response.data is already an array
          productsArray = response.data;
        } else if (response.data && typeof response.data === 'object') {
          // If it's an object that might contain a data property with the array
          if (Array.isArray(response.data.data)) {
            productsArray = response.data.data;
          } else {
            // If it's some other kind of object, try to get properties as array
            console.warn('Unexpected response format. Expected array, got:', typeof response.data);
            productsArray = Object.values(response.data).filter(item => item && typeof item === 'object');
          }
        }
        
        // Ensure each product has a valid price
        this.featuredProducts = productsArray.map(product => ({
          ...product,
          price: product.price || 0  // Default to 0 if price is missing
        }));
      } catch (error) {
        this.error = error.message || 'Failed to fetch featured products';
        console.error('Error fetching featured products:', error);
      } finally {
        this.loading = false;
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