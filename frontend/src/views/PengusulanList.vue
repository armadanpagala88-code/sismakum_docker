<template>
  <div>
    <!-- Page Header -->
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between">
      <div class="mb-4 sm:mb-0">
        <h1 class="text-3xl font-bold text-gray-900">Daftar Pengusulan PERBUB</h1>
        <p class="mt-2 text-sm text-gray-600">Kelola semua pengusulan PERBUB</p>
      </div>
      <router-link to="/dashboard/pengusulan/create" class="btn btn-primary inline-flex items-center">
        <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
        </svg>
        Buat Pengusulan Baru
      </router-link>
    </div>
    
    <!-- Filter -->
    <div class="mb-6">
      <select 
        v-model="filterStatus" 
        @change="loadPengusulan" 
        class="form-select max-w-xs"
      >
        <option value="">Semua Status</option>
        <option value="draft">Draft</option>
        <option value="diajukan">Diajukan</option>
        <option value="diterima">Diterima</option>
        <option value="ditolak">Ditolak</option>
        <option value="revisi">Revisi</option>
      </select>
    </div>
    
    <!-- Loading State -->
    <div v-if="loading" class="card">
      <div class="card-body text-center py-12">
        <div class="inline-block animate-spin rounded-full h-10 w-10 border-4 border-primary-600 border-t-transparent"></div>
        <p class="mt-4 text-sm text-gray-500">Memuat data...</p>
      </div>
    </div>
    
    <!-- Empty State -->
    <div v-else-if="pengusulan.length === 0" class="card">
      <div class="card-body text-center py-12">
        <div class="mx-auto h-16 w-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
          <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
          </svg>
        </div>
        <p class="text-sm font-semibold text-gray-900">Belum ada pengusulan</p>
        <p class="mt-1 text-sm text-gray-500">Mulai dengan membuat pengusulan baru</p>
        <router-link
          to="/dashboard/pengusulan/create"
          class="mt-4 inline-block btn btn-primary"
        >
          Buat Pengusulan Baru
        </router-link>
      </div>
    </div>
    
    <!-- Pengusulan Grid -->
    <div v-else class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
      <div
        v-for="item in pengusulan"
        :key="item.id"
        class="card hover:shadow-lg transition-all duration-200 cursor-pointer group"
        @click="$router.push(`/dashboard/pengusulan/${item.id}`)"
      >
        <div class="card-body">
          <div class="flex items-start justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900 line-clamp-2 group-hover:text-primary-600 transition-colors">
              {{ item.judul_perbub }}
            </h3>
            <span 
              :class="getStatusBadgeClass(item.status)"
              class="badge ml-2 flex-shrink-0"
            >
              {{ getStatusLabel(item.status) }}
            </span>
          </div>
          <div class="space-y-3 text-sm">
            <div class="flex items-center text-gray-600">
              <svg class="h-4 w-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
              </svg>
              <span class="font-medium">Nomor:</span>
              <span class="ml-1">{{ item.nomor_surat }}</span>
            </div>
            <div class="flex items-center text-gray-600">
              <svg class="h-4 w-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
              </svg>
              <span class="font-medium">Tanggal:</span>
              <span class="ml-1">{{ formatDate(item.tanggal_surat) }}</span>
            </div>
            <div class="flex items-center text-gray-600">
              <svg class="h-4 w-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
              </svg>
              <span class="font-medium">Dinas:</span>
              <span class="ml-1 text-gray-900 font-semibold">{{ item.dinas?.name || '-' }}</span>
            </div>
            <p v-if="item.catatan" class="text-xs text-gray-500 mt-3 pt-3 border-t border-gray-200 line-clamp-2">
              <span class="font-medium">Catatan:</span> {{ item.catatan }}
            </p>
          </div>
          <div class="mt-4 pt-4 border-t border-gray-200">
            <button
              @click.stop="$router.push(`/dashboard/pengusulan/${item.id}/edit`)"
              class="btn btn-secondary text-sm w-full"
              v-if="item.status === 'draft' && item.dinas_id === user?.id"
            >
              Edit
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { usePengusulanStore } from '../stores/pengusulan'
import { useAuthStore } from '../stores/auth'

const pengusulanStore = usePengusulanStore()
const authStore = useAuthStore()

const loading = ref(false)
const filterStatus = ref('')

const pengusulan = computed(() => pengusulanStore.pengusulan)
const user = computed(() => authStore.user)

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
  return new Date(date).toLocaleDateString('id-ID')
}

async function loadPengusulan() {
  loading.value = true
  try {
    await pengusulanStore.fetchPengusulan({
      status: filterStatus.value || undefined
    })
  } catch (error) {
    console.error(error)
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  loadPengusulan()
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
