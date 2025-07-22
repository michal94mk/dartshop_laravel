<template>
  <div class="alerts-container">
    <transition-group name="alert">
      <div
        v-for="alert in alertStore.alerts"
        :key="alert.id"
        :class="['alert', `alert-${alert.type}`]"
      >
        <div class="alert-content">
          <div class="alert-icon" v-if="getAlertIcon(alert.type)">
            <i :class="getAlertIcon(alert.type)"></i>
          </div>
          <span class="alert-message">{{ alert.message }}</span>
        </div>
        <button class="alert-close" @click="alertStore.remove(alert.id)">
          <svg class="close-icon" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
          </svg>
        </button>
      </div>
    </transition-group>
  </div>
</template>

<script>
import { useAlertStore } from '../../stores/alertStore';

export default {
  name: 'AlertsContainer',
  setup() {
    const alertStore = useAlertStore();
    
    const getAlertIcon = (type) => {
      switch (type) {
        case 'success':
          return 'fas fa-check-circle';
        case 'error':
          return 'fas fa-exclamation-circle';
        case 'warning':
          return 'fas fa-exclamation-triangle';
        case 'info':
          return 'fas fa-info-circle';
        default:
          return null;
      }
    };
    
    return {
      alertStore,
      getAlertIcon
    };
  }
}
</script>

<style scoped>
.alerts-container {
  position: fixed;
  top: 20px;
  left: 50%;
  transform: translateX(-50%);
  z-index: 9999;
  max-width: 400px;
  width: 400px;
  min-width: 320px;
  pointer-events: none;
  display: flex;
  flex-direction: column;
  align-items: center;
}

.alert {
  pointer-events: auto;
  padding: 16px;
  margin-bottom: 12px;
  border-radius: 8px;
  background: white;
  color: #374151;
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  box-shadow: 0 8px 16px rgba(0, 0, 0, 0.08),
              0 4px 8px rgba(0, 0, 0, 0.06);
  border-left: 4px solid;
  font-weight: 500;
  font-size: 14px;
  line-height: 1.5;
  width: 100%;
}

.alert-content {
  display: flex;
  align-items: center;
  gap: 12px;
  flex-grow: 1;
  margin-right: 12px;
}

.alert-icon {
  flex-shrink: 0;
  font-size: 18px;
}

.alert-success {
  border-left-color: #10b981;
}

.alert-success .alert-icon {
  color: #10b981;
}

.alert-error {
  border-left-color: #ef4444;
}

.alert-error .alert-icon {
  color: #ef4444;
}

.alert-warning {
  border-left-color: #f59e0b;
}

.alert-warning .alert-icon {
  color: #f59e0b;
}

.alert-info {
  border-left-color: #3b82f6;
}

.alert-info .alert-icon {
  color: #3b82f6;
}

.alert-message {
  word-wrap: break-word;
  color: #374151;
}

.alert-close {
  background: none;
  border: none;
  color: #9ca3af;
  cursor: pointer;
  padding: 4px;
  border-radius: 4px;
  display: flex;
  align-items: center;
  justify-content: center;
  width: 24px;
  height: 24px;
  transition: all 0.2s ease;
  flex-shrink: 0;
  margin: -4px -4px 0 0;
}

.close-icon {
  width: 16px;
  height: 16px;
}

.alert-close:hover {
  background-color: #f3f4f6;
  color: #4b5563;
}

/* Animacje */
.alert-enter-active,
.alert-leave-active {
  transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
}

.alert-enter-from {
  opacity: 0;
  transform: translateY(-30px);
}

.alert-leave-to {
  opacity: 0;
  transform: translateY(-30px);
}

/* Responsive design */
@media (max-width: 640px) {
  .alerts-container {
    top: 10px;
    left: 10px;
    right: 10px;
    transform: none;
    max-width: none;
    min-width: auto;
    width: auto;
  }
  
  .alert {
    padding: 12px;
    font-size: 13px;
    width: 100%;
  }
  
  .alert-content {
    gap: 8px;
  }
  
  .alert-icon {
    font-size: 16px;
  }
}
</style> 