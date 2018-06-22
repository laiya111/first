!/bin/bash
yum -y update

rpm -Uvh https://dl.fedoraproject.org/pub/epel/epel-release-latest-6.noarch.rpm

yum install -y bash-completion