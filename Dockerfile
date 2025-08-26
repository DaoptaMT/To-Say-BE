FROM php:8.3-fpm

RUN apt-get update && apt-get install -y \
    zlib1g-dev \
    libpng-dev \
    libicu-dev \
    npm 

RUN docker-php-ext-install \
    gd \
    intl \
    pdo \ 
    pdo_mysql \
    && docker-php-ext-enable \
    gd \
    intl

COPY . /var/www

RUN chown -R www-data:www-data /var/www \
    && chmod -R 775 /var/www

WORKDIR /var/www

EXPOSE 9000
CMD ["php-fpm"]
