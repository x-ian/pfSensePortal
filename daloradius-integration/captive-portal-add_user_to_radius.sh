#!/usr/local/bin/bash

IP=$1
NAME=$2
EMAIL=$(echo $3 | awk '{print tolower($0)}')
OWNER=$4

BASEDIR=`dirname $0`

MAC=$($BASEDIR/resolve_mac_address.sh $IP)
MAC_FIRST_DIGITS=$(echo $MAC | cut -c 1-6 | awk '{print toupper($0)}')
# check in the local copy of the IEEE OUI database
# once in a while get an update from http://standards.ieee.org/develop/regauth/oui/public.html
MAC_VENDOR=$(grep "(base 16)" $BASEDIR/ieee_oui.txt | grep $MAC_FIRST_DIGITS | awk -F"\t" '{ print $3 }' | sed -e 's/ /_/g')

# netbios doesn't seem as reliable as dhcp hostname
#NETBIOS=$($BASEDIR/resolve_netbios_name.sh $IP)
DHCPHOSTNAME=$($BASEDIR/resolve_hostname.sh $IP)

GROUP=APZUnet+other
# auto elevate all @pih.org users
echo $EMAIL | grep "@pih.org"
if [ $? -eq 0 ]; then
	GROUP=APZUnet+user
fi
# auto elevate all APZU users
echo $OWNER | grep "apzu"
if [ $? -eq 0 ]; then
	GROUP=APZUnet+user
fi
# auto elevate all APZU- computers
echo $DHCPHOSTNAME | grep "apzu-" --ignore-case
if [ $? -eq 0 ]; then
	GROUP=APZUnet+user
fi

$BASEDIR/daloradius-new-user-with-mac-auth.sh $MAC "" "$NAME" "$EMAIL" $OWNER $GROUP "$IP $DHCPHOSTNAME $MAC_VENDOR"
#perl -I /usr/local/lib/perl5/site_perl/5.10.1/ -I /usr/local/lib/perl5/site_perl/5.10.1/mach $BASEDIR/send_gmail.perl "$MAC" "$NAME" "$EMAIL" "$IP" "$OWNER" "$DHCPHOSTNAME" "$MAC_VENDOR"
