# Troubleshooting Login Error 500

## Masalah yang Sudah Diperbaiki

1. ✅ **CORS Configuration** - Diperbaiki untuk allow semua origin
2. ✅ **Sanctum Middleware** - EnsureFrontendRequestsAreStateful di-comment untuk API token-based
3. ✅ **AuthController** - Ditambahkan error handling yang lebih baik
4. ✅ **HandleCors Middleware** - Diperbaiki untuk handle OPTIONS request

## Langkah Testing

### 1. Pastikan Server Berjalan
```bash
cd backend
php artisan serve
```

### 2. Test dengan cURL
```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"email":"admin@sismakum.local","password":"password"}'
```

### 3. Cek Log Error
```bash
tail -f storage/logs/laravel.log
```

### 4. Clear Cache
```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
```

## User Default untuk Testing

- **Admin**: admin@sismakum.local / password
- **Dinas**: dinas@sismakum.local / password
- **Bagian Hukum**: hukum@sismakum.local / password

## Jika Masih Error

1. Pastikan database sudah di-migrate:
   ```bash
   php artisan migrate:status
   ```

2. Pastikan user sudah di-seed:
   ```bash
   php artisan db:seed
   ```

3. Cek apakah password di-hash dengan benar:
   ```bash
   php artisan tinker
   >>> Hash::check('password', User::first()->password)
   ```

4. Pastikan Sanctum migration sudah jalan:
   ```bash
   php artisan migrate:status | grep personal_access_tokens
   ```

