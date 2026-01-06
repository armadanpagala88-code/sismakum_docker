import { defineStore } from 'pinia'
import { ref } from 'vue'
import api from '../services/api'

export const useSlideshowStore = defineStore('slideshow', () => {
  const slideshow = ref([])
  const slideshowList = ref([])
  const loading = ref(false)

  // Public endpoint
  async function fetchPublic() {
    loading.value = true
    try {
      const response = await api.get('/slideshow')
      slideshow.value = response.data || []
      return response.data || []
    } catch (error) {
      console.error('Error fetching slideshow:', error)
      slideshow.value = []
      return []
    } finally {
      loading.value = false
    }
  }

  // Admin endpoints
  async function fetchAll() {
    loading.value = true
    try {
      const response = await api.get('/admin/slideshow')
      if (response.data.data) {
        slideshowList.value = response.data.data
      } else if (Array.isArray(response.data)) {
        slideshowList.value = response.data
      } else {
        slideshowList.value = []
      }
      return response.data
    } catch (error) {
      console.error('Error fetching slideshow list:', error)
      slideshowList.value = []
      throw error
    } finally {
      loading.value = false
    }
  }

  async function createSlideshow(data) {
    loading.value = true
    try {
      const isFormData = data instanceof FormData
      const response = await api.post('/admin/slideshow', data, {
        headers: isFormData ? { 'Content-Type': 'multipart/form-data' } : {}
      })
      return response.data
    } catch (error) {
      throw error
    } finally {
      loading.value = false
    }
  }

  async function updateSlideshow(id, data) {
    loading.value = true
    try {
      const isFormData = data instanceof FormData
      const response = await api.put(`/admin/slideshow/${id}`, data, {
        headers: isFormData ? { 'Content-Type': 'multipart/form-data' } : {}
      })
      return response.data
    } catch (error) {
      throw error
    } finally {
      loading.value = false
    }
  }

  async function deleteSlideshow(id) {
    loading.value = true
    try {
      await api.delete(`/admin/slideshow/${id}`)
    } catch (error) {
      throw error
    } finally {
      loading.value = false
    }
  }

  return {
    slideshow,
    slideshowList,
    loading,
    fetchPublic,
    fetchAll,
    createSlideshow,
    updateSlideshow,
    deleteSlideshow
  }
})

