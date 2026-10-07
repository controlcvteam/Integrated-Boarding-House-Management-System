#!/usr/bin/env bash
# ==============================================================================
# Integrated Boarding House Management System - Production Deployment Script
# ==============================================================================

set -e

echo "=========================================================="
echo " Starting IBHMS Live Deployment"
echo "=========================================================="

# 1. Check if .env exists
if [ ! -f .env ]; then
    echo "⚠️  .env file not found. Copying .env.production.example to .env..."
    cp .env.production.example .env
    echo "🔑 Generating application encryption key..."
    php artisan key:generate --force
    echo "⚠️  Please configure your database credentials in .env before continuing."
fi

# 2. Install / Optimize Composer Dependencies
echo "📦 Installing production composer dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction

# 3. Create Storage Symlink
echo "🔗 Ensuring public storage symlink exists..."
php artisan storage:link || true

# 4. Run Database Migrations
echo "🗄️ Running database migrations..."
php artisan migrate --force

# 5. Clear Old Caches & Warm Up Production Caches
echo "⚡ Optimizing configuration, routes, and views..."
php artisan config:clear
php artisan route:clear
php artisan view:clear

php artisan config:cache
php artisan route:cache
php artisan view:cache

# 6. Set Directory Permissions (for Linux hosts)
echo "🔒 Adjusting file permissions for storage & bootstrap/cache..."
chmod -R 775 storage bootstrap/cache || true

echo "=========================================================="
echo "✅ Deployment completed successfully!"
echo "   Live site is ready for traffic."
echo "=========================================================="
