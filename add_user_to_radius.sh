#!/usr/local/bin/bash

IP=$1
NAME=$2
EMAIL=$3

BASEDIR=`dirname $0`

MAC=$($BASEDIR/resolve_mac_address.sh $IP)
NETBIOS=$($BASEDIR/resolve_netbios_name.sh $IP)

$BASEDIR/register_user.sh $MAC "$NAME" "$EMAIL" $NETBIOS

