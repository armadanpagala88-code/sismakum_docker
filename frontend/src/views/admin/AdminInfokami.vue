<template>
  <div>
    <!-- Page Header -->
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between">
      <div class="mb-4 sm:mb-0">
        <h1 class="text-3xl font-bold text-gray-900">Kelola INFOKAMI</h1>
        <p class="mt-2 text-sm text-gray-600">Tambah dan kelola dokumen INFOKAMI</p>
      </div>
      <button
        @click="openModal()"
        class="btn btn-primary inline-flex items-center"
      >
        <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
        </svg>
        Tambah INFOKAMI
      </button>
    </div>

    <!-- INFOKAMI List -->
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
                <th>Nama File</th>
                <th>Deskripsi</th>
                <th>Status</th>
                <th>Tanggal</th>
                <th class="text-right">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="item in infokamiList" :key="item.id">
                <td>
                  <div class="text-sm font-semibold text-gray-900">{{ item.nama_file }}</div>
                </td>
                <td>
                  <div class="text-sm text-gray-600">{{ item.deskripsi ? (item.deskripsi.length > 50 ? item.deskripsi.substring(0, 50) + '...' : item.deskripsi) : '-' }}</div>
                </td>
                <td>
                  <span :class="item.status === 'active' ? 'badge badge-diterima' : 'badge badge-draft'">
                    {{ item.status === 'active' ? 'Active' : 'Inactive' }}
                  </span>
                </td>
                <td>
                  <div class="text-sm text-gray-600">{{ formatDate(item.created_at) }}</div>
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
            {{ editingItem ? 'Edit INFOKAMI' : 'Tambah INFOKAMI' }}
          </h3>
        </div>
        <form @submit.prevent="saveItem" class="card-body space-y-4">
          <div>
            <label class="form-label">Nama File *</label>
            <input
              v-model="form.nama_file"
              type="text"
              class="form-input"
              :disabled="uploading"
              required
            />
          </div>
          <div>
            <label class="form-label">Deskripsi</label>
            <textarea
              v-model="form.deskripsi"
              class="form-input"
              :disabled="uploading"
              rows="4"
            ></textarea>
          </div>
          <div>
            <label class="form-label">File PDF *</label>
            <input
              type="file"
              @change="handlePdfChange"
              accept=".pdf"
              :required="!editingItem"
              :disabled="uploading"
              class="form-input"
            />
            <small class="text-gray-500 text-xs mt-1 block">Format: PDF (maks 50MB)</small>
            <div v-if="editingItem && editingItem.file_pdf" class="mt-2">
              <a
                :href="getFileUrl(editingItem.file_pdf)"
                target="_blank"
                class="text-primary-600 hover:text-primary-700 text-sm"
              >
                Lihat PDF saat ini
              </a>
            </div>
            <!-- Upload Progress Bar -->
            <div v-if="uploading && uploadProgress > 0" class="mt-3">
              <div class="flex items-center justify-between mb-1">
                <span class="text-sm font-medium text-gray-700">Mengupload PDF...</span>
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
            <label class="form-label">Cover Image *</label>
            <input
              type="file"
              @change="handleCoverChange"
              accept="image/*"
              :required="!editingItem"
              :disabled="uploading"
              class="form-input"
            />
            <small class="text-gray-500 text-xs mt-1 block">Format: JPG, PNG, GIF (maks 5MB)</small>
            <div v-if="previewCover" class="mt-2">
              <img
                :src="previewCover"
                alt="Preview Cover"
                class="w-32 h-32 object-cover rounded-lg border border-gray-200"
              />
            </div>
            <div v-else-if="editingItem && editingItem.cover_pdf" class="mt-2">
              <img
                :src="getFileUrl(editingItem.cover_pdf)"
                alt="Cover saat ini"
                class="w-32 h-32 object-cover rounded-lg border border-gray-200"
              />
            </div>
          </div>
          <div>
            <label class="form-label">Status</label>
            <select v-model="form.status" class="form-input" :disabled="uploading">
              <option value="active">Active</option>
              <option value="inactive">Inactive</option>
            </select>
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
              <span v-if="uploading" class="flex items-center">
                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Mengupload...
              </span>
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

