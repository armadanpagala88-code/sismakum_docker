<template>
  <div class="pengusulan-detail">
    <div v-if="loading" class="loading">Memuat data...</div>
    <div v-else-if="!pengusulan" class="empty-state">Data tidak ditemukan</div>
    <div v-else>
      <div class="detail-header">
        <div>
          <h1 class="page-title">{{ pengusulan.judul_perbub }}</h1>
          <span :class="`badge badge-${pengusulan.status}`">
            {{ pengusulan.status }}
          </span>
        </div>
        <div class="header-actions">
          <router-link
            v-if="pengusulan.status === 'draft' && pengusulan.dinas_id === user?.id"
            :to="`/dashboard/pengusulan/${pengusulan.id}/edit`"
            class="btn btn-secondary"
          >
            Edit
          </router-link>
          <button
            v-if="pengusulan.status === 'draft' && pengusulan.dinas_id === user?.id"
            @click="handleAjukan"
            class="btn btn-primary"
            :disabled="submitting"
          >
            Ajukan ke Bagian Hukum
          </button>
        </div>
      </div>
      
      <div class="detail-content">
        <div class="detail-section">
          <h3>Informasi Umum</h3>
          <div class="detail-grid">
            <div>
              <strong>Nomor Surat:</strong>
              <p>{{ pengusulan.nomor_surat }}</p>
            </div>
            <div>
              <strong>Tanggal Surat:</strong>
              <p>{{ formatDate(pengusulan.tanggal_surat) }}</p>
            </div>
            <div v-if="pengusulan.kategori_dokumen">
              <strong>Kategori Dokumen:</strong>
              <p>{{ pengusulan.kategori_dokumen.nama }} ({{ pengusulan.kategori_dokumen.kode }})</p>
            </div>
            <div>
              <strong>Dinas Pengusul:</strong>
              <p>{{ pengusulan.dinas?.name }}</p>
            </div>
            <div v-if="pengusulan.reviewer">
              <strong>Direview Oleh:</strong>
              <p>{{ pengusulan.reviewer?.name }}</p>
            </div>
            <div v-if="pengusulan.reviewed_at">
              <strong>Tanggal Review:</strong>
              <p>{{ formatDateTime(pengusulan.reviewed_at) }}</p>
            </div>
          </div>
        </div>
        
        <div class="detail-section">
          <h3>Latar Belakang</h3>
          <p>{{ pengusulan.latar_belakang }}</p>
        </div>
        
        <div class="detail-section">
          <h3>Maksud dan Tujuan</h3>
          <p>{{ pengusulan.maksud_dan_tujuan }}</p>
        </div>
        
        <div class="detail-section">
          <h3>Ruang Lingkup</h3>
          <p>{{ pengusulan.ruang_lingkup }}</p>
        </div>
        
        <!-- Status Progress -->
        <div v-if="pengusulan.status !== 'draft' && pengusulan.status !== 'revisi'" class="detail-section">
          <div class="flex justify-between items-center mb-4">
            <h3>Progress Usulan</h3>
            <div v-if="(user?.role === 'admin' || user?.role === 'verifikator' || user?.role === 'bagian_hukum') && pengusulan.status !== 'ditolak' && pengusulan.status !== 'selesai'" class="flex items-center space-x-2">
              <select
                v-model="selectedNextStatus"
                class="form-select text-sm"
                @change="handleStatusChange"
              >
                <option value="">Ubah Status ke...</option>
                <option
                  v-for="nextStep in getAvailableNextSteps()"
                  :key="nextStep.status"
                  :value="nextStep.status"
                >
                  {{ nextStep.label }}
                </option>
                <option value="ditolak">Ditolak</option>
              </select>
            </div>
          </div>
          <div class="status-progress-container">
            <!-- Progress Bar untuk status normal -->
            <div v-if="pengusulan.status !== 'ditolak'" class="status-progress">
              <div
                v-for="(step, index) in statusSteps"
                :key="step.status"
                class="status-step"
                :class="getStepClass(step.status, index)"
              >
                <div class="step-indicator">
                  <div class="step-circle">
                    <svg v-if="isStepCompleted(step.status)" class="step-check" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span v-else class="step-number">{{ index + 1 }}</span>
                  </div>
                  <div v-if="index < statusSteps.length - 1" class="step-connector" :class="{'completed': isStepCompleted(step.status), 'active': isStepActive(step.status)}"></div>
                </div>
                <div class="step-content">
                  <div class="flex items-center justify-between">
                    <div>
                      <div class="step-label">{{ step.label }}</div>
                      <div v-if="getStepDate(step.status)" class="step-date">
                        {{ getStepDate(step.status) }}
                      </div>
                    </div>
                    <div class="flex items-center space-x-2 ml-4">
                      <button
                        v-if="(user?.role === 'admin' || user?.role === 'verifikator' || user?.role === 'bagian_hukum') && canMoveToStep(step.status) && pengusulan.status !== 'selesai'"
                        @click="moveToStep(step.status)"
                        class="btn btn-sm btn-primary"
                        :disabled="updatingStatus"
                      >
                        {{ updatingStatus ? 'Memproses...' : 'Pindah ke Sini' }}
                      </button>
                      <button
                        v-if="(user?.role === 'admin' || user?.role === 'verifikator' || user?.role === 'bagian_hukum') && canRollbackToStep(step.status) && pengusulan.status !== 'selesai'"
                        @click="rollbackToStep(step.status)"
                        class="btn btn-sm btn-secondary"
                        :disabled="updatingStatus"
                      >
                        {{ updatingStatus ? 'Memproses...' : 'Rollback ke Sini' }}
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- Status Ditolak -->
            <div v-else class="status-progress">
              <div class="status-step rejected">
                <div class="step-indicator">
                  <div class="step-circle">
                    <svg class="step-check" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                  </div>
                </div>
                <div class="step-content">
                  <div class="step-label">Ditolak</div>
                  <div v-if="getStepDate('ditolak')" class="step-date">
                    {{ getStepDate('ditolak') }}
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Status Tracking (untuk draft dan revisi) -->
        <div v-else class="detail-section">
          <h3>Status & Tracking</h3>
          <div class="status-tracking">
            <div class="status-current mb-6">
              <div class="flex items-center space-x-3">
                <span :class="`badge badge-${pengusulan.status} badge-lg`">
                  {{ getStatusLabel(pengusulan.status) }}
                </span>
                <span class="text-sm text-gray-500">
                  Status saat ini
                </span>
              </div>
            </div>
            
            <div v-if="statusHistory && statusHistory.length > 0" class="status-timeline">
              <h4 class="text-sm font-semibold text-gray-700 mb-4">Riwayat Status</h4>
              <div class="space-y-4">
                <div
                  v-for="(history, index) in statusHistory"
                  :key="history.id"
                  class="timeline-item"
                >
                  <div class="flex items-start space-x-4">
                    <div class="flex-shrink-0">
                      <div class="timeline-dot" :class="{'active': index === 0}"></div>
                      <div v-if="index < statusHistory.length - 1" class="timeline-line"></div>
                    </div>
                    <div class="flex-1">
                      <div class="flex items-center space-x-2 mb-1">
                        <span :class="`badge badge-${history.status}`">
                          {{ getStatusLabel(history.status) }}
                        </span>
                        <span class="text-xs text-gray-500">
                          {{ formatDateTime(history.created_at) }}
                        </span>
                      </div>
                      <p v-if="history.catatan" class="text-sm text-gray-700 mb-1">
                        {{ history.catatan }}
                      </p>
                      <p v-if="history.updated_by" class="text-xs text-gray-500">
                        Oleh: {{ history.updated_by?.name || 'System' }}
                      </p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div v-else class="text-sm text-gray-500">
              Belum ada riwayat status
            </div>
          </div>
        </div>
        
        <!-- Catatan Revisi -->
        <div v-if="pengusulan.catatan_revisi && pengusulan.catatan_revisi.length > 0" class="detail-section">
          <h3>Catatan Revisi</h3>
          <div class="space-y-4">
            <div
              v-for="catatan in pengusulan.catatan_revisi"
              :key="catatan.id"
              :class="getCatatanClass(catatan.tipe)"
              class="p-4 rounded-lg border-l-4"
            >
              <div class="flex justify-between items-start mb-2">
                <span :class="getTipeBadgeClass(catatan.tipe)" class="px-2 py-1 text-xs font-semibold rounded">
                  {{ getTipeLabel(catatan.tipe) }}
                </span>
                <span class="text-xs text-gray-500">
                  {{ formatDateTime(catatan.created_at) }} oleh {{ catatan.creator?.name }}
                </span>
              </div>
              <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ catatan.catatan }}</p>
              <!-- File Download Link -->
              <div v-if="catatan.file_path" class="mt-3 p-2 bg-blue-50 rounded">
                <a
                  :href="`${apiBaseUrl}/storage/${catatan.file_path}`"
                  target="_blank"
                  class="inline-flex items-center text-sm text-blue-600 hover:text-blue-800 font-medium"
                  @click.prevent="downloadReviewFile(catatan)"
                >
                  📄 {{ catatan.file_name || 'Download Dokumen Review' }}
                </a>
              </div>
              <div v-if="catatan.is_resolved" class="mt-2 text-xs text-green-600">
                ✓ Telah diselesaikan pada {{ formatDateTime(catatan.resolved_at) }}
              </div>
            </div>
          </div>
        </div>
        
        <div v-if="pengusulan.catatan && !pengusulan.catatan_revisi" class="detail-section">
          <h3>Catatan Review</h3>
          <div class="catatan-box">
            <p>{{ pengusulan.catatan }}</p>
          </div>
        </div>
        
        <div v-if="pengusulan.dokumen && pengusulan.dokumen.length > 0" class="detail-section">
          <h3>Dokumen Pendukung</h3>
          <div class="dokumen-list">
            <div
              v-for="dok in pengusulan.dokumen"
              :key="dok.id"
              class="dokumen-item"
            >
              <span>{{ dok.nama_file }}</span>
              <a 
                :href="`${apiBaseUrl}/api/dokumen/${encodeURIComponent(dok.path_file)}`" 
                target="_blank" 
                class="btn btn-secondary"
                @click.prevent="downloadDokumen(dok)"
              >
                Download
              </a>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Form Update Setelah Revisi -->
      <div v-if="pengusulan.status === 'revisi' && pengusulan.dinas_id === user?.id" class="review-section">
        <h3>Lengkapi Usulan Setelah Revisi</h3>
        <form @submit.prevent="handleUpdateRevisi" class="review-form">
          <div class="form-group">
            <label class="form-label">Nomor Surat *</label>
            <input v-model="revisiForm.nomor_surat" type="text" class="form-input" required />
          </div>
          <div class="form-group">
            <label class="form-label">Tanggal Surat *</label>
            <input v-model="revisiForm.tanggal_surat" type="date" class="form-input" required />
          </div>
          <div class="form-group">
            <label class="form-label">Kategori Dokumen</label>
            <select
              v-model="revisiForm.kategori_dokumen_id"
              class="form-select"
            >
              <option value="">Pilih Kategori</option>
              <option
                v-for="kategori in kategoriList"
                :key="kategori.id"
                :value="kategori.id"
              >
                {{ kategori.nama }} ({{ kategori.kode }})
              </option>
            </select>
            <small class="form-help">Pilih kategori dokumen yang diusulkan</small>
          </div>
          <div class="form-group">
            <label class="form-label">Judul PERBUB *</label>
            <input v-model="revisiForm.judul_perbub" type="text" class="form-input" required />
          </div>
          <div class="form-group">
            <label class="form-label">Latar Belakang *</label>
            <textarea v-model="revisiForm.latar_belakang" rows="4" class="form-textarea" required></textarea>
          </div>
          <div class="form-group">
            <label class="form-label">Maksud dan Tujuan *</label>
            <textarea v-model="revisiForm.maksud_dan_tujuan" rows="4" class="form-textarea" required></textarea>
          </div>
          <div class="form-group">
            <label class="form-label">Ruang Lingkup *</label>
            <textarea v-model="revisiForm.ruang_lingkup" rows="4" class="form-textarea" required></textarea>
          </div>
          <div class="form-group">
            <label class="form-label">Dokumen Revisi</label>
            <input
              type="file"
              multiple
              accept=".pdf,.doc,.docx"
              @change="handleFileChange"
              class="form-input"
            />
            <small class="text-gray-500">Format: PDF, DOC, DOCX (maks 10MB per file)</small>
          </div>
          <button type="submit" class="btn btn-primary" :disabled="submitting">
            {{ submitting ? 'Memproses...' : 'Kirim Revisi' }}
          </button>
        </form>
      </div>

      <div v-if="(user?.role === 'bagian_hukum' || user?.role === 'verifikator' || user?.role === 'admin') && pengusulan.status === 'diajukan'" class="review-section">
        <h3>Review Pengusulan</h3>
        <form @submit.prevent="handleReview" class="review-form">
          <div class="form-group">
            <label class="form-label">Status *</label>
            <select v-model="reviewForm.status" class="form-input" required>
              <option value="diterima">Diterima</option>
              <option value="ditolak">Ditolak</option>
              <option value="revisi">Revisi</option>
            </select>
          </div>
          
          <div class="form-group">
            <label class="form-label">Catatan *</label>
            <textarea
              v-model="reviewForm.catatan"
              class="form-textarea"
              placeholder="Masukkan catatan review"
              required
            ></textarea>
          </div>
          
          <button type="submit" class="btn btn-primary" :disabled="submitting">
            {{ submitting ? 'Memproses...' : 'Submit Review' }}
          </button>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { usePengusulanStore } from '../stores/pengusulan'
