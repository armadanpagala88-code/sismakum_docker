<template>
  <div class="min-h-screen bg-white">
    <!-- Header -->
    <header class="sticky top-0 z-50 bg-white/95 backdrop-blur-lg border-b border-gray-200 shadow-sm">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
          <div class="flex items-center space-x-3">
            <div class="h-10 w-10 bg-gradient-to-br from-primary-500 to-primary-600 rounded-xl flex items-center justify-center shadow-lg">
              <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </div>
            <h1 class="text-2xl font-bold gradient-text">SISMAKUM</h1>
          </div>
          <!-- Navigation Menu -->
          <nav class="hidden md:flex items-center space-x-1">
            <a
              href="#tentang"
              @click.prevent="scrollToSection('tentang')"
              class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-primary-600 transition-colors rounded-lg hover:bg-gray-100"
            >
              Tentang
            </a>
            <a
              href="#fitur"
              @click.prevent="scrollToSection('fitur')"
              class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-primary-600 transition-colors rounded-lg hover:bg-gray-100"
            >
              Fitur
            </a>
            <a
              href="#berita"
              @click.prevent="scrollToSection('berita')"
              class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-primary-600 transition-colors rounded-lg hover:bg-gray-100"
            >
              Berita
            </a>
            <a
              href="#infokami"
              @click.prevent="scrollToSection('infokami')"
              class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-primary-600 transition-colors rounded-lg hover:bg-gray-100"
            >
              INFOKAMI
            </a>
            <a
              href="#kontak"
              @click.prevent="scrollToSection('kontak')"
              class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-primary-600 transition-colors rounded-lg hover:bg-gray-100"
            >
              Kontak
            </a>
          </nav>
          <!-- Mobile Menu Button -->
          <button
            @click="toggleMobileMenu"
            class="md:hidden p-2 text-gray-700 hover:text-primary-600 transition-colors"
          >
            <svg v-if="!showMobileMenu" class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
            <svg v-else class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
          <div class="flex items-center space-x-4">
            <button
              v-if="!authStore.isAuthenticated"
              @click.stop="openRegisterModal"
              type="button"
              class="btn btn-secondary"
            >
              Daftar
            </button>
            <router-link
              v-if="!authStore.isAuthenticated"
              to="/login"
              class="btn btn-primary"
            >
              Login
            </router-link>
            <router-link
              v-else
              to="/dashboard"
              class="btn btn-primary"
            >
              Dashboard
            </router-link>
          </div>
        </div>
      </div>
      <!-- Mobile Menu -->
      <div
        v-if="showMobileMenu"
        class="md:hidden border-t border-gray-200 bg-white"
      >
        <nav class="px-4 py-3 space-y-1">
          <a
            href="#tentang"
            @click.prevent="scrollToSection('tentang')"
            class="block px-4 py-2 text-sm font-medium text-gray-700 hover:text-primary-600 hover:bg-gray-100 rounded-lg transition-colors"
          >
            Tentang
          </a>
          <a
            href="#fitur"
            @click.prevent="scrollToSection('fitur')"
            class="block px-4 py-2 text-sm font-medium text-gray-700 hover:text-primary-600 hover:bg-gray-100 rounded-lg transition-colors"
          >
            Fitur
          </a>
          <a
            href="#berita"
            @click.prevent="scrollToSection('berita')"
            class="block px-4 py-2 text-sm font-medium text-gray-700 hover:text-primary-600 hover:bg-gray-100 rounded-lg transition-colors"
          >
            Berita
          </a>
          <a
            href="#infokami"
            @click.prevent="scrollToSection('infokami')"
            class="block px-4 py-2 text-sm font-medium text-gray-700 hover:text-primary-600 hover:bg-gray-100 rounded-lg transition-colors"
          >
            INFOKAMI
          </a>
          <a
            href="#kontak"
            @click.prevent="scrollToSection('kontak')"
            class="block px-4 py-2 text-sm font-medium text-gray-700 hover:text-primary-600 hover:bg-gray-100 rounded-lg transition-colors"
          >
            Kontak
          </a>
        </nav>
      </div>
    </header>

    <!-- Slideshow Section -->
    <section v-if="getSlideshowData().length > 0" class="relative h-[500px] md:h-[600px] overflow-hidden">
      <div class="relative w-full h-full">
        <div
          v-for="(slide, index) in getSlideshowData()"
          :key="slide.id || slide.slug"
          v-show="currentSlideIndex === index"
          class="absolute inset-0 transition-opacity duration-1000 cursor-pointer"
          :class="{ 'opacity-100': currentSlideIndex === index, 'opacity-0': currentSlideIndex !== index }"
          @click="handleSlideClick(slide)"
        >
          <img
            :src="getSlideshowImage(slide)"
            :alt="slide.title || slide.judul || 'Slideshow'"
            class="w-full h-full object-cover"
          />
          <div class="absolute inset-0 bg-gradient-to-r from-black/60 to-black/40 flex items-center">
            <!-- Foto Kepemimpinan (Overlay di depan, kiri) -->
            <div v-if="getLeadershipPhotos().length > 0" class="absolute left-4 md:left-8 z-20 flex items-center gap-3 md:gap-6">
              <div v-for="(photo, index) in getLeadershipPhotos()" :key="index" class="flex flex-col items-center">
                <img
                  :src="photo.url"
                  :alt="photo.label"
                  class="w-20 h-20 md:w-28 md:h-28 lg:w-32 lg:h-32 object-cover rounded-full border-4 border-white shadow-2xl"
                  @error="handleImageError"
                />
                <span class="text-white text-xs md:text-sm mt-2 font-semibold text-center whitespace-nowrap">{{ photo.label }}</span>
              </div>
            </div>
            <!-- Text Content (Sebelah kanan) -->
            <div class="text-white px-4 max-w-4xl ml-auto mr-8 md:mr-16" :class="getLeadershipPhotos().length > 0 ? 'ml-72 md:ml-96 lg:ml-[28rem]' : 'mx-auto text-center'">
              <h2 v-if="slide.title || slide.judul" class="text-4xl md:text-6xl font-bold mb-4 animate-fade-in">
                {{ slide.title || slide.judul }}
              </h2>
              <p v-if="slide.description || slide.isi" class="text-xl md:text-2xl text-white/90 mb-8 animate-slide-up line-clamp-2">
                {{ slide.description || stripHtml(slide.isi) }}
              </p>
              <button
                class="btn btn-primary px-8 py-3 text-lg inline-block"
              >
                Baca Selengkapnya
              </button>
            </div>
          </div>
        </div>
      </div>
      <!-- Navigation Dots -->
      <div v-if="getSlideshowData().length > 1" class="absolute bottom-4 left-1/2 transform -translate-x-1/2 flex space-x-2">
        <button
          v-for="(slide, index) in getSlideshowData()"
          :key="slide.id || slide.slug"
          @click="currentSlideIndex = index"
          :class="[
            'w-3 h-3 rounded-full transition-all',
            currentSlideIndex === index ? 'bg-white w-8' : 'bg-white/50'
          ]"
        ></button>
      </div>
      <!-- Navigation Arrows -->
      <div v-if="getSlideshowData().length > 1" class="absolute inset-y-0 left-0 flex items-center">
        <button
          @click="previousSlide"
          class="ml-4 p-2 bg-white/20 hover:bg-white/30 rounded-full text-white transition-colors"
        >
          <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
          </svg>
        </button>
      </div>
      <div v-if="getSlideshowData().length > 1" class="absolute inset-y-0 right-0 flex items-center">
        <button
          @click="nextSlide"
          class="mr-4 p-2 bg-white/20 hover:bg-white/30 rounded-full text-white transition-colors"
        >
          <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
          </svg>
        </button>
      </div>
    </section>

    <!-- Hero Section (Fallback jika tidak ada slideshow) -->
    <section v-else class="hero relative overflow-hidden">
      <div class="absolute inset-0 bg-gradient-to-br from-primary-50 via-white to-primary-50"></div>
      <div class="hero-content relative z-10">
        <div class="text-center max-w-4xl mx-auto">
          <h1 class="text-5xl sm:text-6xl font-bold text-gray-900 mb-6 animate-fade-in">
            Sistem Informasi Pengusulan PERBUB
          </h1>
          <p class="text-xl text-gray-600 mb-8 max-w-2xl mx-auto animate-slide-up">
            Platform digital untuk memfasilitasi proses pengajuan, verifikasi, dan pengelolaan 
            usulan Peraturan Bupati (PERBUB) secara efisien dan transparan.
          </p>
          <div class="flex flex-col sm:flex-row gap-4 justify-center animate-slide-up">
            <router-link
              v-if="!authStore.isAuthenticated"
              to="/login"
              class="btn btn-primary px-8 py-3 text-lg"
            >
              Masuk ke Sistem
            </router-link>
            <a
              href="#tentang"
              class="btn btn-outline px-8 py-3 text-lg"
            >
              Pelajari Lebih Lanjut
            </a>
          </div>
        </div>
      </div>
      <!-- Decorative Elements -->
      <div class="absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none">
        <div class="absolute top-20 left-10 w-72 h-72 bg-primary-200 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-blob"></div>
        <div class="absolute top-40 right-10 w-72 h-72 bg-blue-200 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-blob animation-delay-2000"></div>
        <div class="absolute -bottom-8 left-1/2 w-72 h-72 bg-emerald-200 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-blob animation-delay-4000"></div>
      </div>
    </section>

    <!-- Berita Section -->
    <section v-if="berita.length > 0 || true" class="section bg-white" id="berita">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="section-header">
          <h2 class="section-title">Berita & Informasi Terkini</h2>
          <p class="section-subtitle">
            Dapatkan informasi terbaru seputar pengusulan PERBUB dan kegiatan Bagian Hukum
          </p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
          <div
            v-for="item in getDummyBerita()"
            :key="item.id"
            class="card hover:shadow-xl transition-all duration-300 cursor-pointer group overflow-hidden"
            @click="viewBerita(item.slug)"
          >
            <div v-if="item.gambar" class="relative h-48 overflow-hidden">
              <img
                :src="item.gambar.startsWith('http') ? item.gambar : `${apiBaseUrl}/storage/${item.gambar}`"
                :alt="item.judul"
                class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300"
              />
            </div>
            <div v-else class="h-48 bg-gradient-to-br from-primary-400 to-primary-600 flex items-center justify-center">
              <svg class="h-16 w-16 text-white opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
              </svg>
            </div>
            <div class="card-body">
              <h3 class="text-xl font-bold text-gray-900 mb-2 line-clamp-2 group-hover:text-primary-600 transition-colors">
                {{ item.judul }}
              </h3>
              <p class="text-gray-600 text-sm mb-4 line-clamp-3">{{ stripHtml(item.isi) }}</p>
              <div class="flex justify-between items-center text-sm text-gray-500">
                <span>{{ formatDate(item.published_at) }}</span>
                <span class="flex items-center">
                  <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                  </svg>
                  {{ item.views }} views
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Banner Section -->
    <section v-if="bannerList.length > 0" class="section bg-white py-12">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-wrap items-center justify-center gap-8 md:gap-12">
          <a
            v-for="banner in bannerList"
            :key="banner.id"
            :href="banner.url || '#'"
            :target="banner.url ? '_blank' : '_self'"
            :rel="banner.url ? 'noopener noreferrer' : ''"
            class="flex items-center space-x-3 hover:opacity-80 transition-opacity"
          >
            <img
              v-if="banner.logo"
              :src="getImageUrl(banner.logo)"
              :alt="banner.nama"
              class="h-16 md:h-20 w-auto object-contain grayscale hover:grayscale-0 transition-all duration-300"
              @error="handleImageError"
            />
            <div v-else class="h-16 md:h-20 flex items-center">
              <span class="text-gray-400 text-sm">{{ banner.nama }}</span>
            </div>
          </a>
        </div>
      </div>
    </section>

    <!-- Sambutan Kepala Bagian Hukum -->
    <section v-if="website.sambutan" class="section bg-gray-50" id="sambutan">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="card">
          <div class="card-body p-8 md:p-12">
            <div class="flex flex-col md:flex-row gap-8 items-center">
              <div v-if="website.sambutan.file_path || website.sambutan.image_url" class="flex-shrink-0">
                <img
                  v-if="website.sambutan.file_path"
                  :src="`${apiBaseUrl}/storage/${website.sambutan.file_path}`"
                  alt="Foto Kepala Bagian Hukum"
                  class="w-48 h-48 md:w-64 md:h-64 rounded-2xl object-cover border-4 border-primary-200 shadow-xl"
                />
                <img
                  v-else-if="website.sambutan.image_url"
                  :src="website.sambutan.image_url"
                  alt="Foto Kepala Bagian Hukum"
                  class="w-48 h-48 md:w-64 md:h-64 rounded-2xl object-cover border-4 border-primary-200 shadow-xl"
                />
              </div>
              <div v-else class="flex-shrink-0">
                <div class="w-48 h-48 md:w-64 md:h-64 rounded-2xl bg-gradient-to-br from-primary-400 to-primary-600 flex items-center justify-center border-4 border-primary-200 shadow-xl">
                  <svg class="h-24 w-24 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                  </svg>
                </div>
              </div>
              <div class="flex-1">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                  {{ website.sambutan.title || 'Sambutan Kepala Bagian Hukum' }}
                </h2>
                <div 
                  class="prose prose-lg max-w-none text-gray-700"
                  v-html="website.sambutan.content || getDefaultSambutan()"
                ></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Features Section -->
    <section v-if="website.fitur || (getDefaultFeatures && getDefaultFeatures().length > 0) || true" class="section bg-white" id="fitur">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="section-header">
          <h2 class="section-title">{{ website.fitur?.title || 'Fitur Utama' }}</h2>
          <p class="section-subtitle">
            {{ website.fitur?.content ? parseFiturDescription(website.fitur.content) : 'Platform lengkap untuk pengelolaan pengusulan PERBUB dengan berbagai fitur unggulan' }}
          </p>
        </div>
        <div v-if="website.fitur && website.fitur.content" class="card mb-8">
          <div class="card-body">
            <div class="prose max-w-none" v-html="website.fitur.content"></div>
          </div>
        </div>
        <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
          <div class="card hover:shadow-xl transition-all duration-300 group">
            <div class="card-body text-center">
              <div class="mx-auto h-16 w-16 bg-gradient-to-br from-primary-500 to-primary-600 rounded-2xl flex items-center justify-center mb-4 shadow-lg group-hover:scale-110 transition-transform">
                <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
              </div>
              <h3 class="text-xl font-bold text-gray-900 mb-2">Pengusulan Digital</h3>
              <p class="text-gray-600">
                Ajukan usulan PERBUB secara digital dengan proses yang mudah dan cepat. 
                Upload dokumen pendukung langsung melalui sistem.
              </p>
            </div>
          </div>

          <div class="card hover:shadow-xl transition-all duration-300 group">
            <div class="card-body text-center">
              <div class="mx-auto h-16 w-16 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-2xl flex items-center justify-center mb-4 shadow-lg group-hover:scale-110 transition-transform">
                <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                </svg>
              </div>
              <h3 class="text-xl font-bold text-gray-900 mb-2">Verifikasi Terintegrasi</h3>
              <p class="text-gray-600">
                Sistem verifikasi yang terintegrasi memungkinkan proses review yang efisien 
                dengan catatan dan tracking yang transparan.
              </p>
            </div>
          </div>

          <div class="card hover:shadow-xl transition-all duration-300 group">
            <div class="card-body text-center">
              <div class="mx-auto h-16 w-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center mb-4 shadow-lg group-hover:scale-110 transition-transform">
                <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                </svg>
              </div>
              <h3 class="text-xl font-bold text-gray-900 mb-2">Notifikasi Real-time</h3>
              <p class="text-gray-600">
                Dapatkan notifikasi email otomatis untuk setiap update status pengusulan Anda. 
                Tetap terinformasi tanpa harus login ke sistem.
              </p>
            </div>
          </div>

          <div class="card hover:shadow-xl transition-all duration-300 group">
            <div class="card-body text-center">
              <div class="mx-auto h-16 w-16 bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl flex items-center justify-center mb-4 shadow-lg group-hover:scale-110 transition-transform">
                <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                </svg>
              </div>
              <h3 class="text-xl font-bold text-gray-900 mb-2">Keamanan Data</h3>
              <p class="text-gray-600">
                Data dan dokumen Anda terlindungi dengan sistem keamanan berlapis. 
                Akses terbatas berdasarkan role dan otentikasi yang ketat.
              </p>
            </div>
          </div>

          <div class="card hover:shadow-xl transition-all duration-300 group">
            <div class="card-body text-center">
              <div class="mx-auto h-16 w-16 bg-gradient-to-br from-amber-500 to-amber-600 rounded-2xl flex items-center justify-center mb-4 shadow-lg group-hover:scale-110 transition-transform">
                <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </div>
              <h3 class="text-xl font-bold text-gray-900 mb-2">Tracking Status</h3>
              <p class="text-gray-600">
                Pantau status pengusulan Anda secara real-time. Dari draft hingga diterima, 
                semua proses dapat dilacak dengan mudah.
              </p>
            </div>
          </div>

          <div class="card hover:shadow-xl transition-all duration-300 group">
            <div class="card-body text-center">
              <div class="mx-auto h-16 w-16 bg-gradient-to-br from-rose-500 to-rose-600 rounded-2xl flex items-center justify-center mb-4 shadow-lg group-hover:scale-110 transition-transform">
                <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
              </div>
              <h3 class="text-xl font-bold text-gray-900 mb-2">Dokumen Terpusat</h3>
              <p class="text-gray-600">
                Semua dokumen pengusulan tersimpan terpusat dan mudah diakses. 
                Download dan review dokumen kapan saja.
              </p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Profil Section -->
    <section v-if="website.profil || true" class="section bg-white" id="tentang">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="section-header">
          <h2 class="section-title">{{ website.profil?.title || 'Profil Bagian Hukum' }}</h2>
          <p class="section-subtitle">
            Mengenal lebih dekat Bagian Hukum dan peranannya dalam pengelolaan PERBUB
          </p>
        </div>
        <div class="card">
          <div class="card-body p-8 md:p-12">
            <div 
              class="prose prose-lg max-w-none text-gray-700"
              v-html="website.profil?.content || getDefaultProfil()"
            ></div>
          </div>
        </div>
      </div>
    </section>

    <!-- Visi dan Misi -->
    <section v-if="website.visi_misi || true" class="section bg-gray-50" id="visi-misi">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="section-header">
          <h2 class="section-title">{{ website.visi_misi?.title || 'Visi dan Misi' }}</h2>
        </div>
        <div v-if="website.visi_misi && website.visi_misi.content" class="card">
          <div class="card-body p-8 md:p-12">
            <div 
              class="prose prose-lg max-w-none text-gray-700"
              v-html="website.visi_misi.content"
            ></div>
          </div>
        </div>
        <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-8">
          <div class="card">
            <div class="card-body p-8">
              <div class="flex items-center mb-4">
                <div class="h-12 w-12 bg-gradient-to-br from-primary-500 to-primary-600 rounded-xl flex items-center justify-center mr-4">
                  <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                  </svg>
                </div>
                <h3 class="text-2xl font-bold text-gray-900">Visi</h3>
              </div>
              <div 
                class="prose max-w-none text-gray-700"
                v-html="getVisiContent()"
              ></div>
            </div>
          </div>
          <div class="card">
            <div class="card-body p-8">
              <div class="flex items-center mb-4">
                <div class="h-12 w-12 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl flex items-center justify-center mr-4">
                  <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                  </svg>
                </div>
                <h3 class="text-2xl font-bold text-gray-900">Misi</h3>
              </div>
              <div 
                class="prose max-w-none text-gray-700"
                v-html="getMisiContent()"
              ></div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- INFOKAMI Section -->
    <section class="section bg-white" id="infokami">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="section-header">
          <h2 class="section-title">INFOKAMI</h2>
          <p class="section-subtitle">
            Informasi dan dokumen penting terkait pengusulan PERBUB
          </p>
        </div>
        <div v-if="infokamiList.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <div
            v-for="item in infokamiList"
            :key="item.id"
            class="card hover:shadow-xl transition-all duration-300 cursor-pointer group overflow-hidden"
            @click="openPdfModal(item)"
          >
            <div class="relative h-64 overflow-hidden bg-gray-100">
              <img
                v-if="item.cover_pdf"
                :src="getInfokamiImageUrl(item.cover_pdf)"
                :alt="item.nama_file"
                class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300"
                @error="handleImageError"
              />
              <div v-else class="w-full h-full bg-gradient-to-br from-primary-400 to-primary-600 flex items-center justify-center">
                <svg class="h-20 w-20 text-white opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                </svg>
              </div>
              <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-all duration-300 flex items-center justify-center">
                <div class="opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                  <div class="bg-white/90 rounded-full p-4">
                    <svg class="h-8 w-8 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                  </div>
                </div>
              </div>
            </div>
            <div class="card-body">
              <h3 class="text-lg font-bold text-gray-900 mb-2 line-clamp-2 group-hover:text-primary-600 transition-colors">
                {{ item.nama_file }}
              </h3>
              <p v-if="item.deskripsi" class="text-gray-600 text-sm mb-3 line-clamp-2">
                {{ item.deskripsi }}
              </p>
              <div class="flex items-center text-xs text-gray-500">
                <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <span>{{ formatDate(item.created_at) }}</span>
              </div>
            </div>
          </div>
        </div>
        <div v-else class="text-center py-12">
          <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
          </svg>
          <p class="text-gray-500 text-lg">Belum ada dokumen INFOKAMI tersedia</p>
        </div>
      </div>
    </section>

    <!-- Contact Section -->
    <section v-if="website.contact || true" class="section bg-gradient-to-br from-gray-50 to-white" id="kontak">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="section-header">
          <h2 class="section-title">{{ website.contact?.title || 'Kontak Kami' }}</h2>
          <p class="section-subtitle">
            Hubungi kami jika Anda memerlukan bantuan atau informasi lebih lanjut
          </p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
          <div class="card text-center">
            <div class="card-body p-8">
              <div class="mx-auto h-16 w-16 bg-gradient-to-br from-primary-500 to-primary-600 rounded-2xl flex items-center justify-center mb-4 shadow-lg">
                <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                </svg>
              </div>
              <h3 class="text-lg font-bold text-gray-900 mb-2">Email</h3>
              <p class="text-gray-600">bagianhukum@example.com</p>
            </div>
          </div>
          <div class="card text-center">
            <div class="card-body p-8">
              <div class="mx-auto h-16 w-16 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-2xl flex items-center justify-center mb-4 shadow-lg">
                <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                </svg>
              </div>
              <h3 class="text-lg font-bold text-gray-900 mb-2">Telepon</h3>
              <p class="text-gray-600">(021) 1234-5678</p>
            </div>
          </div>
          <div class="card text-center">
            <div class="card-body p-8">
              <div class="mx-auto h-16 w-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center mb-4 shadow-lg">
                <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
              </div>
              <h3 class="text-lg font-bold text-gray-900 mb-2">Alamat</h3>
              <p class="text-gray-600">Jl. Contoh No. 123<br>Kota, Provinsi 12345</p>
            </div>
          </div>
        </div>
        <div v-if="website.contact?.content" class="mt-8 card">
          <div class="card-body p-8">
            <div 
              class="prose max-w-none text-gray-700"
              v-html="website.contact.content"
            ></div>
          </div>
        </div>
      </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gradient-to-br from-slate-900 to-slate-800 text-white py-12">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
          <div>
            <div class="flex items-center space-x-3 mb-4">
              <div class="h-10 w-10 bg-gradient-to-br from-primary-500 to-primary-600 rounded-xl flex items-center justify-center">
                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </div>
              <h3 class="text-xl font-bold">SISMAKUM</h3>
            </div>
            <p class="text-gray-400 text-sm">
              Sistem Informasi Pengusulan PERBUB yang memudahkan proses pengajuan dan verifikasi peraturan.
            </p>
          </div>
          <div>
            <h4 class="text-sm font-semibold uppercase tracking-wider mb-4">Tautan Cepat</h4>
            <ul class="space-y-2">
              <li>
                <a href="#tentang" class="text-gray-400 hover:text-white transition-colors">Tentang Kami</a>
              </li>
              <li>
                <a href="#fitur" class="text-gray-400 hover:text-white transition-colors">Fitur</a>
              </li>
              <li>
                <a href="#berita" class="text-gray-400 hover:text-white transition-colors">Berita</a>
              </li>
              <li>
                <a href="#kontak" class="text-gray-400 hover:text-white transition-colors">Kontak</a>
              </li>
            </ul>
          </div>
          <div>
            <h4 class="text-sm font-semibold uppercase tracking-wider mb-4">Layanan</h4>
            <ul class="space-y-2">
              <li>
                <router-link to="/login" class="text-gray-400 hover:text-white transition-colors">Login</router-link>
              </li>
              <li>
                <a href="#" class="text-gray-400 hover:text-white transition-colors">Panduan Penggunaan</a>
              </li>
              <li>
                <a href="#" class="text-gray-400 hover:text-white transition-colors">FAQ</a>
              </li>
            </ul>
          </div>
          <div>
            <h4 class="text-sm font-semibold uppercase tracking-wider mb-4">Kontak</h4>
            <ul class="space-y-2 text-gray-400 text-sm">
              <li class="flex items-center">
                <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                </svg>
                bagianhukum@example.com
              </li>
              <li class="flex items-center">
                <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                </svg>
                (021) 1234-5678
              </li>
            </ul>
          </div>
        </div>
        <div class="border-t border-slate-700 pt-8 text-center">
          <p class="text-gray-400 text-sm">
            © {{ new Date().getFullYear() }} SISMAKUM. All rights reserved.
          </p>
        </div>
      </div>
    </footer>

    <!-- Register Modal -->
    <div
      v-if="showRegisterModal"
      class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm overflow-y-auto h-full w-full z-50 flex items-center justify-center p-4"
      @click.self="showRegisterModal = false"
    >
      <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 flex justify-between items-center">
          <h3 class="text-2xl font-bold text-gray-900">Pendaftaran Dinas</h3>
          <button
            @click="showRegisterModal = false"
            class="text-gray-500 hover:text-gray-700 transition-colors"
          >
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>
        <form @submit.prevent="handleRegister" class="p-6 space-y-4">
          <div v-if="registerError" class="bg-rose-50 border-l-4 border-rose-400 p-4 rounded-lg">
            <div class="flex">
              <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-rose-400" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                </svg>
              </div>
              <div class="ml-3">
                <p class="text-sm text-rose-700">{{ registerError }}</p>
              </div>
            </div>
          </div>
          
          <div v-if="registerSuccess" class="bg-emerald-50 border-l-4 border-emerald-400 p-4 rounded-lg">
            <div class="flex">
              <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-emerald-400" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
              </div>
              <div class="ml-3">
                <p class="text-sm text-emerald-700">{{ registerSuccess }}</p>
              </div>
            </div>
          </div>

          <div>
            <label class="form-label">Nama Lengkap *</label>
            <input
              v-model="registerForm.name"
              type="text"
              class="form-input"
              required
              placeholder="Masukkan nama lengkap"
            />
          </div>

          <div>
            <label class="form-label">NIP</label>
            <input
              v-model="registerForm.nip"
              type="text"
              class="form-input"
              placeholder="Masukkan NIP (opsional)"
            />
          </div>

          <div>
            <label class="form-label">Jabatan</label>
            <input
              v-model="registerForm.jabatan"
              type="text"
              class="form-input"
              placeholder="Masukkan jabatan (opsional)"
            />
          </div>

          <div>
            <label class="form-label">Email *</label>
            <input
              v-model="registerForm.email"
              type="email"
              class="form-input"
              required
              placeholder="Masukkan email"
            />
          </div>

          <div>
            <label class="form-label">Password *</label>
            <input
              v-model="registerForm.password"
              type="password"
              class="form-input"
              required
              placeholder="Minimal 6 karakter"
              minlength="6"
            />
          </div>

          <div>
            <label class="form-label">Konfirmasi Password *</label>
            <input
              v-model="registerForm.password_confirmation"
              type="password"
              class="form-input"
              required
              placeholder="Ulangi password"
              minlength="6"
            />
          </div>

          <div>
            <label class="form-label">Dinas/Unit Kerja *</label>
            <select
              v-model="registerForm.dinas_id"
              class="form-select"
              required
              :disabled="dinasList.length === 0"
            >
              <option value="">{{ dinasList.length === 0 ? 'Memuat daftar dinas...' : 'Pilih Dinas/Unit Kerja' }}</option>
              <option
                v-for="dinas in dinasList"
                :key="dinas.id"
                :value="dinas.id"
              >
                {{ dinas.nama_dinas }} {{ dinas.kode_dinas ? `(${dinas.kode_dinas})` : '' }}
              </option>
            </select>
            <small class="text-gray-500 text-xs mt-1 block">
              Pilih dinas atau unit kerja Anda
            </small>
            <div v-if="dinasList.length === 0" class="text-xs text-amber-600 mt-1">
              Belum ada dinas yang tersedia. Silakan hubungi administrator.
            </div>
          </div>

          <div>
            <label class="form-label">Alamat</label>
            <textarea
              v-model="registerForm.alamat"
              rows="3"
              class="form-textarea"
              placeholder="Masukkan alamat dinas (opsional)"
            ></textarea>
          </div>

          <div>
            <label class="form-label">Telepon</label>
            <input
              v-model="registerForm.telepon"
              type="tel"
              class="form-input"
              placeholder="Masukkan nomor telepon (opsional)"
            />
          </div>

          <div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded-lg">
            <p class="text-sm text-blue-700">
              <strong>Catatan:</strong> Setelah pendaftaran, akun Anda akan dinonaktifkan sementara. 
              Administrator akan mengaktifkan akun Anda setelah verifikasi. Anda akan dapat login setelah akun diaktifkan.
            </p>
          </div>

          <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
            <button
              type="button"
              @click="showRegisterModal = false"
              class="btn btn-secondary"
            >
              Batal
            </button>
            <button
              type="submit"
              class="btn btn-primary"
              :disabled="registerLoading"
            >
              <span v-if="registerLoading" class="flex items-center">
                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Memproses...
              </span>
              <span v-else>Daftar</span>
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- INFOKAMI PDF Modal -->
    <div
      v-if="selectedInfokami"
      class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm overflow-y-auto h-full w-full z-50 flex items-center justify-center p-2"
      @click.self="closePdfModal"
    >
      <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-7xl h-[98vh] overflow-hidden flex flex-col">
        <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 flex justify-between items-center z-10">
          <div class="flex-1">
            <h3 class="text-2xl font-bold text-gray-900">{{ selectedInfokami.nama_file }}</h3>
            <p v-if="selectedInfokami.deskripsi" class="text-sm text-gray-600 mt-1">{{ selectedInfokami.deskripsi }}</p>
          </div>
          <div class="flex items-center space-x-3">
            <a
              :href="getInfokamiPdfUrl(selectedInfokami.file_pdf)"
              target="_blank"
              download
              class="btn btn-secondary text-sm px-4 py-2"
              @click.stop
            >
              <svg class="h-4 w-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
              </svg>
              Download
            </a>
            <button
              @click="closePdfModal"
              class="text-gray-500 hover:text-gray-700 transition-colors p-2"
            >
              <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>
        </div>
        <div class="flex-1 overflow-hidden min-h-0">
          <iframe
            :src="getInfokamiPdfUrl(selectedInfokami.file_pdf) + '#toolbar=1'"
            class="w-full h-full border-0 min-h-[800px]"
            frameborder="0"
          ></iframe>
        </div>
      </div>
    </div>

    <!-- Berita Detail Modal -->
    <div
      v-if="selectedBerita"
      class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm overflow-y-auto h-full w-full z-50 flex items-center justify-center p-4"
      @click.self="selectedBerita = null"
    >
      <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-3xl max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 flex justify-between items-center">
          <h3 class="text-2xl font-bold text-gray-900">{{ selectedBerita.judul }}</h3>
          <button
            @click="selectedBerita = null"
            class="text-gray-500 hover:text-gray-700 transition-colors"
          >
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>
        <div class="p-6">
            <div v-if="selectedBerita.gambar" class="mb-6">
            <img
              :src="selectedBerita.gambar.startsWith('http') ? selectedBerita.gambar : `${apiBaseUrl}/storage/${selectedBerita.gambar}`"
              :alt="selectedBerita.judul"
              class="w-full h-64 object-cover rounded-xl"
            />
          </div>
          <div class="flex items-center text-sm text-gray-500 mb-6">
            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
            <span>{{ formatDate(selectedBerita.published_at) }}</span>
            <span v-if="selectedBerita.penulis" class="ml-4 flex items-center">
              <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
              </svg>
              {{ selectedBerita.penulis }}
            </span>
          </div>
          <div
            class="berita-content"
            v-html="selectedBerita.isi"
          ></div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted, computed, ref } from 'vue'
