#!/bin/bash
set -e

echo "Starting SISMAKUM Application..."

cd /var/www/html/backend

# Run migrations if DB is available
php artisan migrate --force 2>/dev/null || echo "Migration skipped (DB not ready)"

# Clear and cache config
php artisan config:clear 2>/dev/null || true
php artisan route:clear 2>/dev/null || true
php artisan view:clear 2>/dev/null || true

# Set permissions
chown -R www-data:www-data /var/www/html/backend/storage
chmod -R 775 /var/www/html/backend/storage

echo "Starting Supervisor..."
exec /usr/bin/supervisord -c /etc/supervisord.conf
