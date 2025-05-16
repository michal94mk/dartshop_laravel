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
  right: 20px;
  z-index: 9999;
  max-width: 350px;
  width: 100%;
}

.alert {
  padding: 12px 16px;
  margin-bottom: 10px;
  border-radius: 4px;
  color: white;
  display: flex;
  align-items: center;
  justify-content: space-between;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
}

.alert-success {
  background-color: #4caf50;
}

.alert-error {
  background-color: #f44336;
}

.alert-warning {
  background-color: #ff9800;
}

.alert-info {
  background-color: #2196f3;
}

.alert-message {
  flex-grow: 1;
  margin-right: 10px;
}

.alert-close {
  background: none;
  border: none;
  color: white;
  font-size: 20px;
  cursor: pointer;
  padding: 0;
  opacity: 0.7;
}

.alert-close:hover {
  opacity: 1;
}

/* Animacje */
.alert-enter-active,
.alert-leave-active {
  transition: all 0.3s ease;
}

.alert-enter-from {
  opacity: 0;
  transform: translateX(50px);
}

.alert-leave-to {
  opacity: 0;
  transform: translateX(50px);
}
</style> 