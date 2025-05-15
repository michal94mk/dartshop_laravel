import { defineStore } from 'pinia';
import api from '../services/api';

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    token: localStorage.getItem('token') || null,
    loading: false,
    error: null,
    isAdmin: false
  }),
  
  getters: {
    isLoggedIn: (state) => {
      return !!state.token && !!state.user;
    },
    
    userName: (state) => {
      return state.user ? state.user.name : '';
    },
    
    userEmail: (state) => {
      return state.user ? state.user.email : '';
    },
    
    userInitial: (state) => {
      return state.user && state.user.name ? state.user.name.charAt(0).toUpperCase() : '';
    }
  },
  
  actions: {
    async login(credentials) {
      this.loading = true;
      this.error = null;
      
      try {
        const response = await api.login(credentials);
        const { user, token } = response.data;
        
        this.user = user;
        this.token = token;
        this.isAdmin = user.roles && user.roles.some(role => role.name === 'admin');
        
        localStorage.setItem('token', token);
        
        return true;
      } catch (error) {
        this.error = error.message || 'Failed to login';
        console.error('Login error:', error);
        return false;
      } finally {
        this.loading = false;
      }
    },
    
    async register(userData) {
      this.loading = true;
      this.error = null;
      
      try {
        const response = await api.register(userData);
        const { user, token } = response.data;
        
        this.user = user;
        this.token = token;
        this.isAdmin = false; // New users are not admins by default
        
        localStorage.setItem('token', token);
        
        return true;
      } catch (error) {
        this.error = error.message || 'Failed to register';
        console.error('Registration error:', error);
        return false;
      } finally {
        this.loading = false;
      }
    },
    
    async logout() {
      this.loading = true;
      
      try {
        if (this.token) {
          await api.logout();
        }
      } catch (error) {
        console.error('Logout error:', error);
      } finally {
        this.user = null;
        this.token = null;
        this.isAdmin = false;
        localStorage.removeItem('token');
        this.loading = false;
      }
    },
    
    async fetchUser() {
      if (!this.token) {
        return false;
      }
      
      this.loading = true;
      this.error = null;
      
      try {
        const response = await api.getUser();
        this.user = response.data;
        this.isAdmin = this.user.roles && this.user.roles.some(role => role.name === 'admin');
        return true;
      } catch (error) {
        this.error = error.message || 'Failed to fetch user data';
        console.error('Error fetching user data:', error);
        // If we get a 401 error, the token is invalid or expired
        if (error.response && error.response.status === 401) {
          this.logout();
        }
        return false;
      } finally {
        this.loading = false;
      }
    },
    
    initAuth() {
      if (this.token) {
        this.fetchUser();
      }
    }
  }
}); 