import { useAuthStore } from '../stores/auth'
import { useKategoriDokumenStore } from '../stores/kategoriDokumen'
import api from '../services/api'

const route = useRoute()
const router = useRouter()
const pengusulanStore = usePengusulanStore()
const authStore = useAuthStore()
const kategoriDokumenStore = useKategoriDokumenStore()

const loading = ref(false)
const submitting = ref(false)

const pengusulan = computed(() => pengusulanStore.currentPengusulan)
const user = computed(() => authStore.user)
const apiBaseUrl = import.meta.env.VITE_API_BASE_URL || window.location.origin
const kategoriList = ref([])
const statusHistory = ref([])
const selectedNextStatus = ref('')
const updatingStatus = ref(false)

const reviewForm = ref({
  status: 'diterima',
  catatan: ''
})

const revisiForm = ref({
  nomor_surat: '',
  tanggal_surat: '',
  kategori_dokumen_id: '',
  judul_perbub: '',
  latar_belakang: '',
  maksud_dan_tujuan: '',
  ruang_lingkup: '',
  dokumen: []
})

function formatDate(date) {
  return new Date(date).toLocaleDateString('id-ID')
}

function formatDateTime(date) {
  return new Date(date).toLocaleString('id-ID')
}

function getCatatanClass(tipe) {
  const classes = {
    revisi: 'bg-yellow-50 border-yellow-300',
    tolak: 'bg-red-50 border-red-300',
    catatan: 'bg-blue-50 border-blue-300'
  }
  return classes[tipe] || 'bg-gray-50 border-gray-300'
}

