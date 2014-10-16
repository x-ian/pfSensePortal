#!/usr/local/bin/bash

# remotely download latest backup file

BASEDIR=/home/pfSensePortal
SERVER=https://172.16.1.2
CONFIG_FILE=config-router-`date +%Y%m%d%H%M%S`.xml

# get pfSense login details
source $BASEDIR/credentials.config

# login
wget -qO/dev/null --keep-session-cookies --save-cookies cookies.txt \
 --post-data "login=Login&usernamefld=`echo $USER`&passwordfld=`echo $PASSWD`" \
 --no-check-certificate $SERVER/diag_backup.php

# get the stuff
wget --keep-session-cookies --load-cookies cookies.txt \
 --post-data 'Submit=download&donotbackuprrd=yes' $SERVER/diag_backup.php \
 --no-check-certificate -O $CONFIG_FILE

scp /home/pfSensePortal/$CONFIG_FILE backup@dev.pih-emr.org:malawi/pfsense
mv /home/pfSensePortal/$CONFIG_FILE /home/pfSensePortal/backup_sequence


