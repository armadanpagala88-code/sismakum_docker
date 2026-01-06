import { defineStore } from 'pinia'
import { ref } from 'vue'
import api from '../services/api'

export const useAdminStore = defineStore('admin', () => {
  const dinas = ref([])
  const users = ref([])
  const loading = ref(false)

  // ========== DINAS ==========
  async function fetchDinas(params = {}) {
    loading.value = true
    try {
      const response = await api.get('/admin/dinas', { params })
      dinas.value = response.data.data || response.data
      return response.data
    } catch (error) {
      throw error
    } finally {
      loading.value = false
    }
  }

  async function createDinas(data) {
    loading.value = true
    try {
      const response = await api.post('/admin/dinas', data)
      return response.data
    } catch (error) {
      throw error
    } finally {
      loading.value = false
    }
  }

  async function updateDinas(id, data) {
    loading.value = true
    try {
      const response = await api.put(`/admin/dinas/${id}`, data)
      return response.data
    } catch (error) {
      throw error
    } finally {
      loading.value = false
    }
  }

  async function deleteDinas(id) {
    loading.value = true
    try {
      await api.delete(`/admin/dinas/${id}`)
    } catch (error) {
      throw error
    } finally {
      loading.value = false
    }
  }

  // ========== USERS ==========
  async function fetchUsers(params = {}) {
    loading.value = true
    try {
      const response = await api.get('/admin/users', { params })
      users.value = response.data.data || response.data
      return response.data
    } catch (error) {
      throw error
    } finally {
      loading.value = false
    }
  }

  async function createUser(data) {
    loading.value = true
    try {
      const response = await api.post('/admin/users', data)
      return response.data
    } catch (error) {
      throw error
    } finally {
      loading.value = false
    }
  }

  async function updateUser(id, data) {
    loading.value = true
    try {
      const response = await api.put(`/admin/users/${id}`, data)
      return response.data
    } catch (error) {
      throw error
    } finally {
      loading.value = false
    }
  }

  async function toggleUserStatus(id) {
    loading.value = true
    try {
      const response = await api.post(`/admin/users/${id}/toggle-status`)
      return response.data
    } catch (error) {
      throw error
    } finally {
      loading.value = false
    }
  }

  async function deleteUser(id) {
    loading.value = true
    try {
      await api.delete(`/admin/users/${id}`)
    } catch (error) {
      throw error
    } finally {
      loading.value = false
    }
  }

  return {
    dinas,
    users,
    loading,
    fetchDinas,
    createDinas,
    updateDinas,
    deleteDinas,
    fetchUsers,
    createUser,
    updateUser,
    toggleUserStatus,
    deleteUser
  }
})

