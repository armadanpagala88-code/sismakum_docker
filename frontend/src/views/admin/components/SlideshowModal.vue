<template>
  <div
    class="fixed inset-0 bg-gray-900 bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center p-4"
    @click.self="$emit('close')"
  >
    <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
      <div class="card-header">
        <h3 class="text-lg font-semibold text-gray-900">
          {{ item ? 'Edit Slideshow' : 'Tambah Slideshow' }}
        </h3>
      </div>
      <form @submit.prevent="handleSubmit" class="card-body space-y-4">
        <div>
          <label class="form-label">Title</label>
          <input
            v-model="form.title"
            type="text"
            class="form-input"
            placeholder="Judul slideshow"
          />
        </div>
        
        <div>
          <label class="form-label">Deskripsi</label>
          <textarea
            v-model="form.description"
            rows="3"
            class="form-textarea"
            placeholder="Deskripsi slideshow (opsional)"
          ></textarea>
        </div>
        
        <div>
          <label class="form-label">Upload Gambar</label>
          <input
            type="file"
            @change="handleFileChange"
            accept="image/*"
            class="form-input"
          />
          <small class="text-gray-500 text-xs mt-1 block">Format: JPG, PNG, GIF (maks 5MB)</small>
          <div v-if="previewImage" class="mt-4">
            <img
              :src="previewImage"
              alt="Preview"
              class="w-full h-48 object-cover rounded-lg border border-gray-200"
            />
          </div>
          <div v-else-if="item && (item.image_path || item.image_url)" class="mt-4">
            <img
              :src="getImageUrl(item)"
              alt="Current"
              class="w-full h-48 object-cover rounded-lg border border-gray-200"
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
          <label class="form-label">Link (opsional)</label>
          <input
            v-model="form.link"
            type="url"
            class="form-input"
            placeholder="https://example.com"
          />
          <small class="text-gray-500 text-xs mt-1 block">Link yang akan dibuka saat slideshow diklik</small>
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
import { ref, watch, onMounted } from 'vue'

const props = defineProps({
  item: {
    type: Object,
    default: null
  }
})

const emit = defineEmits(['close', 'save'])

const apiBaseUrl = import.meta.env.VITE_API_BASE_URL || window.location.origin

const form = ref({
  title: '',
  description: '',
  image_url: '',
  link: '',
  order: 0,
  is_active: true,
  file: null
})

const previewImage = ref(null)

function getImageUrl(item) {
  if (item.image_path) {
    return `${apiBaseUrl}/storage/${item.image_path}`
  }
  return item.image_url || ''
}

function handleFileChange(event) {
  if (event.target.files.length > 0) {
    form.value.file = event.target.files[0]
    const reader = new FileReader()
    reader.onload = (e) => {
      previewImage.value = e.target.result
    }
    reader.readAsDataURL(form.value.file)
  }
}

function handleSubmit() {
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
    // Convert boolean to string for JSON
    submitData.is_active = submitData.is_active ? true : false
    emit('save', submitData)
  }
}

watch(() => props.item, (newItem) => {
  if (newItem) {
    form.value = {
      title: newItem.title || '',
      description: newItem.description || '',
      image_url: newItem.image_url || '',
      link: newItem.link || '',
      order: newItem.order || 0,
      is_active: newItem.is_active !== undefined ? newItem.is_active : true,
      file: null
    }
    previewImage.value = null
  } else {
    form.value = {
      title: '',
      description: '',
      image_url: '',
      link: '',
      order: 0,
      is_active: true,
      file: null
    }
    previewImage.value = null
  }
}, { immediate: true })
</script>

