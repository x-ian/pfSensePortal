#!/usr/local/bin/bash

# there might be a clevererer way, maybe use mac straight from portal

IP=$1

MAC=$(ping -t 2 $IP > /dev/null; arp $IP | awk '{print $4}' | sed s/://g)

echo $MAC
