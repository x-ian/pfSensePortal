#!/usr/local/bin/bash

# pings all network devices in the local network and sends out emails if some 
# are not responding

# best done with a daily cronjob like 0 7 * * * /home/pfSensePortal/monitor_network_devices.sh

BASEDIR=`dirname $0`
LOG=/tmp/monitor_network_devices.log

rm $LOG

for i in `cat $BASEDIR/monitor_network_devices.txt`; do
  /sbin/ping -t 4 $i # >> $LOG
  if [ $? -eq 0 ]; then
    echo  
    # echo $i alive >> $LOG
  else
    echo $i not alive >> $LOG
  fi
done

if [ -e $LOG ]; then
/usr/bin/perl -I /usr/local/lib/perl5/site_perl/5.10.1/ -I /usr/local/lib/perl5/site_perl/5.10.1/mach $BASEDIR/send_gmail_after_monitor_network_devices.perl `date +%Y%m%d-%H%M` "`cat $LOG`"
fi
