<template>
  <div>
    <!-- Page Header -->
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between">
      <div class="mb-4 sm:mb-0">
        <h1 class="text-3xl font-bold text-gray-900">Kelola Berita</h1>
        <p class="mt-2 text-sm text-gray-600">Tambah dan kelola berita website</p>
      </div>
      <button
        @click="openModal()"
        class="btn btn-primary inline-flex items-center"
      >
        <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
        </svg>
        Tambah Berita
      </button>
    </div>

    <!-- Berita List -->
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
                <th>Judul</th>
                <th>Penulis</th>
                <th>Status</th>
                <th>Views</th>
                <th>Tanggal</th>
                <th class="text-right">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="item in beritaList" :key="item.id">
                <td>
                  <div class="text-sm font-semibold text-gray-900">{{ item.judul }}</div>
                </td>
                <td>
                  <div class="text-sm text-gray-600">{{ item.penulis || '-' }}</div>
                </td>
                <td>
                  <span :class="item.is_published ? 'badge badge-diterima' : 'badge badge-draft'">
                    {{ item.is_published ? 'Published' : 'Draft' }}
                  </span>
                </td>
                <td>
                  <span v-if="item.is_headline" class="badge badge-diterima">Headline</span>
                  <span v-else class="text-sm text-gray-400">-</span>
                </td>
                <td>
                  <div class="text-sm text-gray-600">{{ item.views }}</div>
                </td>
                <td>
                  <div class="text-sm text-gray-600">{{ formatDate(item.published_at) }}</div>
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
            {{ editingItem ? 'Edit Berita' : 'Tambah Berita' }}
          </h3>
        </div>
        <form @submit.prevent="saveItem" class="card-body space-y-4">
            <div>
              <label class="form-label">Judul *</label>
              <input
                v-model="form.judul"
                type="text"
                class="form-input"
                required
              />
            </div>
            <div>
              <label class="form-label">Isi *</label>
              <!-- Text Editor -->
              <div class="border border-gray-300 rounded-lg overflow-hidden">
                <div ref="editorContainer" class="min-h-[300px]"></div>
              </div>
              <small class="text-gray-500 text-xs mt-1 block">Gunakan editor untuk memformat konten dengan HTML</small>
            </div>
            <div>
              <label class="form-label">Gambar</label>
              <input
                type="file"
                @change="handleFileChange"
                accept="image/*"
                class="form-input"
              />
              <small class="text-gray-500 text-xs mt-1 block">Format: JPG, PNG, GIF (maks 2MB)</small>
              <div v-if="previewImage" class="mt-2">
                <img
                  :src="previewImage"
                  alt="Preview"
                  class="w-32 h-32 object-cover rounded-lg border border-gray-200"
                />
              </div>
              <div v-else-if="editingItem && editingItem.gambar" class="mt-2">
                <img
                  :src="`${apiBaseUrl}/storage/${editingItem.gambar}`"
                  alt="Preview"
                  class="w-32 h-32 object-cover rounded-lg border border-gray-200"
                />
              </div>
            </div>
            <div>
              <label class="form-label">Penulis</label>
              <input
                v-model="form.penulis"
                type="text"
                class="form-input"
              />
            </div>
            <div>
              <label class="form-label">Order</label>
              <input
                v-model.number="form.order"
                type="number"
                class="form-input"
                min="0"
              />
            </div>
            <div class="flex items-center">
              <input
                v-model="form.is_published"
                type="checkbox"
                class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded"
              />
              <label class="ml-2 block text-sm text-gray-700">Publish</label>
            </div>
            <div class="flex items-center">
              <input
                v-model="form.is_headline"
                type="checkbox"
                class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded"
              />
              <label class="ml-2 block text-sm text-gray-700">Headline (Tampilkan di slideshow)</label>
            </div>
            <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
              <button
                type="button"
                @click="closeModal"
                class="btn btn-secondary"
              >
                Batal
              </button>
              <button
                type="submit"
                class="btn btn-primary"
              >
                Simpan
              </button>
            </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount, nextTick, watch } from 'vue'
import { useBeritaStore } from '../../stores/berita'
import Quill from 'quill'
import 'quill/dist/quill.snow.css'

