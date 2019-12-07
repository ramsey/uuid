ARG PHP_VERSION
ARG ARCH

FROM ${ARCH}/php:${PHP_VERSION}-cli-alpine

RUN apk update \
    && apk add --virtual=builddeps autoconf g++ gcc libc6-compat make re2c \
    && apk add \
        git \
        gmp-dev \
        libsodium-dev \
        libzip-dev \
        unzip \
        util-linux-dev \
    && docker-php-ext-install -j$(nproc) bcmath gmp zip \
    && yes '' | pecl install libsodium-1.0.7 \
    && yes '' | pecl install uuid \
    && yes '' | pecl install xdebug \
    && docker-php-ext-enable libsodium uuid xdebug \
    && apk del builddeps

COPY composer-install.sh /usr/local/bin/composer-install.sh
RUN composer-install.sh \
    && mv composer.phar /usr/local/bin/composer \
    && rm /usr/local/bin/composer-install.sh
