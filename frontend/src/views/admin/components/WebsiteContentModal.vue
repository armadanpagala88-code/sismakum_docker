<template>
  <div
    class="fixed inset-0 bg-gray-900 bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center p-4"
    @click.self="$emit('close')"
  >
    <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-y-auto">
      <div class="card-header">
        <h3 class="text-lg font-semibold text-gray-900">
          {{ item ? 'Edit Konten' : 'Tambah Konten' }}
        </h3>
      </div>
      <form @submit.prevent="handleSubmit" class="card-body space-y-4">
        <div>
          <label class="form-label">Key *</label>
          <input
            v-model="form.key"
            type="text"
            class="form-input"
            :disabled="!!item"
            required
          />
          <small class="text-gray-500 text-xs mt-1 block">
            Key unik untuk konten ini (contoh: profil, visi_misi, fitur, kontak, sambutan)
          </small>
        </div>
        
        <div>
          <label class="form-label">Title</label>
          <input
            v-model="form.title"
            type="text"
            class="form-input"
            placeholder="Judul konten"
          />
        </div>
        
        <div>
          <label class="form-label">Type *</label>
          <select
            v-model="form.type"
            class="form-select"
            required
            @change="onTypeChange"
          >
            <option value="text">Text</option>
            <option value="html">HTML</option>
            <option value="image">Image</option>
          </select>
        </div>
        
        <div>
          <label class="form-label">Content *</label>
          <!-- Text Editor untuk HTML type -->
          <div v-if="form.type === 'html'" class="border border-gray-300 rounded-lg overflow-hidden">
            <div ref="editorContainer" class="min-h-[300px]"></div>
          </div>
          <!-- Textarea untuk text type -->
          <textarea
            v-else
            v-model="form.content"
            rows="10"
            class="form-textarea"
            placeholder="Isi konten"
            required
          ></textarea>
          <small class="text-gray-500 text-xs mt-1 block">
            <span v-if="form.type === 'html'">Gunakan editor untuk memformat konten dengan HTML</span>
            <span v-else>Masukkan konten teks biasa</span>
          </small>
        </div>
        
        <div>
          <label class="form-label">Upload File/Gambar</label>
          <input
            type="file"
            @change="handleFileChange"
            accept="image/*,application/pdf"
            class="form-input"
          />
          <small class="text-gray-500 text-xs mt-1 block">Format: JPG, PNG, GIF, PDF (maks 5MB)</small>
          <div v-if="previewImage" class="mt-4">
            <img
              :src="previewImage"
              alt="Preview"
              class="w-full max-w-md h-48 object-cover rounded-lg border border-gray-200"
            />
          </div>
          <div v-else-if="item && (item.file_path || item.image_url)" class="mt-4">
            <img
              v-if="item.file_path"
              :src="`${apiBaseUrl}/storage/${item.file_path}`"
              alt="Current"
              class="w-full max-w-md h-48 object-cover rounded-lg border border-gray-200"
            />
            <img
              v-else-if="item.image_url"
              :src="item.image_url"
              alt="Current"
              class="w-full max-w-md h-48 object-cover rounded-lg border border-gray-200"
            />
          </div>
        </div>
        
        <div>
          <label class="form-label">Atau Image URL</label>
          <input
            v-model="form.image_url"
            type="url"
            class="form-input"
            placeholder="https://example.com/image.jpg"
          />
          <small class="text-gray-500 text-xs mt-1 block">Gunakan URL jika tidak upload file</small>
        </div>
        
        <div>
          <label class="form-label">Order</label>
          <input
            v-model.number="form.order"
            type="number"
            class="form-input"
            min="0"
          />
          <small class="text-gray-500 text-xs mt-1 block">Urutan tampilan (angka lebih kecil = tampil lebih dulu)</small>
        </div>
        
        <div class="flex items-center">
          <input
            v-model="form.is_active"
            type="checkbox"
            class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded"
          />
          <label class="ml-2 block text-sm text-gray-700">Aktif</label>
        </div>
        
        <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
          <button
            type="button"
            @click="$emit('close')"
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
</template>

