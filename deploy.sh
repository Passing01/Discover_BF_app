#!/bin/bash

# Exit on error
set -e

# Install PHP dependencies
echo "Installing PHP dependencies..."
composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev

# Install Node.js dependencies
echo "Installing Node.js dependencies..."
npm ci --prefer-offline --no-audit --progress=false

# Build assets
echo "Building assets..."
npm run build

# Set application key if not set
if [ -z "$(php artisan tinker --execute="echo config('app.key');")" ]; then
    echo "Generating application key..."
    php artisan key:generate
fi

# Run database migrations
echo "Running database migrations..."
php artisan migrate --force

# Clear all caches
echo "Clearing caches..."
php artisan optimize:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# Optimize the application
echo "Optimizing application..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Set correct permissions
echo "Setting permissions..."
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# Create storage link if it doesn't exist
if [ ! -L "public/storage" ]; then
    echo "Creating storage link..."
    php artisan storage:link
fi

echo "Deployment completed successfully!"
