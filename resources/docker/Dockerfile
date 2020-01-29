ARG PHP_VERSION
ARG ARCH

FROM ${ARCH}/php:${PHP_VERSION}-cli-alpine

ARG XDEBUG

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
    && docker-php-ext-enable libsodium uuid \
    && if [ "${XDEBUG}" == "yes" ]; then yes '' | pecl install xdebug; fi \
    && if [ "${XDEBUG}" == "yes" ]; then docker-php-ext-enable xdebug; fi \
    && apk del builddeps

COPY composer-install.sh /usr/local/bin/composer-install.sh
RUN composer-install.sh \
    && mv composer.phar /usr/local/bin/composer \
    && rm /usr/local/bin/composer-install.sh
