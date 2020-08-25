FROM php:7.4-fpm

ADD docker/fpm/conf/php.ini /usr/local/etc/php/php.ini

RUN apt-get update \
&& docker-php-ext-install pdo pdo_mysql

RUN apt-get update && apt-get install -y \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd
