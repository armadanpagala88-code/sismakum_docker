<template>
  <div class="pengusulan-form">
    <h1 class="page-title">Buat Pengusulan PERBUB Baru</h1>
    
    <form @submit.prevent="handleSubmit" class="form-card">
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
      
      <div class="form-group">
        <label class="form-label">Dokumen Pendukung</label>
        <input
          type="file"
          @change="handleFileChange"
          multiple
          accept=".pdf,.doc,.docx"
          class="form-input"
        />
        <small class="form-help">Format: PDF, DOC, DOCX (Maks. 10MB per file)</small>
      </div>
      
      <div v-if="error" class="error-message">
        {{ error }}
      </div>
      
      <div class="form-actions">
        <router-link to="/dashboard/pengusulan" class="btn btn-secondary">Batal</router-link>
        <button type="submit" class="btn btn-primary" :disabled="loading">
          {{ loading ? 'Menyimpan...' : 'Simpan sebagai Draft' }}
        </button>
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { usePengusulanStore } from '../stores/pengusulan'
import { useKategoriDokumenStore } from '../stores/kategoriDokumen'

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
  ruang_lingkup: '',
  dokumen: []
})

const loading = ref(false)
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

onMounted(() => {
  loadKategori()
})

function handleFileChange(event) {
  form.value.dokumen = Array.from(event.target.files)
}

async function handleSubmit() {
  loading.value = true
  error.value = ''
  
  try {
    await pengusulanStore.createPengusulan(form.value)
    router.push('/dashboard/pengusulan')
  } catch (err) {
    error.value = err.response?.data?.message || 'Terjadi kesalahan saat menyimpan'
  } finally {
    loading.value = false
  }
}
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

.form-help {
  display: block;
  margin-top: 0.25rem;
  font-size: 0.875rem;
  color: #6b7280;
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
</style>

