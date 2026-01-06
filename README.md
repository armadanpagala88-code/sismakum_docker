# SISMAKUM - Sistem Pengusulan PERBUB

Aplikasi untuk pengusulan Peraturan Bupati (PERBUB) oleh Dinas ke Bagian Hukum.

## Struktur Project

```
SISMAKUM2/
├── backend/          # Laravel Backend API
└── frontend/         # Vue.js Frontend
```

## Teknologi

### Backend
- Laravel 10
- Laravel Sanctum (Authentication)
- MySQL Database

### Frontend
- Vue.js 3
- Vue Router
- Pinia (State Management)
- Axios
- Vite

## Instalasi

### Backend

1. Masuk ke folder backend:
```bash
cd backend
```

2. Install dependencies:
```bash
composer install
```

3. Copy file environment:
```bash
cp .env.example .env
```

4. Generate application key:
```bash
php artisan key:generate
```

5. Setup database di file `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sismakum
DB_USERNAME=root
DB_PASSWORD=
```

6. Jalankan migrations:
```bash
php artisan migrate
```

7. (Opsional) Seed database dengan data sample:
```bash
php artisan db:seed
```

8. Buat symbolic link untuk storage:
```bash
php artisan storage:link
```

9. Jalankan server:
```bash
php artisan serve
```

Backend akan berjalan di `http://localhost:8000`

### Frontend

1. Masuk ke folder frontend:
```bash
cd frontend
```

2. Install dependencies:
```bash
npm install
```

3. Jalankan development server:
```bash
npm run dev
```

Frontend akan berjalan di `http://localhost:5173`

## User Default (Setelah Seeding)

- **Admin**
  - Email: `admin@sismakum.local`
  - Password: `password`
  - Role: Admin

- **Dinas**
  - Email: `dinas@sismakum.local`
  - Password: `password`
  - Role: Dinas

- **Bagian Hukum**
  - Email: `hukum@sismakum.local`
  - Password: `password`
  - Role: Bagian Hukum

## Fitur

### Untuk Dinas:
- Membuat pengusulan PERBUB baru
- Mengedit pengusulan yang masih draft
- Mengajukan pengusulan ke Bagian Hukum
- Melihat status pengusulan
- Upload dokumen pendukung

### Untuk Bagian Hukum:
- Melihat daftar pengusulan yang diajukan
- Review pengusulan (Diterima/Ditolak/Revisi)
- Memberikan catatan review
- Melihat detail pengusulan

### Untuk Admin:
- Akses penuh ke semua fitur
- Manajemen pengusulan

## API Endpoints

### Authentication
- `POST /api/login` - Login
- `GET /api/me` - Get current user
- `POST /api/logout` - Logout

### Pengusulan PERBUB
- `GET /api/pengusulan-perbub` - List pengusulan
- `POST /api/pengusulan-perbub` - Create pengusulan
- `GET /api/pengusulan-perbub/{id}` - Get detail
- `PUT /api/pengusulan-perbub/{id}` - Update pengusulan
- `DELETE /api/pengusulan-perbub/{id}` - Delete pengusulan
- `POST /api/pengusulan-perbub/{id}/ajukan` - Ajukan pengusulan
- `POST /api/pengusulan-perbub/{id}/review` - Review pengusulan

## Status Pengusulan

- **draft** - Masih dalam tahap penyusunan
- **diajukan** - Sudah diajukan ke Bagian Hukum
- **diterima** - Diterima oleh Bagian Hukum
- **ditolak** - Ditolak oleh Bagian Hukum
- **revisi** - Perlu direvisi

## Deployment

### Opsi 1: Menggunakan Root Index.php (Shared Hosting)

File `index.php` di root project memudahkan deployment ke hosting karena otomatis mengarahkan request:
- `/api/*` → Laravel Backend
- `/storage/*` → Laravel Storage  
- Lainnya → Vue.js Frontend

**Lihat: [HOSTING_SETUP.md](./HOSTING_SETUP.md)** untuk panduan lengkap setup hosting dengan root index.php.

### Opsi 2: Web Server Direct ke Backend (VPS)

Untuk deployment ke VPS dengan konfigurasi web server langsung ke backend:

**Lihat:**
- **[DEPLOYMENT.md](./DEPLOYMENT.md)** - Panduan lengkap deployment dengan konfigurasi web server
- **[QUICK_DEPLOY.md](./QUICK_DEPLOY.md)** - Panduan cepat deployment

### Poin Penting Deployment:

**Opsi 1 (Root Index.php):**
- Web root: Folder root project (dimana `index.php` berada)
- Frontend build: `frontend/dist/` (otomatis di-handle oleh `index.php`)
- Cocok untuk: Shared hosting, hosting yang tidak bisa konfigurasi web server

**Opsi 2 (Direct ke Backend):**
- Web root: `/var/www/sismakum/backend/public`
- Frontend build: Build dengan `npm run build`, lalu copy hasil `dist/` ke `backend/public/`
- Web server: Konfigurasi Apache atau Nginx untuk menunjuk ke folder `backend/public`
- Cocok untuk: VPS, dedicated server

**File konfigurasi:**
- `apache.conf.example` - Konfigurasi Apache (untuk Opsi 2)
- `nginx.conf.example` - Konfigurasi Nginx (untuk Opsi 2)
- `.htaccess` - Apache config untuk root index.php (untuk Opsi 1)
- `web.config` - IIS config untuk root index.php (untuk Opsi 1)

**Script Deploy:**
- `deploy.sh` - Script otomatis untuk update aplikasi (Opsi 2)

## Lisensi

MIT License

