import { defineStore } from 'pinia'
import { ref } from 'vue'
import api from '../services/api'

export const useBeritaStore = defineStore('berita', () => {
  const berita = ref([])
  const beritaList = ref([])
  const currentBerita = ref(null)
  const loading = ref(false)

  // Public endpoints
  async function fetchPublic() {
    loading.value = true
    try {
      const response = await api.get('/berita')
      berita.value = response.data || []
      return response.data || []
    } catch (error) {
      console.error('Error fetching berita:', error)
      berita.value = []
      return []
    } finally {
      loading.value = false
    }
  }

  async function fetchDetail(slug) {
    loading.value = true
    try {
      const response = await api.get(`/berita/${slug}`)
      currentBerita.value = response.data
      return response.data
    } catch (error) {
      throw error
    } finally {
      loading.value = false
    }
  }

  // Admin endpoints
  async function fetchAll() {
    loading.value = true
    try {
      const response = await api.get('/admin/berita')
      // Handle both paginated and non-paginated responses
      if (response.data.data) {
        beritaList.value = response.data.data
      } else if (Array.isArray(response.data)) {
        beritaList.value = response.data
      } else {
        beritaList.value = []
      }
      return response.data
    } catch (error) {
      console.error('Error fetching berita list:', error)
      beritaList.value = []
      throw error
    } finally {
      loading.value = false
    }
  }

  async function createBerita(data) {
    loading.value = true
    try {
      const formData = new FormData()
      
      // Always send required fields first
      formData.append('judul', data.judul || '')
      formData.append('isi', data.isi || '')
      formData.append('penulis', data.penulis || '')
      formData.append('order', data.order || 0)
      
      // Handle boolean fields
      formData.append('is_published', data.is_published ? '1' : '0')
      formData.append('is_headline', data.is_headline ? '1' : '0')
      
      // Only append gambar if it's a File
      if (data.gambar instanceof File) {
        formData.append('gambar', data.gambar)
      }
      
      const response = await api.post('/admin/berita', formData, {
        headers: {
          'Content-Type': 'multipart/form-data'
        }
      })
      return response.data
    } catch (error) {
      throw error
    } finally {
      loading.value = false
    }
  }

  async function updateBerita(id, data) {
    loading.value = true
    try {
      const formData = new FormData()
      
      // Validate required fields before sending
      if (!data.judul || data.judul.trim() === '') {
        throw new Error('Judul tidak boleh kosong')
      }
      if (!data.isi || data.isi.trim() === '' || data.isi === '<p><br></p>' || data.isi === '<p></p>') {
        throw new Error('Isi berita tidak boleh kosong')
      }
      
      // Always send required fields first
      formData.append('judul', String(data.judul || '').trim())
      formData.append('isi', String(data.isi || ''))
      formData.append('penulis', String(data.penulis || ''))
      formData.append('order', String(data.order || 0))
      
      // Handle boolean fields - ensure they are always sent
      formData.append('is_published', data.is_published ? '1' : '0')
      formData.append('is_headline', data.is_headline ? '1' : '0')
      
      // Only append gambar if it's a File
      if (data.gambar instanceof File) {
        formData.append('gambar', data.gambar)
      }
      
      // Debug: Log FormData contents
      console.log('FormData being sent:', {
        judul: data.judul,
        judulLength: data.judul ? data.judul.length : 0,
        isi: data.isi ? 'present (' + data.isi.length + ' chars)' : 'missing',
        isiPreview: data.isi ? data.isi.substring(0, 100) : '',
        is_published: data.is_published,
        is_headline: data.is_headline,
        penulis: data.penulis,
        order: data.order,
        gambar: data.gambar instanceof File ? 'File' : 'not included'
      })
      
      // Debug: Log all FormData entries
      console.log('FormData entries:')
      for (let pair of formData.entries()) {
        if (pair[1] instanceof File) {
          console.log(pair[0] + ': [File]', pair[1].name, pair[1].size)
        } else {
          const value = pair[1]
          const preview = typeof value === 'string' && value.length > 100 ? value.substring(0, 100) + '...' : value
          console.log(pair[0] + ':', preview, `(${value ? value.length : 0} chars)`)
        }
      }
      
      const response = await api.put(`/admin/berita/${id}`, formData, {
        headers: {
          'Content-Type': 'multipart/form-data'
        }
      })
      return response.data
    } catch (error) {
      console.error('Error updating berita:', error)
      console.error('Error response:', error.response?.data)
      throw error
    } finally {
      loading.value = false
    }
  }

  async function deleteBerita(id) {
    loading.value = true
    try {
      await api.delete(`/admin/berita/${id}`)
    } catch (error) {
      throw error
    } finally {
      loading.value = false
    }
  }

  return {
    berita,
    beritaList,
    currentBerita,
    loading,
    fetchPublic,
    fetchDetail,
    fetchAll,
    createBerita,
    updateBerita,
    deleteBerita
  }
})

