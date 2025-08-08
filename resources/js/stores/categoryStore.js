import { defineStore } from 'pinia';
import { productApi } from '../services';

export const useCategoryStore = defineStore('category', {
  state: () => ({
    categories: [],
    currentCategory: null,
    loading: false,
    error: null,
  }),
  
  getters: {
    categoriesWithProducts: (state) => {
      return state.categories;
    },
    
    getCategoryById: (state) => (id) => {
      return state.categories.find(category => category.id === parseInt(id));
    },
    
    orderedCategories: (state) => {
      return [...state.categories].sort((a, b) => {
        return a.name.localeCompare(b.name);
      });
    }
  },
  
  actions: {
    async fetchCategories() {
      console.log('CategoryStore: fetchCategories called');
      this.loading = true;
      this.error = null;
      
      try {
        const response = await productApi.getCategories();
        console.log('CategoryStore: API response:', response.data);
        
        // Handle new API response format (BaseApiController)
        if (response.data && response.data.success && response.data.data) {
          if (Array.isArray(response.data.data.data)) {
            this.categories = response.data.data.data;
          } else if (Array.isArray(response.data.data)) {
            this.categories = response.data.data;
          } else {
            this.categories = [];
          }
        } else if (response.data && response.data.data) {
          this.categories = response.data.data;
        } else if (Array.isArray(response.data)) {
          this.categories = response.data;
        } else {
          throw new Error('Unexpected API response format');
        }
        
        console.log('CategoryStore: Categories set to:', this.categories);
      } catch (error) {
        console.error('CategoryStore: Error fetching categories:', error);
        this.error = error.message || 'Błąd podczas pobierania kategorii';
        this.categories = [];
      } finally {
        this.loading = false;
      }
    },

    async refreshCategories() {
      console.log('CategoryStore: refreshCategories called');
      await this.fetchCategories();
    },

    async forceRefreshCategories() {
      console.log('CategoryStore: forceRefreshCategories called - bypassing cache');
      this.loading = true;
      this.error = null;
      
      try {
        // Add a timestamp to bypass cache
        const response = await productApi.getCategories({ _t: Date.now() });
        console.log('CategoryStore: Force refresh API response:', response.data);
        
        // Handle new API response format (BaseApiController)
        if (response.data.success && response.data.data) {
          // Nowy format: { success: true, data: { data: [...], meta: {...} } }
          if (Array.isArray(response.data.data)) {
            this.categories = response.data.data;
          } else if (Array.isArray(response.data.data.data)) {
            this.categories = response.data.data.data;
          } else {
            this.categories = [];
          }
        } else if (response.data && response.data.data) {
          // Fallback for old format: { data: [...] }
          this.categories = response.data.data;
        } else if (Array.isArray(response.data)) {
          // Direct array response
          this.categories = response.data;
        } else {
          throw new Error('Unexpected API response format');
        }
        
        console.log('CategoryStore: Categories force refreshed to:', this.categories);
      } catch (error) {
        console.error('CategoryStore: Error force refreshing categories:', error);
        this.error = error.message || 'Błąd podczas odświeżania kategorii';
        this.categories = [];
      } finally {
        this.loading = false;
      }
    },
    
    async fetchCategory(id) {
      console.log('CategoryStore: fetchCategory called with:', id);
      this.loading = true;
      this.error = null;
      
      try {
        const response = await productApi.getCategory(id);
        this.currentCategory = response.data;
        
        // Also update the category in the categories array if it exists
        const index = this.categories.findIndex(cat => cat.id === this.currentCategory.id);
        
        if (index !== -1) {
          this.categories[index] = this.currentCategory;
        }
        
        return this.currentCategory;
      } catch (error) {
        console.error('CategoryStore: Error fetching category:', error);
        this.error = error.message || 'Błąd podczas pobierania kategorii';
        this.currentCategory = null;
        throw error;
      } finally {
        this.loading = false;
      }
    },
    
    clearCurrentCategory() {
      this.currentCategory = null;
    },
    
    clearError() {
      this.error = null;
    }
  }
}); 