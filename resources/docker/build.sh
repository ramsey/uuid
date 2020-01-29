#!/bin/sh

PHP_VERSION=$1
ARCH=$2
XDEBUG=${3:-no}

xdebug_tag="-without-xdebug"
if [ "${XDEBUG}" == "yes" ]; then
    xdebug_tag=""
fi

build_tag="benramsey/ramsey-uuid:php-${PHP_VERSION}-${ARCH}${xdebug_tag}"

docker build \
    --tag ${build_tag} \
    --build-arg PHP_VERSION=${PHP_VERSION} \
    --build-arg ARCH=${ARCH} \
    --build-arg XDEBUG=${XDEBUG} \
    .

docker run --rm ${build_tag} php -v
docker run --rm ${build_tag} composer --version --ansi
