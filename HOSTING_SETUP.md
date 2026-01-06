# Setup Hosting dengan Root Index.php

File `index.php` di root project memudahkan deployment ke hosting karena otomatis mengarahkan request ke frontend atau backend.

## Cara Kerja

1. **Request ke `/api/*`** → Diarahkan ke Laravel backend
2. **Request ke `/storage/*`** → Diarahkan ke Laravel storage
3. **Request lainnya** → Diarahkan ke Vue.js frontend (SPA)

## Struktur File

```
SISMAKUM2/
├── index.php          # Root router (file utama)
├── .htaccess          # Apache configuration
├── web.config         # IIS/Windows configuration
├── backend/           # Laravel backend
└── frontend/          # Vue.js frontend
    └── dist/          # Build output (setelah npm run build)
```

## Setup untuk Hosting

### Opsi 1: Menggunakan Root Index.php (Recommended)

**Keuntungan:**
- ✅ Tidak perlu konfigurasi web server yang rumit
- ✅ Cocok untuk shared hosting
- ✅ Semua request di-handle oleh satu entry point

**Langkah-langkah:**

1. **Upload semua file ke root hosting**
   ```
   /public_html/  (atau folder root hosting Anda)
   ├── index.php
   ├── .htaccess
   ├── backend/
   └── frontend/
   ```

2. **Build frontend untuk production**
   ```bash
   cd frontend
   npm run build
   ```
   Ini akan membuat folder `frontend/dist/` yang berisi file build.

3. **Setup backend Laravel**
   ```bash
   cd backend
   composer install --no-dev --optimize-autoloader
   cp .env.example .env
   php artisan key:generate
   # Edit .env dengan konfigurasi database
   php artisan migrate --force
   php artisan storage:link
   ```

4. **Set permissions** (jika menggunakan Linux hosting)
   ```bash
   chmod 755 index.php
   chmod 644 .htaccess
   chmod -R 755 backend
   chmod -R 755 frontend
   chmod -R 775 backend/storage
   chmod -R 775 backend/bootstrap/cache
   ```

5. **Konfigurasi .env backend**
   ```env
   APP_URL=http://your-domain.com
   FRONTEND_URL=http://your-domain.com
   ```

### Opsi 2: Menggunakan Subfolder (Alternatif)

Jika hosting Anda mengharuskan struktur tertentu:

1. **Upload backend ke subfolder**
   ```
   /public_html/
   ├── index.php        # Root router
   ├── .htaccess
   ├── api/            # Symlink atau copy dari backend/public
   └── app/            # Folder aplikasi
       ├── backend/
       └── frontend/
   ```

## Konfigurasi Web Server

### Apache (Shared Hosting)

File `.htaccess` sudah disertakan dan akan otomatis bekerja. Pastikan:
- `mod_rewrite` enabled
- `AllowOverride All` di konfigurasi Apache

### Nginx

Jika menggunakan VPS dengan Nginx, gunakan konfigurasi berikut:

```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /path/to/SISMAKUM2;
    index index.php index.html;

    # API requests
    location /api {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # PHP files
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }

    # All other requests
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
}
```

### IIS/Windows Hosting

File `web.config` sudah disertakan dan akan otomatis bekerja. Pastikan:
- URL Rewrite module terinstall
- PHP handler dikonfigurasi

## Testing

Setelah setup, test dengan:

1. **Akses homepage**: `http://your-domain.com`
   - Harus menampilkan aplikasi Vue.js

2. **Test API**: `http://your-domain.com/api`
   - Harus menampilkan response dari Laravel

3. **Test login**: Login dengan kredensial default
   - Email: `admin@sismakum.local`
   - Password: `password`

## Troubleshooting

### Error 500 Internal Server Error

**Penyebab:**
- Permissions tidak benar
- PHP version tidak sesuai
- Extension PHP tidak terinstall

**Solusi:**
```bash
# Set permissions
chmod 755 index.php
chmod -R 755 backend
chmod -R 775 backend/storage

# Cek PHP version (harus 8.1+)
php -v

# Cek error log
tail -f backend/storage/logs/laravel.log
```

### Frontend tidak muncul

**Penyebab:**
- Frontend belum di-build
- File dist tidak ada

**Solusi:**
```bash
cd frontend
npm install
npm run build
# Pastikan folder frontend/dist/ ada dan berisi index.html
```

### API tidak bisa diakses

**Penyebab:**
- .htaccess tidak bekerja
- mod_rewrite tidak enabled

**Solusi:**
- Pastikan `.htaccess` ada di root
- Cek apakah hosting mendukung mod_rewrite
- Hubungi support hosting jika perlu

### CORS Error

**Penyebab:**
- Frontend dan backend di domain berbeda
- CORS tidak dikonfigurasi

**Solusi:**
Edit `backend/config/cors.php`:
```php
'paths' => ['api/*', 'sanctum/csrf-cookie'],
'allowed_origins' => ['http://your-domain.com'],
```

## Update Aplikasi

Untuk update aplikasi di hosting:

1. **Backup database dan file**
2. **Upload file baru** (atau pull dari Git)
3. **Update dependencies:**
   ```bash
   cd backend
   composer install --no-dev --optimize-autoloader
   
   cd ../frontend
   npm install
   npm run build
   ```
4. **Run migrations:**
   ```bash
   cd backend
   php artisan migrate --force
   ```
5. **Clear cache:**
   ```bash
   php artisan config:clear
   php artisan route:clear
   php artisan view:clear
   ```

## Keamanan

1. **Jangan upload file sensitif:**
   - `.env` (buat langsung di hosting)
   - `node_modules/`
   - `.git/`

2. **Set permissions dengan benar:**
   ```bash
   chmod 644 .env
   chmod -R 755 backend
   chmod -R 775 backend/storage
   ```

3. **Gunakan HTTPS:**
   - Setup SSL certificate
   - Update `APP_URL` di `.env` ke `https://`

## Catatan Penting

- **Web root** adalah folder root project (dimana `index.php` berada)
- **Frontend build** harus ada di `frontend/dist/` sebelum aplikasi bisa berjalan
- **Backend Laravel** tetap menggunakan struktur standar Laravel
- File `index.php` di root hanya sebagai router, tidak mengubah struktur Laravel
