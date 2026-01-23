# syntax=docker/dockerfile:1
FROM php:8.4-fpm AS base

ARG MAIN_DIR

WORKDIR /var/www/html

# Instalacja rozszerzeń
RUN apt-get update && apt-get install -y --no-install-recommends \
    git unzip libzip-dev libicu-dev libcurl4-openssl-dev libgmp-dev libpq-dev \
    && docker-php-ext-install -j$(nproc) intl pdo_mysql zip curl gmp \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

RUN echo "date.timezone = UTC" > /usr/local/etc/php/conf.d/timezone.ini
COPY docker/php/php.ini /usr/local/etc/php/conf.d/php.ini
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN usermod -u 1000 www-data

# Etap deweloperski
FROM base AS dev
ARG MAIN_DIR
ENV APP_ENV=dev
USER www-data