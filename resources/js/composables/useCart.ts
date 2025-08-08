import { computed } from 'vue'
import { useCartStore } from '@/stores/cartStore'
import { useToast } from 'vue-toastification'
import type { Product, CartItem } from '@/types'

/**
 * Composable for cart operations
 * Provides a clean interface for cart management
 */
export function useCart() {
  const cartStore = useCartStore()
  const toast = useToast()

  // Computed getters
  const items = computed<CartItem[]>(() => cartStore.items)
  const itemsCount = computed<number>(() => cartStore.totalItems)
  const totalAmount = computed<number>(() => cartStore.total)
  const isLoading = computed<boolean>(() => cartStore.isLoading)
  const hasError = computed<boolean>(() => cartStore.hasError)
  const errorMessage = computed<string>(() => cartStore.errorMessage)
  const isEmpty = computed<boolean>(() => cartStore.isEmpty)

  // Actions
  const addToCart = async (
    product: Product, 
    quantity: number = 1,
    showToast: boolean = true
  ): Promise<boolean> => {
    const success = await cartStore.addToCart(product.id, quantity)
    
    if (success && showToast) {
      toast.success(`🛒 Dodano ${product.name} do koszyka!`, {
        toastClassName: 'cart-success-toast',
        bodyClassName: 'cart-success-body'
      })
    } else if (!success && showToast) {
      toast.error('Nie udało się dodać produktu do koszyka', {
        toastClassName: 'cart-error-toast',
        bodyClassName: 'cart-error-body'
      })
    }
    
    return success
  }

  const removeFromCart = async (
    productId: number,
    showToast: boolean = true
  ): Promise<boolean> => {
    const item = items.value.find(item => item.product_id === productId)
    const success = await cartStore.removeFromCart(productId)
    
    if (success && showToast && item) {
      toast.info(`Usunięto ${item.product.name} z koszyka`)
    }
    
    return success
  }

  const updateQuantity = async (
    productId: number,
    quantity: number,
    showToast: boolean = false
  ): Promise<boolean> => {
    if (quantity <= 0) {
      return await removeFromCart(productId, showToast)
    }
    
    const success = await cartStore.updateCartItem(productId, quantity)
    
    if (success && showToast) {
      toast.success('Zaktualizowano ilość w koszyku')
    }
    
    return success
  }

  const clearCart = async (showToast: boolean = true): Promise<boolean> => {
    const success = await cartStore.clearCart()
    
    if (success && showToast) {
      toast.info('Koszyk został wyczyszczony')
    }
    
    return success
  }

  const syncCart = async (): Promise<void> => {
    await cartStore.syncCartAfterLogin()
  }

  // Utility functions
  const getItemQuantity = (productId: number): number => {
    const item = items.value.find(item => item.product_id === productId)
    return item?.quantity || 0
  }

  const isInCart = (productId: number): boolean => {
    return items.value.some(item => item.product_id === productId)
  }

  const getItemByProductId = (productId: number): CartItem | undefined => {
    return items.value.find(item => item.product_id === productId)
  }

  const canAddMore = (productId: number, requestedQuantity: number = 1): boolean => {
    const currentQuantity = getItemQuantity(productId)
    const item = getItemByProductId(productId)
    
    if (!item) {
      // Product not in cart, check if we can add requested quantity
      return requestedQuantity > 0
    }
    
    // Check stock if available
    if (item.product.stock_quantity !== undefined) {
      return (currentQuantity + requestedQuantity) <= item.product.stock_quantity
    }
    
    return true
  }

  const getTotalForProduct = (productId: number): number => {
    const item = getItemByProductId(productId)
    return item ? item.quantity * item.price : 0
  }

  // Format price helper
  const formatPrice = (price: number): string => {
    return new Intl.NumberFormat('pl-PL', {
      minimumFractionDigits: 2,
      maximumFractionDigits: 2
    }).format(price)
  }

  return {
    // State
    items,
    itemsCount,
    totalAmount,
    isLoading,
    hasError,
    errorMessage,
    isEmpty,
    
    // Actions
    addToCart,
    removeFromCart,
    updateQuantity,
    clearCart,
    syncCart,
    
    // Utilities
    getItemQuantity,
    isInCart,
    getItemByProductId,
    canAddMore,
    getTotalForProduct,
    formatPrice
  }
}
