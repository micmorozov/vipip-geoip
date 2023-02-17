FROM php:8.1.11-fpm

RUN apt-get update && apt-get upgrade -y

RUN apt-get install -y wget git zip

COPY ./php.ini /usr/local/etc/php/conf.d/

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

ENV COMPOSER_ALLOW_SUPERUSER=1

WORKDIR /var/www/html

CMD ["php-fpm"]