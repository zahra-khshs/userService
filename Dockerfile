FROM php:8.2-fpm

 RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip

 RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

 RUN pecl install redis && docker-php-ext-enable redis

 COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

RUN composer install

 RUN chown -R www-data:www-data /var/www/html/storage
