#!/bin/sh
#
# This script runs as part of the Travis CI before_install phase. If the ARCH
# environment variable is set and has the value "arm32," then we exit early,
# since we will use a pre-built Docker image to run commands instead.

architecture=${ARCH:-${TRAVIS_CPU_ARCH:-$(uname -m)}}

if [ "${architecture}" = "arm32" ]; then
    exit
fi

yes '' | pecl install -f libsodium-1.0.7
yes '' | pecl install -f uuid-1.0.4
composer self-update
