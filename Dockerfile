FROM php:8.3-fpm-alpine

RUN apk add --no-cache $PHPIZE_DEPS \
    && docker-php-ext-install opcache

WORKDIR /var/www/html

COPY . .
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN composer install --no-interaction --optimize-autoloader

EXPOSE 8089

CMD ["php", "-S", "0.0.0.0:8089", "-t", "public"]
