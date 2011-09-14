#!/usr/local/bin/bash

IP=$1
NAME=$2
EMAIL=$3

MAC=$(/home/resolve_mac_address.sh $IP)

/home/register_user.sh $MAC $NAME $EMAIL

