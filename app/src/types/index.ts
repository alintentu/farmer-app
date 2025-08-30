// User types
export interface User {
  id: string
  name: string
  email: string
  initials: string
  roles: string[]
  last_login_at: string | null
  tenant_id: string
  is_active: boolean
}

// Tenant types
export interface Tenant {
  id: string
  name: string
  plan: string
  is_active: boolean
  trial_ends_at: string | null
  subscription_ends_at: string | null
  is_on_trial: boolean
  has_active_subscription: boolean
}

// Module types
export interface Module {
  id: number
  key: string
  label: string
  description: string
  icon: string
  limits: Record<string, number>
}

// Dashboard types
export interface DashboardData {
  user: User
  tenant: Tenant
  modules: Module[]
  quick_stats: QuickStats
  recent_activity: Activity[]
}

export interface QuickStats {
  total_users: number
  active_users: number
  total_modules: number
  subscription_status: 'active' | 'inactive'
}

export interface Activity {
  id: number
  description: string
  subject_type: string
  subject_id: string
  created_at: string
}

// API Response types
export interface ApiResponse<T> {
  data: T
  message?: string
  errors?: Record<string, string[]>
}

export interface PaginatedResponse<T> {
  data: T[]
  meta: {
    current_page: number
    last_page: number
    per_page: number
    total: number
  }
  links: {
    first: string
    last: string
    prev: string | null
    next: string | null
  }
}

// Auth types
export interface LoginCredentials {
  email: string
  password: string
}

export interface RegisterData {
  name: string
  email: string
  password: string
  password_confirmation: string
  tenant_name: string
}

export interface AuthState {
  user: User | null
  token: string | null
  isAuthenticated: boolean
}

// Task types
export interface Task {
  id: string
  title: string
  description: string
  status: 'todo' | 'in_progress' | 'done'
  priority: 'low' | 'medium' | 'high'
  assigned_to: string | null
  project_id: string
  due_date: string | null
  created_at: string
  updated_at: string
}

export interface Project {
  id: string
  name: string
  description: string
  status: 'active' | 'completed' | 'archived'
  tasks_count: number
  created_at: string
  updated_at: string
}

// Form types
export interface FormErrors {
  [key: string]: string[]
}

// Navigation types
export interface NavigationItem {
  name: string
  href: string
  icon: string
  current: boolean
  badge?: number
  children?: NavigationItem[]
}
