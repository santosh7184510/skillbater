FROM php:8.0-apache

WORKDIR /var/www/html

COPY . .

RUN apt-get update && apt-get install -y unzip git curl

RUN curl -sS https://getcomposer.org/installer | php
RUN mv composer.phar /usr/local/bin/composer

RUN composer install
RUN chmod -R 777 storage
RUN chmod -R 777 bootstrap/cache
# ✅ Enable rewrite
RUN a2enmod rewrite

# ✅ VERY IMPORTANT (THIS FIXES YOUR ISSUE)
RUN sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf

# ✅ SQLite
RUN mkdir -p database
RUN touch database/database.sqlite

CMD php artisan migrate --force && apache2-foreground