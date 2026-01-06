<template>
  <div>
    <!-- Page Header -->
    <div class="mb-8">
      <h1 class="text-3xl font-bold text-gray-900">Kelola Website</h1>
      <p class="mt-2 text-sm text-gray-600">Kelola konten website publik</p>
    </div>

    <!-- Tabs Navigation -->
    <div class="mb-6 border-b border-gray-200">
      <nav class="-mb-px flex space-x-8 overflow-x-auto">
        <button
          v-for="tab in tabs"
          :key="tab.id"
          @click="activeTab = tab.id"
          :class="[
            'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors flex items-center',
            activeTab === tab.id
              ? 'border-primary-500 text-primary-600'
              : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
          ]"
        >
          <component :is="tab.icon" class="h-5 w-5 mr-2" />
          {{ tab.name }}
        </button>
      </nav>
    </div>

    <!-- Tab Content -->
    <div class="mt-6">
      <!-- Slideshow Tab -->
      <div v-if="activeTab === 'slideshow'">
        <!-- Foto Kepemimpinan Section -->
        <div class="mb-8 card">
          <div class="card-header">
            <h3 class="text-lg font-semibold text-gray-900">Foto Kepemimpinan</h3>
            <p class="mt-1 text-sm text-gray-600">Upload foto Bupati, Wakil Bupati, dan Sekda untuk ditampilkan di slideshow header (Format: PNG, maks 2MB)</p>
          </div>
          <div class="card-body">
            <form @submit.prevent="saveLeadershipPhotos" class="space-y-6">
              <!-- Foto Bupati -->
              <div>
                <label class="form-label">Foto Bupati (PNG)</label>
                <input
                  type="file"
                  ref="bupatiFileInput"
                  @change="handlePhotoChange('bupati', $event)"
                  accept="image/png"
                  class="form-input"
                />
                <small class="text-gray-500 text-xs mt-1 block">Format: PNG (maks 2MB)</small>
                <div v-if="photoPreviews.bupati" class="mt-4">
                  <p class="text-sm text-gray-600 mb-2">Preview:</p>
                  <img
                    :src="photoPreviews.bupati"
                    alt="Preview Foto Bupati"
                    class="w-32 h-32 object-cover rounded-full border-4 border-gray-200"
                  />
                </div>
                <div v-else-if="currentPhotos.bupati" class="mt-4">
                  <div class="flex items-center justify-between mb-2">
                    <p class="text-sm text-gray-600">Foto Bupati Saat Ini:</p>
                    <button
                      type="button"
                      @click="deletePhoto('bupati')"
                      class="text-rose-600 hover:text-rose-700 text-sm font-medium"
                    >
                      Hapus
                    </button>
                  </div>
                  <img
                    :key="`bupati-${currentPhotos.bupati}`"
                    :src="getBupatiPhotoUrl(currentPhotos.bupati)"
                    alt="Foto Bupati"
                    class="w-32 h-32 object-cover rounded-full border-4 border-gray-200"
                    @error="handleImageError"
                  />
                </div>
              </div>

              <!-- Foto Wakil Bupati -->
              <div>
                <label class="form-label">Foto Wakil Bupati (PNG)</label>
                <input
                  type="file"
                  ref="wakilBupatiFileInput"
                  @change="handlePhotoChange('wakil_bupati', $event)"
                  accept="image/png"
                  class="form-input"
                />
                <small class="text-gray-500 text-xs mt-1 block">Format: PNG (maks 2MB)</small>
                <div v-if="photoPreviews.wakil_bupati" class="mt-4">
                  <p class="text-sm text-gray-600 mb-2">Preview:</p>
                  <img
                    :src="photoPreviews.wakil_bupati"
                    alt="Preview Foto Wakil Bupati"
                    class="w-32 h-32 object-cover rounded-full border-4 border-gray-200"
                  />
                </div>
                <div v-else-if="currentPhotos.wakil_bupati" class="mt-4">
                  <div class="flex items-center justify-between mb-2">
                    <p class="text-sm text-gray-600">Foto Wakil Bupati Saat Ini:</p>
                    <button
                      type="button"
                      @click="deletePhoto('wakil_bupati')"
                      class="text-rose-600 hover:text-rose-700 text-sm font-medium"
                    >
                      Hapus
                    </button>
                  </div>
                  <img
                    :key="`wakil-${currentPhotos.wakil_bupati}`"
                    :src="getBupatiPhotoUrl(currentPhotos.wakil_bupati)"
                    alt="Foto Wakil Bupati"
                    class="w-32 h-32 object-cover rounded-full border-4 border-gray-200"
                    @error="handleImageError"
                  />
                </div>
              </div>

              <!-- Foto Sekda -->
              <div>
                <label class="form-label">Foto Sekda (PNG)</label>
                <input
                  type="file"
                  ref="sekdaFileInput"
                  @change="handlePhotoChange('sekda', $event)"
                  accept="image/png"
                  class="form-input"
                />
                <small class="text-gray-500 text-xs mt-1 block">Format: PNG (maks 2MB)</small>
                <div v-if="photoPreviews.sekda" class="mt-4">
                  <p class="text-sm text-gray-600 mb-2">Preview:</p>
                  <img
                    :src="photoPreviews.sekda"
                    alt="Preview Foto Sekda"
                    class="w-32 h-32 object-cover rounded-full border-4 border-gray-200"
                  />
                </div>
                <div v-else-if="currentPhotos.sekda" class="mt-4">
                  <div class="flex items-center justify-between mb-2">
                    <p class="text-sm text-gray-600">Foto Sekda Saat Ini:</p>
                    <button
                      type="button"
                      @click="deletePhoto('sekda')"
                      class="text-rose-600 hover:text-rose-700 text-sm font-medium"
                    >
                      Hapus
                    </button>
                  </div>
                  <img
                    :key="`sekda-${currentPhotos.sekda}`"
                    :src="getBupatiPhotoUrl(currentPhotos.sekda)"
                    alt="Foto Sekda"
                    class="w-32 h-32 object-cover rounded-full border-4 border-gray-200"
                    @error="handleImageError"
                  />
                </div>
              </div>

              <div class="flex justify-end">
                <button type="submit" class="btn btn-primary" :disabled="leadershipPhotosUploading">
                  <span v-if="leadershipPhotosUploading">Menyimpan...</span>
                  <span v-else>Simpan Foto Kepemimpinan</span>
                </button>
              </div>
            </form>
          </div>
        </div>

        <div class="mb-6 flex justify-between items-center">
          <div>
            <h2 class="text-2xl font-bold text-gray-900">Kelola Slideshow</h2>
            <p class="mt-1 text-sm text-gray-600">Kelola gambar slideshow untuk header website</p>
          </div>
          <button @click="openSlideshowModal()" class="btn btn-primary inline-flex items-center">
            <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Tambah Slideshow
          </button>
        </div>
        <div class="card">
          <div class="card-body p-0">
            <div v-if="slideshowLoading" class="text-center py-12">
              <div class="inline-block animate-spin rounded-full h-10 w-10 border-4 border-primary-600 border-t-transparent"></div>
              <p class="mt-4 text-sm text-gray-500">Memuat data...</p>
            </div>
            <div v-else-if="slideshowList.length === 0" class="text-center py-12">
              <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
              </svg>
              <h3 class="text-lg font-semibold text-gray-900 mb-2">Belum ada slideshow</h3>
              <p class="text-gray-600 mb-4">Mulai dengan menambahkan gambar slideshow untuk header website</p>
              <button @click="openSlideshowModal()" class="btn btn-primary">
                Tambah Slideshow
              </button>
            </div>
            <div v-else class="overflow-x-auto">
              <table class="table">
                <thead>
                  <tr>
                    <th>Gambar</th>
                    <th>Title</th>
                    <th>Deskripsi</th>
                    <th>Order</th>
                    <th>Status</th>
                    <th class="text-right">Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="item in slideshowList" :key="item.id">
                    <td>
                      <img
                        v-if="item.image_path || item.image_url"
                        :src="getImageUrl(item)"
                        alt="Slideshow"
                        class="w-20 h-16 object-cover rounded-lg border border-gray-200"
                      />
                      <div v-else class="w-20 h-16 bg-gray-200 rounded-lg flex items-center justify-center">
                        <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                      </div>
                    </td>
                    <td>
                      <div class="text-sm font-semibold text-gray-900">{{ item.title || '-' }}</div>
                    </td>
                    <td>
                      <div class="text-sm text-gray-600 max-w-xs truncate">{{ item.description || '-' }}</div>
                    </td>
                    <td>
                      <div class="text-sm text-gray-600">{{ item.order }}</div>
                    </td>
                    <td>
                      <span :class="item.is_active ? 'badge badge-diterima' : 'badge badge-ditolak'">
                        {{ item.is_active ? 'Aktif' : 'Tidak Aktif' }}
                      </span>
                    </td>
                    <td class="text-right">
                      <div class="flex items-center justify-end space-x-2">
                        <button @click="editSlideshow(item)" class="text-primary-600 hover:text-primary-700 font-medium text-sm">Edit</button>
                        <button @click="deleteSlideshow(item.id)" class="text-rose-600 hover:text-rose-700 font-medium text-sm">Hapus</button>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <!-- Sambutan Tab -->
      <div v-if="activeTab === 'sambutan'">
        <WebsiteContentSection
          :items="getContentByKey('sambutan')"
          :loading="loading"
          title="Sambutan Kepala Bagian Hukum"
          content-key="sambutan"
          @edit="openContentModal"
          @delete="deleteWebsiteContent"
        />
      </div>

      <!-- Profil Tab -->
      <div v-if="activeTab === 'profil'">
        <WebsiteContentSection
          :items="getContentByKey('profil')"
          :loading="loading"
          title="Profil Bagian Hukum"
          content-key="profil"
          @edit="openContentModal"
          @delete="deleteWebsiteContent"
        />
      </div>

      <!-- Visi Misi Tab -->
      <div v-if="activeTab === 'visi_misi'">
        <WebsiteContentSection
          :items="getContentByKey('visi_misi')"
          :loading="loading"
          title="Visi dan Misi"
          content-key="visi_misi"
          @edit="openContentModal"
          @delete="deleteWebsiteContent"
        />
      </div>

      <!-- Fitur Utama Tab -->
      <div v-if="activeTab === 'fitur'">
        <WebsiteContentSection
          :items="getContentByKey('fitur')"
          :loading="loading"
          title="Fitur Utama"
          content-key="fitur"
          @edit="openContentModal"
          @delete="deleteWebsiteContent"
        />
      </div>

      <!-- Kontak Tab -->
      <div v-if="activeTab === 'kontak'">
        <WebsiteContentSection
          :items="getContentByKey('kontak')"
          :loading="loading"
          title="Kontak Kami"
          content-key="kontak"
          @edit="openContentModal"
          @delete="deleteWebsiteContent"
        />
      </div>

      <!-- Berita Tab -->
      <div v-if="activeTab === 'berita'">
        <div class="card">
          <div class="card-body">
            <div class="text-center py-8">
              <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
              </svg>
              <h3 class="text-lg font-semibold text-gray-900 mb-2">Kelola Berita</h3>
              <p class="text-gray-600 mb-4">Kelola berita dan informasi terkini melalui menu Kelola Berita.</p>
              <router-link to="/dashboard/admin/berita" class="btn btn-primary inline-flex items-center">
                <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
                Buka Kelola Berita
              </router-link>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Slideshow Modal -->
    <SlideshowModal
      v-if="showSlideshowModal"
      :item="editingSlideshow"
      @close="closeSlideshowModal"
      @save="saveSlideshow"
    />

    <!-- Website Content Modal -->
    <WebsiteContentModal
      v-if="showContentModal"
      :item="editingContent"
      :content-key="currentContentKey"
      @close="closeContentModal"
      @save="handleSaveContent"
    />
  </div>