const beritaStore = useBeritaStore()

const beritaList = ref([])
const loading = ref(false)
const showModal = ref(false)
const editingItem = ref(null)
const form = ref({
  judul: '',
  isi: '',
  gambar: null,
  penulis: '',
  is_published: false,
  is_headline: false,
  order: 0
})
const apiBaseUrl = import.meta.env.VITE_API_BASE_URL || window.location.origin
const editorContainer = ref(null)
let quillEditor = null

function initEditor() {
  // Destroy existing editor first
  destroyEditor()
  
  if (editorContainer.value && !quillEditor) {
    // Clear container completely
    editorContainer.value.innerHTML = ''
    
    quillEditor = new Quill(editorContainer.value, {
      theme: 'snow',
      modules: {
        toolbar: [
          [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
          ['bold', 'italic', 'underline', 'strike'],
          [{ 'list': 'ordered'}, { 'list': 'bullet' }],
          [{ 'script': 'sub'}, { 'script': 'super' }],
          [{ 'indent': '-1'}, { 'indent': '+1' }],
          [{ 'direction': 'rtl' }],
          [{ 'color': [] }, { 'background': [] }],
          [{ 'font': [] }],
          [{ 'align': [] }],
          ['clean'],
          ['link', 'image']
        ]
      },
      placeholder: 'Masukkan isi berita...'
    })
    
    // Set initial content
    if (form.value.isi) {
      quillEditor.root.innerHTML = form.value.isi
    }
    
    // Update form.isi when editor content changes
    quillEditor.on('text-change', () => {
      form.value.isi = quillEditor.root.innerHTML
    })
  }
}

function destroyEditor() {
  if (quillEditor) {
    try {
      // Remove all event listeners
      quillEditor.off('text-change')
      // Get the root element and remove it
      const root = quillEditor.root
      if (root && root.parentNode) {
        root.parentNode.innerHTML = ''
      }
    } catch (e) {
      console.warn('Error destroying editor:', e)
    }
    quillEditor = null
  }
  if (editorContainer.value) {
    editorContainer.value.innerHTML = ''
  }
}

function formatDate(date) {
  if (!date) return '-'
  return new Date(date).toLocaleDateString('id-ID')
}

function handleFileChange(event) {
  if (event.target.files.length > 0) {
    form.value.gambar = event.target.files[0]
    const reader = new FileReader()
    reader.onload = (e) => {
      previewImage.value = e.target.result
    }
    reader.readAsDataURL(form.value.gambar)
  }
}

const previewImage = ref(null)

async function loadBerita() {
  loading.value = true
  try {
    const data = await beritaStore.fetchAll()
    // Ensure we have an array
    if (Array.isArray(beritaStore.beritaList)) {
      beritaList.value = beritaStore.beritaList
    } else if (Array.isArray(data)) {
      beritaList.value = data
    } else if (data?.data && Array.isArray(data.data)) {
      beritaList.value = data.data
    } else {
      beritaList.value = []
    }
  } catch (error) {
    console.error('Error loading berita:', error)
    alert('Gagal memuat data berita: ' + (error.response?.data?.message || error.message))
    beritaList.value = []
  } finally {
    loading.value = false
  }
}

function openModal(item = null) {
  // Destroy editor first if exists
  destroyEditor()
  
  editingItem.value = item
  if (item) {
    form.value = {
      judul: item.judul || '',
      isi: item.isi || '',
      gambar: null,
      penulis: item.penulis || '',
      is_published: item.is_published === true || item.is_published === 1 || item.is_published === '1',
      is_headline: item.is_headline === true || item.is_headline === 1 || item.is_headline === '1',
      order: item.order || 0
    }
    previewImage.value = null
  } else {
    form.value = {
      judul: '',
      isi: '',
      gambar: null,
      penulis: '',
      is_published: false,
      is_headline: false,
      order: 0
    }
    previewImage.value = null
  }
  showModal.value = true
  
  // Initialize editor after modal is shown and DOM is ready
  nextTick(() => {
    // Wait a bit more to ensure modal is fully rendered
    setTimeout(() => {
      initEditor()
    }, 100)
  })
}

function closeModal() {
  destroyEditor()
  showModal.value = false
  editingItem.value = null
  form.value = {
    judul: '',
    isi: '',
    is_headline: false,
    gambar: null,
    penulis: '',
    is_published: false,
    order: 0
  }
  previewImage.value = null
}

function editItem(item) {
  openModal(item)
}

async function saveItem() {
  try {
    // Get content from editor
    let isiContent = form.value.isi
    if (quillEditor) {
      isiContent = quillEditor.root.innerHTML
      // Check if content is actually empty (only whitespace or empty tags)
      const textContent = quillEditor.root.textContent || quillEditor.root.innerText || ''
      if (!textContent.trim()) {
        alert('Isi berita tidak boleh kosong')
        return
      }
    }
    
    // Validate content is not empty
    if (!isiContent || isiContent.trim() === '' || isiContent === '<p><br></p>' || isiContent === '<p></p>') {
      alert('Isi berita tidak boleh kosong')
      return
    }
    
    // Validate required fields
    if (!form.value.judul || form.value.judul.trim() === '') {
      alert('Judul berita tidak boleh kosong')
      return
    }
    
    // Ensure is_published and is_headline are always booleans
    const submitData = {
      judul: form.value.judul.trim(),
      isi: isiContent,
      penulis: form.value.penulis || '',
      order: form.value.order || 0,
      is_published: form.value.is_published === true || form.value.is_published === 'true' || form.value.is_published === 1 || form.value.is_published === '1',
      is_headline: form.value.is_headline === true || form.value.is_headline === 'true' || form.value.is_headline === 1 || form.value.is_headline === '1'
    }
    
    // Only include gambar if it's a File
    if (form.value.gambar instanceof File) {
      submitData.gambar = form.value.gambar
    }
    
    console.log('Submitting data:', {
      judul: submitData.judul,
      judulLength: submitData.judul.length,
      isi: submitData.isi ? 'present (' + submitData.isi.length + ' chars)' : 'missing',
      isiPreview: submitData.isi ? submitData.isi.substring(0, 100) : '',
      is_published: submitData.is_published,
      is_headline: submitData.is_headline,
      penulis: submitData.penulis,
      order: submitData.order,
      gambar: submitData.gambar ? 'File' : 'not included'
    })
    
    if (editingItem.value) {
      await beritaStore.updateBerita(editingItem.value.id, submitData)
    } else {
      await beritaStore.createBerita(submitData)
    }
    closeModal()
    await loadBerita()
  } catch (error) {
    console.error('Error saving berita:', error)
    console.error('Error response:', error.response?.data)
    
    // Show detailed error message
    let errorMessage = 'Gagal menyimpan berita'
    if (error.response?.data?.errors) {
      const errors = error.response.data.errors
      const errorList = Object.keys(errors).map(key => {
        return `${key}: ${errors[key].join(', ')}`
      }).join('\n')
      errorMessage = 'Validasi gagal:\n' + errorList
    } else if (error.response?.data?.message) {
      errorMessage = error.response.data.message
    }
    
    alert(errorMessage)
  }
}

async function deleteItem(id) {
  if (!confirm('Apakah Anda yakin ingin menghapus berita ini?')) return
  
  try {
    await beritaStore.deleteBerita(id)
    await loadBerita()
  } catch (error) {
    console.error('Error deleting berita:', error)
    alert('Gagal menghapus berita')
  }
}

onMounted(() => {
  loadBerita()
})

onBeforeUnmount(() => {
  destroyEditor()
})
</script>

<style>
.ql-container {
  font-family: 'Inter', system-ui, sans-serif;
  font-size: 14px;
}

.ql-editor {
  min-height: 300px;
}

.ql-toolbar {
  border-top: 1px solid #e5e7eb;
  border-left: 1px solid #e5e7eb;
  border-right: 1px solid #e5e7eb;
  border-bottom: none;
  background: #f9fafb;
}

.ql-container {
  border-bottom: 1px solid #e5e7eb;
  border-left: 1px solid #e5e7eb;
  border-right: 1px solid #e5e7eb;
  border-top: none;
}
</style>
