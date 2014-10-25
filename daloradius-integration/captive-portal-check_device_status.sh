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
  SUBJECT="pfSense: Network access for device $MAC is permanently denied"
  BODY="(mail generated by script pfSense:///home/pfSensePortal/daloradius-integration/captive-portal-check_device_status.sh)"

  # send mail in the background
  perl -I /usr/local/lib/perl5/site_perl/5.10.1/ -I /usr/local/lib/perl5/site_perl/5.10.1/mach $BASEDIR/../send_gmail.perl "$SUBJECT" "$BODY" &
  exit 1
fi

echo $RADTEST | grep "Reply-Message"
if [ $? -eq 1 ]; then
  echo "no reply message, assume device not yet registered"
  exit 2
fi

echo "Assume additional restrictions are applied"
SUBJECT="pfSense: Network access for device $MAC was temporarily denied"
BODY="(mail generated by script pfSense:///home/pfSensePortal/daloradius-integration/captive-portal-check_device_status.sh)"

# send mail in the background
perl -I /usr/local/lib/perl5/site_perl/5.10.1/ -I /usr/local/lib/perl5/site_perl/5.10.1/mach $BASEDIR/../send_gmail.perl "$SUBJECT" "$BODY" &
exit 3
