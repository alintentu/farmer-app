import axios, { type AxiosInstance, type AxiosResponse, type AxiosError } from 'axios'
import { useToast } from 'vue-toastification'
import router from '@/router'

// Create axios instance
export const api: AxiosInstance = axios.create({
  baseURL: import.meta.env.VITE_API_URL || '/api',
  timeout: 10000,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
})

// Request interceptor
api.interceptors.request.use(
  (config) => {
    // Add auth token if available
    const token = localStorage.getItem('token')
    if (token) {
      config.headers.Authorization = `Bearer ${token}`
    }
    
    return config
  },
  (error) => {
    return Promise.reject(error)
  }
)

// Response interceptor
api.interceptors.response.use(
  (response: AxiosResponse) => {
    return response
  },
  (error: AxiosError) => {
    const toast = useToast()
    
    if (error.response) {
      const { status, data } = error.response
      
      switch (status) {
        case 401:
          // Unauthorized - clear token and redirect to login
          localStorage.removeItem('token')
          router.push('/login')
          toast.error('Session expired. Please login again.')
          break
          
        case 403:
          // Forbidden
          toast.error('You do not have permission to perform this action.')
          break
          
        case 404:
          // Not found
          toast.error('The requested resource was not found.')
          break
          
        case 422:
          // Validation errors
          if (data && typeof data === 'object' && 'errors' in data) {
            const errors = data.errors as Record<string, string[]>
            Object.values(errors).flat().forEach(message => {
              toast.error(message)
            })
          } else {
            toast.error('Validation failed.')
          }
          break
          
        case 429:
          // Too many requests
          toast.error('Too many requests. Please try again later.')
          break
          
        case 500:
          // Server error
          toast.error('An internal server error occurred.')
          break
          
        default:
          // Other errors
          const message = data && typeof data === 'object' && 'message' in data 
            ? data.message as string 
            : 'An error occurred.'
          toast.error(message)
      }
    } else if (error.request) {
      // Network error
      toast.error('Network error. Please check your connection.')
    } else {
      // Other error
      toast.error('An unexpected error occurred.')
    }
    
    return Promise.reject(error)
  }
)

// API helper functions
export const apiService = {
  // GET request
  get: <T>(url: string, params?: Record<string, any>): Promise<AxiosResponse<T>> => {
    return api.get<T>(url, { params })
  },

  // POST request
  post: <T>(url: string, data?: any): Promise<AxiosResponse<T>> => {
    return api.post<T>(url, data)
  },

  // PUT request
  put: <T>(url: string, data?: any): Promise<AxiosResponse<T>> => {
    return api.put<T>(url, data)
  },

  // PATCH request
  patch: <T>(url: string, data?: any): Promise<AxiosResponse<T>> => {
    return api.patch<T>(url, data)
  },

  // DELETE request
  delete: <T>(url: string): Promise<AxiosResponse<T>> => {
    return api.delete<T>(url)
  },

  // Upload file
  upload: <T>(url: string, file: File, onProgress?: (progress: number) => void): Promise<AxiosResponse<T>> => {
    const formData = new FormData()
    formData.append('file', file)
    
    return api.post<T>(url, formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
      onUploadProgress: (progressEvent) => {
        if (onProgress && progressEvent.total) {
          const progress = Math.round((progressEvent.loaded * 100) / progressEvent.total)
          onProgress(progress)
        }
      },
    })
  },
}
