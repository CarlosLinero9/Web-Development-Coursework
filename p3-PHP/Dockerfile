FROM php:8.3-apache

RUN apt-get update \
    && apt-get install -y git unzip libzip-dev libonig-dev default-mysql-client \
    && docker-php-ext-install mysqli mbstring \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

RUN mkdir -p /usr/local/lib/php \
    && cd /usr/local/lib/php \
    && composer require "twig/twig:^3.0"

WORKDIR /var/www/html