</template>

<script setup>
import { ref, onMounted, h } from 'vue'
import { useWebsiteStore } from '../../stores/website'
import { useSlideshowStore } from '../../stores/slideshow'
import WebsiteContentSection from './components/WebsiteContentSection.vue'
import SlideshowModal from './components/SlideshowModal.vue'
import WebsiteContentModal from './components/WebsiteContentModal.vue'

const websiteStore = useWebsiteStore()
const slideshowStore = useSlideshowStore()

const activeTab = ref('slideshow')
const loading = ref(false)
const slideshowLoading = ref(false)
const websiteList = ref([])
const slideshowList = ref([])
const showSlideshowModal = ref(false)
const showContentModal = ref(false)
const editingSlideshow = ref(null)
const editingContent = ref(null)
const currentContentKey = ref('')
const photoFiles = ref({
  bupati: null,
  wakil_bupati: null,
  sekda: null
})
const photoPreviews = ref({
  bupati: null,
  wakil_bupati: null,
  sekda: null
})
const currentPhotos = ref({
  bupati: null,
  wakil_bupati: null,
  sekda: null
})
const leadershipPhotosUploading = ref(false)
const bupatiFileInput = ref(null)
const wakilBupatiFileInput = ref(null)
const sekdaFileInput = ref(null)

