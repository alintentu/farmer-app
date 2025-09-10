# Use official PHP 8.3 FPM Alpine image
FROM php:8.3-fpm-alpine

# Set working directory
WORKDIR /var/www/html

# Install system dependencies
RUN apk add --no-cache \
    git \
    curl \
    libpng-dev \
    libxml2-dev \
    zip \
    unzip \
    oniguruma-dev \
    libzip-dev \
    icu-dev \
    freetype-dev \
    libjpeg-turbo-dev \
    libwebp-dev

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && apk add --no-cache \
        imagemagick \
        imagemagick-dev \
        ghostscript \
        poppler-utils \
        postgresql-dev \
    && docker-php-ext-install \
        pdo_pgsql \
        pgsql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        intl \
        zip \
        opcache

# Install PECL extensions (redis, imagick)
RUN apk add --no-cache $PHPIZE_DEPS imagemagick-dev \
    && pecl install redis imagick \
    && docker-php-ext-enable redis imagick \
    && apk del -f $PHPIZE_DEPS

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

## Keep runtime lean; code is mounted via volume in docker-compose.
## Composer is available in the container for runtime install.

# Expose port 9000
EXPOSE 9000

# Start PHP-FPM
CMD ["php-fpm"]
