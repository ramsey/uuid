#!/bin/sh
#
# This script uses the ARCH environment variable to determine whether to run
# the commands through a 32-bit Docker container, for 32-bit testing. If the
# ARCH is anything other than "arm32," then it simply executes the commands
# on the local system, rather than in a container.

php_version="${TRAVIS_PHP_VERSION:-$(php -r "echo phpversion();")}"

dots_count=$(echo $php_version | awk -F"." '{print NF-1}')
if [ $dots_count -ge 2 ]; then
    php_version="${php_version%.*}"
fi

architecture="${ARCH:-${TRAVIS_CPU_ARCH:-$(uname -m)}}"

# Only use Xdebug if running as a cron job on Travis CI
xdebug=""
if [ "${TRAVIS_EVENT_TYPE}" != "cron" ]; then
    xdebug="-without-xdebug"
fi

cmd_proxy=""

if [ "${architecture}" = "arm32" ]; then
    image="benramsey/ramsey-uuid:php-${php_version}-arm32v7${xdebug}"
    volumes="-v ${PWD}:${PWD} -v ${HOME}/.composer/cache:/root/.composer/cache"
    cmd_proxy="docker run --rm ${volumes} -w ${PWD} ${image}"
fi

$cmd_proxy "$@"