<script setup>
import { ref, watch, onMounted, onBeforeUnmount, nextTick } from 'vue'
import Quill from 'quill'
import 'quill/dist/quill.snow.css'

const props = defineProps({
  item: {
    type: Object,
    default: null
  },
  contentKey: {
    type: String,
    default: ''
  }
})

const emit = defineEmits(['close', 'save'])

const apiBaseUrl = import.meta.env.VITE_API_BASE_URL || window.location.origin

const form = ref({
  key: '',
  title: '',
  content: '',
  type: 'html',
  is_active: true,
  order: 0,
  image_url: '',
  file: null
})

const previewImage = ref(null)
const editorContainer = ref(null)
let quillEditor = null

function initEditor() {
  // Destroy existing editor first
  destroyEditor()
  
  if (editorContainer.value && form.value.type === 'html' && !quillEditor) {
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
      placeholder: 'Masukkan konten HTML...'
    })
    
    // Set initial content
    if (form.value.content) {
      quillEditor.root.innerHTML = form.value.content
    }
    
    // Update form.content when editor content changes
    quillEditor.on('text-change', () => {
      form.value.content = quillEditor.root.innerHTML
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

function onTypeChange() {
  destroyEditor()
  if (form.value.type === 'html') {
    nextTick(() => {
      // Wait a bit to ensure DOM is ready
      setTimeout(() => {
        initEditor()
      }, 50)
    })
  }
}

function handleFileChange(event) {
  if (event.target.files.length > 0) {
    form.value.file = event.target.files[0]
    if (form.value.file.type.startsWith('image/')) {
      const reader = new FileReader()
      reader.onload = (e) => {
        previewImage.value = e.target.result
      }
      reader.readAsDataURL(form.value.file)
    } else {
      previewImage.value = null
    }
  }
}

function handleSubmit() {
  // Get content from editor if HTML type
  if (form.value.type === 'html' && quillEditor) {
    form.value.content = quillEditor.root.innerHTML
  }
  
  // Validate content is not empty
  if (!form.value.content || form.value.content.trim() === '') {
    alert('Content tidak boleh kosong')
    return
  }
  
  const data = { ...form.value }
  
  // Ensure is_active is always a boolean
  data.is_active = data.is_active === true || data.is_active === 'true' || data.is_active === 1 || data.is_active === '1'
  
  if (data.file) {
    const formData = new FormData()
    Object.keys(data).forEach(key => {
      if (key === 'file' && data[key]) {
        formData.append('file', data[key])
      } else if (key !== 'file') {
        const value = data[key]
        if (key === 'is_active') {
          formData.append(key, value ? '1' : '0')
        } else {
          formData.append(key, value !== null && value !== undefined ? value : '')
        }
      }
    })
    emit('save', formData)
  } else {
    const submitData = { ...data }
    delete submitData.file
    submitData.is_active = submitData.is_active ? true : false
    emit('save', submitData)
  }
}

watch(() => props.item, (newItem) => {
  // Always destroy editor first when item changes
  destroyEditor()
  
  if (newItem) {
    form.value = {
      key: newItem.key || props.contentKey || '',
      title: newItem.title || '',
      content: newItem.content || '',
      type: newItem.type || 'html',
      is_active: newItem.is_active !== undefined ? newItem.is_active : true,
      order: newItem.order || 0,
      image_url: newItem.image_url || '',
      file: null
    }
    previewImage.value = null
    
    // Reinitialize editor with new content
    if (form.value.type === 'html') {
      nextTick(() => {
        setTimeout(() => {
          initEditor()
        }, 100)
      })
    }
  } else {
    form.value = {
      key: props.contentKey || '',
      title: '',
      content: '',
      type: 'html',
      is_active: true,
      order: 0,
      image_url: '',
      file: null
    }
    previewImage.value = null
    
    if (form.value.type === 'html') {
      nextTick(() => {
        setTimeout(() => {
          initEditor()
        }, 100)
      })
    }
  }
}, { immediate: true })

watch(() => props.contentKey, (newKey) => {
  if (!props.item && newKey) {
    form.value.key = newKey
  }
}, { immediate: true })

onMounted(() => {
  // Don't initialize here, let watch handle it
  // This prevents double initialization
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
