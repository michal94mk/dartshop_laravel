<template>
  <div class="admin-loading-container">
    <div class="spinner"></div>
    <p v-if="message" class="loading-text">{{ message }}</p>
  </div>
</template>

<script>
export default {
  name: 'AdminLoadingSpinner',
  props: {
    message: {
      type: String,
      default: 'Ładowanie...'
    }
  }
}
</script>

<style scoped>
.admin-loading-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 2rem;
  min-height: 200px;
}

.spinner {
  width: 32px;
  height: 32px;
  border: 3px solid #f3f4f6;
  border-top: 3px solid #4f46e5;
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
}

.loading-text {
  margin-top: 1rem;
  font-size: 0.875rem;
  color: #6b7280;
  text-align: center;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}
</style> 