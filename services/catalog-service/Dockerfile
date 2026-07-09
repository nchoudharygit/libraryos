FROM php:8.3-fpm-alpine

# Install system dependencies for PostgreSQL
RUN apk add --no-cache \
    postgresql-dev \
    git \
    unzip \
    && docker-php-ext-install pdo pdo_pgsql

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy composer files first (layer caching)
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --ignore-platform-reqs

# Copy app code
COPY . .

# Generate optimized autoloader safely (No root/script conflicts)
RUN composer dump-autoload --optimize --no-scripts

# Ensure directories exist and set permissions
RUN mkdir -p storage bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache vendor

# Run as non-root user
USER www-data

EXPOSE 9000
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8001"]