const apiBaseUrl = import.meta.env.VITE_API_BASE_URL || window.location.origin

// Icon components
const SlideshowIcon = () => h('svg', {
  class: 'h-5 w-5',
  fill: 'none',
  stroke: 'currentColor',
  viewBox: '0 0 24 24'
}, [
  h('path', {
    'stroke-linecap': 'round',
    'stroke-linejoin': 'round',
    'stroke-width': '2',
    d: 'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z'
  })
])

const ProfilIcon = () => h('svg', {
  class: 'h-5 w-5',
  fill: 'none',
  stroke: 'currentColor',
  viewBox: '0 0 24 24'
}, [
  h('path', {
    'stroke-linecap': 'round',
    'stroke-linejoin': 'round',
    'stroke-width': '2',
    d: 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'
  })
])

const VisiMisiIcon = () => h('svg', {
  class: 'h-5 w-5',
  fill: 'none',
  stroke: 'currentColor',
  viewBox: '0 0 24 24'
}, [
  h('path', {
    'stroke-linecap': 'round',
    'stroke-linejoin': 'round',
    'stroke-width': '2',
    d: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'
  })
])

const FiturIcon = () => h('svg', {
  class: 'h-5 w-5',
  fill: 'none',
  stroke: 'currentColor',
  viewBox: '0 0 24 24'
}, [
  h('path', {
    'stroke-linecap': 'round',
    'stroke-linejoin': 'round',
    'stroke-width': '2',
    d: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'
  })
])

