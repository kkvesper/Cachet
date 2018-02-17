FROM php:7.0-alpine

RUN apk update && apk upgrade && apk add --no-cache --update \
    postgresql-client \
    freetype-dev \
    libjpeg-turbo-dev \
    libpng-dev \
    libxml2-dev \
    libmcrypt-dev \
    postgresql-dev \
    wget git curl bash grep

RUN docker-php-ext-install \
    gd \
    xml \
    mcrypt \
    mbstring \
    pgsql \
    pdo_pgsql

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer
RUN composer selfupdate

RUN mkdir /code
WORKDIR /code
ADD . /code
