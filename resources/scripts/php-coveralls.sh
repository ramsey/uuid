#!/bin/sh

set -e

if [ "${TRAVIS_EVENT_TYPE}" != "cron" ]; then
    exit 0
fi

if [ -f vendor/bin/php-coveralls ]; then
    php vendor/bin/php-coveralls -v
else
    php vendor/bin/coveralls -v
fi
