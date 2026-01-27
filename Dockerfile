FROM php:fpm-alpine3.23

# 1. System dependencies install karein
RUN apk add --no-cache \
    libpng-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    curl

# --- NEW LINE ADDED HERE ---
# Composer ki official image se binary file copy karein
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
# ---------------------------

# 2. PHP extensions install karein
RUN docker-php-ext-install pdo pdo_mysql bcmath gd zip

# 3. Working directory set karein
WORKDIR /var/www/html

# 4. Permissions fix karein
RUN chown -R www-data:www-data /var/www/html