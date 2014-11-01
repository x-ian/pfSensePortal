#!/usr/local/bin/bash

# remotely download latest backup file

BASEDIR=/home/pfSensePortal

# get pfSense login details
source $BASEDIR/config.txt

# login
wget -qO/dev/null --keep-session-cookies --save-cookies cookies.txt \
 --post-data "login=Login&usernamefld=`echo $USER`&passwordfld=`echo $PASSWD`" \
 --no-check-certificate https://$PF_IP/diag_backup.php

ssh root@172.16.1.3 'mysql --silent -u radius -pradius radius -e "select username from radacct WHERE (radacct.AcctStopTime IS NULL);"' > /tmp/active_users.txt
cat /tmp/active_users.txt | while read -r line
do 
echo $line
done

# get the stuff
#wget --keep-session-cookies --load-cookies cookies.txt --no-check-certificate \
 #https://$PF_IP/status_captiveportal.php?zone=apzunet&order=&showact=&act=del&id=6894230f2e07
 
