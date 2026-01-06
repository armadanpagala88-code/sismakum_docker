<template>
  <div>
    <!-- Page Header -->
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between">
      <div class="mb-4 sm:mb-0">
        <h1 class="text-3xl font-bold text-gray-900">Kelola Dinas</h1>
        <p class="mt-2 text-sm text-gray-600">Tambah dan kelola data dinas</p>
      </div>
      <button @click="openModal()" class="btn btn-primary inline-flex items-center">
        <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
        </svg>
        Tambah Dinas
      </button>
    </div>

    <!-- Dinas Table -->
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
                <th>Nama Dinas</th>
                <th>Kode</th>
                <th>Email</th>
                <th>Status</th>
                <th>Users</th>
                <th class="text-right">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="dinas in dinas" :key="dinas.id">
                <td>
                  <div class="text-sm font-semibold text-gray-900">{{ dinas.nama_dinas }}</div>
                </td>
                <td>
                  <div class="text-sm text-gray-600">{{ dinas.kode_dinas || '-' }}</div>
                </td>
                <td>
                  <div class="text-sm text-gray-600">{{ dinas.email || '-' }}</div>
                </td>
                <td>
                  <span :class="dinas.is_active ? 'badge badge-diterima' : 'badge badge-ditolak'">
                    {{ dinas.is_active ? 'Aktif' : 'Tidak Aktif' }}
                  </span>
                </td>
                <td>
                  <div class="text-sm text-gray-600">{{ dinas.users_count || 0 }} user</div>
                </td>
                <td class="text-right">
                  <div class="flex items-center justify-end space-x-2">
                    <button
                      @click="editItem(dinas)"
                      class="text-primary-600 hover:text-primary-700 font-medium text-sm"
                    >
                      Edit
                    </button>
                    <button
                      @click="deleteItem(dinas.id)"
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
      <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-md">
        <div class="card-header">
          <h3 class="text-lg font-semibold text-gray-900">
            {{ editingItem ? 'Edit Dinas' : 'Tambah Dinas' }}
          </h3>
        </div>
        <form @submit.prevent="saveItem" class="card-body space-y-4">
          <div>
            <label class="form-label">Nama Dinas *</label>
            <input
              v-model="form.nama_dinas"
              type="text"
              class="form-input"
              required
            />
          </div>
          <div>
            <label class="form-label">Kode Dinas</label>
            <input
              v-model="form.kode_dinas"
              type="text"
              class="form-input"
            />
          </div>
          <div>
            <label class="form-label">Email</label>
            <input
              v-model="form.email"
              type="email"
              class="form-input"
            />
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
import { ref, onMounted } from 'vue'
import { useAdminStore } from '../../stores/admin'

const adminStore = useAdminStore()

const dinas = ref([])
const showModal = ref(false)
const editingItem = ref(null)
const loading = ref(false)
const form = ref({
  nama_dinas: '',
  kode_dinas: '',
  email: '',
  is_active: true
})

async function loadDinas() {
  loading.value = true
  try {
    await adminStore.fetchDinas()
    dinas.value = adminStore.dinas.data || adminStore.dinas
  } catch (error) {
    console.error('Error loading dinas:', error)
    alert('Gagal memuat data dinas')
  } finally {
    loading.value = false
  }
}

function openModal(item = null) {
  editingItem.value = item
  if (item) {
    form.value = { ...item }
  } else {
    form.value = {
      nama_dinas: '',
      kode_dinas: '',
      email: '',
      is_active: true
    }
  }
  showModal.value = true
}

function closeModal() {
  showModal.value = false
  editingItem.value = null
  form.value = {
    nama_dinas: '',
    kode_dinas: '',
    email: '',
    is_active: true
  }
}

function editItem(item) {
  openModal(item)
}

async function saveItem() {
  try {
    if (editingItem.value) {
      await adminStore.updateDinas(editingItem.value.id, form.value)
    } else {
      await adminStore.createDinas(form.value)
    }
    closeModal()
    await loadDinas()
  } catch (error) {
    console.error('Error saving dinas:', error)
    alert(error.response?.data?.message || 'Gagal menyimpan dinas')
  }
}

async function deleteItem(id) {
  if (!confirm('Apakah Anda yakin ingin menghapus dinas ini?')) return
  
  try {
    await adminStore.deleteDinas(id)
    await loadDinas()
  } catch (error) {
    console.error('Error deleting dinas:', error)
    alert('Gagal menghapus dinas')
  }
}

onMounted(() => {
  loadDinas()
})
</script>
