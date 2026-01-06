# Panduan Deployment SISMAKUM ke VPS

Panduan lengkap untuk mengupload dan mengkonfigurasi aplikasi SISMAKUM ke VPS.

## Opsi Deployment

Ada 2 cara deployment:

1. **Menggunakan Root Index.php** (Recommended untuk Shared Hosting)
   - File `index.php` di root otomatis mengarahkan request
   - Cocok untuk shared hosting
   - Lihat [HOSTING_SETUP.md](./HOSTING_SETUP.md) untuk detail

2. **Web Server Direct ke Backend** (Recommended untuk VPS)
   - Web server langsung menunjuk ke `backend/public`
   - Lebih optimal untuk VPS
   - Panduan di bawah ini menggunakan metode ini

---

## Deployment dengan Web Server Direct ke Backend

## Prasyarat

- VPS dengan Ubuntu/Debian
- Akses SSH ke VPS
- PHP 8.1+ dengan ekstensi yang diperlukan
- Composer
- Node.js 18+ dan npm
- MySQL/MariaDB
- Apache atau Nginx
- Git (opsional)

## 1. Persiapan VPS

### Install Dependencies

```bash
# Update sistem
sudo apt update && sudo apt upgrade -y

# Install PHP dan ekstensi
sudo apt install -y php8.1 php8.1-fpm php8.1-mysql php8.1-mbstring \
    php8.1-xml php8.1-bcmath php8.1-curl php8.1-zip php8.1-gd

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Install Node.js dan npm
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt install -y nodejs

# Install MySQL
sudo apt install -y mysql-server

# Install Apache atau Nginx (pilih salah satu)
# Untuk Apache:
sudo apt install -y apache2 libapache2-mod-php8.1

# Untuk Nginx:
sudo apt install -y nginx
```

## 2. Upload File ke VPS

### Metode 1: Menggunakan Git (Recommended)

```bash
# Di VPS, clone repository
cd /var/www
sudo git clone <repository-url> sismakum
sudo chown -R www-data:www-data sismakum
```

### Metode 2: Menggunakan SCP/SFTP

```bash
# Dari komputer lokal, upload ke VPS
scp -r /path/to/SISMAKUM2 user@your-vps-ip:/var/www/sismakum

# Atau menggunakan SFTP client seperti FileZilla
# Upload folder SISMAKUM2 ke /var/www/sismakum
```

### Metode 3: Menggunakan rsync

```bash
rsync -avz --exclude 'node_modules' --exclude 'vendor' \
    /path/to/SISMAKUM2/ user@your-vps-ip:/var/www/sismakum/
```

## 3. Setup Backend Laravel

```bash
# Masuk ke folder backend
cd /var/www/sismakum/backend

# Install dependencies
composer install --optimize-autoloader --no-dev

# Copy file environment
cp .env.example .env

# Generate application key
php artisan key:generate

# Edit file .env
nano .env
```

### Konfigurasi .env

```env
APP_NAME=SISMAKUM
APP_ENV=production
APP_KEY=base64:... (akan di-generate otomatis)
APP_DEBUG=false
APP_URL=http://your-domain.com

LOG_CHANNEL=stack
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sismakum
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_password

# Sanctum
SANCTUM_STATEFUL_DOMAINS=your-domain.com
SESSION_DOMAIN=your-domain.com

# CORS (jika frontend di domain berbeda)
FRONTEND_URL=http://your-domain.com
```

### Setup Database

```bash
# Login ke MySQL
sudo mysql -u root -p

# Buat database dan user
CREATE DATABASE sismakum;
CREATE USER 'sismakum_user'@'localhost' IDENTIFIED BY 'strong_password';
GRANT ALL PRIVILEGES ON sismakum.* TO 'sismakum_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### Jalankan Migrations

```bash
cd /var/www/sismakum/backend
php artisan migrate --force
php artisan db:seed --force

# Buat symbolic link untuk storage
php artisan storage:link

# Optimize aplikasi
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Set Permissions

```bash
# Set ownership
sudo chown -R www-data:www-data /var/www/sismakum

# Set permissions
sudo chmod -R 755 /var/www/sismakum
sudo chmod -R 775 /var/www/sismakum/backend/storage
sudo chmod -R 775 /var/www/sismakum/backend/bootstrap/cache
```

## 4. Build Frontend untuk Production

```bash
# Masuk ke folder frontend
cd /var/www/sismakum/frontend

# Install dependencies
npm install

# Buat file .env untuk frontend (opsional)
echo "VITE_API_URL=/api" > .env.production

# Build untuk production
npm run build
```

Setelah build, folder `dist` akan dibuat. File-file ini akan di-copy ke folder public backend.

```bash
# Copy hasil build ke public backend
cp -r /var/www/sismakum/frontend/dist/* /var/www/sismakum/backend/public/
```

## 5. Konfigurasi Web Server

### Opsi A: Apache Configuration

Buat file konfigurasi Apache:

```bash
sudo nano /etc/apache2/sites-available/sismakum.conf
```

Isi dengan:

```apache
<VirtualHost *:80>
    ServerName your-domain.com
    ServerAlias www.your-domain.com
    
    DocumentRoot /var/www/sismakum/backend/public
    
    <Directory /var/www/sismakum/backend/public>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
        
        # Handle Vue Router (SPA)
        RewriteEngine On
        RewriteBase /
        RewriteRule ^index\.html$ - [L]
        RewriteCond %{REQUEST_FILENAME} !-f
        RewriteCond %{REQUEST_FILENAME} !-d
        RewriteRule . /index.html [L]
    </Directory>
    
    # API Routes
    <Directory /var/www/sismakum/backend/public/api>
        RewriteEngine On
        RewriteCond %{REQUEST_FILENAME} !-f
        RewriteCond %{REQUEST_FILENAME} !-d
        RewriteRule ^ /index.php [L]
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/sismakum_error.log
    CustomLog ${APACHE_LOG_DIR}/sismakum_access.log combined
</VirtualHost>
```

