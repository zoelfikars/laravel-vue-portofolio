FROM php:8.3-fpm-alpine

# Install system dependencies
RUN apk add --no-cache \
    linux-headers \
    postgresql-dev \
    libzip-dev \
    unzip \
    git \
    nodejs \
    npm

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_pgsql

RUN sed -i 's/82/1000/g' /etc/passwd && \
    sed -i 's/82/1000/g' /etc/group
    
# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy project files
COPY . .

# Install dependencies
RUN composer install --no-interaction --optimize-autoloader

RUN chown -R www-data:www-data /var/www/html

USER www-data

EXPOSE 9000
COPY .docker/php/uploads.ini /usr/local/etc/php/conf.d/uploads.ini
CMD ["php-fpm"]
