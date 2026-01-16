# -----------------------------------------------------------
# STAGE 1: Build Frontend (NodeJS)
# Tugas: Compile Vue jadi file statis
# -----------------------------------------------------------
FROM node:20-alpine as frontend_builder

WORKDIR /app

COPY package.json package-lock.json ./
RUN npm ci 

COPY . .

RUN npm run build

# -----------------------------------------------------------
# STAGE 2: Build Backend (PHP FPM)
# Tugas: Menjalankan Laravel
# -----------------------------------------------------------
FROM php:8.3-fpm-alpine

RUN apk add --no-cache \
    postgresql-dev \
    libzip-dev \
    zip \
    unzip \
    icu-dev \
    libpng-dev \
    jpeg-dev

RUN docker-php-ext-configure gd --with-jpeg \
    && docker-php-ext-install pdo pdo_pgsql zip bcmath intl gd opcache

RUN sed -i 's/82/1000/g' /etc/passwd && \
    sed -i 's/82/1000/g' /etc/group

WORKDIR /var/www/html

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY composer.json composer.lock ./

RUN composer install --no-dev --no-interaction --no-scripts --prefer-dist

COPY . .

COPY --from=frontend_builder /app/public/build ./public/build

RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache

RUN composer dump-autoload --optimize --no-dev

USER www-data

EXPOSE 9000
CMD ["php-fpm"]