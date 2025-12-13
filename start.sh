#!/bin/bash
set -e

echo "Running migrations..."
php artisan migrate --force

echo "Seeding database..."
php artisan db:seed --force || true

echo "Creating storage symlink..."
php artisan storage:link || true

echo "Checking Vite build..."

# If Vite build folder is missing, force-build it
if [ ! -f public/build/manifest.json ]; then
    echo "Vite build missing. Running npm install + npm run build..."
    npm install --no-audit --no-fund
    npm run build
    echo "Vite build completed."
else
    echo "Vite build already exists."
fi

echo "Optimizing..."
php artisan optimize:clear
php artisan optimize

echo "Starting Laravel..."
php artisan serve --host=0.0.0.0 --port=8080
