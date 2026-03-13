# 1. Base Image
FROM php:8.4-fpm-alpine

# 2. Set working directory
WORKDIR /var/www/html

# 3. Install Runtime & Build Dependencies
RUN apk add --no-cache \
    libpq-dev \
    libpng-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    postgresql-client \
    # Build dependencies (Temporary) \
    $PHPIZE_DEPS \
    linux-headers

# 4. Install PHP Extensions
RUN docker-php-ext-install pdo pdo_pgsql gd zip

# 5. Install Redis & Cleanup Build Deps
# Humne 'del' use kiya hai taake compile tools remove ho jayein
RUN pecl install redis \
    && docker-php-ext-enable redis \
    && apk del $PHPIZE_DEPS linux-headers

# 6. Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 7. Permissions
COPY . /var/www/html
RUN chown -R www-data:www-data /var/www/html

EXPOSE 9000
CMD ["php-fpm"]