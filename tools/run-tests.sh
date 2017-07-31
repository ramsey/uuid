#!/bin/bash
### Run tests based on CPU architecture
#
# Depending on the ARCH indicated for this test job, run the tests in a
# Docker container based on the PHP version for this job.
#

if [ -z "${ARCH}" ]; then
    echo "The ARCH environment variable must be provided"
    exit 1
fi

if [ -z "${PHP_VERSION}" ]; then
    echo "The PHP_VERSION environment variable must be provided"
    exit 1
fi

if [ -z "${TRAVIS_BUILD_DIR}" ]; then
    echo "The TRAVIS_BUILD_DIR environment variable must be provided"
    exit 1
fi

if [ "${PHP_VERSION}" = "hhvm" ]; then
    docker_tag="${ARCH}-trusty-php-${PHP_VERSION}"
else
    docker_tag="${ARCH}-wheezy-php-${PHP_VERSION}"
fi

declare -a commands
commands[0]="echo \"Environment: \$(uname -a)\""
commands[1]="php --version"
commands[2]="cd ${TRAVIS_BUILD_DIR}"
commands[3]="composer install --no-interaction --prefer-dist"
commands[4]="./vendor/bin/parallel-lint src tests"
commands[5]="./vendor/bin/phpcs src tests --standard=psr2 -sp"
commands[6]="./vendor/bin/phpunit --verbose --coverage-clover build/logs/clover.xml"

printf -v command "%s && " "${commands[@]}"
command=${command::-4}

sudo docker run -v "${TRAVIS_BUILD_DIR}":"${TRAVIS_BUILD_DIR}" \
    benramsey/ramsey-uuid:$docker_tag \
    bash -c "${command}"
