#!/usr/local/bin/bash

IP=$1

BASEDIR=`dirname $0`
source $BASEDIR/../config.txt

MAC=$($BASEDIR/resolve_mac_address.sh $IP)

RADTEST=$(radtest $MAC radius $DR_IP 0 radius)

echo $RADTEST | grep "Access-Accept"
if [ $? -eq 0 ]; then
  echo "device enabled and active"
  exit 0
fi

echo $RADTEST | grep "Reply-Message = \"Your device is permanently disabled.\""
if [ $? -eq 0 ]; then
  echo "Your device is permanently disabled."
  exit 1
fi

echo $RADTEST | grep "Reply-Message"
if [ $? -eq 1 ]; then
  echo "no reply message, assume device not yet registered"
  exit 2
fi

echo "assume addiitonal restrictions are applied"
exit 3

