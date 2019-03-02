# Native image
FROM php:7.2-apache

# Install requirements for PHP extensions
RUN apt-get update && apt-get install libpng-dev libzip-dev libfreetype6-dev libjpeg62-turbo-dev libpng-dev -y

# Install PHP extensions and enable debuging
RUN docker-php-ext-install pdo_mysql
RUN docker-php-ext-configure zip --with-libzip
RUN docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ && docker-php-ext-install -j$(nproc) gd
RUN mv /usr/local/etc/php/php.ini-development /usr/local/etc/php/php.ini

# Copy vhost configuration
COPY .docker/web/vhost.conf /etc/apache2/sites-available/000-default.conf

# Apache
RUN a2enmod rewrite

# Perms
RUN usermod -u 1000 www-data