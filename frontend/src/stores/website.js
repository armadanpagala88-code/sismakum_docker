import { defineStore } from 'pinia'
import { ref } from 'vue'
import api from '../services/api'

export const useWebsiteStore = defineStore('website', () => {
  const website = ref({})
  const websiteList = ref([])
  const loading = ref(false)

  // Public endpoint
  async function fetchPublic() {
    loading.value = true
    try {
      const response = await api.get('/website')
      website.value = response.data || {}
      return response.data || {}
    } catch (error) {
      console.error('Error fetching website:', error)
      website.value = {}
      return {}
    } finally {
      loading.value = false
    }
  }

  // Admin endpoints
  async function fetchAll() {
    loading.value = true
    try {
      const response = await api.get('/admin/website')
      // Handle both paginated and non-paginated responses
      if (response.data.data) {
        websiteList.value = response.data.data
      } else if (Array.isArray(response.data)) {
        websiteList.value = response.data
      } else {
        websiteList.value = []
      }
      return response.data
    } catch (error) {
      console.error('Error fetching website list:', error)
      websiteList.value = []
      throw error
    } finally {
      loading.value = false
    }
  }

  async function createWebsite(data) {
    loading.value = true
    try {
      const isFormData = data instanceof FormData
      // For FormData, don't set Content-Type - let browser set it with boundary
      // The axios interceptor already handles this, but we ensure no transformRequest
      const config = isFormData ? {
        transformRequest: [(data) => {
          // Return FormData as-is, don't let axios transform it
          return data
        }]
      } : {}
      const response = await api.post('/admin/website', data, config)
      return response.data
    } catch (error) {
      console.error('Error in createWebsite:', error)
      console.error('Error response:', error.response)
      throw error
    } finally {
      loading.value = false
    }
  }

  async function updateWebsite(id, data) {
    loading.value = true
    try {
      const isFormData = data instanceof FormData
      // For FormData, don't set Content-Type - let browser set it with boundary
      // The axios interceptor already handles this, but we ensure no transformRequest
      const config = isFormData ? {
        transformRequest: [(data) => {
          // Return FormData as-is, don't let axios transform it
          return data
        }]
      } : {}
      const response = await api.put(`/admin/website/${id}`, data, config)
      return response.data
    } catch (error) {
      console.error('Error in updateWebsite:', error)
      console.error('Error response:', error.response)
      throw error
    } finally {
      loading.value = false
    }
  }

  async function deleteWebsite(id) {
    loading.value = true
    try {
      await api.delete(`/admin/website/${id}`)
    } catch (error) {
      throw error
    } finally {
      loading.value = false
    }
  }

  return {
    website,
    websiteList,
    loading,
    fetchPublic,
    fetchAll,
    createWebsite,
    updateWebsite,
    deleteWebsite
  }
})

