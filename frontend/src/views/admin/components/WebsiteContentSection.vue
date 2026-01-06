<template>
  <div>
    <div class="mb-6 flex justify-between items-center">
      <div>
        <h2 class="text-2xl font-bold text-gray-900">{{ title }}</h2>
        <p class="mt-1 text-sm text-gray-600">Kelola konten {{ title.toLowerCase() }}</p>
      </div>
      <button
        @click="openModal"
        class="btn btn-primary inline-flex items-center"
      >
        <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
        </svg>
        {{ items.length > 0 ? 'Edit Konten' : 'Tambah Konten' }}
      </button>
    </div>

    <div v-if="loading" class="card">
      <div class="card-body text-center py-12">
        <div class="inline-block animate-spin rounded-full h-10 w-10 border-4 border-primary-600 border-t-transparent"></div>
        <p class="mt-4 text-sm text-gray-500">Memuat data...</p>
      </div>
    </div>

    <div v-else-if="items.length > 0" class="card">
      <div class="card-body">
        <div class="space-y-4">
          <div
            v-for="item in items"
            :key="item.id"
            class="border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow"
          >
            <div class="flex justify-between items-start mb-4">
              <div class="flex-1">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">
                  {{ item.title || contentKey }}
                </h3>
                <div v-if="item.file_path || item.image_url" class="mb-4">
                  <img
                    v-if="item.file_path"
                    :src="`${apiBaseUrl}/storage/${item.file_path}`"
                    alt="Content image"
                    class="w-full max-w-md h-48 object-cover rounded-lg border border-gray-200"
                  />
                  <img
                    v-else-if="item.image_url"
                    :src="item.image_url"
                    alt="Content image"
                    class="w-full max-w-md h-48 object-cover rounded-lg border border-gray-200"
                  />
                </div>
                <div
                  v-if="item.content"
                  class="prose max-w-none text-gray-700"
                  v-html="item.content"
                ></div>
                <div v-else class="text-gray-500 italic">Tidak ada konten</div>
              </div>
              <div class="ml-4 flex flex-col space-y-2">
                <button
                  @click="editItem(item)"
                  class="text-primary-600 hover:text-primary-700 font-medium text-sm px-3 py-1 rounded hover:bg-primary-50"
                >
                  Edit
                </button>
                <button
                  @click="deleteItem(item.id)"
                  class="text-rose-600 hover:text-rose-700 font-medium text-sm px-3 py-1 rounded hover:bg-rose-50"
                >
                  Hapus
                </button>
              </div>
            </div>
            <div class="flex items-center justify-between pt-4 border-t border-gray-200">
              <div class="flex items-center space-x-4 text-sm text-gray-500">
                <span>Type: <span class="font-semibold">{{ item.type }}</span></span>
                <span>Order: <span class="font-semibold">{{ item.order }}</span></span>
                <span :class="item.is_active ? 'badge badge-diterima' : 'badge badge-ditolak'">
                  {{ item.is_active ? 'Aktif' : 'Tidak Aktif' }}
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div v-else class="card">
      <div class="card-body text-center py-12">
        <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
        </svg>
        <h3 class="text-lg font-semibold text-gray-900 mb-2">Belum ada konten</h3>
        <p class="text-gray-600 mb-4">Mulai dengan menambahkan konten untuk {{ title.toLowerCase() }}</p>
        <button
          @click="openModal"
          class="btn btn-primary"
        >
          Tambah Konten
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'

const props = defineProps({
  items: {
    type: Array,
    default: () => []
  },
  loading: {
    type: Boolean,
    default: false
  },
  title: {
    type: String,
    required: true
  },
  contentKey: {
    type: String,
    required: true
  }
})

const emit = defineEmits(['save', 'delete', 'edit'])

const apiBaseUrl = import.meta.env.VITE_API_BASE_URL || window.location.origin

function openModal() {
  emit('edit', props.contentKey)
}

function editItem(item) {
  emit('edit', props.contentKey, item)
}

function deleteItem(id) {
  emit('delete', id)
}
</script>

