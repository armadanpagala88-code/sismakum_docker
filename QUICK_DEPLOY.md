# Quick Deploy Guide - SISMAKUM

Panduan cepat untuk deploy aplikasi SISMAKUM ke VPS.

## TL;DR - Langkah Cepat

### 1. Upload ke VPS
```bash
# Via SCP
scp -r SISMAKUM2 user@your-vps:/var/www/sismakum

# Atau via Git
ssh user@your-vps
cd /var/www
sudo git clone <repo-url> sismakum
```

### 2. Setup Backend
```bash
cd /var/www/sismakum/backend
composer install --no-dev
cp .env.example .env
php artisan key:generate
# Edit .env dengan konfigurasi database
php artisan migrate --force
php artisan storage:link
```

### 3. Build Frontend
```bash
cd /var/www/sismakum/frontend
npm install
npm run build
cp -r dist/* ../backend/public/
```

### 4. Setup Web Server

**Untuk Apache:**
```bash
sudo cp apache.conf.example /etc/apache2/sites-available/sismakum.conf
sudo nano /etc/apache2/sites-available/sismakum.conf  # Edit domain
sudo a2enmod rewrite
sudo a2ensite sismakum.conf
sudo systemctl restart apache2
```

**Untuk Nginx:**
```bash
sudo cp nginx.conf.example /etc/nginx/sites-available/sismakum
sudo nano /etc/nginx/sites-available/sismakum  # Edit domain
sudo ln -s /etc/nginx/sites-available/sismakum /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl restart nginx
```

### 5. Set Permissions
```bash
sudo chown -R www-data:www-data /var/www/sismakum
sudo chmod -R 755 /var/www/sismakum
sudo chmod -R 775 /var/www/sismakum/backend/storage
```

### 6. Setup SSL (Opsional)
```bash
sudo certbot --nginx -d your-domain.com
# atau
sudo certbot --apache -d your-domain.com
```

## Menggunakan Script Deploy

Setelah setup awal, untuk update aplikasi:

```bash
cd /var/www/sismakum
sudo ./deploy.sh production
```

## Struktur Penting

- **Web Root**: `/var/www/sismakum/backend/public`
- **Backend**: `/var/www/sismakum/backend`
- **Frontend Build**: `/var/www/sismakum/frontend/dist` → di-copy ke `backend/public`

## Checklist Deployment

- [ ] PHP 8.1+ dan ekstensi terinstall
- [ ] Composer terinstall
- [ ] Node.js dan npm terinstall
- [ ] MySQL database dibuat
- [ ] File .env dikonfigurasi
- [ ] Migrations dijalankan
- [ ] Frontend di-build
- [ ] File frontend di-copy ke backend/public
- [ ] Web server dikonfigurasi
- [ ] Permissions diset dengan benar
- [ ] SSL certificate di-setup (opsional)
- [ ] Firewall dikonfigurasi

## Troubleshooting Cepat

**500 Error:**
```bash
tail -f /var/www/sismakum/backend/storage/logs/laravel.log
```

**Permission Error:**
```bash
sudo chown -R www-data:www-data /var/www/sismakum
sudo chmod -R 775 /var/www/sismakum/backend/storage
```

**API tidak bisa diakses:**
- Pastikan mod_rewrite enabled (Apache)
- Cek konfigurasi web server
- Pastikan .htaccess ada di public folder

**Frontend tidak muncul:**
- Pastikan `npm run build` sudah dijalankan
- Pastikan file dist sudah di-copy ke backend/public
- Cek console browser
