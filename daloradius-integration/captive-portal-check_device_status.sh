#!/usr/local/bin/bash

IP=$1

BASEDIR=`dirname $0`
source $BASEDIR/../config.txt

MAC=$($BASEDIR/resolve_mac_address.sh $IP)

RADTEST=$(radtest $MAC radius $DR_IP 0 radius)

#echo $RADTEST | grep "no response from server"
#if [ $? -eq 0 ]; then
#  echo "RADIUS server offline. Please contact the IT team."
#  echo "$MAC $IP - -1 - RADIUS offline - `date +%Y%m%d-%H%M%S`" >> /tmp/check_device_status.log
#  exit -1
#fi

echo $RADTEST | grep "Access-Accept"
if [ $? -eq 0 ]; then
  echo "device enabled and active"
  echo "$MAC $IP - 0 - enabled and active - `date +%Y%m%d-%H%M%S`" >> /tmp/check_device_status.log
  exit 0
fi

echo $RADTEST | grep "Reply-Message = \"Your device is permanently disabled.\""
if [ $? -eq 0 ]; then
  echo "Device permanently disabled."
  echo "$MAC $IP - 1 - Device permanently disabled - `date +%Y%m%d-%H%M%S`" >> /tmp/check_device_status.log
  exit 1
fi

echo $RADTEST | grep "Reply-Message"
if [ $? -eq 1 ]; then
  echo "no reply message, assume device not yet registered"
  echo "$MAC $IP - 2 - device not yet registere - `date +%Y%m%d-%H%M%S`" >> /tmp/check_device_status.log
  exit 2
fi

# add check for Too many users - please try again later 

echo "Additional restrictions active"
echo "$MAC $IP - 3 - Additional restrictions active - `date +%Y%m%d-%H%M%S`" >> /tmp/check_device_status.log
exit 3
