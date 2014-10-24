#!/usr/local/bin/bash

IP=$1
NAME=$2
EMAIL=$(echo $3 | awk '{print tolower($0)}')
OWNER=$4

DATE=`date +%Y%m%d-%H%M`

BASEDIR=`dirname $0`

MAC=$($BASEDIR/resolve_mac_address.sh $IP)
MAC_FIRST_DIGITS=$(echo $MAC | cut -c 1-6 | awk '{print toupper($0)}')
# check in the local copy of the IEEE OUI database
# once in a while get an update from http://standards.ieee.org/develop/regauth/oui/public.html
MAC_VENDOR=$(grep "(base 16)" $BASEDIR/ieee_oui.txt | grep $MAC_FIRST_DIGITS | awk -F"\t" '{ print $3 }' | sed -e 's/ /_/g')

# netbios doesn't seem as reliable as dhcp hostname
#NETBIOS=$($BASEDIR/resolve_netbios_name.sh $IP)
DHCPHOSTNAME=$($BASEDIR/resolve_hostname.sh $IP)

GROUP=APZUnet-guest
# auto elevate all @pih.org users
echo $EMAIL | grep "@pih.org"
if [ $? -eq 0 ]; then
	GROUP=APZUnet-user
fi
# auto elevate all APZU users
echo $OWNER | grep "apzu"
if [ $? -eq 0 ]; then
	GROUP=APZUnet-user
fi
# auto elevate all APZU- computers
echo $DHCPHOSTNAME | grep "apzu-" --ignore-case
if [ $? -eq 0 ]; then
	GROUP=APZUnet-user
fi

$BASEDIR/daloradius-new-user-with-mac-auth.sh $MAC "" "$NAME" "$EMAIL" $OWNER $GROUP "$IP $DHCPHOSTNAME $MAC_VENDOR"

$SUBJECT="pfSense: New user: $OWNER $NAME $EMAIL";
$BODY="$OWNER \n $MAC \n $NAME \n $EMAIL \n $IP \n $HOSTNAME \n $DATE \n $MAC_VENDOR \n http://172.16.1.7/daloradius/mng-edit.php?username=$MAC";

# send mail in the background
perl -I /usr/local/lib/perl5/site_perl/5.10.1/ -I /usr/local/lib/perl5/site_perl/5.10.1/mach $BASEDIR/../send_gmail.perl "$SUBJECT" "$BODY"