const infokamiList = ref([])
const loading = ref(false)
const showModal = ref(false)
const editingItem = ref(null)
const form = ref({
  nama_file: '',
  file_pdf: null,
  cover_pdf: null,
  deskripsi: '',
  status: 'active'
})
const previewCover = ref(null)
const apiBaseUrl = import.meta.env.VITE_API_BASE_URL || window.location.origin
const uploading = ref(false)
const uploadProgress = ref(0)
const uploadedBytes = ref('0 KB')
const totalBytes = ref('0 KB')

function formatDate(date) {
  if (!date) return '-'
  return new Date(date).toLocaleDateString('id-ID')
}

function getFileUrl(path) {
  if (!path) return ''
  if (path.startsWith('http')) return path
  return `${apiBaseUrl}/storage/${path}`
}

function handlePdfChange(event) {
  const file = event.target.files[0]
  if (file) {
    // Validate file type
    if (file.type !== 'application/pdf') {
      alert('File harus berformat PDF')
      event.target.value = ''
      form.value.file_pdf = null
      return
    }
    // Validate file size (50MB)
    if (file.size > 50 * 1024 * 1024) {
      alert('Ukuran file PDF maksimal 50MB')
      event.target.value = ''
      form.value.file_pdf = null
      return
    }
    form.value.file_pdf = file
    console.log('PDF file selected:', file.name, file.size, file.type)
  }
}

function handleCoverChange(event) {
  const file = event.target.files[0]
  if (file) {
    // Validate file type
    const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp']
    if (!allowedTypes.includes(file.type)) {
      alert('Cover image harus berformat JPG, PNG, GIF, atau WEBP')
      event.target.value = ''
      form.value.cover_pdf = null
      previewCover.value = null
      return
    }
    // Validate file size (5MB)
    if (file.size > 5 * 1024 * 1024) {
      alert('Ukuran cover image maksimal 5MB')
      event.target.value = ''
      form.value.cover_pdf = null
      previewCover.value = null
      return
    }
    form.value.cover_pdf = file
    const reader = new FileReader()
    reader.onload = (e) => {
      previewCover.value = e.target.result
    }
    reader.readAsDataURL(file)
    console.log('Cover file selected:', file.name, file.size, file.type)
  }
}

async function loadInfokami() {
  loading.value = true
  try {
    const response = await api.get('/infokami/all')
    if (response.data.success && Array.isArray(response.data.data)) {
      infokamiList.value = response.data.data
    } else if (Array.isArray(response.data)) {
      infokamiList.value = response.data
    } else {
      infokamiList.value = []
    }
  } catch (error) {
    console.error('Error loading INFOKAMI:', error)
    const errorMessage = error.response?.data?.message || 
                        error.response?.data?.error || 
                        error.message || 
                        'Gagal memuat data INFOKAMI'
    alert('Gagal memuat data INFOKAMI: ' + errorMessage)
    infokamiList.value = []
  } finally {
    loading.value = false
  }
}

function openModal(item = null) {
  editingItem.value = item
  if (item) {
    form.value = {
      nama_file: item.nama_file || '',
      file_pdf: null,
      cover_pdf: null,
      deskripsi: item.deskripsi || '',
      status: item.status || 'active'
    }
    previewCover.value = null
  } else {
    form.value = {
      nama_file: '',
      file_pdf: null,
      cover_pdf: null,
      deskripsi: '',
      status: 'active'
    }
    previewCover.value = null
  }
  showModal.value = true
}

function closeModal() {
  showModal.value = false
  editingItem.value = null
  form.value = {
    nama_file: '',
    file_pdf: null,
    cover_pdf: null,
    deskripsi: '',
    status: 'active'
  }
  previewCover.value = null
  uploading.value = false
  uploadProgress.value = 0
  uploadedBytes.value = '0 KB'
  totalBytes.value = '0 KB'
}

function editItem(item) {
  openModal(item)
}

function formatBytes(bytes) {
  if (bytes === 0) return '0 Bytes'
  const k = 1024
  const sizes = ['Bytes', 'KB', 'MB', 'GB']
  const i = Math.floor(Math.log(bytes) / Math.log(k))
  return Math.round((bytes / Math.pow(k, i)) * 100) / 100 + ' ' + sizes[i]
}

