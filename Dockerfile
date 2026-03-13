# 1. Base Image (PHP 8.2 or 8.3 FPM Alpine)
FROM php:8.4-fpm-alpine

# 2. Set working directory
WORKDIR /var/www/html

# 3. Install System Dependencies (Postgres & Redis support ke liye)
# 'postgresql-dev' drivers ke liye hai aur 'libpq' runtime ke liye
RUN apk update && apk add --no-cache \
    libpq-dev \
    postgresql-client \
    linux-headers \
    autoconf \
    build-base \
    git \
    unzip \
    libpng-dev \
    libzip-dev \
    zip

# 4. Install PHP Extensions
# pdo_pgsql: Postgres ke liye
# pdo_mysql: Agar kabhi MySQL ki zarurat pare (Optional)
RUN docker-php-ext-install pdo pdo_pgsql pdo_mysql gd zip

# 5. Install Redis Extension (using PECL)
RUN pecl install redis && docker-php-ext-enable redis

# 6. Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 7. Copy existing application directory permissions
COPY . /var/www/html
RUN chown -R www-data:www-data /var/www/html

# 8. Expose port 9000 and start php-fpm server
EXPOSE 9000
CMD ["php-fpm"]