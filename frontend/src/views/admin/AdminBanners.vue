<template>
  <div>
    <!-- Page Header -->
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between">
      <div class="mb-4 sm:mb-0">
        <h1 class="text-3xl font-bold text-gray-900">Kelola Banner</h1>
        <p class="mt-2 text-sm text-gray-600">Tambah dan kelola banner/logo mitra</p>
      </div>
      <button
        @click="openModal()"
        class="btn btn-primary inline-flex items-center"
      >
        <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
        </svg>
        Tambah Banner
      </button>
    </div>

    <!-- Banner List -->
    <div class="card">
      <div class="card-body p-0">
        <div v-if="loading" class="text-center py-12">
          <div class="inline-block animate-spin rounded-full h-10 w-10 border-4 border-primary-600 border-t-transparent"></div>
          <p class="mt-4 text-sm text-gray-500">Memuat data...</p>
        </div>
        <div v-else class="overflow-x-auto">
          <table class="table">
            <thead>
              <tr>
                <th>Logo</th>
                <th>Nama</th>
                <th>URL</th>
                <th>Order</th>
                <th>Status</th>
                <th class="text-right">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="item in bannerList" :key="item.id">
                <td>
                  <img
                    v-if="item.logo"
                    :src="getImageUrl(item.logo)"
                    :alt="item.nama"
                    class="h-12 w-auto object-contain"
                  />
                  <span v-else class="text-gray-400 text-sm">-</span>
                </td>
                <td>
                  <div class="text-sm font-semibold text-gray-900">{{ item.nama }}</div>
                </td>
                <td>
                  <div class="text-sm text-gray-600">
                    <a
                      v-if="item.url"
                      :href="item.url"
                      target="_blank"
                      class="text-primary-600 hover:text-primary-700 truncate max-w-xs block"
                    >
                      {{ item.url }}
                    </a>
                    <span v-else>-</span>
                  </div>
                </td>
                <td>
                  <div class="text-sm text-gray-600">{{ item.order }}</div>
                </td>
                <td>
                  <span :class="item.is_active ? 'badge badge-diterima' : 'badge badge-draft'">
                    {{ item.is_active ? 'Active' : 'Inactive' }}
                  </span>
                </td>
                <td class="text-right">
                  <div class="flex items-center justify-end space-x-2">
                    <button
                      @click="editItem(item)"
                      class="text-primary-600 hover:text-primary-700 font-medium text-sm"
                    >
                      Edit
                    </button>
                    <button
                      @click="deleteItem(item.id)"
                      class="text-rose-600 hover:text-rose-700 font-medium text-sm"
                    >
                      Hapus
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Modal -->
    <div
      v-if="showModal"
      class="fixed inset-0 bg-gray-900 bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center p-4"
      @click.self="closeModal"
    >
      <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-y-auto">
        <div class="card-header">
          <h3 class="text-lg font-semibold text-gray-900">
            {{ editingItem ? 'Edit Banner' : 'Tambah Banner' }}
          </h3>
        </div>
        <form @submit.prevent="saveItem" class="card-body space-y-4">
          <div>
            <label class="form-label">Nama *</label>
            <input
              v-model="form.nama"
              type="text"
              class="form-input"
              :disabled="uploading"
              required
            />
          </div>
          <div>
            <label class="form-label">URL</label>
            <input
              v-model="form.url"
              type="url"
              class="form-input"
              :disabled="uploading"
              placeholder="https://example.com"
            />
            <small class="text-gray-500 text-xs mt-1 block">URL link banner (opsional)</small>
          </div>
          <div>
            <label class="form-label">Logo *</label>
            <input
              type="file"
              @change="handleLogoChange"
              accept="image/*"
              :required="!editingItem"
              :disabled="uploading"
              class="form-input"
            />
            <small class="text-gray-500 text-xs mt-1 block">Format: JPG, PNG, GIF, WEBP (maks 2MB)</small>
            <div v-if="logoPreview" class="mt-2">
              <img
                :src="logoPreview"
                alt="Preview"
                class="h-32 w-auto object-contain rounded-lg border border-gray-200"
              />
            </div>
            <div v-else-if="editingItem && editingItem.logo" class="mt-2">
              <img
                :src="getImageUrl(editingItem.logo)"
                alt="Current logo"
                class="h-32 w-auto object-contain rounded-lg border border-gray-200"
              />
            </div>
            <!-- Upload Progress Bar -->
            <div v-if="uploading && uploadProgress > 0" class="mt-3">
              <div class="flex items-center justify-between mb-1">
                <span class="text-sm font-medium text-gray-700">Mengupload logo...</span>
                <span class="text-sm text-gray-600">{{ uploadProgress }}%</span>
              </div>
              <div class="w-full bg-gray-200 rounded-full h-2.5">
                <div
                  class="bg-primary-600 h-2.5 rounded-full transition-all duration-300"
                  :style="{ width: uploadProgress + '%' }"
                ></div>
              </div>
              <p class="text-xs text-gray-500 mt-1">{{ uploadedBytes }} / {{ totalBytes }}</p>
            </div>
          </div>
          <div>
            <label class="form-label">Order</label>
            <input
              v-model.number="form.order"
              type="number"
              class="form-input"
              :disabled="uploading"
              min="0"
            />
            <small class="text-gray-500 text-xs mt-1 block">Urutan tampilan (angka lebih kecil ditampilkan lebih dulu)</small>
          </div>
          <div class="flex items-center">
            <input
              v-model="form.is_active"
              type="checkbox"
              class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded"
              :disabled="uploading"
            />
            <label class="ml-2 block text-sm text-gray-700">Aktif</label>
          </div>
          <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
            <button
              type="button"
              @click="closeModal"
              class="btn btn-secondary"
              :disabled="uploading"
            >
              Batal
            </button>
            <button
              type="submit"
              class="btn btn-primary"
              :disabled="uploading"
            >
              <span v-if="uploading">Menyimpan...</span>
              <span v-else>Simpan</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '../../services/api'

