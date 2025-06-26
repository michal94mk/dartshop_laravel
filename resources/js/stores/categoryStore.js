import { defineStore } from 'pinia';
import api from '../services/api';

export const useCategoryStore = defineStore('category', {
  state: () => ({
    categories: [],
    currentCategory: null,
    loading: false,
    error: null,
  }),
  
  getters: {
    categoriesWithProducts: (state) => {
      return state.categories.filter(category => 
        category.products_count > 0
      );
    },
    
    getCategoryById: (state) => (id) => {
      return state.categories.find(category => category.id === parseInt(id));
    },
    
    getCategoryBySlug: (state) => (slug) => {
      return state.categories.find(category => category.slug === slug);
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
        const response = await api.getCategories();
        console.log('CategoryStore: API response:', response.data);
        
        // Handle the new API response structure
        if (response.data && response.data.data) {
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
    
    async fetchCategory(identifier) {
      console.log('CategoryStore: fetchCategory called with:', identifier);
      this.loading = true;
      this.error = null;
      
      try {
        const response = await api.getCategory(identifier);
        this.currentCategory = response.data;
        
        // Also update the category in the categories array if it exists
        const index = this.categories.findIndex(cat => 
          cat.id === this.currentCategory.id || cat.slug === this.currentCategory.slug
        );
        
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