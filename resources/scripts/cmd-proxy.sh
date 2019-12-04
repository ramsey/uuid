#!/bin/sh
#
# This script uses the ARCH environment variable to determine whether to run
# the commands through a 32-bit Docker container, for 32-bit testing. If the
# ARCH is anything other than "arm32," then it simply executes the commands
# on the local system, rather than in a container.

php_version="${TRAVIS_PHP_VERSION:-$(php -r "echo phpversion();")}"
php_version="${php_version%.*}"
architecture="${ARCH:-${TRAVIS_CPU_ARCH:-$(uname -m)}}"

cmd_proxy=""

if [ "${architecture}" = "arm32" ]; then
    image="benramsey/ramsey-uuid:php-${php_version}-arm32v7"
    volumes="-v ${PWD}:/app -v ${HOME}/.composer:/root/.composer"
    cmd_proxy="docker run -it --rm ${volumes} -w /app ${image}"
fi

$cmd_proxy $@
