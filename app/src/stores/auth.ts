import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import type { User, LoginCredentials, RegisterData, AuthState } from '@/types'
import { api } from '@/services/api'
import { useToast } from 'vue-toastification'

export const useAuthStore = defineStore('auth', () => {
  const router = useRouter()
  const toast = useToast()

  // State
  const user = ref<User | null>(null)
  const token = ref<string | null>(localStorage.getItem('token'))
  const isLoading = ref(false)

  // Getters
  const isAuthenticated = computed(() => !!token.value && !!user.value)
  const isAdmin = computed(() => user.value?.roles.includes('admin') || user.value?.roles.includes('owner'))
  const isOwner = computed(() => user.value?.roles.includes('owner'))

  // Actions
  const login = async (credentials: LoginCredentials): Promise<boolean> => {
    try {
      isLoading.value = true
      
      const response = await api.post<{ token: string; user: User }>('/auth/login', credentials)
      
      token.value = response.data.token
      user.value = response.data.user
      
      localStorage.setItem('token', response.data.token)
      
      // Set auth header for future requests
      api.defaults.headers.common['Authorization'] = `Bearer ${response.data.token}`
      
      toast.success('Welcome back!')
      return true
    } catch (error: any) {
      const message = error.response?.data?.message || 'Login failed'
      toast.error(message)
      return false
    } finally {
      isLoading.value = false
    }
  }

  const register = async (data: RegisterData): Promise<boolean> => {
    try {
      isLoading.value = true
      
      const response = await api.post<{ token: string; user: User }>('/auth/register', data)
      
      token.value = response.data.token
      user.value = response.data.user
      
      localStorage.setItem('token', response.data.token)
      
      // Set auth header for future requests
      api.defaults.headers.common['Authorization'] = `Bearer ${response.data.token}`
      
      toast.success('Account created successfully!')
      return true
    } catch (error: any) {
      const message = error.response?.data?.message || 'Registration failed'
      toast.error(message)
      return false
    } finally {
      isLoading.value = false
    }
  }

  const logout = async (): Promise<void> => {
    try {
      if (token.value) {
        await api.post('/auth/logout')
      }
    } catch (error) {
      // Ignore logout errors
    } finally {
      // Clear state
      user.value = null
      token.value = null
      localStorage.removeItem('token')
      delete api.defaults.headers.common['Authorization']
      
      // Redirect to login
      router.push('/login')
      toast.info('You have been logged out')
    }
  }

  const fetchUser = async (): Promise<boolean> => {
    if (!token.value) {
      return false
    }

    try {
      const response = await api.get<{ user: User }>('/me')
      user.value = response.data.user
      return true
    } catch (error) {
      // Token might be invalid, clear it
      logout()
      return false
    }
  }

  const updateProfile = async (data: Partial<User>): Promise<boolean> => {
    try {
      isLoading.value = true
      
      const response = await api.put<{ user: User }>('/me', data)
      user.value = response.data.user
      
      toast.success('Profile updated successfully!')
      return true
    } catch (error: any) {
      const message = error.response?.data?.message || 'Profile update failed'
      toast.error(message)
      return false
    } finally {
      isLoading.value = false
    }
  }

  const updatePassword = async (data: { current_password: string; password: string; password_confirmation: string }): Promise<boolean> => {
    try {
      isLoading.value = true
      
      await api.put('/me/password', data)
      
      toast.success('Password updated successfully!')
      return true
    } catch (error: any) {
      const message = error.response?.data?.message || 'Password update failed'
      toast.error(message)
      return false
    } finally {
      isLoading.value = false
    }
  }

  // Initialize auth state
  const initialize = async (): Promise<void> => {
    if (token.value) {
      api.defaults.headers.common['Authorization'] = `Bearer ${token.value}`
      await fetchUser()
    }
  }

  return {
    // State
    user,
    token,
    isLoading,
    
    // Getters
    isAuthenticated,
    isAdmin,
    isOwner,
    
    // Actions
    login,
    register,
    logout,
    fetchUser,
    updateProfile,
    updatePassword,
    initialize
  }
})
