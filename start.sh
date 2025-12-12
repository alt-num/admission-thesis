#!/bin/bash
set -e

echo "Running migrations..."
php artisan migrate --force

echo "Seeding database..."
php artisan db:seed --force || true

echo "Creating storage symlink..."
php artisan storage:link || true

echo "Optimizing..."
php artisan optimize:clear
php artisan optimize

echo "Starting Laravel..."
php artisan serve --host=0.0.0.0 --port=8080
