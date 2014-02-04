#!/usr/local/bin/bash

# TODO, docs
# kudos for urlencode to http://www.unix.com/shell-programming-scripting/59936-url-encoding.html

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

for i in 1; do 
  # place request
  # https://172.16.1.2/pkg.php?xml=freeradius.xml&act=del&id=2
  URL="pkg.php?xml=freeradius.xml&act=del&id=8"
  wget $WGET_OPTIONS --load-cookies $TEMP_COOKIES "$SERVER/$URL" -O $TEMP_OUTFILE
done

# TODO, cleanup & logging
rm -f $TEMP_COOKIES
rm -f $TEMP_OUTFILE
