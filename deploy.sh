#!/bin/bash

# Script Deployment SISMAKUM ke VPS
# Usage: ./deploy.sh [production|staging]

set -e

ENVIRONMENT=${1:-production}
PROJECT_DIR="/var/www/sismakum"
BACKEND_DIR="$PROJECT_DIR/backend"
FRONTEND_DIR="$PROJECT_DIR/frontend"

echo "🚀 Starting deployment for $ENVIRONMENT environment..."

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Function to print colored output
print_success() {
    echo -e "${GREEN}✓ $1${NC}"
}

print_error() {
    echo -e "${RED}✗ $1${NC}"
}

print_info() {
    echo -e "${YELLOW}ℹ $1${NC}"
}

# Check if running as root or with sudo
if [ "$EUID" -ne 0 ]; then 
    print_error "Please run as root or with sudo"
    exit 1
fi

# Check if project directory exists
if [ ! -d "$PROJECT_DIR" ]; then
    print_error "Project directory not found: $PROJECT_DIR"
    exit 1
fi

print_info "Step 1: Updating backend dependencies..."
cd $BACKEND_DIR
composer install --optimize-autoloader --no-dev
print_success "Backend dependencies updated"

print_info "Step 2: Running migrations..."
php artisan migrate --force
print_success "Migrations completed"

print_info "Step 3: Clearing and caching configuration..."
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

if [ "$ENVIRONMENT" = "production" ]; then
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    print_success "Configuration cached for production"
else
    print_success "Configuration cleared for development"
fi

print_info "Step 4: Building frontend..."
cd $FRONTEND_DIR
npm install
npm run build
print_success "Frontend built successfully"

print_info "Step 5: Copying frontend build to public directory..."
if [ -d "$FRONTEND_DIR/dist" ]; then
    cp -r $FRONTEND_DIR/dist/* $BACKEND_DIR/public/
    print_success "Frontend files copied to public directory"
else
    print_error "Frontend dist directory not found"
    exit 1
fi

print_info "Step 6: Setting permissions..."
chown -R www-data:www-data $PROJECT_DIR
chmod -R 755 $PROJECT_DIR
chmod -R 775 $BACKEND_DIR/storage
chmod -R 775 $BACKEND_DIR/bootstrap/cache
print_success "Permissions set"

print_info "Step 7: Creating storage link..."
cd $BACKEND_DIR
php artisan storage:link 2>/dev/null || true
print_success "Storage link created"

print_info "Step 8: Restarting web server..."
if systemctl is-active --quiet apache2; then
    systemctl restart apache2
    print_success "Apache restarted"
elif systemctl is-active --quiet nginx; then
    systemctl restart nginx
    print_success "Nginx restarted"
else
    print_error "No web server found (Apache/Nginx)"
fi

print_success "🎉 Deployment completed successfully!"
print_info "Application is now live at your configured domain"
