FROM php:fpm-alpine3.23

# 1️⃣ System + Build Dependencies
RUN apk add --no-cache \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    curl \
    $PHPIZE_DEPS

# 2️⃣ GD configure karein (important for Alpine)
RUN docker-php-ext-configure gd \
    --with-freetype \
    --with-jpeg

# 3️⃣ PHP Extensions install karein
RUN docker-php-ext-install \
    pdo \
    pdo_mysql \
    bcmath \
    gd \
    zip

# 4️⃣ 🔥 Redis Extension install karein
RUN pecl install redis \
    && docker-php-ext-enable redis

# 5️⃣ Composer copy karein
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 6️⃣ Working directory
WORKDIR /var/www/html

# 7️⃣ Permissions fix
RUN chown -R www-data:www-data /var/www/html

EXPOSE 9000

CMD ["php-fpm"]