import { api } from '@/services/api'

export interface ContentPdf {
  id: string
  name: string
  description?: string
  language?: string
  is_active: boolean
  processing_status: string
  file_path: string
  tokens_used: number
  processed_at?: string
  created_at?: string
}

export interface ContentPdfPage {
  id: string
  content_pdf_id: string
  page_number: number
  text?: string
  is_active: boolean
  embedding_status: string
  tokens_used: number
}

export interface ContentPdfImage {
  id: string
  content_pdf_id: string
  page_number: number
  image_path: string
  is_active: boolean
  embedding_status: string
  tokens_used: number
}

export const contentLibraryService = {
  list: (params: { q?: string; language?: string; status?: string; page?: number; per_page?: number }) =>
    api.get('/admin/content/pdfs', { params }).then(r => r.data),

  get: (id: string) => api.get(`/admin/content/pdfs/${id}`).then(r => r.data),

  upload: (payload: { name: string; description?: string; language?: string; is_active?: boolean; pdf: File }) => {
    const fd = new FormData()
    fd.append('name', payload.name)
    if (payload.description) fd.append('description', payload.description)
    if (payload.language) fd.append('language', payload.language)
    if (typeof payload.is_active === 'boolean') fd.append('is_active', String(payload.is_active))
    fd.append('pdf', payload.pdf)
    return api.post('/admin/content/pdfs', fd, { headers: { 'Content-Type': 'multipart/form-data' } }).then(r => r.data)
  },

  togglePage: (pdfId: string, pageId: string) => api.patch(`/admin/content/pdfs/${pdfId}/pages/${pageId}/toggle`).then(r => r.data),
  toggleImage: (pdfId: string, imageId: string) => api.patch(`/admin/content/pdfs/${pdfId}/images/${imageId}/toggle`).then(r => r.data),
  downloadUrl: (id: string) => `/api/admin/content/pdfs/${id}/download`,
}

