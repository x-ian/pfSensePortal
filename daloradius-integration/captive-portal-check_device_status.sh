#!/usr/local/bin/bash

IP=$1

BASEDIR=`dirname $0`
source $BASEDIR/../config.txt

MAC=$($BASEDIR/resolve_mac_address.sh $IP)

RADTEST=$(radtest $MAC radius $DR_IP 0 radius)

echo $RADTEST | grep "Access-Accept"
if [ $? -eq 0 ]; then
  # will not happen for calls from captive portal as this will already send an auth packet before
  echo "device enabled and active"
  echo "$MAC $IP - 0 - enabled and active - `date +%Y%m%d-%H%M%S`" >> /tmp/check_device_status.log
  exit 0
fi

echo $RADTEST | grep "Reply-Message"
CHECK_REPLY=$?
echo $RADTEST | grep "Access-Reject"
CHECK_ACCESS=$?
if [ $CHECK_REPLY -eq 1 ] && [ $CHECK_ACCESS -eq 0]; then
  echo "no reply message, assume device not yet registered"
  echo "$MAC $IP - 1 - device not yet registered - `date +%Y%m%d-%H%M%S`" >> /tmp/check_device_status.log
  exit 1
fi

echo $RADTEST | grep "Reply-Message = \"Too many users. Please try again later\""
if [ $? -eq 0 ]; then
  echo "Too many users. Please try again later."
  echo "$MAC $IP - 2 - Too many users - `date +%Y%m%d-%H%M%S`" >> /tmp/check_device_status.log
  exit 2
fi

echo $RADTEST | grep "Reply-Message = \"Over quota.\""
if [ $? -eq 0 ]; then
  echo "Over quota."
  echo "$MAC $IP - 3 - Over quota - `date +%Y%m%d-%H%M%S`" >> /tmp/check_device_status.log
  exit 3
fi

echo $RADTEST | grep "Reply-Message = \"Your device is permanently disabled.\""
if [ $? -eq 0 ]; then
  echo "Device permanently disabled."
  echo "$MAC $IP - 4 - Device permanently disabled - `date +%Y%m%d-%H%M%S`" >> /tmp/check_device_status.log
  exit 4
fi

# unknown response from radcheck - either radius server is down or unknown restrictions active
# e.g. "no response from server"
# e.g. "Failed to find IP address for pfsense.apzunet"
echo "RADIUS server offline or unknown response. Please contact the IT team."
echo "$MAC $IP - 9 - RADIUS offline or unknown response - `date +%Y%m%d-%H%M%S`" >> /tmp/check_device_status.log
exit 9
