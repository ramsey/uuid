#!/bin/bash
### Build a Docker image for a target PHP version, CPU arch, and Debian version
#
# Based on: https://github.com/docker-32bit/debian
#
# See also:
# https://www.tomaz.me/2013/12/02/running-travis-ci-tests-on-arm.html
#
# Note: Building HHVM with this script is not supported. See instead:
# https://gist.github.com/ramsey/04cb15ff955d54484980
#
# Recommended approach for running this script to build Docker images:
#
#     vagrant init ubuntu/trusty64
#     vagrant up
#     vagrant ssh
#     sudo apt-get install docker.io
#     sudo docker login
#     cd /vagrant
#     sudo ./tools/build-docker-image.sh 5.6.14 mips mips wheezy
#
# or (for 64-bit, standard Debian):
#
#     sudo ./tools/build-docker-image.sh 5.6.14 x86_64 amd64 wheezy
#

if [ $EUID -ne 0 ]; then
   echo "This script must be run as root" 1>&2
   exit 1
fi

### settings
php_version=${1:-5.6.14}
qemu_arch=${2:-mips}
deb_arch=${3:-mips}
suite=${4:-wheezy}

chroot_dir="/tmp/chroot/${qemu_arch}-${suite}-php-${php_version}"
apt_mirror="http://ftp.us.debian.org/debian"
docker_image="benramsey/ramsey-uuid:${qemu_arch}-${suite}-php-${php_version}"
tmp_package="/tmp/${qemu_arch}-${suite}-php-${php_version}.tgz"
php_package="https://secure.php.net/distributions/php-${php_version}.tar.bz2"


### make sure that the required tools are installed
export DEBIAN_FRONTEND=noninteractive
apt-get update
apt-get install -y wget debootstrap qemu-user-static binfmt-support \
    docker.io php5-cli php5-curl

### install a minbase system with debootstrap
debootstrap --foreign --arch=$deb_arch $suite $chroot_dir $apt_mirror
cp "/usr/bin/qemu-${qemu_arch}-static" $chroot_dir/usr/bin/
chroot $chroot_dir ./debootstrap/debootstrap --second-stage

### update the list of package sources
cat <<EOF > $chroot_dir/etc/apt/sources.list
deb $apt_mirror $suite main contrib non-free
deb $apt_mirror $suite-updates main contrib non-free
deb http://security.debian.org/ $suite/updates main contrib non-free
EOF

### upgrade packages
chroot $chroot_dir apt-get update -qq
chroot $chroot_dir apt-get upgrade -qq -y

### locale configuration
chroot $chroot_dir apt-get install -qq -y debconf
chroot $chroot_dir bash -c 'echo "en_US.UTF-8 UTF-8" >> /etc/locale.gen'
chroot $chroot_dir dpkg-reconfigure locales

### install dependencies to build PHP
chroot $chroot_dir apt-get --allow-unauthenticated install -qq -y \
    autoconf build-essential libcurl3-openssl-dev libgmp-dev libmcrypt-dev \
    libreadline-dev libxml2-dev uuid-dev curl git

### download, build, and install the PHP version needed for this chroot
mkdir -p $chroot_dir/php-src
cd $chroot_dir/php-src
wget $php_package
tar xf "php-${php_version}.tar.bz2"
chroot $chroot_dir bash -c "cd /php-src/php-${php_version} && ./configure --disable-all --enable-bcmath --with-gmp --disable-cgi --enable-xml --enable-libxml --enable-dom --enable-filter --enable-ctype --enable-json --with-openssl --enable-phar --enable-hash --with-curl --enable-simplexml --enable-tokenizer --enable-xmlwriter --enable-zip"
chroot $chroot_dir bash -c "cd /php-src/php-${php_version} && make && make install"
chroot $chroot_dir cp "/php-src/php-${php_version}/php.ini-development" /usr/local/lib/php.ini
chroot $chroot_dir bash -c 'printf "date.timezone=UTC\n" >> /usr/local/lib/php.ini'

### download, build, and install the PECL UUID extension
wget https://pecl.php.net/get/uuid-1.0.4.tgz
tar zxf uuid-1.0.4.tgz
chroot $chroot_dir bash -c "cd /php-src/uuid-1.0.4 && phpize && ./configure && make && make install"
chroot $chroot_dir bash -c 'printf "extension=uuid.so\n" >> /usr/local/lib/php.ini'


### download, build, and install the PECL libsodium extension
mkdir -p $chroot_dir/libsodium-src
cd $chroot_dir/libsodium-src
wget https://download.libsodium.org/libsodium/releases/libsodium-1.0.8.tar.gz
tar zxf libsodium-1.0.8.tar.gz
chroot $chroot_dir bash -c "cd /libsodium-src/libsodium-1.0.8 && ./configure && make && make install"
cd $chroot_dir/php-src
wget http://pecl.php.net/get/libsodium-1.0.2.tgz
tar zxf libsodium-1.0.2.tgz
chroot $chroot_dir bash -c "cd /php-src/libsodium-1.0.2 && phpize && ./configure && make && make install"
chroot $chroot_dir bash -c 'printf "extension=libsodium.so\n" >> /usr/local/lib/php.ini'


### download, build, and install Xdebug
wget http://xdebug.org/files/xdebug-2.4.0rc3.tgz
tar zxf xdebug-2.4.0rc3.tgz
chroot $chroot_dir bash -c "cd /php-src/xdebug-2.4.0RC3 && phpize && ./configure --enable-xdebug && make && make install"
chroot $chroot_dir bash -c "printf \"zend_extension=\$(php -r \"echo ini_get('extension_dir');\")/xdebug.so\n\" >> /usr/local/lib/php.ini"


### globally install Composer
chroot $chroot_dir bash -c "curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer"

### cleanup
chroot $chroot_dir apt-get autoclean
chroot $chroot_dir apt-get clean
chroot $chroot_dir apt-get autoremove

cd /tmp
rm -rf $chroot_dir/php-src

### create a tar archive from the chroot directory
tar cfz $tmp_package -C $chroot_dir .

### import this tar archive into a docker image:
cat $tmp_package | docker import - $docker_image

### push image to Docker Hub
docker push $docker_image

### cleanup
rm $tmp_package
rm -rf $chroot_dir

echo "Done!"
