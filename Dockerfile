# Stage 1: Build Vue Frontend
FROM node:18-alpine AS frontend-builder
WORKDIR /app/frontend
COPY frontend/package*.json ./
RUN npm ci
COPY frontend/ ./
RUN npm run build

# Stage 2: PHP/Laravel Backend + Nginx
FROM php:8.2-fpm-alpine

# Install dependencies
RUN apk add --no-cache \
    nginx \
    supervisor \
    curl \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    zip \
    unzip \
    git \
    oniguruma-dev \
    libxml2-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd xml

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy backend
COPY backend/ ./backend/

# Copy .env.example as .env if .env doesn't exist
RUN cp ./backend/.env.example ./backend/.env 2>/dev/null || true

# Install Laravel dependencies
WORKDIR /var/www/html/backend
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Generate APP_KEY if not set, create storage link, cache config
RUN php artisan key:generate --force || true
RUN php artisan storage:link || true
RUN php artisan config:cache || true
RUN php artisan route:cache || true
RUN php artisan view:cache || true

# Set permissions
RUN chown -R www-data:www-data /var/www/html/backend
RUN chmod -R 775 /var/www/html/backend/storage /var/www/html/backend/bootstrap/cache

# Copy frontend build
WORKDIR /var/www/html
COPY --from=frontend-builder /app/frontend/dist ./frontend/dist

# Nginx config
RUN mkdir -p /run/nginx
COPY nginx.conf /etc/nginx/http.d/default.conf

# Supervisor config
COPY supervisord.conf /etc/supervisord.conf

# Create startup script
RUN echo '#!/bin/sh' > /start.sh && \
    echo 'cd /var/www/html/backend' >> /start.sh && \
    echo 'php artisan migrate --force || true' >> /start.sh && \
    echo 'php artisan config:cache || true' >> /start.sh && \
    echo 'chown -R www-data:www-data /var/www/html/backend/storage' >> /start.sh && \
    echo 'exec /usr/bin/supervisord -c /etc/supervisord.conf' >> /start.sh && \
    chmod +x /start.sh

# Expose port
EXPOSE 80

# Start with startup script
CMD ["/start.sh"]
