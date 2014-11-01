#!/usr/local/bin/bash

# remotely download latest backup file

BASEDIR=/home/pfSensePortal

# get pfSense login details
source $BASEDIR/config.txt

# login
wget -qO/dev/null --keep-session-cookies --save-cookies cookies.txt \
 --post-data "login=Login&usernamefld=`echo $USER`&passwordfld=`echo $PASSWD`" \
 --no-check-certificate https://$PF_IP/diag_backup.php

wget --keep-session-cookies --load-cookies cookies.txt --no-check-certificate --output-document=all.html \
  "https://$PF_IP/status_captiveportal.php?zone=apzunet&order=&showact=&act=del&id=4c72b958c5d2dee1"

cat all.html | grep '<a href="?zone=apzunet&order=&showact=&act=del&id' | cut -c11-67 | while read -r url
do
  wget --keep-session-cookies --load-cookies cookies.txt --no-check-certificate  --output-document=all2.html \
    "https://$PF_IP/status_captiveportal.php$url"
done

rm all.html
rm all2.html
rm cookies.txt
