<template>
  <div class="pengusulan-form">
    <h1 class="page-title">Edit Pengusulan PERBUB</h1>
    
    <div v-if="loading" class="loading">Memuat data...</div>
    <form v-else @submit.prevent="handleSubmit" class="form-card">
      <div class="form-group">
        <label class="form-label">Nomor Surat *</label>
        <input
          v-model="form.nomor_surat"
          type="text"
          class="form-input"
          required
          placeholder="Masukkan nomor surat"
        />
      </div>
      
      <div class="form-group">
        <label class="form-label">Tanggal Surat *</label>
        <input
          v-model="form.tanggal_surat"
          type="date"
          class="form-input"
          required
        />
      </div>
      
      <div class="form-group">
        <label class="form-label">Kategori Dokumen</label>
        <select
          v-model="form.kategori_dokumen_id"
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
        <input
          v-model="form.judul_perbub"
          type="text"
          class="form-input"
          required
          placeholder="Masukkan judul PERBUB"
        />
      </div>
      
      <div class="form-group">
        <label class="form-label">Latar Belakang *</label>
        <textarea
          v-model="form.latar_belakang"
          class="form-textarea"
          required
          placeholder="Jelaskan latar belakang pengusulan PERBUB"
        ></textarea>
      </div>
      
      <div class="form-group">
        <label class="form-label">Maksud dan Tujuan *</label>
        <textarea
          v-model="form.maksud_dan_tujuan"
          class="form-textarea"
          required
          placeholder="Jelaskan maksud dan tujuan PERBUB"
        ></textarea>
      </div>
      
      <div class="form-group">
        <label class="form-label">Ruang Lingkup *</label>
        <textarea
          v-model="form.ruang_lingkup"
          class="form-textarea"
          required
          placeholder="Jelaskan ruang lingkup PERBUB"
        ></textarea>
      </div>
      
      <div v-if="error" class="error-message">
        {{ error }}
      </div>
      
      <div class="form-actions">
        <router-link :to="`/dashboard/pengusulan/${$route.params.id}`" class="btn btn-secondary">Batal</router-link>
        <button type="submit" class="btn btn-primary" :disabled="submitting">
          {{ submitting ? 'Menyimpan...' : 'Simpan Perubahan' }}
        </button>
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { usePengusulanStore } from '../stores/pengusulan'
import { useKategoriDokumenStore } from '../stores/kategoriDokumen'

const route = useRoute()
const router = useRouter()
const pengusulanStore = usePengusulanStore()
const kategoriDokumenStore = useKategoriDokumenStore()

const form = ref({
  nomor_surat: '',
  tanggal_surat: '',
  kategori_dokumen_id: '',
  judul_perbub: '',
  latar_belakang: '',
  maksud_dan_tujuan: '',
  ruang_lingkup: ''
})

const loading = ref(false)
const submitting = ref(false)
const error = ref('')
const kategoriList = ref([])

async function loadKategori() {
  try {
    await kategoriDokumenStore.fetchActive()
    kategoriList.value = kategoriDokumenStore.kategoriList
  } catch (err) {
    console.error('Error loading kategori:', err)
  }
}

async function loadData() {
  loading.value = true
  try {
    const data = await pengusulanStore.fetchDetail(route.params.id)
    form.value = {
      nomor_surat: data.nomor_surat,
      tanggal_surat: data.tanggal_surat,
      kategori_dokumen_id: data.kategori_dokumen_id || '',
      judul_perbub: data.judul_perbub,
      latar_belakang: data.latar_belakang,
      maksud_dan_tujuan: data.maksud_dan_tujuan,
      ruang_lingkup: data.ruang_lingkup
    }
  } catch (err) {
    error.value = 'Gagal memuat data'
  } finally {
    loading.value = false
  }
}

async function handleSubmit() {
  submitting.value = true
  error.value = ''
  
  try {
    await pengusulanStore.updatePengusulan(route.params.id, form.value)
    router.push(`/dashboard/pengusulan/${route.params.id}`)
  } catch (err) {
    error.value = err.response?.data?.message || 'Terjadi kesalahan saat menyimpan'
  } finally {
    submitting.value = false
  }
}

onMounted(() => {
  loadKategori()
  loadData()
})
</script>

<style scoped>
.pengusulan-form {
  max-width: 800px;
}

.page-title {
  font-size: 2rem;
  font-weight: 700;
  margin-bottom: 2rem;
  color: #1f2937;
}

.form-card {
  background: white;
  padding: 2rem;
  border-radius: 0.5rem;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.form-actions {
  display: flex;
  gap: 1rem;
  justify-content: flex-end;
  margin-top: 2rem;
}

.error-message {
  background-color: #fee2e2;
  color: #991b1b;
  padding: 0.75rem;
  border-radius: 0.375rem;
  margin-bottom: 1rem;
  font-size: 0.875rem;
}

.loading {
  text-align: center;
  padding: 3rem;
  color: #6b7280;
}
</style>

