import { defineStore } from 'pinia';

// Type definitions
type AlertType = 'success' | 'error' | 'warning' | 'info';

interface Alert {
  id: number;
  message: string;
  type: AlertType;
  timeout: number;
}

interface AlertState {
  alerts: Alert[];
  nextId: number;
  successMessage: string;
  errorMessage: string;
  infoMessage: string;
}

export const useAlertStore = defineStore('alert', {
  state: (): AlertState => ({
    alerts: [],
    nextId: 1,
    successMessage: '',
    errorMessage: '',
    infoMessage: ''
  }),
  
  actions: {
    add(message: string, type: AlertType = 'info', timeout: number = 5000): number {
      const id = this.nextId++;
      this.alerts.push({ id, message, type, timeout });
      
      if (timeout > 0) {
        setTimeout(() => {
          this.remove(id);
        }, timeout);
      }
      
      return id;
    },
    
    success(message: string, timeout: number = 5000): number {
      this.successMessage = message;
      
      // Automatically clear success message after timeout
      if (timeout > 0) {
        setTimeout(() => {
          this.successMessage = '';
        }, timeout);
      }
      
      return this.add(message, 'success', timeout);
    },
    
    error(message: string, timeout: number = 5000): number {
      this.errorMessage = message;
      
      // Automatically clear error message after timeout
      if (timeout > 0) {
        setTimeout(() => {
          this.errorMessage = '';
        }, timeout);
      }
      
      return this.add(message, 'error', timeout);
    },
    
    warning(message: string, timeout: number = 5000): number {
      return this.add(message, 'warning', timeout);
    },
    
    info(message: string, timeout: number = 5000): number {
      this.infoMessage = message;
      
      // Automatically clear info message after timeout
      if (timeout > 0) {
        setTimeout(() => {
          this.infoMessage = '';
        }, timeout);
      }
      
      return this.add(message, 'info', timeout);
    },
    
    remove(id: number): void {
      const index = this.alerts.findIndex(alert => alert.id === id);
      if (index !== -1) {
        this.alerts.splice(index, 1);
      }
    },
    
    clear(): void {
      this.alerts = [];
      this.successMessage = '';
      this.errorMessage = '';
      this.infoMessage = '';
    },
    
    clearSuccess(): void {
      this.successMessage = '';
    },
    
    clearError(): void {
      this.errorMessage = '';
    },
    
    clearInfo(): void {
      this.infoMessage = '';
    }
  }
});
