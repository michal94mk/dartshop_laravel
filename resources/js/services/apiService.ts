import axios, { 
  type AxiosInstance, 
  type AxiosResponse, 
  type AxiosError, 
  type AxiosRequestConfig 
} from 'axios'
import { useToast } from 'vue-toastification'
import type { ApiResponse, PaginatedResponse } from '@/types'

/**
 * Enhanced API Service with TypeScript support
 * Centralized HTTP client with error handling and interceptors
 */
class ApiService {
  private api: AxiosInstance
  private toast = useToast()

  constructor() {
    this.api = axios.create({
      baseURL: 'http://dartshop.toadres.pl/api',
      timeout: 15000,
      headers: {
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json'
      },
      withCredentials: true
    })

    this.setupInterceptors()
  }

  private setupInterceptors(): void {
    // Request interceptor
    this.api.interceptors.request.use(
      (config) => {
        // Add CSRF token
        const token = document.head.querySelector('meta[name="csrf-token"]') as HTMLMetaElement
        if (token) {
          config.headers['X-CSRF-TOKEN'] = token.content
        }

        // Add XSRF token from cookie
        const xsrfToken = this.getXSRFToken()
        if (xsrfToken) {
          config.headers['X-XSRF-TOKEN'] = xsrfToken
        }

        // Add cache busting for GET requests
        if (config.method === 'get') {
          config.params = { ...config.params, _t: Date.now() }
        }

        // Log requests in development
        if (import.meta.env.DEV) {
          console.log(`🔄 ${config.method?.toUpperCase()} ${config.url}`, config)
        }

        return config
      },
      (error) => {
        if (import.meta.env.DEV) {
          console.error('❌ Request Error:', error)
        }
        return Promise.reject(error)
      }
    )

    // Response interceptor
    this.api.interceptors.response.use(
      (response) => {
        if (import.meta.env.DEV) {
          console.log(`✅ ${response.config.method?.toUpperCase()} ${response.config.url}`, response.data)
        }
        return response
      },
      (error: AxiosError) => this.handleResponseError(error)
    )
  }

  private async handleResponseError(error: AxiosError): Promise<never> {
    const { response } = error
    const originalUrl = (error.config as any)?.url as string | undefined
    const originalMethod = ((error.config as any)?.method || '').toString().toLowerCase()

    if (import.meta.env.DEV) {
      console.error('❌ Response Error:', error)
    }

    // Handle specific status codes
    switch (response?.status) {
      case 401: {
        const isUserGet = originalMethod === 'get' && typeof originalUrl === 'string' && (/\/user(\?|$)/.test(originalUrl) || originalUrl === '/user')
        if (isUserGet) {
          // Let caller handle guest state without forcing logout/redirect
          break
        }
        await this.handle401Error()
        break
      }
      case 403:
        this.toast.error('Brak uprawnień do wykonania tej operacji')
        break
      case 419:
        // CSRF token mismatch - try to refresh and retry
        if (!(error.config as any)?.metadata?.retried) {
          return this.retryRequestAfterCSRFRefresh(error) as never
        }
        break
      case 422:
        // Validation errors - handled by calling code
        break
      case 429:
        this.toast.error('Zbyt wiele żądań. Spróbuj ponownie za chwilę.')
        break
      case 500:
      case 502:
      case 503:
      case 504:
        this.toast.error('Błąd serwera. Spróbuj ponownie później.')
        break
      default:
        if (!response) {
          this.toast.error('Błąd połączenia z serwerem')
        }
    }

    return Promise.reject(error)
  }

  private async handle401Error(): Promise<void> {
    // Clear auth state and redirect to login
    const { useAuthStore } = await import('@/stores/authStore')
    const authStore = useAuthStore()
    
    await authStore.forceLogout()
    
    // Redirect to login if not already there
    if (!window.location.pathname.includes('/login')) {
      window.location.href = '/login?expired=1'
    }
  }

  private async retryRequestAfterCSRFRefresh(error: AxiosError): Promise<AxiosResponse> {
    try {
      // Refresh CSRF token
      await axios.get('/sanctum/csrf-cookie')
      
      // Mark request as retried to prevent infinite loops
      const config = { 
        ...error.config, 
        metadata: { retried: true } 
      } as any
      
      // Retry original request
      return await this.api(config)
    } catch (refreshError) {
      console.error('Failed to refresh CSRF token:', refreshError)
      throw error
    }
  }

