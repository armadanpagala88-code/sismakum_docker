# File di Root Project

File-file di root project ini digunakan untuk deployment ke hosting.

## File Penting

### `index.php`
File utama yang mengarahkan request ke frontend atau backend:
- Request `/api/*` → Diarahkan ke Laravel backend
- Request `/storage/*` → Diarahkan ke Laravel storage
- Request lainnya → Diarahkan ke Vue.js frontend (SPA)

**Cara kerja:**
1. Jika frontend sudah di-build (`frontend/dist/index.html` ada), serve file dari dist
2. Jika belum di-build, tampilkan pesan development mode

### `.htaccess`
Konfigurasi Apache untuk:
- Rewrite rules mengarahkan request ke `index.php`
- Security: deny access ke file sensitif
- Enable mod_rewrite

### `web.config`
Konfigurasi IIS/Windows hosting untuk:
- URL rewrite rules
- Default document
- Security settings

## Struktur Deployment

```
SISMAKUM2/                    # Root hosting
├── index.php                 # Entry point utama
├── .htaccess                 # Apache config
├── web.config                # IIS config
├── backend/                  # Laravel backend
│   ├── public/              # Laravel public (tidak langsung diakses)
│   └── ...
└── frontend/                 # Vue.js frontend
    └── dist/                # Build output (setelah npm run build)
        └── index.html       # Vue.js SPA
```

## Setup untuk Hosting

1. **Upload semua file** ke root hosting
2. **Build frontend**: `cd frontend && npm run build`
3. **Setup backend**: `cd backend && composer install && php artisan migrate`
4. **Set permissions** (Linux): `chmod 755 index.php`

Lihat [HOSTING_SETUP.md](./HOSTING_SETUP.md) untuk panduan lengkap.

## Testing

Setelah upload:
- Akses homepage → Harus menampilkan aplikasi Vue.js
- Akses `/api` → Harus menampilkan response Laravel
- Login dengan kredensial default

## Catatan

- File `index.php` ini hanya sebagai router, tidak mengubah struktur Laravel
- Backend Laravel tetap menggunakan struktur standar
- Frontend harus di-build sebelum deployment (`npm run build`)
- Hasil build ada di `frontend/dist/` dan akan otomatis di-serve oleh `index.php`
