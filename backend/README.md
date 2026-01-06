# Backend Laravel - Sistem Pengusulan PERBUB

## Instalasi

1. Install dependencies:
```bash
composer install
```

2. Copy file .env:
```bash
cp .env.example .env
```

3. Generate application key:
```bash
php artisan key:generate
```

4. Setup database di file .env

5. Jalankan migrations:
```bash
php artisan migrate
```

6. Jalankan server:
```bash
php artisan serve
```

## API Endpoints

### Authentication
- POST `/api/login` - Login
- GET `/api/me` - Get current user
- POST `/api/logout` - Logout

### Pengusulan PERBUB
- GET `/api/pengusulan-perbub` - List pengusulan
- POST `/api/pengusulan-perbub` - Create pengusulan
- GET `/api/pengusulan-perbub/{id}` - Get detail
- PUT `/api/pengusulan-perbub/{id}` - Update pengusulan
- DELETE `/api/pengusulan-perbub/{id}` - Delete pengusulan
- POST `/api/pengusulan-perbub/{id}/ajukan` - Ajukan pengusulan
- POST `/api/pengusulan-perbub/{id}/review` - Review pengusulan (bagian hukum)

