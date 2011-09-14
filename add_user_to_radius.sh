#!/usr/local/bin/bash

IP=$1
NAME=$2
EMAIL=$3

BASEDIR=`dirname $0`

MAC=$($BASEDIR/resolve_mac_address.sh $IP)

$BASEDIR/register_user.sh $MAC $NAME $EMAIL

