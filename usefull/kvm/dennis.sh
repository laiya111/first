#!/bin/bash
yum -y update

rpm -Uvh https://dl.fedoraproject.org/pub/epel/epel-release-latest-7.noarch.rpm

yum install -y bash-completion

yum install -y zsh

yum install -y git

sh -c "$(curl -fsSL https://raw.github.com/robbyrussell/oh-my-zsh/master/tools/install.sh)"

yum install -y readline-devel pcre-devel openssl-devel gcc

yum install -y yum-utils

sh -c "$(yum-config-manager --add-repo https://openresty.org/package/centos/openresty.repo)"

yum install -y openresty

yum install -y openresty-resty
