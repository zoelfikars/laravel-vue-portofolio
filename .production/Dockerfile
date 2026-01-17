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

# 1. Install Dependencies System + Nginx + Supervisor
RUN apk add --no-cache \
    postgresql-dev \
    libzip-dev \
    zip \
    unzip \
    icu-dev \
    libpng-dev \
    jpeg-dev \
    nginx \
    supervisor

# 2. Configure PHP Extensions
RUN docker-php-ext-configure gd --with-jpeg \
    && docker-php-ext-install pdo pdo_pgsql zip bcmath intl gd opcache

# 3. Setup User ID (Optional, sesuaikan kebutuhan)
RUN sed -i 's/82/1000/g' /etc/passwd && \
    sed -i 's/82/1000/g' /etc/group

WORKDIR /var/www/html

# 4. Install Composer Dependencies
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --no-scripts --prefer-dist

# 5. Copy Application Code
COPY . .
COPY --from=frontend_builder /app/public/build ./public/build

# 6. COPY CONFIGURATION FILES (PENTING!)
# Pastikan file nginx.conf dan supervisord.conf ada di folder .production
COPY .production/nginx.conf /etc/nginx/http.d/default.conf
COPY .production/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# 7. Set Permissions
# Kita harus pastikan Nginx bisa menulis log meski usernya www-data
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache \
    && mkdir -p /run/nginx \
    && chown -R www-data:www-data /run/nginx /var/lib/nginx /var/log/nginx

RUN composer dump-autoload --optimize --no-dev

# 8. Switch User
USER www-data

# 9. EXPOSE PORT 80 (HTTP)
EXPOSE 80

# 10. JALANKAN SUPERVISOR (Bukan PHP-FPM lagi)
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]