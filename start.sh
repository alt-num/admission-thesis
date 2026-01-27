#!/bin/bash
set -e

echo "RAILWAY_SERVICE_NAME=$RAILWAY_SERVICE_NAME"

if [[ "$RAILWAY_SERVICE_NAME" == "essu-worker" ]]; then
  echo "Starting QUEUE WORKER..."

  php artisan queue:work --verbose --tries=3 --timeout=90

else
  echo "Starting WEB SERVICE..."

  echo "Building frontend..."
  npm ci --no-audit --no-fund
  npm run build

  echo "Running migrations..."
  php artisan migrate --force

  echo "Seeding database..."
  php artisan db:seed --force || true

  echo "Creating storage symlink..."
  php artisan storage:link || true

  echo "Optimizing..."
  php artisan optimize

  echo "Starting Laravel..."
  php artisan serve --host=0.0.0.0 --port=8080
fi