import { useWebsiteStore } from '../stores/website'
import { useBeritaStore } from '../stores/berita'
import { useSlideshowStore } from '../stores/slideshow'
import { useAuthStore } from '../stores/auth'

const websiteStore = useWebsiteStore()
const beritaStore = useBeritaStore()
const slideshowStore = useSlideshowStore()
const authStore = useAuthStore()

const website = computed(() => {
  try {
    return websiteStore.website || {}
  } catch (error) {
    console.warn('Error getting website:', error)
    return {}
  }
})
const berita = computed(() => {
  try {
    return beritaStore.berita || []
  } catch (error) {
    console.warn('Error getting berita:', error)
    return []
  }
})
const slideshow = computed(() => {
  try {
    return slideshowStore.slideshow || []
  } catch (error) {
    console.warn('Error getting slideshow:', error)
    return []
  }
})
const selectedBerita = ref(null)
const selectedInfokami = ref(null)
const infokamiList = ref([])
const infokamiLoading = ref(false)
const currentSlideIndex = ref(0)
const headlineBerita = ref([])
const bannerList = ref([])
const apiBaseUrl = import.meta.env.VITE_API_BASE_URL || window.location.origin
const showRegisterModal = ref(false)
const registerLoading = ref(false)
const registerError = ref('')
const registerSuccess = ref('')
const showMobileMenu = ref(false)

