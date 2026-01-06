import { defineStore } from 'pinia'
import { ref } from 'vue'
import api from '../services/api'

export const useKategoriDokumenStore = defineStore('kategoriDokumen', () => {
  const kategoriList = ref([])
  const loading = ref(false)

  async function fetchAll() {
    loading.value = true
    try {
      const response = await api.get('/kategori-dokumen')
      kategoriList.value = response.data || []
      return response.data || []
    } catch (error) {
      console.error('Error fetching kategori dokumen:', error)
      kategoriList.value = []
      return []
    } finally {
      loading.value = false
    }
  }

  async function fetchActive() {
    loading.value = true
    try {
      const response = await api.get('/kategori-dokumen')
      const active = (response.data || []).filter(k => k.is_active === true)
      kategoriList.value = active
      return active
    } catch (error) {
      console.error('Error fetching active kategori dokumen:', error)
      kategoriList.value = []
      return []
    } finally {
      loading.value = false
    }
  }

  return {
    kategoriList,
    loading,
    fetchAll,
    fetchActive
  }
})

