# -----------------------------------------------------------
# STAGE 1: Build Frontend (NodeJS)
# -----------------------------------------------------------
FROM node:20-alpine as frontend_builder

WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci 
COPY . .
RUN npm run build

# -----------------------------------------------------------
# STAGE 2: Build Backend (PHP FPM + Nginx + Supervisor)
# -----------------------------------------------------------
FROM php:8.3-fpm-alpine

RUN apk add --no-cache \
    postgresql-dev libzip-dev zip unzip icu-dev libpng-dev jpeg-dev nginx supervisor \
    && docker-php-ext-configure gd --with-jpeg \
    && docker-php-ext-install pdo pdo_pgsql zip bcmath intl gd opcache

RUN mkdir -p /run/nginx /var/lib/nginx/tmp /var/log/nginx /var/log/supervisor /run/supervisord \
    && chown -R www-data:www-data /run/nginx /var/lib/nginx /var/log/nginx /var/log/supervisor /run/supervisord \
    && chmod -R 775 /var/lib/nginx /run/nginx    

WORKDIR /var/www/html

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --no-scripts --prefer-dist --no-autoloader

COPY .production/nginx.conf /etc/nginx/http.d/default.conf
COPY .production/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

COPY --chown=www-data:www-data . .
COPY --chown=www-data:www-data --from=frontend_builder /app/public/build ./public/build

RUN composer dump-autoload --optimize --no-dev \
    && mkdir -p storage/framework/sessions storage/framework/views storage/framework/cache storage/logs bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

USER www-data
EXPOSE 80
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]