const registerForm = ref({
  name: '',
  nip: '',
  jabatan: '',
  email: '',
  password: '',
  password_confirmation: '',
  dinas_id: '',
  alamat: '',
  telepon: ''
})

const dinasList = ref([])

function stripHtml(html) {
  if (!html) return ''
  const tmp = document.createElement('DIV')
  tmp.innerHTML = html
  return tmp.textContent || tmp.innerText || ''
}

function formatDate(date) {
  if (!date) return new Date().toLocaleDateString('id-ID')
  return new Date(date).toLocaleDateString('id-ID', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

function getDefaultSambutan() {
  return `
    <p class="text-lg text-gray-700 mb-4">
      Assalamualaikum Warahmatullahi Wabarakatuh
    </p>
    <p class="text-gray-700 mb-4">
      Puji syukur kehadirat Allah SWT, atas rahmat dan karunia-Nya, kami dapat menyajikan 
      Sistem Informasi Pengusulan PERBUB (SISMAKUM) sebagai platform digital untuk memfasilitasi 
      proses pengajuan Peraturan Bupati secara efisien dan transparan.
    </p>
    <p class="text-gray-700 mb-4">
      Sistem ini dirancang untuk memudahkan seluruh dinas dalam mengajukan usulan peraturan, 
      sekaligus memberikan kemudahan bagi Bagian Hukum dalam melakukan verifikasi dan pengelolaan 
      usulan tersebut.
    </p>
    <p class="text-gray-700">
      Kami berharap sistem ini dapat memberikan kontribusi positif dalam meningkatkan kualitas 
      pelayanan publik dan transparansi dalam proses pengelolaan peraturan daerah.
    </p>
    <p class="text-gray-700 mt-4 font-semibold">
      Wassalamualaikum Warahmatullahi Wabarakatuh
    </p>
  `
}

function getDefaultProfil() {
  return `
    <p class="text-lg text-gray-700 mb-4">
      Bagian Hukum merupakan unit kerja yang bertanggung jawab dalam pengelolaan dan verifikasi 
      usulan Peraturan Bupati (PERBUB) dari berbagai dinas di lingkungan Pemerintah Daerah.
    </p>
    <p class="text-gray-700 mb-4">
      <strong>Fungsi Utama:</strong>
    </p>
    <ul class="list-disc list-inside text-gray-700 mb-4 space-y-2">
      <li>Menerima dan menelaah usulan PERBUB dari dinas terkait</li>
      <li>Melakukan verifikasi dan analisis hukum terhadap usulan yang masuk</li>
      <li>Memberikan rekomendasi dan catatan perbaikan jika diperlukan</li>
      <li>Mengkoordinasikan proses pengesahan PERBUB</li>
      <li>Menyediakan layanan konsultasi hukum bagi dinas</li>
    </ul>
    <p class="text-gray-700">
      Dengan adanya sistem SISMAKUM, proses pengelolaan PERBUB menjadi lebih terstruktur, 
      transparan, dan dapat dilacak dengan mudah oleh semua pihak terkait.
    </p>
  `
}

function getVisiContent() {
  if (website.value.visi_misi && website.value.visi_misi.content) {
    // Parse visi dari konten HTML
    const parser = new DOMParser()
    const doc = parser.parseFromString(website.value.visi_misi.content, 'text/html')
    const visiElement = doc.querySelector('[data-section="visi"]') || doc.querySelector('.visi') || doc.querySelector('h3:contains("Visi")')
    if (visiElement) {
      return visiElement.innerHTML
    }
    // Jika tidak ada struktur khusus, ambil paragraf pertama
    const firstP = doc.querySelector('p')
    if (firstP) {
      return firstP.innerHTML
    }
  }
  return `
    <p class="text-lg font-semibold text-gray-900 mb-2">
      Menjadi bagian hukum yang profesional, transparan, dan terpercaya dalam pengelolaan 
      Peraturan Bupati untuk mewujudkan tata kelola pemerintahan yang baik dan berkelanjutan.
    </p>
  `
}

function getMisiContent() {
  if (website.value.visi_misi && website.value.visi_misi.content) {
    // Parse misi dari konten HTML
    const parser = new DOMParser()
    const doc = parser.parseFromString(website.value.visi_misi.content, 'text/html')
    const misiElement = doc.querySelector('[data-section="misi"]') || doc.querySelector('.misi') || doc.querySelector('ul')
    if (misiElement) {
      return misiElement.innerHTML
    }
  }
  return `
    <ul class="list-disc list-inside text-gray-700 space-y-2">
      <li>Menyediakan layanan verifikasi dan analisis hukum yang berkualitas</li>
      <li>Memfasilitasi proses pengajuan PERBUB yang efisien dan transparan</li>
      <li>Meningkatkan koordinasi dan komunikasi dengan dinas terkait</li>
      <li>Mengembangkan sistem informasi yang modern dan user-friendly</li>
      <li>Memastikan kepatuhan terhadap peraturan perundang-undangan yang berlaku</li>
    </ul>
  `
}

function getSlideshowImage(slide) {
  // Handle berita headline
  if (slide.gambar) {
    if (slide.gambar.startsWith('http')) {
      return slide.gambar
    }
    return `${apiBaseUrl}/storage/${slide.gambar}`
  }
  // Handle slideshow biasa
  if (slide.image_path) {
    return `${apiBaseUrl}/storage/${slide.image_path}`
  }
  return slide.image_url || ''
}

function getSlideshowData() {
  // Prioritaskan berita headline jika ada
  if (headlineBerita.value.length > 0) {
    return headlineBerita.value
  }
  // Fallback ke slideshow biasa
  return slideshow.value
}

function getLeadershipPhotos() {
  try {
    const photos = []
    
    // Cari foto kepemimpinan dari website settings
    if (website.value && typeof website.value === 'object') {
      // Cari setting dengan key bupati_photo (kompatibilitas dengan data lama)
      let leadershipSetting = null
      
      // Cek semua values di website object
      const websiteValues = Object.values(website.value)
      
      // Prioritas 1: Cari dengan key bupati_photo
      leadershipSetting = websiteValues.find(item => 
        item && item.key === 'bupati_photo'
      )
      
      // Prioritas 2: Cari yang memiliki foto kepemimpinan
      if (!leadershipSetting) {
        leadershipSetting = websiteValues.find(item => 
          item && (item.bupati_photo || item.wakil_bupati_photo || item.sekda_photo)
        )
      }
      
      if (leadershipSetting) {
        console.log('Leadership setting found:', leadershipSetting)
        
        // Bupati (kiri) - selalu ditambahkan pertama
        if (leadershipSetting.bupati_photo) {
          const bupatiUrl = getImageUrl(leadershipSetting.bupati_photo)
          if (bupatiUrl) {
            photos.push({
              url: bupatiUrl,
              label: 'Bupati'
            })
            console.log('Bupati photo added:', bupatiUrl)
          }
        }
        
        // Wakil Bupati (tengah) - selalu ditambahkan kedua
        if (leadershipSetting.wakil_bupati_photo) {
          const wakilUrl = getImageUrl(leadershipSetting.wakil_bupati_photo)
          if (wakilUrl) {
            photos.push({
              url: wakilUrl,
              label: 'Wakil Bupati'
            })
            console.log('Wakil Bupati photo added:', wakilUrl)
          }
        }
        
        // Sekda (kanan) - selalu ditambahkan ketiga
        if (leadershipSetting.sekda_photo) {
          const sekdaUrl = getImageUrl(leadershipSetting.sekda_photo)
          if (sekdaUrl) {
            photos.push({
              url: sekdaUrl,
              label: 'Sekda'
            })
            console.log('Sekda photo added:', sekdaUrl)
          }
        }
      } else {
        console.log('No leadership setting found. Website keys:', Object.keys(website.value))
        console.log('Website values:', websiteValues)
      }
    }
    
    // Debug log untuk membantu troubleshooting
    console.log('Total leadership photos found:', photos.length)
    if (photos.length > 0) {
      console.log('Photos:', photos.map(p => p.label))
    }
    
    return photos
  } catch (error) {
    console.warn('Error getting leadership photos:', error)
    return []
  }
}

function handleSlideClick(slide) {
  // Jika slide adalah berita headline, buka detail berita
  if (slide.slug) {
    viewBerita(slide.slug)
  } else if (slide.link) {
    window.location.href = slide.link
  }
}

function nextSlide() {
  const slideshowData = getSlideshowData()
  if (slideshowData.length > 0) {
    currentSlideIndex.value = (currentSlideIndex.value + 1) % slideshowData.length
  }
}

function previousSlide() {
  const slideshowData = getSlideshowData()
  if (slideshowData.length > 0) {
    currentSlideIndex.value = (currentSlideIndex.value - 1 + slideshowData.length) % slideshowData.length
  }
}

async function loadHeadlineBerita() {
  try {
    const response = await fetch(`${apiBaseUrl}/api/berita/headline`, {
      method: 'GET',
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json'
      }
    })
    
    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`)
    }
    
    const data = await response.json()
    
    if (Array.isArray(data)) {
      headlineBerita.value = data
    } else if (data && Array.isArray(data.data)) {
      headlineBerita.value = data.data
    } else {
      headlineBerita.value = []
    }
  } catch (error) {
    console.error('Error loading headline berita:', error)
    headlineBerita.value = []
  }
}

function parseFiturDescription(content) {
  try {
    if (!content) return 'Platform lengkap untuk pengelolaan pengusulan PERBUB dengan berbagai fitur unggulan'
    // Extract text dari HTML untuk digunakan sebagai subtitle
    const tmp = document.createElement('DIV')
    tmp.innerHTML = content
    const text = tmp.textContent || tmp.innerText || ''
    return text.substring(0, 150) + (text.length > 150 ? '...' : '')
  } catch (error) {
    console.warn('Error parsing fitur description:', error)
    return 'Platform lengkap untuk pengelolaan pengusulan PERBUB dengan berbagai fitur unggulan'
  }
}

function getDefaultFeatures() {
  // Return default features jika tidak ada dari API
  try {
    return []
  } catch (error) {
    console.warn('Error getting default features:', error)
    return []
  }
}

function getDummyBerita() {
  // Jika ada berita dari API, gunakan itu saja
  if (berita.value && berita.value.length > 0) {
    return berita.value.slice(0, 6)
  }
  
  // Fallback dummy berita jika tidak ada dari API
  return [
    {
      id: 1,
      judul: 'Peluncuran Sistem SISMAKUM Versi Terbaru',
      slug: 'peluncuran-sistem-sismakum-versi-terbaru',
      isi: 'Sistem Informasi Pengusulan PERBUB (SISMAKUM) telah diperbarui dengan berbagai fitur baru yang memudahkan proses pengajuan dan verifikasi usulan peraturan.',
      published_at: new Date().toISOString(),
      views: 1250,
      penulis: 'Admin Bagian Hukum',
      gambar: 'https://images.unsplash.com/photo-1451187580459-43490279c0fa?w=800&h=600&fit=crop'
    },
    {
      id: 2,
      judul: 'Workshop Penggunaan SISMAKUM untuk Dinas',
      slug: 'workshop-penggunaan-sismakum-untuk-dinas',
      isi: 'Bagian Hukum mengadakan workshop pelatihan penggunaan sistem SISMAKUM untuk seluruh dinas guna meningkatkan pemahaman dan efektivitas pengajuan PERBUB.',
      published_at: new Date(Date.now() - 86400000 * 3).toISOString(),
      views: 890,
      penulis: 'Tim Bagian Hukum',
      gambar: 'https://images.unsplash.com/photo-1524178232363-1fb2b075b655?w=800&h=600&fit=crop'
    },
    {
      id: 3,
      judul: 'Panduan Lengkap Pengajuan PERBUB Melalui SISMAKUM',
      slug: 'panduan-lengkap-pengajuan-perbub-melalui-sismakum',
      isi: 'Panduan lengkap untuk mengajukan Peraturan Bupati melalui sistem SISMAKUM, termasuk persiapan dokumen, proses pengajuan, dan tracking status.',
      published_at: new Date(Date.now() - 86400000 * 7).toISOString(),
      views: 2150,
      penulis: 'Admin Bagian Hukum',
      gambar: 'https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?w=800&h=600&fit=crop'
    }
  ]
}

async function viewBerita(slug) {
  try {
    // Cek apakah berita dari API atau dummy
    const beritaItem = berita.value.find(b => b.slug === slug) || 
                      getDummyBerita().find(b => b.slug === slug)
    
    if (beritaItem) {
      selectedBerita.value = beritaItem
      return
    }
    
    // Jika tidak ditemukan, coba fetch dari API
    await beritaStore.fetchDetail(slug)
    selectedBerita.value = beritaStore.currentBerita
  } catch (error) {
    console.error('Error loading berita:', error)
    // Fallback ke dummy berita
    const dummy = getDummyBerita().find(b => b.slug === slug)
    if (dummy) {
      selectedBerita.value = dummy
    }
  }
}

async function openRegisterModal() {
  showRegisterModal.value = true
  // Load dinas list when modal opens
  await loadDinasList()
}

async function loadDinasList() {
  try {
    console.log('Loading dinas from:', `${apiBaseUrl}/api/dinas`)
    const response = await fetch(`${apiBaseUrl}/api/dinas`, {
      method: 'GET',
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json'
      }
    })
    
    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`)
    }
    
    const data = await response.json()
    console.log('Dinas response:', data) // Debug log
    
    // Handle both array and object with data property
    if (Array.isArray(data)) {
      dinasList.value = data
    } else if (data && Array.isArray(data.data)) {
      dinasList.value = data.data
    } else {
      dinasList.value = []
    }
    
    console.log('Dinas list set to:', dinasList.value) // Debug log
  } catch (error) {
    console.error('Error loading dinas list:', error)
    alert('Gagal memuat daftar dinas. Silakan refresh halaman atau hubungi administrator.')
    dinasList.value = []
  }
}