const KontakIcon = () => h('svg', {
  class: 'h-5 w-5',
  fill: 'none',
  stroke: 'currentColor',
  viewBox: '0 0 24 24'
}, [
  h('path', {
    'stroke-linecap': 'round',
    'stroke-linejoin': 'round',
    'stroke-width': '2',
    d: 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z'
  })
])

const BeritaIcon = () => h('svg', {
  class: 'h-5 w-5',
  fill: 'none',
  stroke: 'currentColor',
  viewBox: '0 0 24 24'
}, [
  h('path', {
    'stroke-linecap': 'round',
    'stroke-linejoin': 'round',
    'stroke-width': '2',
    d: 'M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z'
  })
])

const SambutanIcon = () => h('svg', {
  class: 'h-5 w-5',
  fill: 'none',
  stroke: 'currentColor',
  viewBox: '0 0 24 24'
}, [
  h('path', {
    'stroke-linecap': 'round',
    'stroke-linejoin': 'round',
    'stroke-width': '2',
    d: 'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z'
  })
])

const tabs = [
  { id: 'slideshow', name: 'Slideshow', icon: SlideshowIcon },
  { id: 'sambutan', name: 'Sambutan', icon: SambutanIcon },
  { id: 'profil', name: 'Profil', icon: ProfilIcon },
  { id: 'visi_misi', name: 'Visi & Misi', icon: VisiMisiIcon },
  { id: 'fitur', name: 'Fitur Utama', icon: FiturIcon },
  { id: 'kontak', name: 'Kontak', icon: KontakIcon },
  { id: 'berita', name: 'Berita', icon: BeritaIcon },
]

