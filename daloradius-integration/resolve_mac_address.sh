#!/usr/local/bin/bash

# there might be a clevererer way, maybe use mac straight from portal

IP=$1

# skip the ping and see if we can speed it up and still have it working reliably
#MAC=$(ping -t 2 $IP > /dev/null; arp $IP | awk '{print $4}' | sed s/://g)
MAC=$(arp $IP | awk '{print $4}' | sed s/://g)

echo $MAC
