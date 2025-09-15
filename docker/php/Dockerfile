FROM php:8.2-fpm-alpine

RUN apk add --no-cache \
    bash git icu-dev oniguruma-dev libpq-dev \
 && docker-php-ext-install intl pdo pdo_pgsql \
 && rm -rf /var/cache/apk/*

WORKDIR /var/www/html

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
RUN composer --version
