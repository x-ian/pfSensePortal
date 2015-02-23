#!/usr/local/bin/bash

IP=$1

BASEDIR=`dirname $0`
source $BASEDIR/../config.txt

STATUS_LOG=/home/client_activities_log/status-`date +%Y%m%d`.log

MAC=$($BASEDIR/resolve_mac_address.sh $IP)

RADTEST=$(radtest $MAC radius $DR_IP 0 radius) >/dev/null

echo $RADTEST | grep "Reply-Message" >/dev/null
CHECK_REPLY=$?
echo $RADTEST | grep "Access-Reject" >/dev/null
CHECK_ACCESS=$?

if [ $CHECK_REPLY -eq 1 ] && [ $CHECK_ACCESS -eq 0 ]; then
  echo "no reply message, assume device not yet registered"
  echo "$MAC - $IP - 1 - device not yet registered - `date +%Y%m%d-%H%M%S`" >> $STATUS_LOG
  exit 1
fi

echo $RADTEST | grep "Too many users" >/dev/null
if [ $? -eq 0 ]; then
  echo $RADTEST | grep Reply-Message | sed 's/.*Reply-Message = //g'
  echo "Too many users. Please try again later."
  echo "$MAC - $IP - 2 - Too many users - `date +%Y%m%d-%H%M%S`" >> $STATUS_LOG
  exit 2
fi

echo $RADTEST | grep "usage time has been reached" >/dev/null
if [ $? -eq 0 ]; then
  echo "Data volume reached"
  echo "$MAC - $IP - 3 - Data volume reached - `date +%Y%m%d-%H%M%S`" >> $STATUS_LOG
  exit 3
fi

echo $RADTEST | grep "Your device is permanently disabled" >/dev/null
if [ $? -eq 0 ]; then
  echo $RADTEST | grep Reply-Message | sed 's/.*Reply-Message = //g'
  echo "Device permanently disabled."
  echo "$MAC - $IP - 4 - Device permanently disabled - `date +%Y%m%d-%H%M%S`" >> $STATUS_LOG
  exit 4
fi

echo $RADTEST | grep "Data bundle during business hours exceeded" >/dev/null
if [ $? -eq 0 ]; then
  echo $RADTEST | grep Reply-Message | sed 's/.*Reply-Message = //g'
  echo "Data bundle during business hours exceeded."
  echo "$MAC - $IP - 5 - Data bundle during business hours exceeded - `date +%Y%m%d-%H%M%S`" >> $STATUS_LOG
  exit 5
fi

echo $RADTEST | grep "Access-Accept" >/dev/null
if [ $? -eq 0 ]; then
  # will not happen for calls from captive portal as this will already send an auth packet before
  # simply adding it here for completeness
  echo "device enabled and active"
  echo "$MAC - $IP - 0 - enabled and active - `date +%Y%m%d-%H%M%S`" >> $STATUS_LOG
  exit 0
fi

# unknown response from radcheck - either radius server is down or unknown restrictions active
# e.g. "no response from server"
# e.g. "Failed to find IP address for pfsense.apzunet"
echo "RADIUS server offline or unknown response. Please contact the IT team."
echo $RADTEST
echo "$MAC - $IP - 9 - RADIUS offline or unknown response - `date +%Y%m%d-%H%M%S`" >> $STATUS_LOG
exit 9

