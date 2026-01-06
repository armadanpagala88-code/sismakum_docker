import { defineStore } from 'pinia'
import { ref } from 'vue'
import api from '../services/api'

export const usePengusulanStore = defineStore('pengusulan', () => {
  const pengusulan = ref([])
  const currentPengusulan = ref(null)
  const loading = ref(false)

  async function fetchPengusulan(params = {}) {
    loading.value = true
    try {
      const response = await api.get('/pengusulan-perbub', { params })
      pengusulan.value = response.data.data || response.data
      return response.data
    } catch (error) {
      throw error
    } finally {
      loading.value = false
    }
  }

  async function fetchDetail(id) {
    loading.value = true
    try {
      const response = await api.get(`/pengusulan-perbub/${id}`)
      currentPengusulan.value = response.data
      return response.data
    } catch (error) {
      throw error
    } finally {
      loading.value = false
    }
  }

  async function createPengusulan(data) {
    loading.value = true
    try {
      const formData = new FormData()
      Object.keys(data).forEach(key => {
        if (key === 'dokumen' && Array.isArray(data[key])) {
          data[key].forEach(file => {
            formData.append('dokumen[]', file)
          })
        } else {
          formData.append(key, data[key])
        }
      })
      
      const response = await api.post('/pengusulan-perbub', formData, {
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

  async function updatePengusulan(id, data) {
    loading.value = true
    try {
      const response = await api.put(`/pengusulan-perbub/${id}`, data)
      return response.data
    } catch (error) {
      throw error
    } finally {
      loading.value = false
    }
  }

  async function ajukanPengusulan(id) {
    loading.value = true
    try {
      const response = await api.post(`/pengusulan-perbub/${id}/ajukan`)
      return response.data
    } catch (error) {
      throw error
    } finally {
      loading.value = false
    }
  }

  async function reviewPengusulan(id, status, catatan, file = null) {
    loading.value = true
    try {
      const formData = new FormData()
      formData.append('status', status)
      formData.append('catatan', catatan)
      if (file) {
        formData.append('file_review', file)
      }
      
      const response = await api.post(`/pengusulan-perbub/${id}/review`, formData, {
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

  async function deletePengusulan(id) {
    loading.value = true
    try {
      await api.delete(`/pengusulan-perbub/${id}`)
    } catch (error) {
      throw error
    } finally {
      loading.value = false
    }
  }

  async function updateAfterRevisi(id, data) {
    loading.value = true
    try {
      const formData = new FormData()
      Object.keys(data).forEach(key => {
        if (key === 'dokumen' && Array.isArray(data[key])) {
          data[key].forEach(file => {
            formData.append('dokumen[]', file)
          })
        } else {
          formData.append(key, data[key])
        }
      })
      
      const response = await api.put(`/pengusulan-perbub/${id}/update-revisi`, formData, {
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

  async function updateStatus(id, status, catatan = null) {
    loading.value = true
    try {
      const response = await api.put(`/pengusulan-perbub/${id}/update-status`, {
        status,
        catatan
      })
      if (currentPengusulan.value && currentPengusulan.value.id === id) {
        await fetchDetail(id)
      }
      return response.data
    } catch (error) {
      throw error
    } finally {
      loading.value = false
    }
  }

  return {
    pengusulan,
    currentPengusulan,
    loading,
    fetchPengusulan,
    fetchDetail,
    createPengusulan,
    updatePengusulan,
    ajukanPengusulan,
    reviewPengusulan,
    deletePengusulan,
    updateAfterRevisi,
    updateStatus
  }
})
