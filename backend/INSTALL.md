# Panduan Instalasi SISMAKUM Backend

## Persyaratan
- PHP >= 8.1
- Composer
- MySQL/MariaDB
- Node.js & NPM (untuk frontend)

## Langkah Instalasi

### 1. Install Dependencies
```bash
cd backend
composer install
```

### 2. Setup Environment
```bash
cp .env.example .env
php artisan key:generate
```

### 3. Konfigurasi Database
Edit file `.env` dan sesuaikan konfigurasi database:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sismakum
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Jalankan Migrations
```bash
php artisan migrate
```

### 5. Seed Database (Opsional)
```bash
php artisan db:seed
```

Ini akan membuat 3 user default:
- **Admin**: admin@sismakum.local / password
- **Dinas**: dinas@sismakum.local / password  
- **Bagian Hukum**: hukum@sismakum.local / password

### 6. Buat Storage Link
```bash
php artisan storage:link
```

### 7. Jalankan Server
```bash
php artisan serve
```

Server akan berjalan di `http://localhost:8000`

## Troubleshooting

### Error PHP.ini
Jika muncul error:
```
PHP: syntax error, unexpected END_OF_LINE, expecting '=' in /opt/homebrew/etc/php/8.4/php.ini on line 2
```

Ini adalah masalah konfigurasi PHP lokal. Perbaiki file `/opt/homebrew/etc/php/8.4/php.ini` di baris 2.

### Error Method hourly() does not exist
Sudah diperbaiki di `routes/console.php`. Pastikan tidak ada `->hourly()` di file tersebut.

### Error .env.example not found
File sudah dibuat. Jika masih error, jalankan:
```bash
cp .env.example .env
```

## Testing API

Setelah server berjalan, test dengan:
```bash
curl http://localhost:8000/api/login \
  -X POST \
  -H "Content-Type: application/json" \
  -d '{"email":"dinas@sismakum.local","password":"password"}'
```

