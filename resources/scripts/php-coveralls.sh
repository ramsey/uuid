#!/bin/bash

set -e

if [ -f vendor/bin/php-coveralls ]; then
    php vendor/bin/php-coveralls
else
    php vendor/bin/coveralls
fi