function getImageUrl(item) {
  if (item.image_path) {
    return `${apiBaseUrl}/storage/${item.image_path}`
  }
  return item.image_url || ''
}

function getContentByKey(key) {
  return websiteList.value.filter(item => item.key === key)
}

async function loadWebsite() {
  loading.value = true
  try {
    const data = await websiteStore.fetchAll()
    if (Array.isArray(websiteStore.websiteList)) {
      websiteList.value = websiteStore.websiteList
    } else if (Array.isArray(data)) {
      websiteList.value = data
    } else if (data?.data && Array.isArray(data.data)) {
      websiteList.value = data.data
    } else {
      websiteList.value = []
    }
  } catch (error) {
    console.error('Error loading website:', error)
    websiteList.value = []
  } finally {
    loading.value = false
  }
}

async function loadSlideshow() {
  slideshowLoading.value = true
  try {
    const data = await slideshowStore.fetchAll()
    if (Array.isArray(slideshowStore.slideshowList)) {
      slideshowList.value = slideshowStore.slideshowList
    } else if (Array.isArray(data)) {
      slideshowList.value = data
    } else if (data?.data && Array.isArray(data.data)) {
      slideshowList.value = data.data
    } else {
      slideshowList.value = []
    }
  } catch (error) {
    console.error('Error loading slideshow:', error)
    slideshowList.value = []
  } finally {
    slideshowLoading.value = false
  }
}

function openSlideshowModal(item = null) {
  editingSlideshow.value = item
  showSlideshowModal.value = true
}

function closeSlideshowModal() {
  showSlideshowModal.value = false
  editingSlideshow.value = null
}

async function saveSlideshow(data) {
  try {
    if (editingSlideshow.value) {
      await slideshowStore.updateSlideshow(editingSlideshow.value.id, data)
    } else {
      await slideshowStore.createSlideshow(data)
    }
    closeSlideshowModal()
    await loadSlideshow()
  } catch (error) {
    console.error('Error saving slideshow:', error)
    alert(error.response?.data?.message || 'Gagal menyimpan slideshow')
  }
}

async function deleteSlideshow(id) {
  if (!confirm('Apakah Anda yakin ingin menghapus slideshow ini?')) return
  try {
    await slideshowStore.deleteSlideshow(id)
    await loadSlideshow()
  } catch (error) {
    console.error('Error deleting slideshow:', error)
    alert('Gagal menghapus slideshow')
  }
}

function editSlideshow(item) {
  openSlideshowModal(item)
}

async function saveWebsiteContent(data) {
  try {
    if (editingContent.value) {
      await websiteStore.updateWebsite(editingContent.value.id, data)
    } else {
      await websiteStore.createWebsite(data)
    }
    closeContentModal()
    await loadWebsite()
  } catch (error) {
    console.error('Error saving content:', error)
    alert(error.response?.data?.message || 'Gagal menyimpan konten')
  }
}

function deleteWebsiteContent(id) {
  if (!confirm('Apakah Anda yakin ingin menghapus konten ini?')) return
  websiteStore.deleteWebsite(id).then(() => {
    loadWebsite()
  }).catch(error => {
    console.error('Error deleting content:', error)
    alert('Gagal menghapus konten')
  })
}

function handleSaveContent(data) {
  saveWebsiteContent(data)
}

function openContentModal(key, item = null) {
  currentContentKey.value = key
  editingContent.value = item || null
  showContentModal.value = true
}

function closeContentModal() {
  showContentModal.value = false
  editingContent.value = null
  currentContentKey.value = ''
}

function handlePhotoChange(type, event) {
  if (event.target.files.length > 0) {
    const file = event.target.files[0]
    console.log(`Photo selected for ${type}:`, file.name, file.type, file.size)
    
    if (file.type !== 'image/png') {
      alert('File harus berformat PNG')
      event.target.value = ''
      photoFiles.value[type] = null
      photoPreviews.value[type] = null
      return
    }
    if (file.size > 2 * 1024 * 1024) {
      alert('Ukuran file maksimal 2MB')
      event.target.value = ''
      photoFiles.value[type] = null
      photoPreviews.value[type] = null
      return
    }
    photoFiles.value[type] = file
    console.log(`Photo file saved for ${type}:`, photoFiles.value[type])
    
    const reader = new FileReader()
    reader.onload = (e) => {
      photoPreviews.value[type] = e.target.result
      console.log(`Preview generated for ${type}`)
    }
    reader.readAsDataURL(file)
  } else {
    console.log(`No file selected for ${type}`)
  }
}

