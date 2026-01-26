FROM php:fpm-alpine3.23

# 1. System dependencies install karein (Jo extensions build karne ke liye chahiye)
RUN apk add --no-cache \
    libpng-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    curl

# 2. PHP extensions install karein
RUN docker-php-ext-install pdo pdo_mysql bcmath gd zip

# 3. Working directory set karein
WORKDIR /var/www/html

# 4. Permissions fix karein (Optional but recommended)
RUN chown -R www-data:www-data /var/www/html