import { computed } from 'vue'
import router from '@/router'
import { useAuthStore } from '@/stores/authStore'
import type { User, LoginCredentials, RegisterData } from '@/types'

/**
 * Composable for authentication logic
 * Provides a clean interface for auth operations
 */
export function useAuth() {
  const authStore = useAuthStore()

  // Computed getters
  const user = computed<User | null>(() => authStore.user)
  const isLoggedIn = computed<boolean>(() => authStore.isLoggedIn)
  const isAdmin = computed<boolean>(() => authStore.isAdmin)
  const isLoading = computed<boolean>(() => authStore.isLoading)
  const isEmailVerified = computed<boolean>(() => authStore.isEmailVerified)
  const hasError = computed<boolean>(() => authStore.hasError)
  const errorMessage = computed<string>(() => authStore.errorMessage)

  // Actions
  const login = async (credentials: LoginCredentials): Promise<boolean> => {
    const success = await authStore.login(credentials.email, credentials.password)
    
    if (success) {
      // Redirect after successful login
      const redirect = router.currentRoute.value.query.redirect as string
      if (redirect) {
        await router.push(redirect)
      } else if (isAdmin.value) {
        await router.push('/admin/dashboard')
      } else {
        await router.push('/profile')
      }
    }
    
    return success
  }

  const register = async (data: RegisterData): Promise<boolean> => {
    const success = await authStore.register(
      data.name,
      data.first_name,
      data.last_name,
      data.email,
      data.password,
      data.password_confirmation,
      data.privacy_policy_accepted,
      data.newsletter_consent
    )
    
    if (success) {
      await router.push('/profile')
    }
    
    return success
  }

  const logout = async (): Promise<void> => {
    await authStore.logout()
    await router.push('/')
  }

  const loginWithGoogle = async (): Promise<boolean> => {
    return await authStore.loginWithGoogle()
  }

  const refreshUser = async (): Promise<User | null> => {
    return await authStore.refreshUser()
  }

  const resendVerificationEmail = async (): Promise<boolean | string> => {
    return await authStore.resendVerificationEmail()
  }

  // Utility functions
  const hasPermission = (permission: string): boolean => {
    return authStore.hasPermission(permission)
  }

  const hasRole = (role: string): boolean => {
    return authStore.hasRole(role)
  }

  const requireAuth = async (): Promise<boolean> => {
    if (!isLoggedIn.value) {
      await router.push('/login')
      return false
    }
    return true
  }

  const requireAdmin = async (): Promise<boolean> => {
    if (!(await requireAuth())) return false
    
    if (!isAdmin.value) {
      await router.push('/')
      return false
    }
    return true
  }

  return {
    // State
    user,
    isLoggedIn,
    isAdmin,
    isLoading,
    isEmailVerified,
    hasError,
    errorMessage,
    
    // Actions
    login,
    register,
    logout,
    loginWithGoogle,
    refreshUser,
    resendVerificationEmail,
    
    // Utilities
    hasPermission,
    hasRole,
    requireAuth,
    requireAdmin
  }
}
