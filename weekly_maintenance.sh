#!/usr/local/bin/bash

# all the housekeeping stuff that I want to be done sunday nights
# let's say right before midnight
# 55 23 * * Sun ...

# do some accounting and clean it up
echo BIG_TIME_TODO

# compacting squid cache (http://doc.pfsense.org/index.php/Squid_Package_Tuning)
/usr/local/sbin/squid -k rotate

# some internal backup
/home/pfSensePortal/download_backup.sh

# just for the case, restart once in a while
/sbin/reboot

