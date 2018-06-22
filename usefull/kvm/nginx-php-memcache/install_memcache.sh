wget wget http://pecl.php.net/get/memcache-2.2.7.tgz
tar -xzvf memcache-2.2.7.tgz
cd memcache-2.2.7
/usr/local/php-5.6/bin/phpize
./configure --with-php-config=/usr/local/php-5.6/bin/php-config
make && make install


wget https://launchpad.net/libmemcached/1.0/1.0.18/+download/libmemcached-1.0.18.tar.gz
tar -xzvf libmemcached-1.0.18.tar.gz
cd libmemcached-1.0.18
./configure --prefix=/usr/local/libmemcached --with-memcached
make && make install

wget http://pecl.php.net/get/memcached-2.2.0.tgz
tar -xzvf memcached-2.2.0.tgz
cd memcached-2.2.0
/usr/local/php-5.6/bin/phpize
./configure --prefix=/usr/local/memcached --with-php-config=/usr/local/php-5.6/bin/php-config --with-libmemcached-dir=/usr/local/libmemcached/  --disable-memcached-sasl
make && make install
