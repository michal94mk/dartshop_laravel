import apiService from './apiService'
import type { Product, Category, Brand, Review, PaginatedResponse, ProductFilters } from '@/types'

/**
 * Product-related API calls
 */
export const productApi = {
  // Products
  async getProducts(filters: ProductFilters = {}): Promise<PaginatedResponse<Product>> {
    return apiService.getPaginated<Product>('/products', filters)
  },

  async getProduct(id: number): Promise<Product> {
    return apiService.get<Product>(`/products/${id}`)
  },

  async getFeaturedProducts(): Promise<Product[]> {
    return apiService.get<Product[]>('/products/featured')
  },

  async searchProducts(query: string, filters: ProductFilters = {}): Promise<PaginatedResponse<Product>> {
    return apiService.getPaginated<Product>('/products/search', { query, ...filters })
  },

  // Categories
  async getCategories(): Promise<Category[]> {
    return apiService.get<Category[]>('/categories')
  },

  async getCategory(id: number): Promise<Category> {
    return apiService.get<Category>(`/categories/${id}`)
  },

  async getCategoryProducts(id: number, filters: ProductFilters = {}): Promise<PaginatedResponse<Product>> {
    return apiService.getPaginated<Product>(`/categories/${id}/products`, filters)
  },

  // Brands
  async getBrands(): Promise<Brand[]> {
    return apiService.get<Brand[]>('/brands')
  },

  async getBrand(id: number): Promise<Brand> {
    return apiService.get<Brand>(`/brands/${id}`)
  },

  async getBrandProducts(id: number, filters: ProductFilters = {}): Promise<PaginatedResponse<Product>> {
    return apiService.getPaginated<Product>(`/brands/${id}/products`, filters)
  },

  // Reviews
  async getProductReviews(productId: number, page: number = 1): Promise<PaginatedResponse<Review>> {
    return apiService.getPaginated<Review>(`/products/${productId}/reviews`, { page })
  },

  async createReview(productId: number, data: { rating: number; comment?: string }): Promise<Review> {
    return apiService.post<Review>(`/products/${productId}/reviews`, data)
  },

  async updateReview(reviewId: number, data: { rating: number; comment?: string }): Promise<Review> {
    return apiService.put<Review>(`/reviews/${reviewId}`, data)
  },

  async deleteReview(reviewId: number): Promise<void> {
    return apiService.delete<void>(`/reviews/${reviewId}`)
  }
}

export default productApi
