FROM php:8.0-apache

WORKDIR /var/www/html

COPY . .

RUN apt-get update && apt-get install -y unzip git curl

RUN curl -sS https://getcomposer.org/installer | php
RUN mv composer.phar /usr/local/bin/composer

RUN composer install

# ✅ Fix permissions
RUN chmod -R 777 storage
RUN chmod -R 777 bootstrap/cache

# ✅ Enable rewrite
RUN a2enmod rewrite

# ✅ FORCE APACHE ROOT (STRONG FIX)
RUN echo '<VirtualHost *:80>
    DocumentRoot /var/www/html/public
    <Directory /var/www/html/public>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>' > /etc/apache2/sites-available/000-default.conf

# ✅ SQLite
RUN mkdir -p database
RUN touch database/database.sqlite

CMD php artisan migrate --force && apache2-foreground