function getBupatiPhotoUrl(path) {
  if (!path) return ''
  if (path.startsWith('http')) return path
  // Add cache busting timestamp to force browser to reload image
  const timestamp = new Date().getTime()
  return `${apiBaseUrl}/storage/${path}?t=${timestamp}`
}

function handleImageError(event) {
  console.warn('Image failed to load:', event.target.src)
  event.target.src = 'data:image/svg+xml,%3Csvg xmlns=%27http://www.w3.org/2000/svg%27 width=%27100%27 height=%27100%27%3E%3Crect fill=%27%23ddd%27 width=%27100%27 height=%27100%27/%3E%3Ctext x=%2750%25%27 y=%2750%27 text-anchor=%27middle%27 dy=%27.3em%27 fill=%27%23999%27%3ENo Image%3C/text%3E%3C/svg%3E'
}

async function deletePhoto(type) {
  const photoNames = {
    bupati: 'Bupati',
    wakil_bupati: 'Wakil Bupati',
    sekda: 'Sekda'
  }
  
  if (!confirm(`Apakah Anda yakin ingin menghapus foto ${photoNames[type]}?`)) {
    return
  }

  try {
    // Cari setting untuk foto kepemimpinan
    let leadershipSetting = websiteList.value.find(item => 
      item.key === 'bupati_photo' || item.key === 'leadership_photos'
    )

    if (!leadershipSetting) {
      alert('Setting foto kepemimpinan tidak ditemukan')
      return
    }

    const formData = new FormData()
    formData.append('key', 'bupati_photo')
    formData.append('title', 'Foto Kepemimpinan')
    formData.append('type', 'image')
    formData.append('is_active', '1')
    formData.append('order', '0')
    
    // Set foto yang akan dihapus menjadi string khusus "DELETE"
    // Backend akan mengecek string ini dan menghapus foto
    if (type === 'bupati') {
      formData.append('bupati_photo', 'DELETE') // String khusus untuk hapus
      console.log('Deleting bupati photo')
    } else if (type === 'wakil_bupati') {
      formData.append('wakil_bupati_photo', 'DELETE') // String khusus untuk hapus
      console.log('Deleting wakil_bupati photo')
    } else if (type === 'sekda') {
      formData.append('sekda_photo', 'DELETE') // String khusus untuk hapus
      console.log('Deleting sekda photo')
    }

    console.log(`Deleting ${type} photo, keeping others`)
    const response = await websiteStore.updateWebsite(leadershipSetting.id, formData)
    console.log('Delete response:', response)
    
    alert(`Foto ${photoNames[type]} berhasil dihapus`)
    
    // Update currentPhotos langsung dari response
    if (response) {
      currentPhotos.value = {
        bupati: response.bupati_photo || null,
        wakil_bupati: response.wakil_bupati_photo || null,
        sekda: response.sekda_photo || null
      }
    }
    
    // Reload data untuk memastikan
    await loadWebsite()
    
    // Update currentPhotos lagi setelah reload
    setTimeout(() => {
      const updated = websiteList.value.find(item => 
        item.key === 'bupati_photo' || item.key === 'leadership_photos'
      )
      if (updated) {
        currentPhotos.value = {
          bupati: updated.bupati_photo || null,
          wakil_bupati: updated.wakil_bupati_photo || null,
          sekda: updated.sekda_photo || null
        }
        console.log('CurrentPhotos after delete:', currentPhotos.value)
      }
    }, 500)
  } catch (error) {
    console.error('Error deleting photo:', error)
    console.error('Error response:', error.response)
    alert('Gagal menghapus foto: ' + (error.response?.data?.message || error.message))
  }
}

