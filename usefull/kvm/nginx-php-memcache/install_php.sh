#!/usr/bin/bash

wget http://am1.php.net/distributions/php-5.6.31.tar.gz
tar -xzvf php-5.6.31.tar.gz

yum -y install gcc gcc-c++  make cmake automake autoconf kernel-devel ncurses-devel libxml2 libxml2-devel openssl-devel curl curl-devel libjpeg-devel libpng libpng-devel pcre-devel libtool-libs freetype-devel gd zlib-devel file bison patch mlocate flex diffutils   readline-devel glibc-devel glib2-devel bzip2-devel gettext-devel libcap-devel libmcrypt-devel openldap openldap-devellibxslt-devel libxslt-devel

cd php-5.6.31
./configure --prefix=/usr/local/php-5.6 \
--with-curl \
--with-freetype-dir \
--with-gd \
--with-gettext \
--with-iconv-dir \
--with-kerberos \
--with-libdir=lib64 \
--with-libxml-dir \
--with-mysql \
--with-mysqli \
--with-openssl \
--with-pcre-regex \
--with-pdo-mysql \
--with-pdo-sqlite \
--with-pear \
--with-png-dir \
--with-xmlrpc \
--with-xsl \
--with-zlib \
--enable-fpm \
--enable-bcmath \
--enable-libxml \
--enable-inline-optimization \
--enable-gd-native-ttf \
--enable-mbregex \
--enable-mbstring \
--enable-pcntl \
--enable-shmop \
--enable-soap \
--enable-sockets \
--enable-sysvsem \
--enable-xml \
--enable-zip

make && make install

cp -R ./sapi/fpm/php-fpm.conf /usr/local/php-5.6/etc/php-fpm.conf
#cp php.ini-development /usr/local/php-5.6/lib/php.ini
cp -R ./sapi/fpm/php-fpm /etc/init.d/php-fpm5.6

cp ./scripts/php.ini /usr/local/php-5.6/lib
