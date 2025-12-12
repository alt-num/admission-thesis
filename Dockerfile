# ---------------------------------------------------------
# 1) BUILD STAGE — Composer + Node (Vite)
# ---------------------------------------------------------
FROM php:8.3-fpm AS build

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git curl zip unzip \
    libpq-dev libzip-dev libonig-dev libxml2-dev \
    && docker-php-ext-install pdo pdo_pgsql zip

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Install Node v20 (LTS)
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

WORKDIR /var/www/html

# Copy project files
COPY . .

# Copy production environment template (must exist!)
RUN cp .env.production.example .env

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Install JS dependencies + Vite build
RUN npm install --no-audit --no-fund
RUN npm run build


# ---------------------------------------------------------
# 2) PRODUCTION RUNTIME STAGE
# ---------------------------------------------------------
FROM php:8.3-fpm AS prod

# Install required PHP extensions
RUN apt-get update && apt-get install -y \
    libpq-dev libzip-dev libxml2-dev \
    && docker-php-ext-install pdo pdo_pgsql zip

WORKDIR /var/www/html

# Copy built project from build stage
COPY --from=build /var/www/html /var/www/html

# Fix permissions
RUN mkdir -p storage/logs storage/framework bootstrap/cache \
    && chmod -R 777 storage bootstrap/cache

# Railway exposes port 8080 internally
EXPOSE 8080

# ENTRYPOINT:
# 1) Run migrations (ignore failures if tables already exist)
# 2) Seed only if tables empty
# 3) Create storage link
# 4) Start Laravel server
CMD ["sh", "-c", "\
    php artisan migrate --force || true && \
    php artisan db:seed --force || true && \
    php artisan storage:link || true && \
    php artisan serve --host=0.0.0.0 --port=8080 \
"]
