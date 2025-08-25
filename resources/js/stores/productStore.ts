import { defineStore } from 'pinia';
import { apiService } from '../services/apiService';
import type { Product, ProductFilters, PaginationInfo, ApiResponse, PaginatedResponse } from '@/types';

// Type definitions
interface ProductState {
  products: Product[];
  latestProducts: Product[];
  currentProduct: Product | null;
  loading: boolean;
  error: string | null;
  filters: ProductFilters;
  pagination: PaginationInfo;
}

interface FetchProductsParams {
  page?: number;
  per_page?: number;
  category_id?: number | null;
  brand_id?: number | null;
  search?: string;
  price_min?: number | null;
  price_max?: number | null;
  sort_by?: string;
  sort_direction?: 'asc' | 'desc';
  _t?: number;
  _r?: string;
}

interface SetFiltersParams {
  category_id?: number | null;
  brand_id?: number | null;
  search?: string;
  min_price?: number;
  max_price?: number;
  sort_by?: string;
  sort_direction?: 'asc' | 'desc';
}

export const useProductStore = defineStore('product', {
  state: (): ProductState => ({
    products: [],
    latestProducts: [],
    currentProduct: null,
    loading: true,
    error: null,
    filters: {
      category_id: null,
      brand_id: null,
      search: '',
      min_price: 0,
      max_price: 1000,
      sort_by: 'created_at',
      sort_direction: 'desc'
    },
    pagination: {
      current_page: 1,
      last_page: 1,
      per_page: 12,
      total: 0
    }
  }),
  
  getters: {
    getProductById: (state) => (id: number): Product | undefined => {
      return state.products.find(product => product.id === id);
    },
    filteredProducts: (state): Product[] => {
      return state.products;
    }
  },
  
  actions: {
    async fetchProducts(params: Partial<FetchProductsParams> = {}): Promise<void> {
      console.log('fetchProducts started with params:', params);
      this.loading = true;
      this.error = null;
      
      try {
        const requestParams: FetchProductsParams = {
          page: this.pagination.current_page,
          per_page: this.pagination.per_page,
          category_id: this.filters.category_id,
          brand_id: this.filters.brand_id,
          search: this.filters.search,
          price_min: this.filters.min_price,
          price_max: this.filters.max_price,
          sort_by: this.filters.sort_by,
          sort_direction: this.filters.sort_direction,
          ...params,
          _t: new Date().getTime(),
          _r: Math.random().toString(36).substring(7)
        };
        
        console.log('Sending API request with params:', requestParams);
        
        let apiUrl = '/api/products';
        if ((window as any).Laravel && (window as any).Laravel.apiUrl) {
          apiUrl = `${(window as any).Laravel.apiUrl}/products`;
        }
        
        const response = await apiService.get<PaginatedResponse<Product>>('/products', requestParams);
        
        // apiService extracts the data from { success: true, data: {...} }
        // So response is already the paginated data structure
        if (response && response.data && Array.isArray(response.data)) {
          this.products = response.data.map(product => ({
            ...product,
            price: product.price || '0',
            name: product.name || 'Unnamed Product',
            image_url: product.image_url || null
          }));
          
          this.pagination = {
            current_page: response.current_page || 1,
            last_page: response.last_page || 1,
            per_page: response.per_page || 12,
            total: response.total || 0
          };
        } else {
          console.error('Unexpected API response format:', response);
          throw new Error('Invalid API response format - Unexpected structure');
        }
        
        this.loading = false;
        this.error = null;
        
      } catch (error: any) {
        console.error('Error fetching products:', error);
        this.loading = false;
        this.error = 'Wystąpił błąd podczas ładowania produktów. Spróbuj ponownie później.';
        this.products = [];
      }
    },

    setPage(page: number): void {
      if (page >= 1 && page <= this.pagination.last_page) {
        this.pagination.current_page = page;
        this.fetchProducts();
      }
    },

    async fetchLatestProducts(): Promise<void> {
      this.loading = true;
      this.error = null;
      
      try {
        const response = await apiService.get<{ data: Product[], meta: { count: number } }>('/products/latest');
        console.log('Latest products API response:', response);
        
        // apiService extracts the data from { success: true, data: {...} }
        // So response is already { data: [...products...], meta: { count: ... } }
        if (response && response.data && Array.isArray(response.data)) {
          this.latestProducts = response.data.map(product => ({
            ...product,
            price: product.price || '0',
            name: product.name || 'Unnamed Product',
            image_url: product.image_url || null
          }));
        } else {
          console.error('Unexpected latest products API response format:', response);
          this.latestProducts = [];
        }
        
        console.log('Processed latest products:', this.latestProducts);
      } catch (error: any) {
        console.error('Error fetching latest products:', error);
        this.latestProducts = [];
        this.error = 'Wystąpił błąd podczas ładowania produktów. Spróbuj ponownie później.';
      } finally {
        this.loading = false;
      }
    },
    
    async fetchProductById(id: number): Promise<Product> {
      this.loading = true;
      this.error = null;
      
      try {
        console.log(`Fetching product with ID: ${id}`);
        const response = await apiService.get<Product>(`/products/${id}`);
        console.log('API Response:', response);
        
        if (response) {
          // apiService extracts the data from { success: true, data: {...} }
          // So response is already the product object
          this.currentProduct = {
            ...response,
            price: response.price || '0',
            name: response.name || 'Unnamed Product',
            image_url: response.image_url || null
          };
          
          console.log('Current product set:', this.currentProduct);
          
          // Return the current product for the component to use
          return this.currentProduct;
        } else {
          throw new Error('Invalid API response format');
        }
      } catch (error: any) {
        console.error('Error fetching product by ID:', error);
        this.error = error.message || 'Failed to fetch product details';
        
        // Create a fallback product
        this.currentProduct = {
          id: id,
          name: 'Fallback Product',
          description: 'This is a fallback product due to API error',
          price: '99.99',
          image_url: null,
          slug: 'fallback-product',
          category_id: 1,
          is_active: true,
          is_featured: false,
          stock_quantity: 0,
          reviews_count: 0,
          average_rating: 0,
          created_at: new Date().toISOString(),
          updated_at: new Date().toISOString()
        };
        
        // Return the fallback product
        return this.currentProduct;
      } finally {
        this.loading = false;
      }
    },
    
    setFilters(filters: SetFiltersParams): void {
      this.filters = {
        ...this.filters,
        ...filters
      };
    },
    
    clearAllFilters(): void {
      this.filters = {
        category_id: null,
        brand_id: null,
        search: '',
        min_price: 0,
        max_price: 1000,
        sort_by: 'created_at',
        sort_direction: 'desc'
      };
    },
    
    clearSearchFilter(): void {
      this.filters.search = '';
    },
    
    forceReloadProducts(): void {
      this.latestProducts = [];
      this.loading = true;
      this.error = null;
      this.fetchLatestProducts();
    }
  }
});