async function handleRegister() {
  registerLoading.value = true
  registerError.value = ''
  registerSuccess.value = ''

  try {
    const response = await fetch(`${apiBaseUrl}/api/register`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify(registerForm.value)
    })

    const data = await response.json()

    if (!response.ok) {
      if (data.errors) {
        const errorMessages = Object.values(data.errors).flat()
        registerError.value = errorMessages.join(', ')
      } else {
        registerError.value = data.message || 'Terjadi kesalahan saat pendaftaran'
      }
      return
    }

    registerSuccess.value = data.message || 'Pendaftaran berhasil! Akun Anda akan diaktifkan oleh administrator.'
    
    // Reset form
    registerForm.value = {
      name: '',
      nip: '',
      jabatan: '',
      email: '',
      password: '',
      password_confirmation: '',
      dinas_id: '',
      alamat: '',
      telepon: ''
    }

    // Close modal after 3 seconds
    setTimeout(() => {
      showRegisterModal.value = false
      registerSuccess.value = ''
    }, 3000)
  } catch (error) {
    registerError.value = 'Terjadi kesalahan: ' + error.message
  } finally {
    registerLoading.value = false
  }
}

function getInfokamiImageUrl(path) {
  if (!path) return ''
  if (path.startsWith('http')) return path
  if (path.startsWith('/')) return `${apiBaseUrl}${path}`
  return `${apiBaseUrl}/storage/${path}`
}

