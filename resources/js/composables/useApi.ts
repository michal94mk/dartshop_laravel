import { ref, computed } from 'vue'
import axios, { type AxiosResponse, type AxiosError } from 'axios'
import { useToast } from 'vue-toastification'
import type { ApiResponse } from '@/types'

/**
 * Composable for API calls with loading states and error handling
 */
export function useApi() {
  const toast = useToast()
  
  const isLoading = ref(false)
  const error = ref<string | null>(null)
  const hasError = computed(() => !!error.value)

  // Clear error state
  const clearError = () => {
    error.value = null
  }

  // Generic API call wrapper
  const apiCall = async <T>(
    request: () => Promise<AxiosResponse<ApiResponse<T>>>,
    options: {
      showSuccessToast?: boolean
      successMessage?: string
      showErrorToast?: boolean
      silent?: boolean
    } = {}
  ): Promise<T | null> => {
    const {
      showSuccessToast = false,
      successMessage = 'Operacja wykonana pomyślnie',
      showErrorToast = true,
      silent = false
    } = options

    isLoading.value = true
    clearError()

    try {
      const response = await request()
      
      if (response.data.success) {
        if (showSuccessToast && !silent) {
          toast.success(successMessage)
        }
        return response.data.data
      } else {
        throw new Error(response.data.message || 'API returned unsuccessful response')
      }
    } catch (err) {
      const axiosError = err as AxiosError<ApiResponse<any>>
      let errorMessage = 'Wystąpił nieoczekiwany błąd'

      if (axiosError.response?.data?.message) {
        errorMessage = axiosError.response.data.message
      } else if (axiosError.message) {
        errorMessage = axiosError.message
      }

      error.value = errorMessage

      if (showErrorToast && !silent) {
        toast.error(errorMessage)
      }

      // Log error in development
      if (import.meta.env.DEV) {
        console.error('API Error:', axiosError)
      }

      return null
    } finally {
      isLoading.value = false
    }
  }

  // GET request wrapper
  const get = async <T>(
    url: string,
    params?: Record<string, any>,
    options?: {
      showSuccessToast?: boolean
      successMessage?: string
      showErrorToast?: boolean
      silent?: boolean
    }
  ): Promise<T | null> => {
    return await apiCall(
      () => axios.get<ApiResponse<T>>(url, { params }),
      options
    )
  }

  // POST request wrapper
  const post = async <T>(
    url: string,
    data?: any,
    options?: {
      showSuccessToast?: boolean
      successMessage?: string
      showErrorToast?: boolean
      silent?: boolean
    }
  ): Promise<T | null> => {
    return await apiCall(
      () => axios.post<ApiResponse<T>>(url, data),
      options
    )
  }

  // PUT request wrapper
  const put = async <T>(
    url: string,
    data?: any,
    options?: {
      showSuccessToast?: boolean
      successMessage?: string
      showErrorToast?: boolean
      silent?: boolean
    }
  ): Promise<T | null> => {
    return await apiCall(
      () => axios.put<ApiResponse<T>>(url, data),
      options
    )
  }

  // DELETE request wrapper
  const del = async <T>(
    url: string,
    options?: {
      showSuccessToast?: boolean
      successMessage?: string
      showErrorToast?: boolean
      silent?: boolean
    }
  ): Promise<T | null> => {
    return await apiCall(
      () => axios.delete<ApiResponse<T>>(url),
      options
    )
  }

  return {
    // State
    isLoading,
    error,
    hasError,
    
    // Actions
    clearError,
    apiCall,
    get,
    post,
    put,
    delete: del
  }
}
