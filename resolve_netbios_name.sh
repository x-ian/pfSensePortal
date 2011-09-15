#!/usr/local/bin/bash

IP=$1

NETBIOS=$(/usr/local/bin/nbtscan -s : $IP | awk -F":" '{print $2}')

echo $NETBIOS
