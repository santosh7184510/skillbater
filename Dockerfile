FROM php:8.0-apache

WORKDIR /var/www/html

COPY . .

RUN apt-get update && apt-get install -y unzip git curl

RUN curl -sS https://getcomposer.org/installer | php
RUN mv composer.phar /usr/local/bin/composer

RUN composer install

# ✅ create sqlite file
RUN mkdir -p database
RUN touch database/database.sqlite

CMD php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=10000