function getTipeBadgeClass(tipe) {
  const classes = {
    revisi: 'bg-yellow-100 text-yellow-800',
    tolak: 'bg-red-100 text-red-800',
    catatan: 'bg-blue-100 text-blue-800'
  }
  return classes[tipe] || 'bg-gray-100 text-gray-800'
}

function getTipeLabel(tipe) {
  const labels = {
    revisi: 'Revisi',
    tolak: 'Ditolak',
    catatan: 'Catatan'
  }
  return labels[tipe] || tipe
}

async function downloadDokumen(dok) {
  try {
    const token = localStorage.getItem('token')
    const url = `${apiBaseUrl}/api/dokumen/${encodeURIComponent(dok.path_file)}`
    
    const response = await fetch(url, {
      method: 'GET',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/octet-stream, */*'
      }
    })
    
    if (!response.ok) {
      if (response.status === 401) {
        alert('Session expired. Silakan login kembali.')
        router.push('/login')
        return
      }
      throw new Error(`Failed to download file: ${response.statusText}`)
    }
    
    const blob = await response.blob()
    const downloadUrl = window.URL.createObjectURL(blob)
    const link = document.createElement('a')
    link.href = downloadUrl
    link.download = dok.nama_file || 'document'
    document.body.appendChild(link)
    link.click()
    document.body.removeChild(link)
    window.URL.revokeObjectURL(downloadUrl)
  } catch (error) {
    console.error('Error downloading file:', error)
    alert('Gagal mengunduh dokumen: ' + error.message)
  }
}


async function downloadReviewFile(catatan) {
  try {
    const token = localStorage.getItem('token')
    const url = `${apiBaseUrl}/api/dokumen/${encodeURIComponent(catatan.file_path)}`
    
    const response = await fetch(url, {
      method: 'GET',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/octet-stream, */*'
      }
    })
    
    if (!response.ok) {
      if (response.status === 401) {
        alert('Session expired. Silakan login kembali.')
        router.push('/login')
        return
      }
      throw new Error(`Failed to download file: ${response.statusText}`)
    }
    
    const blob = await response.blob()
    const downloadUrl = window.URL.createObjectURL(blob)
    const link = document.createElement('a')
    link.href = downloadUrl
    link.download = catatan.file_name || 'review_document'
    document.body.appendChild(link)
    link.click()
    document.body.removeChild(link)
    window.URL.revokeObjectURL(downloadUrl)
  } catch (error) {
    console.error('Error downloading review file:', error)
    alert('Gagal mengunduh dokumen review: ' + error.message)
  }
}
async function loadDetail() {
  loading.value = true
  try {
    await pengusulanStore.fetchDetail(route.params.id)
    
    // Load status history
    await loadStatusHistory()
    
    // Populate revisi form if status is revisi
    if (pengusulan.value && pengusulan.value.status === 'revisi') {
      const tanggalSurat = pengusulan.value.tanggal_surat
        ? new Date(pengusulan.value.tanggal_surat).toISOString().split('T')[0]
        : ''
      revisiForm.value = {
        nomor_surat: pengusulan.value.nomor_surat || '',
        tanggal_surat: tanggalSurat,
        kategori_dokumen_id: pengusulan.value.kategori_dokumen_id || '',
        judul_perbub: pengusulan.value.judul_perbub || '',
        latar_belakang: pengusulan.value.latar_belakang || '',
        maksud_dan_tujuan: pengusulan.value.maksud_dan_tujuan || '',
        ruang_lingkup: pengusulan.value.ruang_lingkup || '',
        dokumen: []
      }
    }
  } catch (error) {
    console.error(error)
  } finally {
    loading.value = false
  }
}

