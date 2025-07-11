import { defineStore } from 'pinia';

export const useAlertStore = defineStore('alert', {
  state: () => ({
    alerts: [],
    nextId: 1,
    successMessage: '',
    errorMessage: '',
    infoMessage: ''
  }),
  
  actions: {
    add(message, type = 'info', timeout = 5000) {
      const id = this.nextId++;
      this.alerts.push({ id, message, type, timeout });
      
      if (timeout > 0) {
        setTimeout(() => {
          this.remove(id);
        }, timeout);
      }
      
      return id;
    },
    
    success(message, timeout = 5000) {
      this.successMessage = message;
      
              // Automatically clear success message after timeout
      if (timeout > 0) {
        setTimeout(() => {
          this.successMessage = '';
        }, timeout);
      }
      
      return this.add(message, 'success', timeout);
    },
    
    error(message, timeout = 5000) {
      this.errorMessage = message;
      
              // Automatically clear error message after timeout
      if (timeout > 0) {
        setTimeout(() => {
          this.errorMessage = '';
        }, timeout);
      }
      
      return this.add(message, 'error', timeout);
    },
    
    warning(message, timeout = 5000) {
      return this.add(message, 'warning', timeout);
    },
    
    info(message, timeout = 5000) {
      this.infoMessage = message;
      
      // Automatically clear info message after timeout
      if (timeout > 0) {
        setTimeout(() => {
          this.infoMessage = '';
        }, timeout);
      }
      
      return this.add(message, 'info', timeout);
    },
    
    remove(id) {
      const index = this.alerts.findIndex(alert => alert.id === id);
      if (index !== -1) {
        this.alerts.splice(index, 1);
      }
    },
    
    clear() {
      this.alerts = [];
      this.successMessage = '';
      this.errorMessage = '';
      this.infoMessage = '';
    },
    
    clearSuccess() {
      this.successMessage = '';
    },
    
    clearError() {
      this.errorMessage = '';
    },
    
    clearInfo() {
      this.infoMessage = '';
    }
  }
}); 