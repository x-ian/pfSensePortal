#!/bin/bash

# takes the radius account information to list the time a user (mac address) last connected

TODAY=`date +%Y-%m-%d`
USERS_CSV=users_last_seen_`echo $TODAY`.csv
RADIUS_DIR=/var/log/radacct/192.168.10.1
PFSENSE_CONFIG=/conf/config.xml
#PFSENSE_CONFIG=./config.xml

# --------------------------------------
# get all ever registered mac addresses and users
grep "<username>" $PFSENSE_CONFIG | sed  "s/<username>//g" | sed "s/<\/username>//g" | tr -d "(" | tr -d ")" | tr -d " " | tr -d "\t" >mac_addresses.txt

# --------------------------------------
# scan through all radius logs to find last time users showed up
echo 'MAC;TYPE;NAME;EMAIL;HOSTNAME;INITIAL_IP;REGISTERED;LAST_SEEN' >$USERS_CSV
for MAC in `cat mac_addresses.txt`; do 
  LAST_SEEN=`grep -l $MAC $RADIUS_DIR/detail-* | tail -1 | sed "s/^.*detail-//g"`
  USER_DETAILS=`grep -A 10 $MAC $PFSENSE_CONFIG | grep description | sed -e "s/<description><\!\[CDATA\[//g" | sed -e "s/\]\]><\/description>//g" | tr -d "\t" | tr -d " "`
  LINE="$MAC;$USER_DETAILS;$LAST_SEEN"
  echo $LINE >> $USERS_CSV
done

rm mac_addresses.txt
