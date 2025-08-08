import apiService from './apiService'
import type { User, LoginCredentials, RegisterData } from '@/types'

/**
 * Authentication-related API calls
 */
export const authApi = {
  // Get current user
  async getUser(): Promise<User> {
    return apiService.get<User>('/user', undefined, { suppressErrorToast: true })
  },

  // Login
  async login(credentials: LoginCredentials): Promise<{
    user: User
    token: string
    token_type: string
  }> {
    return apiService.post<{
      user: User
      token: string
      token_type: string
    }>('/login', credentials)
  },

  // Register
  async register(data: RegisterData): Promise<{
    user: User
    token: string
    token_type: string
  }> {
    return apiService.post<{
      user: User
      token: string
      token_type: string
    }>('/register', data)
  },

  // Logout
  async logout(): Promise<{ message: string }> {
    return apiService.post<{ message: string }>('/logout')
  },

  // Refresh CSRF cookie
  async refreshCSRF(): Promise<void> {
    return apiService.get<void>('/sanctum/csrf-cookie', undefined, { suppressErrorToast: true })
  },

  // Google OAuth
  async getGoogleRedirectUrl(): Promise<{ url: string }> {
    return apiService.get<{ url: string }>('/auth/google/redirect')
  },

  async handleGoogleCallback(code: string, state: string): Promise<{
    user: User
    token: string
    token_type: string
  }> {
    return apiService.post<{
      user: User
      token: string
      token_type: string
    }>('/auth/google/callback', { code, state })
  },

  // Password reset
  async forgotPassword(email: string): Promise<{ message: string }> {
    return apiService.post<{ message: string }>('/password/forgot', { email })
  },

  async resetPassword(data: {
    token: string
    email: string
    password: string
    password_confirmation: string
  }): Promise<{ message: string }> {
    return apiService.post<{ message: string }>('/password/reset', data)
  },

  // Email verification
  async resendVerificationEmail(): Promise<{ message: string }> {
    return apiService.post<{ message: string }>('/email/verification-notification')
  },

  async verifyEmail(id: string, hash: string, signature: string, expires: string): Promise<{ message: string }> {
    return apiService.post<{ message: string }>(`/email/verify/${id}/${hash}`, {
      signature,
      expires
    })
  },

  // Profile management
  async updateProfile(data: Partial<User>): Promise<User> {
    return apiService.put<User>('/user/profile', data)
  },

  async updatePassword(data: {
    current_password: string
    password: string
    password_confirmation: string
  }): Promise<{ message: string }> {
    return apiService.put<{ message: string }>('/user/password', data)
  },

  async deleteAccount(): Promise<{ message: string }> {
    return apiService.delete<{ message: string }>('/user/account')
  }
}

export default authApi