async function loadStatusHistory() {
  try {
    const response = await api.get(`/pengusulan-perbub/${route.params.id}/status-history`)
    statusHistory.value = response.data || []
  } catch (error) {
    console.error('Error loading status history:', error)
    // Fallback to status_history from pengusulan if available
    if (pengusulan.value && pengusulan.value.status_history) {
      statusHistory.value = pengusulan.value.status_history
    }
  }
}

function getStatusLabel(status) {
  const labels = {
    'draft': 'Draft',
    'diajukan': 'Diajukan',
    'usulan': 'Usulan',
    'pengkajian_usulan': 'Pengkajian Usulan',
    'pengkajian_draf': 'Pengkajian Draf',
    'harmonisasi_kemenkumham': 'Harmonisasi Kemenkumham',
    'fasilitasi_biro_hukum_provinsi': 'Fasilitasi Biro Hukum Provinsi',
    'diterima': 'Diterima',
    'selesai': 'Selesai',
    'ditolak': 'Ditolak',
    'revisi': 'Revisi',
  }
  return labels[status] || status
}

// Status steps untuk progress bar
const statusSteps = [
  { status: 'usulan', label: 'Usulan' },
  { status: 'pengkajian_usulan', label: 'Pengkajian Usulan' },
  { status: 'pengkajian_draf', label: 'Pengkajian Draf' },
  { status: 'harmonisasi_kemenkumham', label: 'Harmonisasi Kemenkumham' },
  { status: 'fasilitasi_biro_hukum_provinsi', label: 'Fasilitasi Biro Hukum Provinsi' },
  { status: 'selesai', label: 'Selesai' },
]

