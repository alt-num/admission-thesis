#!/bin/bash

# Run database migrations
php artisan migrate --force || true

# Seed database (if you want)
php artisan db:seed --force || true

# Create storage symlink
php artisan storage:link || true

# Clear and optimize (recommended)
php artisan optimize:clear
php artisan optimize

# Start Laravel
php artisan serve --host=0.0.0.0 --port=8080