function getInfokamiPdfUrl(path) {
  if (!path) return ''
  if (path.startsWith('http')) return path
  if (path.startsWith('/')) return `${apiBaseUrl}${path}`
  return `${apiBaseUrl}/storage/${path}`
}

function getImageUrl(path) {
  if (!path) return ''
  if (path.startsWith('http')) return path
  // Add cache busting timestamp to force browser to reload image
  const timestamp = new Date().getTime()
  return `${apiBaseUrl}/storage/${path}?t=${timestamp}`
}

function handleImageError(event) {
  event.target.src = 'data:image/svg+xml,%3Csvg xmlns=%27http://www.w3.org/2000/svg%27 width=%27100%27 height=%27100%27%3E%3Crect fill=%27%23ddd%27 width=%27100%27 height=%27100%27/%3E%3Ctext x=%2750%25%27 y=%2750%27 text-anchor=%27middle%27 dy=%27.3em%27 fill=%27%23999%27%3ENo Image%3C/text%3E%3C/svg%3E'
}

function openPdfModal(item) {
  selectedInfokami.value = item
}

function closePdfModal() {
  selectedInfokami.value = null
}

function scrollToSection(sectionId) {
  showMobileMenu.value = false
  const element = document.getElementById(sectionId)
  if (element) {
    const headerOffset = 80
    const elementPosition = element.getBoundingClientRect().top
    const offsetPosition = elementPosition + window.pageYOffset - headerOffset

    window.scrollTo({
      top: offsetPosition,
      behavior: 'smooth'
    })
  }
}

