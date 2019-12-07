#!/bin/sh

PHP_VERSION=$1
ARCH=$2

docker build \
    --tag benramsey/ramsey-uuid:php-${PHP_VERSION}-${ARCH} \
    --build-arg PHP_VERSION=${PHP_VERSION} \
    --build-arg ARCH=${ARCH} \
    .
