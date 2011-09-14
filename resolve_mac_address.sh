#!/usr/local/bin/bash

IP=$1

MAC=$(ping -t 2 $IP > /dev/null && arp $IP | awk '{print $4}' | sed s/://g)

echo $MAC
