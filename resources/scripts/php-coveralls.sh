#!/bin/sh

set -e

if [ -f vendor/bin/php-coveralls ]; then
    php vendor/bin/php-coveralls -v
else
    php vendor/bin/coveralls -v
fi