function toggleMobileMenu() {
  showMobileMenu.value = !showMobileMenu.value
}

async function loadInfokami() {
  infokamiLoading.value = true
  try {
    const response = await fetch(`${apiBaseUrl}/api/infokami`, {
      method: 'GET',
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json'
      }
    })
    
    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`)
    }
    
    const data = await response.json()
    
    if (data.success && Array.isArray(data.data)) {
      infokamiList.value = data.data
    } else if (Array.isArray(data)) {
      infokamiList.value = data
    } else {
      infokamiList.value = []
    }
  } catch (error) {
    console.error('Error loading INFOKAMI:', error)
    infokamiList.value = []
  } finally {
    infokamiLoading.value = false
  }
}

async function loadBanners() {
  try {
    const response = await fetch(`${apiBaseUrl}/api/banners`, {
      method: 'GET',
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json'
      }
    })
    
    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`)
    }
    
    const data = await response.json()
    
    if (Array.isArray(data)) {
      bannerList.value = data
    } else if (data && Array.isArray(data.data)) {
      bannerList.value = data.data
    } else {
      bannerList.value = []
    }
  } catch (error) {
    console.error('Error loading banners:', error)
    bannerList.value = []
  }
}

onMounted(async () => {
  try {
    await Promise.all([
      websiteStore.fetchPublic().catch(err => {
        console.warn('Error loading website content:', err)
        return {}
      }),
      beritaStore.fetchPublic().catch(err => {
        console.warn('Error loading berita:', err)
        return []
      }),
      slideshowStore.fetchPublic().catch(err => {
        console.warn('Error loading slideshow:', err)
        return []
      }),
      loadHeadlineBerita().catch(err => {
        console.warn('Error loading headline berita:', err)
      }),
      loadInfokami().catch(err => {
        console.warn('Error loading infokami:', err)
      }),
      loadBanners().catch(err => {
        console.warn('Error loading banners:', err)
      })
    ])
    
    // Auto-rotate slideshow
    try {
      const slideshowData = getSlideshowData()
      if (slideshowData.length > 1) {
        setInterval(() => {
          nextSlide()
        }, 5000) // Change slide every 5 seconds
      }
    } catch (error) {
      console.warn('Error setting up slideshow rotation:', error)
    }
  } catch (error) {
    console.error('Error loading website:', error)
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

.line-clamp-3 {
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

@keyframes blob {
  0% {
    transform: translate(0px, 0px) scale(1);
  }
  33% {
    transform: translate(30px, -50px) scale(1.1);
  }
  66% {
    transform: translate(-20px, 20px) scale(0.9);
  }
  100% {
    transform: translate(0px, 0px) scale(1);
  }
}

.animate-blob {
  animation: blob 7s infinite;
}

.animation-delay-2000 {
  animation-delay: 2s;
}

.animation-delay-4000 {
  animation-delay: 4s;
}
</style>
