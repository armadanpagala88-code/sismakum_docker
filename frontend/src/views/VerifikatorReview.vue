<template>
  <div class="space-y-6" v-if="pengusulan">
    <div>
      <router-link
        to="/dashboard/verifikator"
        class="text-blue-600 hover:text-blue-800 mb-4 inline-block"
      >
        ← Kembali
      </router-link>
      <h1 class="text-2xl font-bold text-gray-900">Review Usulan PERBUB</h1>
    </div>

    <!-- Usulan Details -->
    <div class="bg-white rounded-lg shadow p-6 space-y-4">
      <div>
        <label class="block text-sm font-medium text-gray-700">Judul PERBUB</label>
        <p class="mt-1 text-gray-900">{{ pengusulan.judul_perbub }}</p>
      </div>
      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700">Nomor Surat</label>
          <p class="mt-1 text-gray-900">{{ pengusulan.nomor_surat }}</p>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700">Tanggal Surat</label>
          <p class="mt-1 text-gray-900">{{ formatDate(pengusulan.tanggal_surat) }}</p>
        </div>
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700">Dinas</label>
        <p class="mt-1 text-gray-900">{{ pengusulan.dinas?.name || '-' }}</p>
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700">Latar Belakang</label>
        <p class="mt-1 text-gray-900 whitespace-pre-wrap">{{ pengusulan.latar_belakang }}</p>
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700">Maksud dan Tujuan</label>
        <p class="mt-1 text-gray-900 whitespace-pre-wrap">{{ pengusulan.maksud_dan_tujuan }}</p>
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700">Ruang Lingkup</label>
        <p class="mt-1 text-gray-900 whitespace-pre-wrap">{{ pengusulan.ruang_lingkup }}</p>
      </div>
      <div v-if="pengusulan.dokumen && pengusulan.dokumen.length > 0">
        <label class="block text-sm font-medium text-gray-700 mb-2">Dokumen</label>
        <div class="space-y-2">
          <div
            v-for="doc in pengusulan.dokumen"
            :key="doc.id"
            class="flex items-center justify-between p-3 bg-gray-50 rounded-lg"
          >
            <span class="text-sm text-gray-900">{{ doc.nama_file }}</span>
            <a
              :href="`${apiBaseUrl}/api/dokumen/${encodePathForUrl(doc.path_file)}`"
              target="_blank"
              class="text-blue-600 hover:text-blue-800 text-sm"
              @click.prevent="downloadDokumen(doc)"
            >
              Download
            </a>
          </div>
        </div>
      </div>
    </div>

    <!-- Catatan Revisi Sebelumnya -->
    <div v-if="pengusulan.catatan_revisi && pengusulan.catatan_revisi.length > 0" class="bg-yellow-50 border border-yellow-200 rounded-lg p-6">
      <h2 class="text-lg font-medium text-gray-900 mb-4">Catatan Revisi Sebelumnya</h2>
      <div class="space-y-4">
        <div
          v-for="catatan in pengusulan.catatan_revisi"
          :key="catatan.id"
          class="bg-white rounded-lg p-4 border border-yellow-200"
        >
          <div class="flex justify-between items-start mb-2">
            <span
              :class="getTipeClass(catatan.tipe)"
              class="px-2 py-1 text-xs font-semibold rounded"
            >
              {{ getTipeLabel(catatan.tipe) }}
            </span>
            <span class="text-xs text-gray-500">
              {{ formatDate(catatan.created_at) }} oleh {{ catatan.creator?.name }}
            </span>
          </div>
          <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ catatan.catatan }}</p>
          <!-- Word File Download Link -->
          <div v-if="catatan.file_path" class="mt-2">
            <a
              :href="`${apiBaseUrl}/storage/${catatan.file_path}`"
              target="_blank"
              class="inline-flex items-center text-sm text-blue-600 hover:text-blue-800"
              @click.prevent="downloadReviewFile(catatan)"
            >
              📄 {{ catatan.file_name || 'Download Dokumen Word' }}
            </a>
          </div>
          <!-- PDF File Download Link -->
          <div v-if="catatan.file_review_pdf" class="mt-2">
            <a
              :href="`${apiBaseUrl}/storage/${catatan.file_review_pdf}`"
              target="_blank"
              class="inline-flex items-center text-sm text-red-600 hover:text-red-800"
              @click.prevent="downloadPdfFile(catatan)"
            >
              📕 {{ catatan.file_review_pdf_name || 'Download PDF' }}
            </a>
          </div>
          <div v-if="catatan.is_resolved" class="mt-2 text-xs text-green-600">
            ✓ Telah diselesaikan
          </div>
        </div>
      </div>
    </div>

    <!-- Review Form -->
    <div class="bg-white rounded-lg shadow p-6">
      <h2 class="text-lg font-medium text-gray-900 mb-4">Review Usulan</h2>
      <form @submit.prevent="submitReview" class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Status *</label>
          <select
            v-model="reviewForm.status"
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            required
          >
            <option value="diterima">Diterima</option>
            <option value="revisi">Perlu Revisi</option>
            <option value="ditolak">Ditolak</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Catatan *</label>
          <textarea
            v-model="reviewForm.catatan"
            rows="6"
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            placeholder="Berikan catatan untuk usulan ini..."
            required
          ></textarea>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Upload Dokumen Word (Opsional)</label>
          <input
            type="file"
            @change="handleFileChange"
            accept=".doc,.docx,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document"
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
          />
          <p class="mt-1 text-xs text-gray-500">Format: .doc, .docx (Maks. 10MB)</p>
          <p v-if="reviewForm.file" class="mt-1 text-xs text-green-600">
            File dipilih: {{ reviewForm.file.name }}
          </p>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Upload Dokumen PDF (Opsional)</label>
          <input
            type="file"
            @change="handlePdfChange"
            accept=".pdf,application/pdf"
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
          />
          <p class="mt-1 text-xs text-gray-500">Format: .pdf (Maks. 5MB)</p>
          <p v-if="reviewForm.filePdf" class="mt-1 text-xs text-green-600">
            File dipilih: {{ reviewForm.filePdf.name }}
          </p>
        </div>
        <div class="flex justify-end">
          <button
            type="submit"
            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
          >
            Submit Review
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { usePengusulanStore } from '../stores/pengusulan'
import api from '../services/api'

