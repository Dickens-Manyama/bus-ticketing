FROM php:8.3-fpm

# Set environment variables for non-interactive install
ENV DEBIAN_FRONTEND=noninteractive

# Install system dependencies and clean up
RUN apt-get update && apt-get install -y \
    nginx \
    supervisor \
    git \
    unzip \
    libpq-dev \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    && docker-php-ext-install pdo pdo_pgsql pgsql zip gd mbstring xml \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /app

# Copy only composer files to leverage Docker cache
COPY composer.json composer.lock ./

# Install dependencies
RUN composer install --prefer-dist --no-dev --no-progress --no-scripts --optimize-autoloader

# Copy example configs for production
COPY common/config/main-local.php.example common/config/main-local.php
COPY common/config/params-local.php.example common/config/params-local.php
COPY frontend/config/main-local.php.example frontend/config/main-local.php
COPY frontend/config/params-local.php.example frontend/config/params-local.php
COPY backend/config/main-local.php.example backend/config/main-local.php
COPY backend/config/params-local.php.example backend/config/params-local.php
COPY console/config/main-local.php.example console/config/main-local.php
COPY console/config/params-local.php.example console/config/params-local.php

# Copy the rest of the application code
COPY . .

# Ensure runtime, assets, and uploads are writable and owned by www-data
RUN mkdir -p /app/frontend/runtime /app/frontend/web/assets /app/frontend/web/uploads \
    && chmod -R 777 /app/frontend/runtime /app/frontend/web/assets /app/frontend/web/uploads \
    && chown -R www-data:www-data /app/frontend/runtime /app/frontend/web/assets /app/frontend/web/uploads \
    && mkdir -p /app/backend/runtime /app/backend/web/assets /app/backend/web/uploads \
    && chmod -R 777 /app/backend/runtime /app/backend/web/assets /app/backend/web/uploads \
    && chown -R www-data:www-data /app/backend/runtime /app/backend/web/assets /app/backend/web/uploads

# RUN php init --env=Production --overwrite=y

# Copy nginx and supervisor configs
COPY nginx.conf /etc/nginx/nginx.conf
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Expose port
EXPOSE 8080

CMD ["/usr/bin/supervisord"] 