// Fungsi untuk menentukan urutan status
function getStatusOrder(status) {
  const order = {
    'diajukan': 0.5, // Status awal, sebelum usulan
    'diterima': 0.5, // Status awal, sebelum usulan
    'usulan': 1,
    'pengkajian_usulan': 2,
    'pengkajian_draf': 3,
    'harmonisasi_kemenkumham': 4,
    'fasilitasi_biro_hukum_provinsi': 5,
    'selesai': 6,
    'ditolak': -1, // Status khusus
  }
  return order[status] || 0
}

// Cek apakah step sudah completed
function isStepCompleted(stepStatus) {
  const currentStatus = pengusulan.value?.status
  const currentOrder = getStatusOrder(currentStatus)
  const stepOrder = getStatusOrder(stepStatus)
  
  // Jika status ditolak, tidak ada yang completed
  if (currentStatus === 'ditolak') {
    return false
  }
  
  // Jika status diajukan atau diterima, semua step masih pending
  if (currentStatus === 'diajukan' || currentStatus === 'diterima') {
    return false
  }
  
  return stepOrder > 0 && stepOrder < currentOrder
}

// Cek apakah step sedang active
function isStepActive(stepStatus) {
  const currentStatus = pengusulan.value?.status
  
  // Jika status diajukan atau diterima, step pertama (usulan) adalah active
  if ((currentStatus === 'diajukan' || currentStatus === 'diterima') && stepStatus === 'usulan') {
    return true
  }
  
  return currentStatus === stepStatus
}

