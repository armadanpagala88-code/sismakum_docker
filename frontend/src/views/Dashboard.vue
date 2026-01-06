<template>
  <div>
    <!-- Page Header -->
    <div class="mb-8">
      <h1 class="text-4xl font-bold text-gray-900 mb-2">Dashboard</h1>
      <p v-if="user?.role === 'dinas'" class="text-lg text-gray-700 font-semibold mb-2">
        Selamat datang di dashboard, Dinas {{ namaDinas || '' }}
      </p>
      <p class="text-lg text-gray-600">Ringkasan pengusulan PERBUB</p>
    </div>
    
    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4 mb-8">
      <div class="card hover:shadow-lg transition-all duration-200 group">
        <div class="card-body">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <div class="h-14 w-14 bg-gradient-to-br from-primary-500 to-primary-600 rounded-2xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                <svg class="h-7 w-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
              </div>
            </div>
            <div class="ml-5 flex-1">
              <p class="text-sm font-medium text-gray-600 mb-1">Total Pengusulan</p>
              <p class="text-3xl font-bold text-gray-900">{{ stats.total }}</p>
            </div>
          </div>
        </div>
      </div>

      <div class="card hover:shadow-lg transition-all duration-200 group">
        <div class="card-body">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <div class="h-14 w-14 bg-gradient-to-br from-gray-500 to-gray-600 rounded-2xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                <svg class="h-7 w-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
              </div>
            </div>
            <div class="ml-5 flex-1">
              <p class="text-sm font-medium text-gray-600 mb-1">Draft</p>
              <p class="text-3xl font-bold text-gray-900">{{ stats.draft }}</p>
            </div>
          </div>
        </div>
      </div>

      <div class="card hover:shadow-lg transition-all duration-200 group">
        <div class="card-body">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <div class="h-14 w-14 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                <svg class="h-7 w-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </div>
            </div>
            <div class="ml-5 flex-1">
              <p class="text-sm font-medium text-gray-600 mb-1">Diajukan</p>
              <p class="text-3xl font-bold text-gray-900">{{ stats.diajukan }}</p>
            </div>
          </div>
        </div>
      </div>

      <div class="card hover:shadow-lg transition-all duration-200 group">
        <div class="card-body">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <div class="h-14 w-14 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-2xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                <svg class="h-7 w-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </div>
            </div>
            <div class="ml-5 flex-1">
              <p class="text-sm font-medium text-gray-600 mb-1">Diterima</p>
              <p class="text-3xl font-bold text-gray-900">{{ stats.diterima }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Recent Pengusulan -->
    <div class="card">
      <div class="card-header">
        <div class="flex items-center justify-between">
          <h2 class="text-xl font-bold text-gray-900">Pengusulan Terbaru</h2>
          <router-link
            v-if="user?.role === 'dinas'"
            to="/dashboard/pengusulan"
            class="text-sm font-semibold text-primary-600 hover:text-primary-700 transition-colors"
          >
            Lihat Semua →
          </router-link>
        </div>
      </div>
      
      <div class="card-body">
        <div v-if="loading" class="text-center py-12">
          <div class="inline-block animate-spin rounded-full h-12 w-12 border-4 border-primary-600 border-t-transparent"></div>
          <p class="mt-4 text-sm text-gray-500">Memuat data...</p>
        </div>
        
        <div v-else-if="recentPengusulan.length === 0" class="text-center py-12">
          <div class="mx-auto h-20 w-20 bg-gray-100 rounded-2xl flex items-center justify-center mb-4">
            <svg class="h-10 w-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
          </div>
          <p class="text-base font-semibold text-gray-900">Belum ada pengusulan</p>
          <p class="mt-2 text-sm text-gray-500">Mulai dengan membuat pengusulan baru</p>
          <router-link
            v-if="user?.role === 'dinas'"
            to="/dashboard/pengusulan/create"
            class="mt-6 inline-block btn btn-primary"
          >
            Buat Pengusulan Baru
          </router-link>
        </div>
        
        <div v-else class="divide-y divide-gray-100">
          <div
            v-for="item in recentPengusulan"
            :key="item.id"
            class="py-5 first:pt-0 last:pb-0 hover:bg-gray-50 -mx-6 px-6 rounded-xl cursor-pointer transition-all duration-200 group"
            @click="$router.push(`/dashboard/pengusulan/${item.id}`)"
          >
            <div class="flex items-start justify-between">
              <div class="flex-1 min-w-0">
                <div class="flex items-center space-x-3 mb-3">
                  <h4 class="text-lg font-bold text-gray-900 line-clamp-2 group-hover:text-primary-600 transition-colors">
                    {{ item.judul_perbub }}
                  </h4>
                  <span 
                    :class="getStatusBadgeClass(item.status)"
                    class="badge flex-shrink-0"
                  >
                    {{ getStatusLabel(item.status) }}
                  </span>
                </div>
                <div class="flex flex-wrap items-center gap-5 text-sm text-gray-600">
                  <div class="flex items-center">
                    <svg class="h-4 w-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <span class="font-semibold">Nomor:</span>
                    <span class="ml-1.5">{{ item.nomor_surat }}</span>
                  </div>
                  <div class="flex items-center">
                    <svg class="h-4 w-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                    <span class="font-semibold">Dinas:</span>
                    <span class="ml-1.5 text-gray-900 font-bold">{{ item.dinas?.name || 'Tidak diketahui' }}</span>
                  </div>
                  <div class="flex items-center">
                    <svg class="h-4 w-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <span class="font-semibold">Tanggal:</span>
                    <span class="ml-1.5">{{ formatDate(item.tanggal_surat) }}</span>
                  </div>
                </div>
              </div>
              <svg class="h-5 w-5 text-gray-400 ml-4 flex-shrink-0 group-hover:text-primary-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
              </svg>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed, watch } from 'vue'
