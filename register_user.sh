#!/usr/local/bin/bash

# TODO, docs
# kudos for urlencode to http://www.unix.com/shell-programming-scripting/59936-url-encoding.html

MAC=$1
NAME=$2
EMAIL=$3
NETBIOS=$4
DHCPHOSTNAME=$5

LOGFILE=/var/log/apzu-portal.log
BASEDIR=`dirname $0`
TODAY=`date +%Y-%m-%d`
DESCRIPTION=`echo "$NAME; $EMAIL; $DHCPHOSTNAME; $NETBIOS; $TODAY" | sed -f $BASEDIR/urlencode.sed`
ADDITIONAL_OPTIONS=`echo "WISPr-Bandwidth-Max-Down = 50000, WISPr-Bandwidth-Max-Up = 25000" | sed -f $BASEDIR/urlencode.sed`

SERVER=https://172.16.1.2
PFSENSE_RADIUS_PASSWD=pfSense
SESSIONTIME=3600

TEMP_COOKIES=`mktemp /tmp/wget.cookies.XXXXXX`
TEMP_OUTFILE=`mktemp /tmp/wget.outfile.XXXXXX`

# Tget pfSense login details
source $BASEDIR/credentials.config

# login
URL="login=Login&usernamefld=`echo $USER`&passwordfld=`echo $PASSWD`"
wget --keep-session-cookies --save-cookies $TEMP_COOKIES --post-data $URL --no-check-certificate $SERVER -O $TEMP_OUTFILE

# TODO?, get next free user id, maybe not necessary to always recalc the current highest number
NEW_USER_ID=6000

# get page with csrf token
URL="pkg_edit.php?xml=freeradius.xml&id=$NEW_USER_ID"
wget --keep-session-cookies --load-cookies $TEMP_COOKIES --no-check-certificate "$SERVER/$URL" -O $TEMP_OUTFILE

# parse csrf token
CSRF=$(grep name=\'__csrf_magic\' `echo $TEMP_OUTFILE` | sed "s/^.*value=\"//g" | sed "s/\".*$//g" | sed "s/:/%3A/g" | sed "s/,/%2C/g")

# place request
URL="pkg_edit.php?xml=freeradius.xml&id=$NEW_USER_ID"
POST="__csrf_magic=`echo $CSRF`&xml=freeradius.xml&username=`echo $MAC`&password=`echo $PFSENSE_RADIUS_PASSWD`&ip=&multiconnet=1&expiration=&sessiontime=`echo $SESSIONTIME`&onlinetime=&description=`echo $DESCRIPTION`&vlanid=&additionaloptions=`echo $ADDITIONAL_OPTIONS`&id=$NEW_USER_ID&Submit=Save"
wget --keep-session-cookies --load-cookies $TEMP_COOKIES --no-check-certificate --post-data $POST "$SERVER/$URL" -O $TEMP_OUTFILE

# TODO, cleanup & logging
echo "Register system `date`; $MAC; $NAME; $EMAIL; $NETBIOS; $TODAY">> $LOGFILE
rm -f $TEMP_COOKIES
rm -f $TEMP_OUTFILE
