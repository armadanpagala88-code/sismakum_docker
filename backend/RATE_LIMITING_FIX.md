# Fix Rate Limiting "Too Many Attempts"

## Masalah
Error "Too Many Attempts" terjadi karena rate limiting Laravel terlalu ketat.

## Solusi yang Diterapkan

### 1. Custom Rate Limiter
Di `RouteServiceProvider`, dibuat custom rate limiter:
- **API**: 120 requests per minute (ditingkatkan dari default 60)
- **Login**: 10 attempts per minute (untuk keamanan)

### 2. Clear Cache
Jalankan perintah berikut untuk clear cache:
```bash
php artisan cache:clear
php artisan route:clear
php artisan config:clear
```

### 3. Jika Masih Error
Jika masih mengalami "Too Many Attempts", tunggu beberapa menit atau:

**Untuk Development (Nonaktifkan throttle):**
Ubah di `app/Http/Kernel.php`:
```php
'api' => [
    // 'throttle:api', // Comment ini untuk disable
    \Illuminate\Routing\Middleware\SubstituteBindings::class,
],
```

**Atau tingkatkan limit:**
Ubah di `RouteServiceProvider.php`:
```php
RateLimiter::for('api', function ($request) {
    return Limit::perMinute(500)->by(optional($request->user())->id ?: $request->ip());
});
```

## Rate Limiting Configuration

- **API Routes**: 120 requests/minute
- **Login Route**: 10 attempts/minute
- **Rate limiter menggunakan IP address atau user ID**