import { usePengusulanStore } from '../stores/pengusulan'
import { useAuthStore } from '../stores/auth'
import { useAdminStore } from '../stores/admin'
import api from '../services/api'

const pengusulanStore = usePengusulanStore()
const authStore = useAuthStore()
const adminStore = useAdminStore()

const loading = ref(false)
const recentPengusulan = ref([])
const dinasData = ref(null)

const user = computed(() => authStore.user)

const namaDinas = computed(() => {
  if (user.value?.role === 'dinas') {
    // Cek dari user.dinas langsung
    if (user.value?.dinas) {
      return user.value.dinas.nama_dinas || user.value.dinas.name || ''
    }
    // Cek dari dinasData yang di-fetch terpisah
    if (dinasData.value) {
      return dinasData.value.nama_dinas || dinasData.value.name || ''
    }
  }
  return ''
})

// Fetch dinas data jika user punya dinas_id tapi belum ada relasi dinas
async function loadDinasData() {
  if (user.value?.role === 'dinas' && user.value?.dinas_id && !user.value?.dinas) {
    try {
      // Coba endpoint public dulu
      let response
      try {
        response = await api.get(`/api/dinas/${user.value.dinas_id}`)
      } catch (e) {
        // Jika gagal, coba endpoint admin
        response = await api.get(`/admin/dinas/${user.value.dinas_id}`)
      }
      if (response.data) {
        dinasData.value = response.data.data || response.data
      }
    } catch (error) {
      console.error('Error loading dinas data:', error)
      // Coba fetch semua dinas dan cari yang sesuai
      try {
        const allDinas = await api.get('/api/dinas')
        const dinasList = allDinas.data.data || allDinas.data || []
        const foundDinas = Array.isArray(dinasList) 
          ? dinasList.find(d => d.id === user.value.dinas_id)
          : null
        if (foundDinas) {
          dinasData.value = foundDinas
        }
      } catch (e) {
        console.error('Error loading all dinas:', e)
      }
    }
  }
}

// Watch user changes
watch(() => user.value, (newUser) => {
  if (newUser?.role === 'dinas') {
    loadDinasData()
  }
}, { immediate: true })

const stats = computed(() => {
  const total = recentPengusulan.value.length
  const draft = recentPengusulan.value.filter(p => p.status === 'draft').length
  const diajukan = recentPengusulan.value.filter(p => p.status === 'diajukan').length
  const diterima = recentPengusulan.value.filter(p => p.status === 'diterima').length
  
  return { total, draft, diajukan, diterima }
})

function getStatusBadgeClass(status) {
  const classes = {
    draft: 'badge-draft',
    diajukan: 'badge-diajukan',
    revisi: 'badge-revisi',
    diterima: 'badge-diterima',
    ditolak: 'badge-ditolak'
  }
  return classes[status] || 'badge-draft'
}

function getStatusLabel(status) {
  const labels = {
    draft: 'Draft',
    diajukan: 'Diajukan',
    revisi: 'Revisi',
    diterima: 'Diterima',
    ditolak: 'Ditolak'
  }
  return labels[status] || status
}

function formatDate(date) {
  if (!date) return '-'
  return new Date(date).toLocaleDateString('id-ID', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

onMounted(async () => {
  // Ensure user data is loaded
  if (authStore.isAuthenticated && !authStore.user) {
    await authStore.fetchUser()
  }
  
  // Load dinas data if needed
  await loadDinasData()
  
  loading.value = true
  try {
    const response = await pengusulanStore.fetchPengusulan({ per_page: 5 })
    if (response.data) {
      recentPengusulan.value = Array.isArray(response.data) ? response.data : []
    } else if (Array.isArray(response)) {
      recentPengusulan.value = response.slice(0, 5)
    } else {
      recentPengusulan.value = []
    }
  } catch (error) {
    console.error('Error loading pengusulan:', error)
    recentPengusulan.value = []
  } finally {
    loading.value = false
  }
})
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
