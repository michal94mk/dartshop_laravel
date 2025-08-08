import { ref, reactive, computed } from 'vue'
import type { Ref } from 'vue'

/**
 * Validation rule type
 */
export type ValidationRule<T = any> = {
  validator: (value: T) => boolean
  message: string
}

/**
 * Form field configuration
 */
export interface FormField<T = any> {
  value: T
  rules?: ValidationRule<T>[]
  touched?: boolean
}

/**
 * Composable for form management with validation
 */
export function useForm<T extends Record<string, any>>(
  initialData: T,
  validationRules: Partial<Record<keyof T, ValidationRule[]>> = {}
) {
  // Form data
  const form = reactive({ ...initialData })
  
  // Form state
  const isSubmitting = ref(false)
  const touched = ref<Record<keyof T, boolean>>({} as Record<keyof T, boolean>)
  const errors = ref<Record<keyof T, string[]>>({} as Record<keyof T, string[]>)

  // Mark field as touched
  const markTouched = (field: keyof T) => {
    touched.value[field] = true
  }

  // Mark all fields as touched
  const markAllTouched = () => {
    Object.keys(form).forEach(key => {
      touched.value[key as keyof T] = true
    })
  }

  // Validate single field
  const validateField = (field: keyof T): boolean => {
    const rules = validationRules[field] || []
    const value = (form as any)[field]
    const fieldErrors: string[] = []

    rules.forEach(rule => {
      if (!rule.validator(value)) {
        fieldErrors.push(rule.message)
      }
    })

    errors.value[field] = fieldErrors
    return fieldErrors.length === 0
  }

  // Validate all fields
  const validate = (): boolean => {
    let isValid = true
    
    Object.keys(validationRules).forEach(field => {
      const fieldValid = validateField(field as keyof T)
      if (!fieldValid) {
        isValid = false
      }
    })

    return isValid
  }

  // Clear errors for a field
  const clearFieldError = (field: keyof T) => {
    errors.value[field] = []
  }

  // Clear all errors
  const clearErrors = () => {
    Object.keys(errors.value).forEach(key => {
      errors.value[key as keyof T] = []
    })
  }

  // Set server errors (e.g., from API response)
  const setServerErrors = (serverErrors: Record<string, string[]>) => {
    Object.keys(serverErrors).forEach(key => {
      if (key in form) {
        errors.value[key as keyof T] = serverErrors[key]
      }
    })
  }

  // Reset form to initial state
  const reset = () => {
    Object.keys(initialData).forEach(key => {
      (form as any)[key] = (initialData as any)[key]
    })
    clearErrors()
    touched.value = {} as Record<keyof T, boolean>
    isSubmitting.value = false
  }

  // Submit handler
  const submit = async (
    submitFn: (formData: T) => Promise<any>
  ): Promise<boolean> => {
    markAllTouched()
    
    if (!validate()) {
      return false
    }

    isSubmitting.value = true
    clearErrors()

    try {
      await submitFn({ ...form } as T)
      return true
    } catch (error: any) {
      // Handle validation errors from server
      if (error.response?.status === 422 && error.response?.data?.errors) {
        setServerErrors(error.response.data.errors)
      }
      return false
    } finally {
      isSubmitting.value = false
    }
  }

  // Computed properties
  const isValid = computed(() => {
    return Object.values(errors.value).every((fieldErrors: any) => fieldErrors.length === 0)
  })

  const hasErrors = computed(() => {
    return Object.values(errors.value).some((fieldErrors: any) => fieldErrors.length > 0)
  })

  const isDirty = computed(() => {
    return Object.keys(form).some(key => {
      return (form as any)[key] !== (initialData as any)[key]
    })
  })

  // Get error for specific field
  const getFieldError = (field: keyof T): string | null => {
    const fieldErrors = errors.value[field]
    return fieldErrors && fieldErrors.length > 0 ? fieldErrors[0] : null
  }

  // Check if field has error
  const hasFieldError = (field: keyof T): boolean => {
    return !!(errors.value[field]?.length > 0)
  }

  // Check if field is touched
  const isFieldTouched = (field: keyof T): boolean => {
    return !!touched.value[field]
  }

  return {
    // Form data
    form,
    
    // State
    isSubmitting,
    isValid,
    hasErrors,
    isDirty,
    errors,
    touched,
    
    // Actions
    submit,
    reset,
    validate,
    validateField,
    markTouched,
    markAllTouched,
    clearErrors,
    clearFieldError,
    setServerErrors,
    
    // Helpers
    getFieldError,
    hasFieldError,
    isFieldTouched
  }
}

// Common validation rules
export const validationRules = {
  required: (message = 'To pole jest wymagane'): ValidationRule => ({
    validator: (value: any) => {
      if (typeof value === 'string') {
        return value.trim().length > 0
      }
      return value !== null && value !== undefined
    },
    message
  }),

  email: (message = 'Podaj poprawny adres email'): ValidationRule => ({
    validator: (value: string) => {
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
      return emailRegex.test(value)
    },
    message
  }),

  minLength: (length: number, message?: string): ValidationRule => ({
    validator: (value: string) => value.length >= length,
    message: message || `Minimum ${length} znaków`
  }),

  maxLength: (length: number, message?: string): ValidationRule => ({
    validator: (value: string) => value.length <= length,
    message: message || `Maksimum ${length} znaków`
  }),

  confirmed: (confirmValue: Ref<string>, message = 'Hasła muszą być identyczne'): ValidationRule => ({
    validator: (value: string) => value === confirmValue.value,
    message
  }),

  numeric: (message = 'Podaj liczbę'): ValidationRule => ({
    validator: (value: any) => !isNaN(Number(value)),
    message
  }),

  min: (minValue: number, message?: string): ValidationRule => ({
    validator: (value: any) => Number(value) >= minValue,
    message: message || `Minimalna wartość: ${minValue}`
  }),

  max: (maxValue: number, message?: string): ValidationRule => ({
    validator: (value: any) => Number(value) <= maxValue,
    message: message || `Maksymalna wartość: ${maxValue}`
  })
}
