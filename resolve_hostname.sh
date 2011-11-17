#!/usr/local/bin/bash

# instead of scanning the log file, it might be better/easier to let DHCP write the 
# hostnames into the hosts file and simply take the names from there with something
# like "grep 192.168.11.53 hosts | awk '{print $3}'"

IP=$1

DHCPHOST=$(grep "DHCPOFFER on $IP" /var/log/dhcpd.log | tail -r -n 1 | sed s/\(//g |sed s/\)//g | awk -F" " '{print $11}')

echo $DHCPHOST
