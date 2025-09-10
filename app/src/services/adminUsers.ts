import { api } from '@/services/api'

export interface AdminUser {
  id: string
  name: string
  email: string
  city?: string | null
  phone?: string | null
  region?: string | null
  language?: string | null
  profile_image_path?: string | null
  is_active: boolean
  created_at?: string
}

export const adminUsersService = {
  list: (params: { q?: string; page?: number; per_page?: number }) => api.get('/admin/users', { params }).then(r => r.data),
  show: (id: string) => api.get(`/admin/users/${id}`).then(r => r.data),
  invite: (payload: { name: string; email: string; role: string; region?: string; language?: string }) => api.post('/admin/users', payload).then(r => r.data),
  update: (id: string, payload: { name?: string; city?: string; phone?: string; region?: string; language?: string; profile_image?: File | null }) => {
    const fd = new FormData()
    if (payload.name) fd.append('name', payload.name)
    if (payload.city !== undefined) fd.append('city', payload.city || '')
    if (payload.phone !== undefined) fd.append('phone', payload.phone || '')
    if (payload.region !== undefined) fd.append('region', payload.region || '')
    if (payload.language !== undefined) fd.append('language', payload.language || '')
    if (payload.profile_image) fd.append('profile_image', payload.profile_image)
    return api.put(`/admin/users/${id}`, fd, { headers: { 'Content-Type': 'multipart/form-data' } }).then(r => r.data)
  }
}