// Get class untuk step
function getStepClass(stepStatus, index) {
  const classes = []
  
  if (pengusulan.value?.status === 'ditolak') {
    classes.push('rejected')
  } else if (isStepCompleted(stepStatus)) {
    classes.push('completed')
  } else if (isStepActive(stepStatus)) {
    classes.push('active')
  } else {
    classes.push('pending')
  }
  
  return classes.join(' ')
}

// Get tanggal untuk step dari statusHistory
function getStepDate(stepStatus) {
  if (!statusHistory.value || statusHistory.value.length === 0) {
    return null
  }
  
  const history = statusHistory.value.find(h => h.status === stepStatus)
  if (history && history.created_at) {
    return formatDate(history.created_at)
  }
  
  return null
}

// Get available next steps untuk dropdown
function getAvailableNextSteps() {
  const currentStatus = pengusulan.value?.status
  const currentOrder = getStatusOrder(currentStatus)
  
  // Jika status diajukan atau diterima, next step adalah usulan
  if (currentStatus === 'diajukan' || currentStatus === 'diterima') {
    return statusSteps.filter(s => getStatusOrder(s.status) === 1)
  }
  
  // Return semua step yang order-nya lebih besar dari current
  return statusSteps.filter(s => {
    const stepOrder = getStatusOrder(s.status)
    return stepOrder > currentOrder && stepOrder > 0
  })
}

// Cek apakah bisa pindah ke step tertentu
function canMoveToStep(stepStatus) {
  const currentStatus = pengusulan.value?.status
  const currentOrder = getStatusOrder(currentStatus)
  const stepOrder = getStatusOrder(stepStatus)
  
  // Tidak bisa pindah ke step yang sudah completed atau step yang sama
  if (isStepCompleted(stepStatus) || currentStatus === stepStatus) {
    return false
  }
  
  // Bisa pindah ke step berikutnya atau step yang lebih maju
  return stepOrder > currentOrder || (currentStatus === 'diajukan' && stepStatus === 'usulan')
}

// Pindah ke step tertentu
async function moveToStep(stepStatus) {
  if (!confirm(`Yakin ingin mengubah status menjadi "${getStatusLabel(stepStatus)}"?`)) {
    return
  }
  
  updatingStatus.value = true
  try {
    await pengusulanStore.updateStatus(pengusulan.value.id, stepStatus, `Status diubah menjadi ${getStatusLabel(stepStatus)}`)
    await loadStatusHistory()
    await loadDetail()
    alert('Status berhasil diubah')
  } catch (error) {
    console.error('Error updating status:', error)
    alert('Gagal mengubah status: ' + (error.response?.data?.message || error.message))
  } finally {
    updatingStatus.value = false
  }
}

// Handle status change dari dropdown
async function handleStatusChange() {
  if (!selectedNextStatus.value) {
    return
  }
  
  await moveToStep(selectedNextStatus.value)
  selectedNextStatus.value = ''
}

// Cek apakah bisa rollback ke step tertentu
function canRollbackToStep(stepStatus) {
  const currentStatus = pengusulan.value?.status
  const currentOrder = getStatusOrder(currentStatus)
  const stepOrder = getStatusOrder(stepStatus)
  
  // Tidak bisa rollback ke step yang sama atau step yang belum dicapai
  if (currentStatus === stepStatus || !isStepCompleted(stepStatus)) {
    return false
  }
  
  // Bisa rollback ke step yang sudah completed
  return stepOrder > 0 && stepOrder < currentOrder
}

