FROM php:8.2-fpm-alpine

RUN apk add --no-cache \
    php-mysqli \
    php-pdo_mysql \
    php-session \
    && docker-php-ext-install pdo pdo_mysql

# Optional: Install Composer
COPY --from=composer /usr/bin/composer /usr/bin/composer
