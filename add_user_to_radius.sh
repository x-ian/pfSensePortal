#!/usr/local/bin/bash

IP=$1
NAME=$2
EMAIL=$3
OWNER=$4

BASEDIR=`dirname $0`

MAC=$($BASEDIR/resolve_mac_address.sh $IP)

# check if we already have this user/MAC address and ignore if necessary
# sometimes after restarting it seems that the Captive Portal redirects to the register page, 
# even though the radius entry already exists
PFSENSE_CONFIG=/conf/config.xml
grep "$MAC" $PFSENSE_CONFIG
if [ $? -ne 0 ]; then
  # no entry found, continue to register

  # netbios doesn't seem as reliable as dhcp hostname
  #NETBIOS=$($BASEDIR/resolve_netbios_name.sh $IP)
  DHCPHOSTNAME=$($BASEDIR/resolve_hostname.sh $IP)

  $BASEDIR/register_user.sh $MAC "$NAME" "$EMAIL" $IP $OWNER "$DHCPHOSTNAME"
  perl -I /usr/local/lib/perl5/site_perl/5.10.1/ -I /usr/local/lib/perl5/site_perl/5.10.1/mach $BASEDIR/send_gmail.perl "$MAC" "$NAME" "$EMAIL" "$IP" "$OWNER" "$DHCPHOSTNAME"
fi
