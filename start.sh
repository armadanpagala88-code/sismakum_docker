#!/bin/bash
set -e

echo "Starting SISMAKUM Application..."

cd /var/www/html/backend

# Clear all cached config to prevent dev package loading
rm -rf bootstrap/cache/*.php 2>/dev/null || true
rm -rf storage/framework/cache/data/* 2>/dev/null || true

# Regenerate autoload
php -d memory_limit=-1 /usr/bin/composer dump-autoload --no-scripts --no-dev 2>/dev/null || true

# Run migrations if DB is available
php artisan migrate --force 2>/dev/null || echo "Migration skipped (DB not ready)"

# Optimize for production (without dev packages)
php artisan config:clear 2>/dev/null || true
php artisan route:clear 2>/dev/null || true
php artisan view:clear 2>/dev/null || true
php artisan cache:clear 2>/dev/null || true

# Set permissions
chown -R www-data:www-data /var/www/html/backend/storage
chmod -R 775 /var/www/html/backend/storage

echo "Starting Supervisor..."
exec /usr/bin/supervisord -c /etc/supervisord.conf
