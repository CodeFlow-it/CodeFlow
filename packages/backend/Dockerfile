FROM php:8.2-apache

WORKDIR /var/www/app

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libicu-dev \
    libzip-dev \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install \
    intl \
    zip \
    pdo_mysql \
    pdo \
    sockets

RUN a2enmod rewrite

COPY . /var/www/app

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN composer install --no-scripts

RUN composer dump-autoload --optimize

RUN chown -R www-data:www-data /var/www/app/

RUN sed -i 's!/var/www/html!/var/www/app/public!g' \
    /etc/apache2/sites-available/000-default.conf

EXPOSE 80

CMD ["apache2-foreground"]
