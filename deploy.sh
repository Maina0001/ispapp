#!/bin/bash
set -e

cd /var/www/ispapp

echo "Pulling latest code..."
git pull origin main

echo "Installing dependencies..."
composer install --no-dev --optimize-autoloader

echo "Running migrations..."
php artisan migrate --force

echo "Clearing caches..."
php artisan optimize:clear

echo "Rebuilding caches..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

echo "Restarting Horizon gracefully..."
php artisan horizon:terminate

echo "Deploy complete."
