FROM php:8.3-apache

RUN apt-get update \
    && apt-get install -y git unzip libzip-dev default-mysql-client \
    && docker-php-ext-install mysqli mbstring \
    && a2enmod rewrite \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . /var/www/html

RUN composer install --no-interaction --prefer-dist

COPY docker/vhost.conf /etc/apache2/sites-available/000-default.conf
COPY docker/php.ini /usr/local/etc/php/conf.d/sibw.ini
