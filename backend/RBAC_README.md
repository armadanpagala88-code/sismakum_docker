# RBAC (Role-Based Access Control) - Dokumentasi

## Fitur RBAC

Sistem RBAC telah diimplementasikan dengan 3 role utama:
- **Admin**: Akses penuh untuk mengelola dinas dan user
- **Dinas**: Dapat membuat dan mengajukan pengusulan PERBUB
- **Bagian Hukum**: Dapat review pengusulan PERBUB

## Database Structure

### Tabel `dinas`
- `id` - Primary key
- `nama_dinas` - Nama dinas
- `kode_dinas` - Kode unik dinas (optional)
- `alamat` - Alamat dinas
- `telepon` - Nomor telepon
- `email` - Email dinas
- `is_active` - Status aktif/tidak aktif

### Tabel `users`
- Menambahkan kolom `dinas_id` untuk relasi dengan dinas

## API Endpoints (Admin Only)

### Dinas Management
- `GET /api/admin/dinas` - List semua dinas
- `POST /api/admin/dinas` - Buat dinas baru
- `GET /api/admin/dinas/{id}` - Detail dinas
- `PUT /api/admin/dinas/{id}` - Update dinas
- `DELETE /api/admin/dinas/{id}` - Hapus dinas

### User Management
- `GET /api/admin/users` - List semua user
- `POST /api/admin/users` - Buat user baru
- `GET /api/admin/users/{id}` - Detail user
- `PUT /api/admin/users/{id}` - Update user
- `DELETE /api/admin/users/{id}` - Hapus user

## Middleware

### AdminMiddleware
Middleware untuk memastikan hanya admin yang bisa mengakses route admin.

## Frontend Routes

- `/admin` - Admin Dashboard
- `/admin/dinas` - Kelola Dinas
- `/admin/users` - Kelola Users

## Setup

1. Jalankan migrations:
```bash
php artisan migrate
```

2. Login sebagai admin:
- Email: `admin@sismakum.local`
- Password: `password`

3. Akses menu Admin di navigation bar untuk mengelola dinas dan user.

## Cara Menggunakan

### Membuat Dinas Baru
1. Login sebagai admin
2. Klik menu "Admin" > "Kelola Dinas"
3. Klik "Tambah Dinas"
4. Isi form dan simpan

### Membuat User untuk Dinas
1. Login sebagai admin
2. Klik menu "Admin" > "Kelola Users"
3. Klik "Tambah User"
4. Isi form:
   - Nama, Email, Password
   - Role: pilih "Dinas"
   - Dinas: pilih dinas yang sudah dibuat
   - Unit Kerja (optional)
5. Simpan

