<template>
  <div>
    <!-- Page Header -->
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between">
      <div class="mb-4 sm:mb-0">
        <h1 class="text-3xl font-bold text-gray-900">Kelola Pengguna</h1>
        <p class="mt-2 text-sm text-gray-600">Tambah dan kelola pengguna sistem</p>
      </div>
      <button @click="openModal()" class="btn btn-primary inline-flex items-center">
        <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
        </svg>
        Tambah User
      </button>
    </div>

    <!-- Users Table -->
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
                <th>Nama</th>
                <th>Email</th>
                <th>Role</th>
                <th>Dinas</th>
                <th>Status</th>
                <th class="text-right">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="user in users" :key="user.id">
                <td>
                  <div class="text-sm font-semibold text-gray-900">{{ user.name }}</div>
                </td>
                <td>
                  <div class="text-sm text-gray-600">{{ user.email }}</div>
                </td>
                <td>
                  <span class="badge" :class="{
                    'badge-diterima': user.role === 'admin',
                    'badge-diajukan': user.role === 'dinas',
                    'badge-revisi': user.role === 'bagian_hukum' || user.role === 'verifikator'
                  }">
                    {{ user.role?.replace('_', ' ') }}
                  </span>
                </td>
                <td>
                  <div class="text-sm text-gray-600">{{ user.dinas?.nama_dinas || '-' }}</div>
                </td>
                <td>
                  <span class="badge" :class="{
                    'badge-diterima': user.is_active,
                    'badge-ditolak': !user.is_active
                  }">
                    {{ user.is_active ? 'Aktif' : 'Nonaktif' }}
                  </span>
                </td>
                <td class="text-right">
                  <div class="flex items-center justify-end space-x-2">
                    <button
                      @click="toggleUserStatus(user)"
                      class="text-sm font-medium px-3 py-1 rounded"
                      :class="user.is_active 
                        ? 'text-amber-600 hover:text-amber-700 bg-amber-50 hover:bg-amber-100' 
                        : 'text-emerald-600 hover:text-emerald-700 bg-emerald-50 hover:bg-emerald-100'"
                    >
                      {{ user.is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                    </button>
                    <button
                      @click="editUser(user)"
                      class="text-primary-600 hover:text-primary-700 font-medium text-sm"
                    >
                      Edit
                    </button>
                    <button
                      @click="deleteUser(user.id)"
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
      <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <div class="card-header">
          <h3 class="text-lg font-semibold text-gray-900">
            {{ editingUser ? 'Edit Pengguna' : 'Tambah Pengguna' }}
          </h3>
        </div>
        <form @submit.prevent="saveUser" class="card-body space-y-4">
          <div>
            <label class="form-label">Nama *</label>
            <input v-model="form.name" type="text" class="form-input" required />
          </div>
          <div>
            <label class="form-label">Email *</label>
            <input v-model="form.email" type="email" class="form-input" required />
          </div>
          <div>
            <label class="form-label">Password {{ editingUser ? '(kosongkan jika tidak diubah)' : '*' }}</label>
            <input v-model="form.password" type="password" class="form-input" :required="!editingUser" />
          </div>
          <div>
            <label class="form-label">Role *</label>
            <select v-model="form.role" class="form-select" required>
              <option value="dinas">Dinas</option>
              <option value="bagian_hukum">Bagian Hukum</option>
              <option value="verifikator">Verifikator</option>
              <option value="admin">Admin</option>
            </select>
          </div>
          <div>
            <label class="form-label">Dinas</label>
            <select v-model="form.dinas_id" class="form-select">
              <option value="">Pilih Dinas (Opsional)</option>
              <option v-for="dinas in allDinas" :key="dinas.id" :value="dinas.id">
                {{ dinas.nama_dinas }}
              </option>
            </select>
            <small class="text-gray-500 text-xs mt-1 block">Pilih dinas jika role adalah "Dinas"</small>
          </div>
          <div>
            <label class="form-label">Unit Kerja</label>
            <input v-model="form.unit_kerja" type="text" class="form-input" />
          </div>
          <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
            <button type="button" @click="closeModal" class="btn btn-secondary">
              Batal
            </button>
            <button type="submit" class="btn btn-primary">
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

const users = ref([])
const allDinas = ref([])
const showModal = ref(false)
const editingUser = ref(null)
const loading = ref(false)
const form = ref({
  name: '',
  email: '',
  password: '',
  role: 'dinas',
  dinas_id: '',
  unit_kerja: ''
})

async function loadUsers() {
  loading.value = true
  try {
    await adminStore.fetchUsers()
    users.value = adminStore.users.data || adminStore.users
  } catch (error) {
    console.error('Error loading users:', error)
    alert('Gagal memuat data users')
  } finally {
    loading.value = false
  }
}

async function loadDinas() {
  try {
    await adminStore.fetchDinas()
    allDinas.value = adminStore.dinas.data || adminStore.dinas
  } catch (error) {
    console.error('Error loading dinas:', error)
  }
}

function openModal(user = null) {
  editingUser.value = user
  if (user) {
    form.value = {
      name: user.name,
      email: user.email,
      password: '',
      role: user.role,
      dinas_id: user.dinas_id || '',
      unit_kerja: user.unit_kerja || ''
    }
  } else {
    form.value = {
      name: '',
      email: '',
      password: '',
      role: 'dinas',
      dinas_id: '',
      unit_kerja: ''
    }
  }
  showModal.value = true
}

function closeModal() {
  showModal.value = false
  editingUser.value = null
  form.value = {
    name: '',
    email: '',
    password: '',
    role: 'dinas',
    dinas_id: '',
    unit_kerja: ''
  }
}

function editUser(user) {
  openModal(user)
}

async function saveUser() {
  try {
    const data = { ...form.value }
    
    if (data.dinas_id === '') {
      data.dinas_id = null
    }
    
    if (editingUser.value && !data.password) {
      delete data.password
    }
    
    if (editingUser.value) {
      await adminStore.updateUser(editingUser.value.id, data)
    } else {
      await adminStore.createUser(data)
    }
    closeModal()
    await loadUsers()
  } catch (error) {
    console.error('Error saving user:', error)
    let errorMessage = 'Gagal menyimpan user'
    
    if (error.response?.data?.message) {
      errorMessage = error.response.data.message
    } else if (error.response?.data?.errors) {
      const errors = error.response.data.errors
      const errorList = Object.entries(errors).map(([field, messages]) => {
        return `${field}: ${Array.isArray(messages) ? messages.join(', ') : messages}`
      }).join('\n')
      errorMessage = 'Validasi gagal:\n' + errorList
    } else if (error.message) {
      errorMessage = error.message
    }
    
    alert(errorMessage)
  }
}

async function toggleUserStatus(user) {
  if (confirm(`Yakin ingin ${user.is_active ? 'menonaktifkan' : 'mengaktifkan'} akun ${user.name}?`)) {
    try {
      await adminStore.toggleUserStatus(user.id)
      await loadUsers()
    } catch (error) {
      console.error('Error toggling user status:', error)
      alert('Gagal mengubah status user')
    }
  }
}

async function deleteUser(id) {
  if (!confirm('Apakah Anda yakin ingin menghapus user ini?')) return
  
  try {
    await adminStore.deleteUser(id)
    await loadUsers()
  } catch (error) {
    console.error('Error deleting user:', error)
    alert('Gagal menghapus user')
  }
}

onMounted(() => {
  loadUsers()
  loadDinas()
})
</script>
