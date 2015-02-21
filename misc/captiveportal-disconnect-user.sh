#!/bin/bash

BASEDIR=/home/marsPortal
MAC=$1

TMP_COOKIES=`mktemp`-cookies.txt
TMP_ALL=`mktemp`-all.html
TMP_ALL2=`mktemp`-all2.html

# get pfSense login details
source $BASEDIR/config.txt

# login
wget -qO/dev/null --keep-session-cookies --save-cookies $TMP_COOKIES \
 --post-data "login=Login&usernamefld=`echo $USER`&passwordfld=`echo $PASSWD`" \
 --no-check-certificate $PF_SERVER/diag_backup.php

# get all active users
wget --keep-session-cookies --load-cookies $TMP_COOKIES --no-check-certificate --output-document=$TMP_ALL \
  "$PF_SERVER/status_captiveportal.php?zone=$ZONE&order=&showact=&act=del&id=4c72b958c5d2dee1"

# loop over users and terminate all sessions
# should only be one, but I cloned the disconnect-all-users script
cat $TMP_ALL | grep -A3 `echo $MAC` | tail -1  | cut -d "\"" -f2 | while read -r url
do
  wget --keep-session-cookies --load-cookies $TMP_COOKIES --no-check-certificate  --output-document=$TMP_ALL2 \
    "$PF_SERVER/status_captiveportal.php$url"
done

rm $TMP_ALL
rm $TMP_ALL2
rm $TMP_COOKIES