// Rollback ke step tertentu
async function rollbackToStep(stepStatus) {
  if (!confirm(`Yakin ingin rollback status menjadi "${getStatusLabel(stepStatus)}"?`)) {
    return
  }
  
  updatingStatus.value = true
  try {
    await pengusulanStore.updateStatus(pengusulan.value.id, stepStatus, `Status di-rollback menjadi ${getStatusLabel(stepStatus)}`)
    await loadStatusHistory()
    await loadDetail()
    alert('Status berhasil di-rollback')
  } catch (error) {
    console.error('Error rolling back status:', error)
    alert('Gagal rollback status: ' + (error.response?.data?.message || error.message))
  } finally {
    updatingStatus.value = false
  }
}

function handleFileChange(event) {
  revisiForm.value.dokumen = Array.from(event.target.files)
}

async function handleAjukan() {
  submitting.value = true
  try {
    await pengusulanStore.ajukanPengusulan(route.params.id)
    await loadDetail()
  } catch (error) {
    console.error(error)
    alert('Gagal mengajukan pengusulan')
  } finally {
    submitting.value = false
  }
}

async function handleReview() {
  submitting.value = true
  try {
    await pengusulanStore.reviewPengusulan(
      route.params.id,
      reviewForm.value.status,
      reviewForm.value.catatan
    )
    await loadDetail()
    reviewForm.value = { status: 'diterima', catatan: '' }
    alert('Review berhasil disimpan')
  } catch (error) {
    console.error(error)
    alert(error.response?.data?.message || 'Gagal melakukan review')
  } finally {
    submitting.value = false
  }
}

async function handleUpdateRevisi() {
  submitting.value = true
  try {
    await pengusulanStore.updateAfterRevisi(route.params.id, revisiForm.value)
    await loadDetail()
    alert('Revisi berhasil dikirim')
    router.push('/dashboard/pengusulan')
  } catch (error) {
    console.error(error)
    alert(error.response?.data?.message || 'Gagal mengirim revisi')
  } finally {
    submitting.value = false
  }
}

async function loadKategori() {
  try {
    await kategoriDokumenStore.fetchActive()
    kategoriList.value = kategoriDokumenStore.kategoriList
  } catch (err) {
    console.error('Error loading kategori:', err)
  }
}

onMounted(() => {
  loadKategori()
  loadDetail()
})
</script>

<style scoped>
.pengusulan-detail {
  max-width: 1000px;
}

.detail-header {
  display: flex;
  justify-content: space-between;
  align-items: start;
  margin-bottom: 2rem;
  padding-bottom: 1rem;
  border-bottom: 2px solid #e5e7eb;
}

.page-title {
  font-size: 2rem;
  font-weight: 700;
  color: #1f2937;
  margin-bottom: 0.5rem;
}

.header-actions {
  display: flex;
  gap: 0.5rem;
}

.detail-content {
  background: white;
  padding: 2rem;
  border-radius: 0.5rem;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.detail-section {
  margin-bottom: 2rem;
}

.detail-section h3 {
  font-size: 1.25rem;
  font-weight: 600;
  color: #1f2937;
  margin-bottom: 1rem;
  padding-bottom: 0.5rem;
  border-bottom: 1px solid #e5e7eb;
}

.detail-section p {
  color: #4b5563;
  line-height: 1.6;
}

.detail-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 1rem;
}

.detail-grid div {
  padding: 0.75rem;
  background: #f9fafb;
  border-radius: 0.375rem;
}

.detail-grid strong {
  display: block;
  color: #6b7280;
  font-size: 0.875rem;
  margin-bottom: 0.25rem;
}

.detail-grid p {
  color: #1f2937;
  font-weight: 500;
}

.catatan-box {
  background: #fef3c7;
  border-left: 4px solid #f59e0b;
  padding: 1rem;
  border-radius: 0.375rem;
}

.dokumen-list {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.dokumen-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0.75rem;
  background: #f9fafb;
  border-radius: 0.375rem;
}

.review-section {
  margin-top: 2rem;
  background: white;
  padding: 2rem;
  border-radius: 0.5rem;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.review-section h3 {
  font-size: 1.25rem;
  font-weight: 600;
  margin-bottom: 1.5rem;
  color: #1f2937;
}

.review-form {
  max-width: 600px;
}

.loading, .empty-state {
  text-align: center;
  padding: 3rem;
  color: #6b7280;
}
</style>