const loading = ref(false)
const showModal = ref(false)
const editingItem = ref(null)
const bannerList = ref([])
const uploading = ref(false)
const uploadProgress = ref(0)
const uploadedBytes = ref('0 KB')
const totalBytes = ref('0 KB')
const logoPreview = ref(null)

const form = ref({
  nama: '',
  logo: null,
  url: '',
  order: 0,
  is_active: true,
})

const baseUrl = window.location.origin

function getImageUrl(path) {
  if (!path) return ''
  if (path.startsWith('http')) return path
  return `${baseUrl}/storage/${path}`
}

function formatBytes(bytes) {
  if (bytes === 0) return '0 KB'
  const k = 1024
  const sizes = ['Bytes', 'KB', 'MB', 'GB']
  const i = Math.floor(Math.log(bytes) / Math.log(k))
  return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i]
}

async function loadBanners() {
  loading.value = true
  try {
    const response = await api.get('/admin/banners')
    if (response.data.success) {
      bannerList.value = response.data.data || []
    } else {
      bannerList.value = []
    }
  } catch (error) {
    console.error('Error loading banners:', error)
    alert('Gagal memuat data banner: ' + (error.response?.data?.message || error.message))
    bannerList.value = []
  } finally {
    loading.value = false
  }
}

function openModal(item = null) {
  editingItem.value = item
  if (item) {
    form.value = {
      nama: item.nama || '',
      logo: null,
      url: item.url || '',
      order: item.order || 0,
      is_active: item.is_active !== undefined ? item.is_active : true,
    }
    logoPreview.value = null
  } else {
    form.value = {
      nama: '',
      logo: null,
      url: '',
      order: 0,
      is_active: true,
    }
    logoPreview.value = null
  }
  showModal.value = true
}

function closeModal() {
  showModal.value = false
  editingItem.value = null
  form.value = {
    nama: '',
    logo: null,
    url: '',
    order: 0,
    is_active: true,
  }
  logoPreview.value = null
  uploading.value = false
  uploadProgress.value = 0
}

function handleLogoChange(event) {
  if (event.target.files.length > 0) {
    form.value.logo = event.target.files[0]
    const reader = new FileReader()
    reader.onload = (e) => {
      logoPreview.value = e.target.result
    }
    reader.readAsDataURL(form.value.logo)
  }
}

function editItem(item) {
  openModal(item)
}

async function saveItem() {
  uploading.value = true
  uploadProgress.value = 0
  uploadedBytes.value = '0 KB'
  totalBytes.value = '0 KB'

  try {
    const formData = new FormData()
    formData.append('nama', form.value.nama)
    formData.append('url', form.value.url || '')
    formData.append('order', form.value.order || 0)
    formData.append('is_active', form.value.is_active ? '1' : '0')

    if (form.value.logo instanceof File) {
      formData.append('logo', form.value.logo)
      totalBytes.value = formatBytes(form.value.logo.size)
    }

    let response
    if (editingItem.value) {
      formData.append('_method', 'PUT')
      response = await api.post(`/admin/banners/${editingItem.value.id}`, formData, {
        headers: {
          'Content-Type': 'multipart/form-data',
        },
        onUploadProgress: (progressEvent) => {
          if (progressEvent.total) {
            uploadProgress.value = Math.round((progressEvent.loaded * 100) / progressEvent.total)
            uploadedBytes.value = formatBytes(progressEvent.loaded)
            totalBytes.value = formatBytes(progressEvent.total)
          }
        },
      })
    } else {
      response = await api.post('/admin/banners', formData, {
        headers: {
          'Content-Type': 'multipart/form-data',
        },
        onUploadProgress: (progressEvent) => {
          if (progressEvent.total) {
            uploadProgress.value = Math.round((progressEvent.loaded * 100) / progressEvent.total)
            uploadedBytes.value = formatBytes(progressEvent.loaded)
            totalBytes.value = formatBytes(progressEvent.total)
          }
        },
      })
    }

    if (response.data.success) {
      alert('Banner berhasil disimpan')
      closeModal()
      await loadBanners()
    } else {
      alert('Gagal menyimpan banner: ' + (response.data.message || 'Unknown error'))
    }
  } catch (error) {
    console.error('Error saving banner:', error)
    alert('Gagal menyimpan banner: ' + (error.response?.data?.message || error.message))
  } finally {
    uploading.value = false
    uploadProgress.value = 0
  }
}

async function deleteItem(id) {
  if (!confirm('Apakah Anda yakin ingin menghapus banner ini?')) {
    return
  }

  try {
    const response = await api.delete(`/admin/banners/${id}`)
    if (response.data.success) {
      alert('Banner berhasil dihapus')
      await loadBanners()
    } else {
      alert('Gagal menghapus banner: ' + (response.data.message || 'Unknown error'))
    }
  } catch (error) {
    console.error('Error deleting banner:', error)
    alert('Gagal menghapus banner: ' + (error.response?.data?.message || error.message))
  }
}

onMounted(() => {
  loadBanners()
})
</script>

