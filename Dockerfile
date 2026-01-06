# Stage 1: Build Vue Frontend
FROM node:18-alpine AS frontend-builder
WORKDIR /app/frontend

# Copy package files
COPY frontend/package*.json ./

# Install dependencies
RUN npm ci --legacy-peer-deps

# Copy source code
COPY frontend/ ./

# Build frontend
RUN npm run build

# Debug: list dist contents
RUN ls -la /app/frontend/dist/ || echo "No dist folder"

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
    bash \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd xml

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy backend
COPY backend/ ./backend/

# Create .env file from .env.example
RUN if [ -f ./backend/.env.example ]; then cp ./backend/.env.example ./backend/.env; fi

# Install Laravel dependencies
WORKDIR /var/www/html/backend
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

# Regenerate autoload without scripts
RUN composer dump-autoload --no-scripts

# Laravel artisan commands
RUN php artisan key:generate --force 2>/dev/null || true
RUN php artisan storage:link 2>/dev/null || true

# Set permissions
RUN chown -R www-data:www-data /var/www/html/backend
RUN chmod -R 775 /var/www/html/backend/storage /var/www/html/backend/bootstrap/cache

# Copy frontend build from builder stage
WORKDIR /var/www/html
COPY --from=frontend-builder /app/frontend/dist ./frontend/dist

# Debug: verify frontend files exist
RUN ls -la /var/www/html/frontend/dist/ && echo "Frontend dist OK"

# Nginx config
RUN mkdir -p /run/nginx /var/log/nginx
COPY nginx.conf /etc/nginx/http.d/default.conf

# Supervisor config
COPY supervisord.conf /etc/supervisord.conf

# Create startup script
COPY start.sh /start.sh
RUN chmod +x /start.sh

# Expose port
EXPOSE 80

# Start with startup script
CMD ["/start.sh"]
