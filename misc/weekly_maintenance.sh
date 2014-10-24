#!/usr/local/bin/bash

# all the housekeeping stuff that I want to be done sunday nights
# let's say right before midnight
# 55 23 * * Sun ...

BASEDIR=/home/pfSensePortal

source $BASEDIR/credentials.config
 
# reset ntop traffic stats
/usr/local/bin/wget --user `echo $USER` --password `echo $PASSWD` http://172.16.1.2:3000/resetStats.html

# do some accounting and clean it up
echo BIG_TIME_TODO

# compacting squid cache (http://doc.pfsense.org/index.php/Squid_Package_Tuning)
#/usr/local/sbin/squid -k rotate

# clearing out and recreating the whole squid cache dir
/usr/local/sbin/squid -k shutdown
/bin/sleep 10
/bin/rm -rf /var/squid/cache/*
/usr/local/sbin/squid -z

# some internal backup
#/home/pfSensePortal/download_backup.sh

# clean up DHCP leases as they seem to be never removed. ideally this should maybe be done monthly or quarterly
/bin/rm -f /var/dhcpd/var/db/dhcpd.leases
/bin/rm -f /var/dhcpd/var/db/dhcpd.leases~

# just for the case, restart once in a while
/sbin/reboot

