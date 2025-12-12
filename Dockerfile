# ---------------------------------------------------------
# 1) BUILD STAGE (Composer + Node + Vite)
# ---------------------------------------------------------
FROM php:8.3-fpm AS build

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git curl zip unzip \
    libpq-dev libzip-dev libonig-dev libxml2-dev \
    && docker-php-ext-install pdo pdo_pgsql zip

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Install Node 18 (for Vite)
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs

WORKDIR /var/www/html

# Copy project files
COPY . .

# IMPORTANT: Copy production env (ensure .env.production.example exists)
RUN cp .env.production.example .env

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Build frontend assets
RUN npm install --no-audit --no-fund
RUN npm run build

# ---------------------------------------------------------
# 2) PRODUCTION RUNTIME STAGE
# ---------------------------------------------------------
FROM php:8.3-fpm AS prod

RUN apt-get update && apt-get install -y \
    libpq-dev libzip-dev libxml2-dev \
    && docker-php-ext-install pdo pdo_pgsql zip

WORKDIR /var/www/html

# Copy built app
COPY --from=build /var/www/html /var/www/html

# ---- CRITICAL: storage & cache permissions ----
# ensure storage and bootstrap/cache are writable by the PHP process
RUN mkdir -p storage/logs storage/framework bootstrap/cache \
    && chmod -R 777 storage bootstrap/cache

# Expose port 8080 for Railway
EXPOSE 8080

# Run migrations, seed, link storage, then start Laravel
CMD ["sh", "-c", "php artisan migrate --force && php artisan db:seed --force && php artisan storage:link || true && php artisan serve --host=0.0.0.0 --port=8080"]
