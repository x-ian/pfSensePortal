#!/usr/local/bin/bash

# TODO, docs
# kudos for urlencode to http://www.unix.com/shell-programming-scripting/59936-url-encoding.html

MAC=$1
NAME=$2
EMAIL=$3

BASEDIR=`dirname $0`
TODAY=`date +%Y-%m-%d`
DESCRIPTION=`echo "$NAME; $EMAIL; $TODAY" | sed -f $BASEDIR/urlencode.sed`
ADDITIONAL_OPTIONS=`echo "WISPr-Bandwidth-Max-Down = 50000, WISPr-Bandwidth-Max-Up = 25000" | sed -f $BASEDIR/urlencode.sed`

SERVER=https://172.16.1.2
PFSENSE_RADIUS_PASSWD=pfSense
SESSIONTIME=3600

# Tget pfSense login details
source $BASEDIR/credentials.config

# login
URL="login=Login&usernamefld=`echo $USER`&passwordfld=`echo $PASSWD`"
OUTFILE=pf_login.html
wget --keep-session-cookies --save-cookies cookies.txt  --post-data $URL --no-check-certificate $SERVER -O $OUTFILE

# get next free user id
NEW_USER_ID=3

# get page with csrf token
URL="pkg_edit.php?xml=freeradius.xml&id=$NEW_USER_ID"
OUTFILE=pf_add_user.html
wget  --keep-session-cookies --load-cookies cookies.txt --no-check-certificate "$SERVER/$URL" -O $OUTFILE

# parse csrf token
CSRF=`grep name=\'__csrf_magic\' pf_add_user.html  | sed "s/^.*value=\"//g" | sed "s/\".*$//g" | sed "s/:/%3A/g" | sed "s/,/%2C/g"`

# place request
URL="pkg_edit.php?xml=freeradius.xml&id=$NEW_USER_ID"
OUTFILE=pf_add_user_2.html
POST="__csrf_magic=`echo $CSRF`&xml=freeradius.xml&username=`echo $MAC`&password=`echo $PFSENSE_RADIUS_PASSWD`&ip=&multiconnet=1&expiration=&sessiontime=`echo $SESSIONTIME`&onlinetime=&description=`echo $DESCRIPTION`&vlanid=&additionaloptions=`echo $ADDITIONAL_OPTIONS`&id=$NEW_USER_ID&Submit=Save"
wget --keep-session-cookies --load-cookies cookies.txt  --no-check-certificate --post-data $POST "$SERVER/$URL" -O $OUTFILE

# TODO, cleanup & logging