async function saveLeadershipPhotos() {
  // Check if at least one photo is selected
  if (!photoFiles.value.bupati && !photoFiles.value.wakil_bupati && !photoFiles.value.sekda) {
    alert('Silakan pilih minimal satu foto untuk diupload')
    return
  }

  leadershipPhotosUploading.value = true
  try {
    // Cari atau buat setting untuk foto kepemimpinan (gunakan key bupati_photo untuk kompatibilitas)
    let leadershipSetting = websiteList.value.find(item => 
      item.key === 'bupati_photo' || item.key === 'leadership_photos'
    )
    
    const formData = new FormData()
    
    // Append photos if selected (hanya kirim foto yang baru dipilih)
    console.log('Photo files before append:', {
      bupati: photoFiles.value.bupati ? photoFiles.value.bupati.name : null,
      wakil_bupati: photoFiles.value.wakil_bupati ? photoFiles.value.wakil_bupati.name : null,
      sekda: photoFiles.value.sekda ? photoFiles.value.sekda.name : null
    })
    
    if (photoFiles.value.bupati) {
      // Ensure file is a File object
      if (photoFiles.value.bupati instanceof File) {
        formData.append('bupati_photo', photoFiles.value.bupati, photoFiles.value.bupati.name)
        console.log('✓ Appended bupati photo:', photoFiles.value.bupati.name, photoFiles.value.bupati.size, 'bytes', photoFiles.value.bupati.type)
      } else {
        console.error('❌ bupati photo is not a File object:', typeof photoFiles.value.bupati, photoFiles.value.bupati)
      }
    }
    if (photoFiles.value.wakil_bupati) {
      // Ensure file is a File object
      if (photoFiles.value.wakil_bupati instanceof File) {
        formData.append('wakil_bupati_photo', photoFiles.value.wakil_bupati, photoFiles.value.wakil_bupati.name)
        console.log('✓ Appended wakil_bupati photo:', photoFiles.value.wakil_bupati.name, photoFiles.value.wakil_bupati.size, 'bytes', photoFiles.value.wakil_bupati.type)
      } else {
        console.error('❌ wakil_bupati photo is not a File object:', typeof photoFiles.value.wakil_bupati, photoFiles.value.wakil_bupati)
      }
    }
    if (photoFiles.value.sekda) {
      // Ensure file is a File object
      if (photoFiles.value.sekda instanceof File) {
        formData.append('sekda_photo', photoFiles.value.sekda, photoFiles.value.sekda.name)
        console.log('✓ Appended sekda photo:', photoFiles.value.sekda.name, photoFiles.value.sekda.size, 'bytes', photoFiles.value.sekda.type)
      } else {
        console.error('❌ sekda photo is not a File object:', typeof photoFiles.value.sekda, photoFiles.value.sekda)
      }
    }
    
    // Debug: Log FormData contents
    console.log('FormData entries:')
    for (let pair of formData.entries()) {
      console.log('  ', pair[0], ':', pair[1] instanceof File ? `File(${pair[1].name}, ${pair[1].size} bytes)` : pair[1])
    }
    
    formData.append('key', 'bupati_photo')
    formData.append('title', 'Foto Kepemimpinan')
    formData.append('type', 'image')
    formData.append('is_active', '1')
    formData.append('order', '0')

    if (leadershipSetting) {
      // Update existing
      console.log('Updating leadership photos, setting ID:', leadershipSetting.id)
      console.log('Current photos before update:', {
        bupati: leadershipSetting.bupati_photo,
        wakil_bupati: leadershipSetting.wakil_bupati_photo,
        sekda: leadershipSetting.sekda_photo
      })
      
      // Verify FormData before sending
      console.log('FormData verification before send:')
      const formDataEntries = []
      for (let pair of formData.entries()) {
        if (pair[1] instanceof File) {
          formDataEntries.push(`${pair[0]}: File(${pair[1].name}, ${pair[1].size} bytes, ${pair[1].type})`)
        } else {
          formDataEntries.push(`${pair[0]}: ${pair[1]}`)
        }
      }
      console.log('FormData entries:', formDataEntries)
      
      try {
        const response = await websiteStore.updateWebsite(leadershipSetting.id, formData)
        console.log('Update response:', response)
        
        // Update currentPhotos immediately from response
        if (response) {
          console.log('Updating currentPhotos from response:', {
            bupati: response.bupati_photo,
            wakil_bupati: response.wakil_bupati_photo,
            sekda: response.sekda_photo
          })
          // Force update dengan membuat object baru untuk trigger reactivity dan force re-render
          // Gunakan response data langsung, jangan fallback ke currentPhotos karena bisa foto lama
          currentPhotos.value = {
            bupati: response.bupati_photo || null,
            wakil_bupati: response.wakil_bupati_photo || null,
            sekda: response.sekda_photo || null
          }
          console.log('CurrentPhotos updated:', currentPhotos.value)
        }
        
        alert('Foto kepemimpinan berhasil disimpan')
        
        // Reset previews and files
        photoFiles.value = { bupati: null, wakil_bupati: null, sekda: null }
        photoPreviews.value = { bupati: null, wakil_bupati: null, sekda: null }
        
        // Reload website list to get updated data
        await loadWebsite()
      } catch (error) {
        console.error('Error saving leadership photos:', error)
        console.error('Error response:', error.response)
        const errorMessage = error.response?.data?.message || error.message || 'Gagal menyimpan foto kepemimpinan'
        alert(`Error: ${errorMessage}`)
        throw error
      }
    } else {
      // Create new
      console.log('Creating new leadership photos setting')
      try {
        const response = await websiteStore.createWebsite(formData)
        console.log('Create response:', response)
        
        alert('Foto kepemimpinan berhasil disimpan')
        
        // Reset previews and files
        photoFiles.value = { bupati: null, wakil_bupati: null, sekda: null }
        photoPreviews.value = { bupati: null, wakil_bupati: null, sekda: null }
        
        // Reload website list to get updated data
        await loadWebsite()
      } catch (error) {
        console.error('Error creating leadership photos:', error)
        console.error('Error response:', error.response)
        const errorMessage = error.response?.data?.message || error.message || 'Gagal menyimpan foto kepemimpinan'
        alert(`Error: ${errorMessage}`)
        throw error
      }
    }
    
    // Reset file inputs
    if (bupatiFileInput.value) {
      bupatiFileInput.value.value = ''
    }
    if (wakilBupatiFileInput.value) {
      wakilBupatiFileInput.value.value = ''
    }
    if (sekdaFileInput.value) {
      sekdaFileInput.value.value = ''
    }
    
    // Reload data
    await loadWebsite()
    
    // Reload current photos setelah delay untuk memastikan data ter-update
    setTimeout(() => {
      const updated = websiteList.value.find(item => 
        item.key === 'bupati_photo' || item.key === 'leadership_photos'
      )
      if (updated) {
        console.log('Updated photos after reload:', {
          bupati: updated.bupati_photo,
          wakil_bupati: updated.wakil_bupati_photo,
          sekda: updated.sekda_photo
        })
        currentPhotos.value.bupati = updated.bupati_photo
        currentPhotos.value.wakil_bupati = updated.wakil_bupati_photo
        currentPhotos.value.sekda = updated.sekda_photo
      } else {
        console.warn('Leadership setting not found after update')
      }
    }, 1000)
  } catch (error) {
    console.error('Error saving leadership photos:', error)
    console.error('Error response:', error.response)
    console.error('Error details:', error.response?.data)
    alert('Gagal menyimpan foto kepemimpinan: ' + (error.response?.data?.message || error.message || 'Unknown error'))
  } finally {
    leadershipPhotosUploading.value = false
  }
}

onMounted(() => {
  loadWebsite()
  loadSlideshow()
  
  // Load current leadership photos after website loads
  setTimeout(() => {
    const leadershipSetting = websiteList.value.find(item => 
      item.key === 'bupati_photo' || item.key === 'leadership_photos'
    )
    if (leadershipSetting) {
      currentPhotos.value.bupati = leadershipSetting.bupati_photo
      currentPhotos.value.wakil_bupati = leadershipSetting.wakil_bupati_photo
      currentPhotos.value.sekda = leadershipSetting.sekda_photo
    }
  }, 1000)
})
</script>