  private getXSRFToken(): string | null {
    const cookie = document.cookie
      .split('; ')
      .find(row => row.startsWith('XSRF-TOKEN='))
    
    return cookie ? decodeURIComponent(cookie.split('=')[1]) : null
  }

  // Generic request method
  private async request<T>(
    config: AxiosRequestConfig,
    options: {
      suppressErrorToast?: boolean
      transformResponse?: (data: any) => T
    } = {}
  ): Promise<T> {
    try {
      const response = await this.api(config)
      
      if (options.transformResponse) {
        return options.transformResponse(response.data)
      }
      
      // Handle standardized API responses
      if (response.data && typeof response.data === 'object' && 'success' in response.data) {
        const apiResponse = response.data as ApiResponse<T>
        if (apiResponse.success) {
          return apiResponse.data
        } else {
          throw new Error(apiResponse.message || 'API returned unsuccessful response')
        }
      }
      
      return response.data
    } catch (error) {
      if (!options.suppressErrorToast) {
        this.handleError(error as AxiosError)
      }
      throw error
    }
  }

  private handleError(error: AxiosError): void {
    const response = error.response
    let message = 'Wystąpił nieoczekiwany błąd'

    if (response?.data && typeof response.data === 'object') {
      const data = response.data as any
      message = data.message || data.error || message
    } else if (error.message) {
      message = error.message
    }

    // Don't show toast for validation errors (422) - handled by forms
    if (response?.status !== 422) {
      this.toast.error(message)
    }
  }

  // HTTP Methods
  async get<T>(url: string, params?: any, options?: { suppressErrorToast?: boolean }): Promise<T> {
    return this.request<T>({ method: 'GET', url, params }, options)
  }

  async post<T>(url: string, data?: any, options?: { suppressErrorToast?: boolean }): Promise<T> {
    return this.request<T>({ method: 'POST', url, data }, options)
  }

  async put<T>(url: string, data?: any, options?: { suppressErrorToast?: boolean }): Promise<T> {
    return this.request<T>({ method: 'PUT', url, data }, options)
  }

  async patch<T>(url: string, data?: any, options?: { suppressErrorToast?: boolean }): Promise<T> {
    return this.request<T>({ method: 'PATCH', url, data }, options)
  }

  async delete<T>(url: string, options?: { suppressErrorToast?: boolean }): Promise<T> {
    return this.request<T>({ method: 'DELETE', url }, options)
  }

  // Specialized methods for common patterns
  async getPaginated<T>(
    url: string, 
    params?: any
  ): Promise<PaginatedResponse<T>> {
    return this.get<PaginatedResponse<T>>(url, params)
  }

  async uploadFile<T>(
    url: string,
    formData: FormData,
    onProgress?: (progress: number) => void
  ): Promise<T> {
    return this.request<T>({
      method: 'POST',
      url,
      data: formData,
      headers: {
        'Content-Type': 'multipart/form-data'
      },
      onUploadProgress: (progressEvent) => {
        if (onProgress && progressEvent.total) {
          const progress = (progressEvent.loaded / progressEvent.total) * 100
          onProgress(Math.round(progress))
        }
      }
    })
  }

  // Download file
  async downloadFile(url: string, filename?: string): Promise<void> {
    try {
      const response = await this.api({
        method: 'GET',
        url,
        responseType: 'blob'
      })

      // Create blob link to download
      const blob = new Blob([response.data])
      const downloadUrl = window.URL.createObjectURL(blob)
      const link = document.createElement('a')
      
      link.href = downloadUrl
      link.download = filename || 'download'
      document.body.appendChild(link)
      link.click()
      
      // Cleanup
      link.remove()
      window.URL.revokeObjectURL(downloadUrl)
    } catch (error) {
      this.handleError(error as AxiosError)
      throw error
    }
  }

  // Health check
  async healthCheck(): Promise<{ status: string; timestamp: string }> {
    return this.get<{ status: string; timestamp: string }>('/health')
  }
}

// Create singleton instance
export const apiService = new ApiService()
export default apiService
