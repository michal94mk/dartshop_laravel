import { defineStore } from 'pinia';
import { productApi } from '../services';
import type { Category, ApiResponse } from '@/types';

// Type definitions
interface CategoryState {
  categories: Category[];
  currentCategory: Category | null;
  loading: boolean;
  error: string | null;
}

interface CategoryParams {
  _t?: number;
}

export const useCategoryStore = defineStore('category', {
  state: (): CategoryState => ({
    categories: [],
    currentCategory: null,
    loading: false,
    error: null,
  }),
  
  getters: {
    categoriesWithProducts: (state): Category[] => {
      return state.categories;
    },
    
    getCategoryById: (state) => (id: number): Category | undefined => {
      return state.categories.find(category => category.id === id);
    },
    
    orderedCategories: (state): Category[] => {
      return [...state.categories].sort((a, b) => {
        return a.name.localeCompare(b.name);
      });
    }
  },
  
  actions: {
    async fetchCategories(): Promise<void> {
      console.log('CategoryStore: fetchCategories called');
      this.loading = true;
      this.error = null;
      
      try {
        const response = await productApi.getCategories();
        console.log('CategoryStore: API response:', response);
        
        // Handle API response format - apiService returns { data: [...categories...], meta: {...} }
        if (response && response.data && Array.isArray(response.data)) {
          this.categories = response.data;
        } else {
          this.categories = [];
        }
        
        console.log('CategoryStore: Categories set to:', this.categories);
      } catch (error: any) {
        console.error('CategoryStore: Error fetching categories:', error);
        this.error = error.message || 'Błąd podczas pobierania kategorii';
        this.categories = [];
      } finally {
        this.loading = false;
      }
    },

    async refreshCategories(): Promise<void> {
      console.log('CategoryStore: refreshCategories called');
      await this.fetchCategories();
    },

    async forceRefreshCategories(): Promise<void> {
      console.log('CategoryStore: forceRefreshCategories called - bypassing cache');
      this.loading = true;
      this.error = null;
      
      try {
        // Add a timestamp to bypass cache
        const response = await productApi.getCategories({ _t: Date.now() });
        console.log('CategoryStore: Force refresh API response:', response);
        
        // Handle API response format - apiService returns { data: [...categories...], meta: {...} }
        if (response && response.data && Array.isArray(response.data)) {
          this.categories = response.data;
        } else {
          this.categories = [];
        }
        
        console.log('CategoryStore: Categories force refreshed to:', this.categories);
      } catch (error: any) {
        console.error('CategoryStore: Error force refreshing categories:', error);
        this.error = error.message || 'Błąd podczas odświeżania kategorii';
        this.categories = [];
      } finally {
        this.loading = false;
      }
    },
    
    async fetchCategory(id: number): Promise<Category> {
      console.log('CategoryStore: fetchCategory called with:', id);
      this.loading = true;
      this.error = null;
      
      try {
        const response = await productApi.getCategory(id);
        this.currentCategory = response;
        
        // Also update the category in the categories array if it exists
        const index = this.categories.findIndex(cat => cat.id === this.currentCategory!.id);
        
        if (index !== -1) {
          this.categories[index] = this.currentCategory!;
        }
        
        return this.currentCategory!;
      } catch (error: any) {
        console.error('CategoryStore: Error fetching category:', error);
        this.error = error.message || 'Błąd podczas pobierania kategorii';
        this.currentCategory = null;
        throw error;
      } finally {
        this.loading = false;
      }
    },
    
    clearCurrentCategory(): void {
      this.currentCategory = null;
    },
    
    clearError(): void {
      this.error = null;
    }
  }
});
