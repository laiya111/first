# !/usr/bin/bash

yum install wget
wget http://nginx.org/download/nginx-1.10.3.tar.gz
yum -y install gcc gcc-c++ make libtool zlib zlib-devel openssl openssl-devel pcre pcre-devel
tar -xzvf nginx-1.10.3.tar.gz
wget wget http://downloads.sourceforge.net/project/pcre/pcre/8.35/pcre-8.35.tar.gz
tar -xzvf  pcre-8.35.tar.gz -C /usr/local/src
wget wget http://www.zlib.net/zlib-1.2.11.tar.gz
tar -xzvf  zlib-1.2.11.tar.gz -C /usr/local/src
mkdir -p /usr/local/nginx-log/nginx
mkdir -p /var/run/nginx
mkdir -p /var/tmp/nginx

cd /root/nginx-1.10.3
./configure --prefix=/usr/local/nginx --sbin-path=/usr/sbin/nginx --conf-path=/etc/nginx/nginx.conf  \
--error-log-path=/usr/local/nginx-log/nginx/error.log --http-log-path=/usr/local/nginx-log/nginx/access.log \
--pid-path=/var/run/nginx/nginx.pid --lock-path=/var/lock/nginx.lock  --user=nginx --group=nginx \
--with-http_ssl_module --with-http_stub_status_module --with-http_gzip_static_module \
--http-client-body-temp-path=/var/tmp/nginx/client --http-proxy-temp-path=/var/tmp/nginx/proxy \
--http-fastcgi-temp-path=/var/tmp/nginx/fcgi --http-uwsgi-temp-path=/var/tmp/nginx/uwsgi \
--with-pcre=/usr/local/src/pcre-8.35/ --with-zlib=/usr/local/src/zlib-1.2.11/
make && make install

cp ./scripts/nginx.conf /etc/nginx
cp -r ./scripts/vhost /etc/nginx
