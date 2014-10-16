#!/usr/local/bin/bash

IP=$1
NAME=$2
EMAIL=$3
OWNER=$4

BASEDIR=`dirname $0`

MAC=$($BASEDIR/resolve_mac_address.sh $IP)
MAC_FIRST_DIGITS=$(echo $MAC | cut -c 1-6 | awk '{print toupper($0)}')
# check in the local copy of the IEEE OUI database
# once in a while get an update from http://standards.ieee.org/develop/regauth/oui/public.html
MAC_VENDOR=$(grep "(base 16)" $BASEDIR/ieee_oui.txt | grep $MAC_FIRST_DIGITS | awk -F"\t" '{ print $3 }' | sed -e 's/ /_/g')

# check if we already have this user/MAC address and ignore if necessary
# sometimes after restarting it seems that the Captive Portal redirects to the register page, 
# even though the radius entry already exists
#PFSENSE_CONFIG=/conf/config.xml
#grep "$MAC" $PFSENSE_CONFIG
#if [ $? -ne 0 ]; then
  # no entry found, continue to register

  # netbios doesn't seem as reliable as dhcp hostname
  #NETBIOS=$($BASEDIR/resolve_netbios_name.sh $IP)
  DHCPHOSTNAME=$($BASEDIR/resolve_hostname.sh $IP)

  $BASEDIR/daloradius-new-user-with-mac-auth.sh $MAC "" "$NAME" "$EMAIL" $OWNER APZUnet+user "$IP $DHCPHOSTNAME $MAC_VENDOR"
  #perl -I /usr/local/lib/perl5/site_perl/5.10.1/ -I /usr/local/lib/perl5/site_perl/5.10.1/mach $BASEDIR/send_gmail.perl "$MAC" "$NAME" "$EMAIL" "$IP" "$OWNER" "$DHCPHOSTNAME" "$MAC_VENDOR"
  #fi
