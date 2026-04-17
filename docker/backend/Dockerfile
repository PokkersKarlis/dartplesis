FROM php:8.3-apache

RUN a2enmod rewrite headers

RUN apt-get update \
  && apt-get install -y --no-install-recommends \
    git \
    unzip \
    libicu-dev \
    libzip-dev \
  && docker-php-ext-install -j"$(nproc)" intl pdo_mysql zip \
  && rm -rf /var/lib/apt/lists/*

# Composer (copied from official Composer image)
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY docker/backend/000-default.conf /etc/apache2/sites-available/000-default.conf

WORKDIR /var/www/html

