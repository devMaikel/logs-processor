FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    build-essential \
    libpq-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    curl

RUN docker-php-ext-install pdo pdo_pgsql pgsql zip

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www
