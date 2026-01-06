<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Sidebar -->
    <aside
      :class="[
        'fixed inset-y-0 left-0 z-50 w-64 bg-gradient-to-b from-slate-900 to-slate-800 transform transition-transform duration-300 ease-in-out lg:translate-x-0',
        sidebarOpen ? 'translate-x-0' : '-translate-x-full'
      ]"
    >
      <div class="flex flex-col h-full">
        <!-- Logo -->
        <div class="flex items-center justify-between h-16 px-6 border-b border-slate-700/50">
          <div class="flex items-center space-x-3">
            <div class="flex-shrink-0">
              <div class="h-10 w-10 bg-gradient-to-br from-primary-500 to-primary-600 rounded-xl flex items-center justify-center shadow-lg">
                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </div>
            </div>
            <span class="text-xl font-bold text-white">SISMAKUM</span>
          </div>
          <button
            @click="sidebarOpen = false"
            class="lg:hidden text-slate-400 hover:text-white transition-colors"
          >
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto">
          <router-link
            to="/dashboard"
            class="sidebar-link"
            :class="$route.path === '/dashboard' || $route.path === '/dashboard/' ? 'sidebar-link-active text-white bg-primary-600/20' : 'sidebar-link-inactive text-slate-300'"
            @click="sidebarOpen = false"
          >
            <svg class="h-5 w-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
            </svg>
            Dashboard
          </router-link>

          <router-link
            v-if="user?.role === 'dinas'"
            to="/dashboard/pengusulan"
            class="sidebar-link"
            :class="$route.path.startsWith('/dashboard/pengusulan') ? 'sidebar-link-active text-white bg-primary-600/20' : 'sidebar-link-inactive text-slate-300'"
            @click="sidebarOpen = false"
          >
            <svg class="h-5 w-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            Pengusulan
          </router-link>

          <router-link
            v-if="['verifikator', 'bagian_hukum', 'admin'].includes(user?.role)"
            to="/dashboard/verifikator"
            class="sidebar-link"
            :class="$route.path.startsWith('/dashboard/verifikator') ? 'sidebar-link-active text-white bg-primary-600/20' : 'sidebar-link-inactive text-slate-300'"
            @click="sidebarOpen = false"
          >
            <svg class="h-5 w-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
            </svg>
            Verifikasi
          </router-link>

          <div v-if="user?.role === 'admin'" class="pt-4">
            <p class="px-4 text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Admin</p>
            <router-link
              to="/dashboard/admin"
              class="sidebar-link"
              :class="$route.path === '/dashboard/admin' || $route.path === '/dashboard/admin/' ? 'sidebar-link-active text-white bg-primary-600/20' : 'sidebar-link-inactive text-slate-300'"
              @click="sidebarOpen = false"
            >
              <svg class="h-5 w-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
              </svg>
              Dashboard Admin
            </router-link>
            <router-link
              to="/dashboard/admin/dinas"
              class="sidebar-link"
              :class="$route.path === '/dashboard/admin/dinas' || $route.path.startsWith('/dashboard/admin/dinas') ? 'sidebar-link-active text-white bg-primary-600/20' : 'sidebar-link-inactive text-slate-300'"
              @click="sidebarOpen = false"
            >
              <svg class="h-5 w-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
              </svg>
              Kelola Dinas
            </router-link>
            <router-link
              to="/dashboard/admin/users"
              class="sidebar-link"
              :class="$route.path === '/dashboard/admin/users' || $route.path.startsWith('/dashboard/admin/users') ? 'sidebar-link-active text-white bg-primary-600/20' : 'sidebar-link-inactive text-slate-300'"
              @click="sidebarOpen = false"
            >
              <svg class="h-5 w-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
              </svg>
              Kelola Users
            </router-link>
            <router-link
              to="/dashboard/admin/website"
              class="sidebar-link"
              :class="$route.path === '/dashboard/admin/website' || $route.path.startsWith('/dashboard/admin/website') ? 'sidebar-link-active text-white bg-primary-600/20' : 'sidebar-link-inactive text-slate-300'"
              @click="sidebarOpen = false"
            >
              <svg class="h-5 w-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path>
              </svg>
              Kelola Website
            </router-link>
            <router-link
              to="/dashboard/admin/berita"
              class="sidebar-link"
              :class="$route.path === '/dashboard/admin/berita' || $route.path.startsWith('/dashboard/admin/berita') ? 'sidebar-link-active text-white bg-primary-600/20' : 'sidebar-link-inactive text-slate-300'"
              @click="sidebarOpen = false"
            >
              <svg class="h-5 w-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
              </svg>
              Kelola Berita
            </router-link>
            <router-link
              to="/dashboard/admin/infokami"
              class="sidebar-link"
              :class="$route.path === '/dashboard/admin/infokami' || $route.path.startsWith('/dashboard/admin/infokami') ? 'sidebar-link-active text-white bg-primary-600/20' : 'sidebar-link-inactive text-slate-300'"
              @click="sidebarOpen = false"
            >
              <svg class="h-5 w-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
              </svg>
              Kelola INFOKAMI
            </router-link>
            <router-link
              to="/dashboard/admin/banners"
              class="sidebar-link"
              :class="$route.path === '/dashboard/admin/banners' || $route.path.startsWith('/dashboard/admin/banners') ? 'sidebar-link-active text-white bg-primary-600/20' : 'sidebar-link-inactive text-slate-300'"
              @click="sidebarOpen = false"
            >
              <svg class="h-5 w-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
              </svg>
              Kelola Banner
            </router-link>
          </div>

          <div class="pt-4 border-t border-slate-700/50">
            <a
              href="/website"
              target="_blank"
              class="sidebar-link sidebar-link-inactive text-slate-300"
              @click="sidebarOpen = false"
            >
              <svg class="h-5 w-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path>
              </svg>
              Website Publik
            </a>
          </div>
        </nav>

        <!-- User Info -->
        <div class="p-4 border-t border-slate-700/50">
          <div class="flex items-center space-x-3 mb-3">
            <div class="flex-shrink-0">
              <div class="h-10 w-10 bg-gradient-to-br from-primary-500 to-primary-600 rounded-xl flex items-center justify-center shadow-lg">
                <span class="text-sm font-bold text-white">{{ userInitials }}</span>
              </div>
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-sm font-semibold text-white truncate">{{ user?.name }}</p>
              <p class="text-xs text-slate-400 capitalize">{{ user?.role?.replace('_', ' ') }}</p>
            </div>
          </div>
          <button
            @click="handleLogout"
            class="w-full flex items-center justify-center px-4 py-2.5 text-sm font-semibold text-slate-300 hover:text-white hover:bg-slate-700/50 rounded-xl transition-all duration-200"
          >
            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
            </svg>
            Logout
          </button>
        </div>
      </div>
    </aside>

    <!-- Sidebar Overlay -->
    <div
      v-if="sidebarOpen"
      @click="sidebarOpen = false"
      class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm z-40 lg:hidden transition-opacity"
    ></div>

    <!-- Main Content -->
    <div class="lg:pl-64">
      <!-- Top Header -->
      <header class="sticky top-0 z-30 bg-white/80 backdrop-blur-lg border-b border-gray-200/50 shadow-sm">
        <div class="flex items-center justify-between h-16 px-4 sm:px-6 lg:px-8">
          <button
            @click="sidebarOpen = true"
            class="lg:hidden p-2 rounded-lg text-gray-600 hover:bg-gray-100 transition-colors"
          >
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
          </button>

          <div class="flex-1 flex items-center justify-end space-x-4">
            <div class="hidden sm:flex sm:items-center sm:space-x-4">
              <div class="text-right">
                <p class="text-sm font-semibold text-gray-900">{{ user?.name }}</p>
                <p v-if="namaDinas" class="text-xs text-gray-600 font-medium">
                  {{ namaDinas }}
                </p>
                <p v-else class="text-xs text-gray-500 capitalize">{{ user?.role?.replace('_', ' ') }}</p>
              </div>
            </div>
          </div>
        </div>
      </header>

      <!-- Page Content -->
      <main class="p-4 sm:p-6 lg:p-8">
        <router-view />
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const router = useRouter()
const authStore = useAuthStore()

const sidebarOpen = ref(false)
const user = computed(() => authStore.user)

const userInitials = computed(() => {
  if (!user.value?.name) return 'U'
  const names = user.value.name.split(' ')
  if (names.length >= 2) {
    return (names[0][0] + names[1][0]).toUpperCase()
  }
  return user.value.name.substring(0, 2).toUpperCase()
})

const namaDinas = computed(() => {
  if (user.value?.role === 'dinas') {
    if (user.value?.dinas) {
      return user.value.dinas.nama_dinas || user.value.dinas.name || 'Dinas'
    }
  }
  return null
})

onMounted(async () => {
  if (authStore.isAuthenticated && !authStore.user) {
    await authStore.fetchUser()
  }
})

async function handleLogout() {
  await authStore.logout()
  router.push('/login')
}
</script>

<style scoped>
.sidebar-link-active {
  @apply bg-primary-600/20 text-white;
}

.sidebar-link-inactive {
  @apply text-slate-300 hover:bg-slate-700/50 hover:text-white;
}
</style>
