FROM php:8.3-fpm

# install required dependencies
RUN apt-get update && \
    apt-get install -y git zip unzip libpq-dev && \
    docker-php-ext-install pdo pdo_mysql && \
    docker-php-ext-install pdo pdo_pgsql

# install latest version of composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# setup working directory
WORKDIR /www/rest-api

COPY . .

# Install Laravel dependencies
RUN composer install --no-interaction --optimize-autoloader

# expose port
EXPOSE 9000

CMD php-fpm