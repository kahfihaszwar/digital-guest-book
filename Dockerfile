# ---------- Stage 1: build frontend assets (Vite + Tailwind) ----------
FROM node:22-alpine AS assets
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci --ignore-scripts
COPY . .
RUN npm run build

# ---------- Stage 2: install PHP dependencies ----------
FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-interaction --prefer-dist --optimize-autoloader
COPY . .
RUN composer dump-autoload --no-dev --optimize --no-scripts

# ---------- Stage 3: runtime (Apache + PHP) ----------
FROM php:8.4-apache

RUN apt-get update \
    && apt-get install -y --no-install-recommends libzip-dev libsqlite3-dev \
    && docker-php-ext-install bcmath zip pdo_sqlite \
    && rm -rf /var/lib/apt/lists/*

# Render expects the service to listen on port 10000 by default
RUN a2enmod rewrite \
    && sed -ri 's/Listen 80/Listen 10000/' /etc/apache2/ports.conf

COPY docker/apache-vhost.conf /etc/apache2/sites-available/000-default.conf

WORKDIR /var/www/html
COPY . .
COPY --from=vendor /app/vendor ./vendor
COPY --from=assets /app/public/build ./public/build

RUN mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache \
    && touch database/database.sqlite \
    && chown -R www-data:www-data storage bootstrap/cache database

COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 10000
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
