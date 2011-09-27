#!/usr/local/bin/bash

IP=$1

DHCPHOST=$(grep "DHCPOFFER on $IP" /var/log/dhcpd.log | tail -r -n 1 | sed s/\(//g |sed s/\)//g | awk -F" " '{print $11}')

echo $DHCPHOST