const route = useRoute()
const router = useRouter()
const pengusulanStore = usePengusulanStore()

const pengusulan = ref(null)
const reviewForm = ref({
  status: 'diterima',
  catatan: '',
  file: null,
  filePdf: null
})
const apiBaseUrl = import.meta.env.VITE_API_BASE_URL || window.location.origin

// Helper function to encode path for URL while preserving slashes
function encodePathForUrl(path) {
  if (!path) return ''
  // Split by slash, encode each segment, then rejoin with slash
  return path.split('/').map(segment => encodeURIComponent(segment)).join('/')
}

function formatDate(date) {
  if (!date) return '-'
  return new Date(date).toLocaleDateString('id-ID', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

function getTipeClass(tipe) {
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

function handleFileChange(event) {
  const file = event.target.files[0]
  if (file) {
    // Validate file type
    const validTypes = ['application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document']
    if (!validTypes.includes(file.type) && !file.name.match(/\.(doc|docx)$/i)) {
      alert('Hanya file Word (.doc, .docx) yang diperbolehkan')
      event.target.value = ''
      return
    }
    // Validate file size (10MB)
    if (file.size > 10 * 1024 * 1024) {
      alert('Ukuran file maksimal 10MB')
      event.target.value = ''
      return
    }
    reviewForm.value.file = file
  }
}

function handlePdfChange(event) {
  const file = event.target.files[0]
  if (file) {
    // Validate file type
    if (file.type !== 'application/pdf' && !file.name.match(/\.pdf$/i)) {
      alert('Hanya file PDF (.pdf) yang diperbolehkan')
      event.target.value = ''
      return
    }
    // Validate file size (5MB)
    if (file.size > 5 * 1024 * 1024) {
      alert('Ukuran file maksimal 5MB')
      event.target.value = ''
      return
    }
    reviewForm.value.filePdf = file
  }
}

async function downloadDokumen(doc) {
  try {
    const token = localStorage.getItem('token')
    const url = `${apiBaseUrl}/api/dokumen/${encodePathForUrl(doc.path_file)}`
    
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
    link.download = doc.nama_file || 'document'
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
    const url = `${apiBaseUrl}/api/dokumen/${encodePathForUrl(catatan.file_path)}`
    
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
    console.error('Error downloading file:', error)
    alert('Gagal mengunduh dokumen: ' + error.message)
  }
}

async function downloadPdfFile(catatan) {
  try {
    const token = localStorage.getItem('token')
    const url = `${apiBaseUrl}/api/dokumen/${encodePathForUrl(catatan.file_review_pdf)}`
    
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
    link.download = catatan.file_review_pdf_name || 'review_document.pdf'
    document.body.appendChild(link)
    link.click()
    document.body.removeChild(link)
    window.URL.revokeObjectURL(downloadUrl)
  } catch (error) {
    console.error('Error downloading file:', error)
    alert('Gagal mengunduh dokumen: ' + error.message)
  }
}

async function loadPengusulan() {
  try {
    await pengusulanStore.fetchDetail(route.params.id)
    pengusulan.value = pengusulanStore.currentPengusulan
  } catch (error) {
    console.error('Error loading pengusulan:', error)
    alert('Gagal memuat data usulan')
  }
}

async function submitReview() {
  try {
    await pengusulanStore.reviewPengusulan(
      route.params.id,
      reviewForm.value.status,
      reviewForm.value.catatan,
      reviewForm.value.file,
      reviewForm.value.filePdf
    )
    alert('Review berhasil disimpan')
    router.push('/dashboard/verifikator')
  } catch (error) {
    console.error('Error submitting review:', error)
    alert(error.response?.data?.message || 'Gagal menyimpan review')
  }
}

onMounted(() => {
  loadPengusulan()
})
</script>
