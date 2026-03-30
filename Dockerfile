FROM php:8.0-apache

WORKDIR /var/www/html

COPY . .

RUN apt-get update && apt-get install -y unzip git curl

RUN curl -sS https://getcomposer.org/installer | php
RUN mv composer.phar /usr/local/bin/composer

RUN composer install

RUN chmod -R 777 storage
RUN chmod -R 777 bootstrap/cache

RUN a2enmod rewrite

ENV APACHE_DOCUMENT_ROOT /var/www/html/public

RUN sed -ri "s!/var/www/html!${APACHE_DOCUMENT_ROOT}!g" /etc/apache2/sites-available/*.conf
RUN sed -ri "s!/var/www/!${APACHE_DOCUMENT_ROOT}!g" /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

RUN mkdir -p database
RUN touch database/database.sqlite

CMD php artisan migrate --force && apache2-foreground