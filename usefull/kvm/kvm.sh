#!/bin/bash
yum install kvm libvirt python-virtinst qemu-kvm virt-viewer bridge-utils

sh -c "$(/etc/init.d/libvirtd start)"

#划分硬盘
sh -c "$(qemu-img create -f qcow2 -o preallocation=metadata /home/kvm/centos65-x64-mysql.qcow2 200G)"

#bridge网络模式
sh -c "$(virt-install --name=gatewat-4 --ram 4096 --vcpus=4 -f /home/kvm/gateway-4.qcow2 --cdrom /home/iso/CentOS-6.5-x86_64-bin-DVD1.iso --graphics vnc,listen=0.0.0.0,port=5920, --network bridge=br0 --force --autostart)"
