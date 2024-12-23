FROM php:7.2.34-apache
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    mariadb-client \
    zip \
    unzip \
    git \
    && docker-php-ext-install pdo pdo_mysql gd mbstring tokenizer

RUN a2enmod rewrite
WORKDIR /var/www/html

COPY . .

COPY --from=composer:2.2 /usr/bin/composer /usr/bin/composer

RUN composer install --no-dev --optimize-autoloader


EXPOSE 7000

CMD php artisan serve --host=0.0.0.0 --port=7000