Aktifkan site dan modul yang diperlukan:

```bash
# Enable mod_rewrite
sudo a2enmod rewrite
sudo a2enmod headers

# Enable site
sudo a2ensite sismakum.conf

# Disable default site (opsional)
sudo a2dissite 000-default.conf

# Restart Apache
sudo systemctl restart apache2
```

### Opsi B: Nginx Configuration

Buat file konfigurasi Nginx:

```bash
sudo nano /etc/nginx/sites-available/sismakum
```

Isi dengan:

```nginx
server {
    listen 80;
    server_name your-domain.com www.your-domain.com;
    root /var/www/sismakum/backend/public;
    index index.php index.html;

    # Log files
    access_log /var/log/nginx/sismakum_access.log;
    error_log /var/log/nginx/sismakum_error.log;

    # API Routes - Laravel
    location /api {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # Laravel public files
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    # Vue.js SPA - handle all routes
    location / {
        try_files $uri $uri/ /index.html;
    }

    # Static files caching
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|svg|woff|woff2|ttf|eot)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }

    # Deny access to hidden files
    location ~ /\. {
        deny all;
    }
}
```

Aktifkan site:

```bash
# Create symbolic link
sudo ln -s /etc/nginx/sites-available/sismakum /etc/nginx/sites-enabled/

# Test konfigurasi
sudo nginx -t

# Restart Nginx
sudo systemctl restart nginx
```

## 6. Setup SSL dengan Let's Encrypt (Opsional tapi Recommended)

```bash
# Install Certbot
sudo apt install -y certbot python3-certbot-apache
# atau untuk Nginx:
sudo apt install -y certbot python3-certbot-nginx

# Generate SSL certificate
# Untuk Apache:
sudo certbot --apache -d your-domain.com -d www.your-domain.com

# Untuk Nginx:
sudo certbot --nginx -d your-domain.com -d www.your-domain.com

# Auto-renewal sudah otomatis setup
```

## 7. Konfigurasi Firewall

```bash
# Jika menggunakan UFW
sudo ufw allow 'Apache Full'
# atau untuk Nginx:
sudo ufw allow 'Nginx Full'

# Enable firewall
sudo ufw enable
```

## 8. Setup Cron Job untuk Laravel Scheduler

```bash
# Edit crontab
sudo crontab -e -u www-data

# Tambahkan baris berikut:
* * * * * cd /var/www/sismakum/backend && php artisan schedule:run >> /dev/null 2>&1
```

## 9. Optimasi dan Security

### Update .env untuk Production

```env
APP_DEBUG=false
APP_ENV=production
```

### Setup Queue Worker (jika menggunakan queue)

```bash
# Install Supervisor
sudo apt install -y supervisor

# Buat konfigurasi supervisor
sudo nano /etc/supervisor/conf.d/sismakum-worker.conf
```

Isi dengan:

```ini
[program:sismakum-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/sismakum/backend/artisan queue:work --sleep=3 --tries=3
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/sismakum/backend/storage/logs/worker.log
stopwaitsecs=3600
```

```bash
# Reload supervisor
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start sismakum-worker:*
```

## 10. Testing

1. Akses `http://your-domain.com` atau `http://your-vps-ip`
2. Test login dengan kredensial default
3. Test semua fitur aplikasi

## 11. Update Aplikasi di Masa Depan

```bash
# Pull update dari Git
cd /var/www/sismakum
sudo git pull

# Update backend
cd backend
composer install --optimize-autoloader --no-dev
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Update frontend
cd ../frontend
npm install
npm run build
cp -r dist/* ../backend/public/

# Set permissions
sudo chown -R www-data:www-data /var/www/sismakum
sudo chmod -R 755 /var/www/sismakum
sudo chmod -R 775 /var/www/sismakum/backend/storage
```

## Troubleshooting

### Permission Denied
```bash
sudo chown -R www-data:www-data /var/www/sismakum
sudo chmod -R 755 /var/www/sismakum
sudo chmod -R 775 /var/www/sismakum/backend/storage
```

### 500 Internal Server Error
- Cek log: `tail -f /var/www/sismakum/backend/storage/logs/laravel.log`
- Pastikan `.env` sudah dikonfigurasi dengan benar
- Pastikan permissions sudah benar

### API tidak bisa diakses
- Pastikan mod_rewrite sudah enabled (Apache)
- Cek konfigurasi Nginx/Apache
- Pastikan CORS sudah dikonfigurasi di backend

### Frontend tidak muncul
- Pastikan build sudah dijalankan: `npm run build`
- Pastikan file dist sudah di-copy ke `backend/public`
- Cek console browser untuk error

## Struktur Direktori di VPS

```
/var/www/sismakum/
├── backend/              # Laravel backend
│   ├── app/
│   ├── public/          # Web root (berisi file frontend build)
│   ├── storage/
│   └── ...
└── frontend/            # Source code Vue.js (untuk development)
    ├── src/
    └── dist/            # Build output (di-copy ke backend/public)
```

## Catatan Penting

1. **Web root** adalah `/var/www/sismakum/backend/public`
2. File frontend yang sudah di-build harus di-copy ke `backend/public`
3. Semua request akan di-handle oleh Laravel, kecuali file static
4. Vue Router akan di-handle oleh `index.html` di public folder
5. API routes (`/api/*`) akan di-handle oleh Laravel
