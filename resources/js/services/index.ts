// Export the main API service
export { default as apiService } from './apiService'

// Export specialized API services
export { default as authApi } from './authApi'
export { default as productApi } from './productApi'
export { default as cartApi } from './cartApi'

// Re-export existing services that can be gradually migrated
export { default as api } from './api'
// export { default as newsletterService } from './newsletterService'