async function saveItem() {
  try {
    // Validasi untuk create baru
    if (!editingItem.value) {
      if (!form.value.file_pdf || !(form.value.file_pdf instanceof File)) {
        alert('File PDF wajib diisi')
        return
      }
      if (!form.value.cover_pdf || !(form.value.cover_pdf instanceof File)) {
        alert('Cover image wajib diisi')
        return
      }
    }
    
    const formData = new FormData()
    formData.append('nama_file', form.value.nama_file)
    formData.append('deskripsi', form.value.deskripsi || '')
    formData.append('status', form.value.status)
    
    // Calculate total bytes for progress tracking
    let totalSize = 0
    if (form.value.file_pdf instanceof File) {
      totalSize += form.value.file_pdf.size
    }
    if (form.value.cover_pdf instanceof File) {
      totalSize += form.value.cover_pdf.size
    }
    totalBytes.value = formatBytes(totalSize)
    
    // Reset upload progress
    uploading.value = true
    uploadProgress.value = 0
    uploadedBytes.value = '0 KB'
    
    // Append files - must be File objects
    if (form.value.file_pdf instanceof File) {
      formData.append('file_pdf', form.value.file_pdf)
      console.log('PDF appended to FormData:', form.value.file_pdf.name, form.value.file_pdf.size, form.value.file_pdf.type)
    } else if (form.value.file_pdf) {
      console.error('file_pdf is not a File object:', typeof form.value.file_pdf, form.value.file_pdf)
      alert('File PDF tidak valid. Silakan pilih file lagi.')
      uploading.value = false
      return
    }
    
    if (form.value.cover_pdf instanceof File) {
      formData.append('cover_pdf', form.value.cover_pdf)
      console.log('Cover appended to FormData:', form.value.cover_pdf.name, form.value.cover_pdf.size, form.value.cover_pdf.type)
    } else if (form.value.cover_pdf) {
      console.error('cover_pdf is not a File object:', typeof form.value.cover_pdf, form.value.cover_pdf)
      alert('Cover image tidak valid. Silakan pilih file lagi.')
      uploading.value = false
      return
    }
    
    // Debug: Check FormData contents
    console.log('FormData entries:')
    for (let pair of formData.entries()) {
      if (pair[1] instanceof File) {
        console.log(pair[0] + ': [File]', pair[1].name, pair[1].size, pair[1].type)
      } else {
        console.log(pair[0] + ': ', pair[1])
      }
    }
    
    // Configure axios with upload progress
    const config = {
      onUploadProgress: (progressEvent) => {
        if (progressEvent.total) {
          const percentCompleted = Math.round((progressEvent.loaded * 100) / progressEvent.total)
          uploadProgress.value = percentCompleted
          uploadedBytes.value = formatBytes(progressEvent.loaded)
        }
      }
    }
    
    let response
    if (editingItem.value) {
      response = await api.put(`/admin/infokami/${editingItem.value.id}`, formData, config)
    } else {
      response = await api.post('/admin/infokami', formData, config)
    }
    
    if (response.data.success) {
      alert(response.data.message || 'Data berhasil disimpan')
      closeModal()
      await loadInfokami()
    } else {
      alert(response.data.message || 'Gagal menyimpan INFOKAMI')
    }
  } catch (error) {
    console.error('Error saving INFOKAMI:', error)
    console.error('Error response:', error.response)
    let errorMessage = 'Gagal menyimpan INFOKAMI'
    
    if (error.response?.data) {
      if (error.response.data.errors) {
        // Validation errors
        const errors = Object.values(error.response.data.errors).flat()
        errorMessage = errors.join('\n')
      } else if (error.response.data.message) {
        errorMessage = error.response.data.message
      }
    } else if (error.message) {
      errorMessage = error.message
    }
    
    alert(errorMessage)
  } finally {
    uploading.value = false
    // Keep progress visible for a moment before resetting
    setTimeout(() => {
      uploadProgress.value = 0
      uploadedBytes.value = '0 KB'
      totalBytes.value = '0 KB'
    }, 1000)
  }
}

async function deleteItem(id) {
  if (!confirm('Apakah Anda yakin ingin menghapus INFOKAMI ini?')) return
  
  try {
    await api.delete(`/admin/infokami/${id}`)
    await loadInfokami()
  } catch (error) {
    console.error('Error deleting INFOKAMI:', error)
    alert('Gagal menghapus INFOKAMI')
  }
}

onMounted(() => {
  loadInfokami()
})
</script>

<style scoped>
/* Add any custom styles here if needed */
</style>

