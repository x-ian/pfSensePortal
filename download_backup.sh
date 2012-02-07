#!/usr/local/bin/bash

# remotely download latest backup file

BASEDIR=`dirname $0`
SERVER=https://172.16.1.2

# get pfSense login details
source $BASEDIR/credentials.config

# login
wget -qO/dev/null --keep-session-cookies --save-cookies cookies.txt \
 --post-data "login=Login&usernamefld=`echo $USER`&passwordfld=`echo $PASSWD`" \
 --no-check-certificate $SERVER/diag_backup.php

# get the stuff
wget --keep-session-cookies --load-cookies cookies.txt \
 --post-data 'Submit=download&donotbackuprrd=yes' $SERVER/diag_backup.php \
 --no-check-certificate -O config-router-`date +%Y%m%d%H%M%S`.xml