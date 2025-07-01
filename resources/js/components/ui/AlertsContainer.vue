<template>
  <div class="alerts-container">
    <transition-group name="alert">
      <div
        v-for="alert in alertStore.alerts"
        :key="alert.id"
        :class="['alert', `alert-${alert.type}`]"
      >
        <span class="alert-message">{{ alert.message }}</span>
        <button class="alert-close" @click="alertStore.remove(alert.id)">×</button>
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
    
    return {
      alertStore
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
  width: auto;
  min-width: 320px;
}

.alert {
  padding: 16px 20px;
  margin-bottom: 12px;
  border-radius: 12px;
  color: white;
  display: flex;
  align-items: center;
  justify-content: space-between;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255, 255, 255, 0.1);
  font-weight: 500;
  font-size: 14px;
  line-height: 1.4;
}

.alert-success {
  background: linear-gradient(135deg, #10b981 0%, #059669 100%);
}

.alert-error {
  background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
}

.alert-warning {
  background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
}

.alert-info {
  background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
}

.alert-message {
  flex-grow: 1;
  margin-right: 12px;
  word-wrap: break-word;
}

.alert-close {
  background: none;
  border: none;
  color: white;
  font-size: 18px;
  cursor: pointer;
  padding: 4px;
  opacity: 0.8;
  border-radius: 4px;
  display: flex;
  align-items: center;
  justify-content: center;
  width: 24px;
  height: 24px;
  transition: all 0.2s ease;
}

.alert-close:hover {
  opacity: 1;
  background: rgba(255, 255, 255, 0.2);
  transform: scale(1.1);
}

/* Animacje */
.alert-enter-active,
.alert-leave-active {
  transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
}

.alert-enter-from {
  opacity: 0;
  transform: translateY(-30px) scale(0.9);
}

.alert-leave-to {
  opacity: 0;
  transform: translateY(-30px) scale(0.9);
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
  }
  
  .alert {
    padding: 14px 16px;
    font-size: 13px;
  }
}
</style> 