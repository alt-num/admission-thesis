# ---------------------------------------------------------
# 1) BUILD STAGE — Composer + Node + Vite
# ---------------------------------------------------------
FROM php:8.3-fpm AS build

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git curl zip unzip \
    libpq-dev libzip-dev libxml2-dev \
    && docker-php-ext-install pdo pdo_pgsql zip

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Install Node 24 (your real version)
RUN curl -fsSL https://deb.nodesource.com/setup_24.x | bash - \
    && apt-get install -y nodejs

WORKDIR /var/www/html

# Copy the project
COPY . .

# Use your production env example
RUN cp .env.production.example .env

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Build frontend (Vite)
RUN npm install --no-audit --no-fund
RUN npm run build

# ---------------------------------------------------------
# 2) PRODUCTION STAGE — Apache + PHP
# ---------------------------------------------------------
FROM php:8.3-apache AS prod

# Install PHP extensions for Laravel
RUN apt-get update && apt-get install -y \
    libpq-dev libzip-dev libxml2-dev \
    && docker-php-ext-install pdo pdo_pgsql zip

# Enable URL rewriting (Laravel routing)
RUN a2enmod rewrite

WORKDIR /var/www/html

# Copy built app from stage 1
COPY --from=build /var/www/html /var/www/html

# Fix Laravel permission requirements
RUN mkdir -p storage/logs storage/framework/bootstrap/cache \
    && chmod -R 777 storage bootstrap/cache

# Railway uses PORT environment variable
ENV PORT=8080
EXPOSE 8080

# Run Apache
CMD ["apache2-foreground"]
