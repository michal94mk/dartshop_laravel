import apiService from './apiService'
import type { CartItem } from '@/types'

/**
 * Cart-related API calls
 */
export const cartApi = {
  // Get current cart
  async getCart(): Promise<CartItem[]> {
    return apiService.get<CartItem[]>('/cart')
  },

  // Add item to cart
  async addToCart(productId: number, quantity: number = 1): Promise<CartItem> {
    return apiService.post<CartItem>('/cart/add', { 
      product_id: productId, 
      quantity 
    })
  },

  // Update cart item quantity
  async updateCartItem(productId: number, quantity: number): Promise<CartItem> {
    return apiService.put<CartItem>('/cart/update', { 
      product_id: productId, 
      quantity 
    })
  },

  // Remove item from cart
  async removeFromCart(productId: number): Promise<void> {
    return apiService.delete<void>(`/cart/remove/${productId}`)
  },

  // Clear entire cart
  async clearCart(): Promise<void> {
    return apiService.delete<void>('/cart/clear')
  },

  // Sync cart (merge guest cart with user cart after login)
  async syncCart(guestCartItems: Array<{product_id: number, quantity: number}>): Promise<CartItem[]> {
    return apiService.post<CartItem[]>('/cart/sync', { items: guestCartItems })
  },

  // Get cart total
  async getCartTotal(): Promise<{ total: number; items_count: number }> {
    return apiService.get<{ total: number; items_count: number }>('/cart/total')
  }
}

export default cartApi
