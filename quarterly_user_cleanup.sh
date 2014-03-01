#!/usr/local/bin/bash

# automatic, scheduled cleanup of freeRADIUS users
# a quarterly crontab like should work:
# Am too lazy to figure out how to only remove users before a certain register date, or users that haven't been active recently, so I'm brute-forcing the deletion of 50 patients every quarter.

BASEDIR=`dirname $0`
TODAY=`date +%Y-%m-%d`

WGET_OPTIONS="--timeout=5 --tries=3 --keep-session-cookies --no-check-certificate"

SERVER=https://172.16.1.2
PFSENSE_RADIUS_PASSWD=pfSense

TEMP_COOKIES=`mktemp /tmp/wget.cookies.XXXXXX`
TEMP_OUTFILE=`mktemp /tmp/wget.outfile.XXXXXX`

# get pfSense login details
source $BASEDIR/credentials.config

# login
URL="login=Login&usernamefld=`echo $USER`&passwordfld=`echo $PASSWD`"
wget $WGET_OPTIONS --save-cookies $TEMP_COOKIES --post-data $URL $SERVER -O $TEMP_OUTFILE

# get page with csrf token
URL="pkg_edit.php?xml=freeradius.xml&id=$NEW_USER_ID"
wget $WGET_OPTIONS --load-cookies $TEMP_COOKIES "$SERVER/$URL" -O $TEMP_OUTFILE

# parse csrf token
CSRF=$(grep name=\'__csrf_magic\' `echo $TEMP_OUTFILE` | sed "s/^.*value=\"//g" | sed "s/\".*$//g" | sed "s/:/%3A/g" | sed "s/,/%2C/g")

# delete the first x users (starting with id 2 and excluding id 0 for admin and 1 for guest)
for i in 1; do # 2 3 4 5 6 7 8 9 10 11 12 13 14 15 16 17 18 19 20 21 22 23 24 25 26 27 28 29 30 31 32 33 34 35 36 37 38 39 40 41 42 43 44 45 46 47 48 49 50; do 
  # place request
  # https://172.16.1.2/pkg.php?xml=freeradius.xml&act=del&id=2
  URL="pkg.php?xml=freeradius.xml&act=del&id=2"
  wget $WGET_OPTIONS --load-cookies $TEMP_COOKIES "$SERVER/$URL" -O $TEMP_OUTFILE
done

# TODO, cleanup & logging
rm -f $TEMP_COOKIES
rm -f $TEMP_OUTFILE
