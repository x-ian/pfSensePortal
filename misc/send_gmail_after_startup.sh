#!/usr/local/bin/bash

BASEDIR=/home/pfSensePortal

SUBJECT="`echo "172.16.1.2 - pfSense: System startup: "` `date +%Y%m%d-%H%M`"
BODY=`echo "(mail generated by script pfSense:///home/pfSensePortal/misc/send_gmail_after_startup.sh)"`

/usr/bin/perl -I /usr/local/lib/perl5/site_perl/5.10.1/ -I /usr/local/lib/perl5/site_perl/5.10.1/mach $BASEDIR/send_gmail.perl "$SUBJECT" "$BODY"
