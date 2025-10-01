// Core entity types
export interface User {
  id: number
  name: string
  first_name?: string
  last_name?: string
  email: string
  email_verified_at?: string | null
  is_admin?: boolean
  is_google_user?: boolean
  roles?: string[]
  permissions?: string[]
  created_at: string
  updated_at: string
}

export interface Product {
  id: number
  name: string
  slug: string
  description: string
  short_description?: string
  price: string
  promotion_price?: string
  image?: string
  image_url?: string
  category_id: number
  brand_id?: number
  is_active: boolean
  is_featured: boolean
  reviews_count: number
  average_rating: number
  category?: Category
  brand?: Brand
  created_at: string
  updated_at: string
}

export interface Category {
  id: number
  name: string
  slug: string
  description?: string
  image_url?: string
  is_active: boolean
  products_count?: number
  created_at: string
  updated_at: string
}

export interface Brand {
  id: number
  name: string
  slug: string
  description?: string
  logo_url?: string
  is_active: boolean
  created_at: string
  updated_at: string
}

export interface CartItem {
  id: number
  product_id: number
  quantity: number
  product?: Product
  created_at?: string
  updated_at?: string
}

export interface Order {
  id: number
  user_id: number
  status: OrderStatus
  total_amount: number
  shipping_address: ShippingAddress
  items: OrderItem[]
  created_at: string
  updated_at: string
}

export interface OrderItem {
  id: number
  order_id: number
  product_id: number
  quantity: number
  price: number
  product: Product
}

export interface ShippingAddress {
  id: number
  user_id: number
  first_name: string
  last_name: string
  address_line_1: string
  address_line_2?: string
  city: string
  postal_code: string
  country: string
  phone?: string
}

export interface Review {
  id: number
  user_id: number
  product_id: number
  rating: number
  comment?: string
  is_approved: boolean
  user: Pick<User, 'id' | 'name'>
  product: Pick<Product, 'id' | 'name'>
  created_at: string
  updated_at: string
}

export interface Promotion {
  id: number
  name: string
  description?: string
  discount_type: 'percentage' | 'fixed'
  discount_value: number
  start_date: string
  end_date: string
  is_active: boolean
  badge_text?: string
  badge_color?: string
  created_at: string
  updated_at: string
}

// Enums
export enum OrderStatus {
  PENDING = 'pending',
  PROCESSING = 'processing',
  SHIPPED = 'shipped',
  DELIVERED = 'delivered',
  CANCELLED = 'cancelled'
}

export enum PaymentStatus {
  PENDING = 'pending',
  COMPLETED = 'completed',
  FAILED = 'failed',
  REFUNDED = 'refunded'
}

// API Response types
export interface ApiResponse<T> {
  success: boolean
  data: T
  message?: string
  errors?: Record<string, string[]>
}

export interface PaginatedResponse<T> {
  data: T[]
  current_page: number
  last_page: number
  per_page: number
  total: number
  from: number
  to: number
}

// Form types
export interface LoginCredentials {
  email: string
  password: string
}

export interface RegisterData {
  name: string
  first_name: string
  last_name: string
  email: string
  password: string
  password_confirmation: string
  privacy_policy_accepted: boolean
  newsletter_consent?: boolean
}

export interface ContactFormData {
  name: string
  email: string
  subject: string
  message: string
}

// Store state types
export interface AuthState {
  user: User | null
  isLoading: boolean
  isRegularLoading: boolean
  isGoogleLoading: boolean
  hasError: boolean
  errorMessage: string
  permissions: string[]
  authInitialized: boolean
}

export interface CartState {
  items: CartItem[]
  isLoading: boolean
  loadingProductIds: number[]
  hasError: boolean
  errorMessage: string
}

export interface ProductState {
  products: Product[]
  featuredProducts: Product[]
  currentProduct: Product | null
  isLoading: boolean
  hasError: boolean
  errorMessage: string
  filters: ProductFilters
  pagination: PaginationInfo
}

export interface ProductFilters {
  search?: string
  category_id?: number
  brand_id?: number
  min_price?: number
  max_price?: number
  priceRange?: [number | null, number | null]
  sort_by?: string
  sort_direction?: 'asc' | 'desc'
}

export interface PaginationInfo {
  current_page: number
  last_page: number
  per_page: number
  total: number
}
