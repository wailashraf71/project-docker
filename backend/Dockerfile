FROM php:8.3-fpm-alpine

RUN apk add --no-cache \
    bash curl git unzip libpq-dev icu-dev oniguruma-dev \
 && docker-php-ext-install pdo_pgsql intl mbstring opcache

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

