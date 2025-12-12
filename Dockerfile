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
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash -
RUN apt-get install -y nodejs

WORKDIR /var/www/html

# Copy project files
COPY . .

# Ensure env exists
RUN cp .env.production.example .env || touch .env

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Build frontend
RUN npm install
RUN npm run build

# ---------------------------------------------------------
# 2) PRODUCTION RUNTIME STAGE
# ---------------------------------------------------------
FROM php:8.3-fpm AS prod

RUN apt-get update && apt-get install -y \
    libpq-dev libzip-dev libxml2-dev \
    && docker-php-ext-install pdo pdo_pgsql zip

WORKDIR /var/www/html

# Copy everything from the build container
COPY --from=build /var/www/html /var/www/html

# Expose port 8080 (Railway required)
EXPOSE 8080

# Start Laravel
CMD ["sh", "-c", "php artisan migrate --force && php artisan db:seed --force && php artisan serve --host=0.0.0.0 --port=8080"]

